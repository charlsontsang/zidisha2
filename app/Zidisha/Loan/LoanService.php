<?php

namespace Zidisha\Loan;

use Carbon\Carbon;
use Faker\Provider\DateTime;
use Propel\Runtime\Connection\ConnectionInterface;
use Zidisha\Admin\Setting;
use Zidisha\Analytics\MixpanelService;
use Zidisha\Balance\Transaction;
use Zidisha\Balance\TransactionQuery;
use Zidisha\Balance\TransactionService;
use Zidisha\Borrower\Borrower;
use Zidisha\Currency\Converter;
use Zidisha\Currency\ExchangeRate;
use Zidisha\Currency\ExchangeRateQuery;
use Zidisha\Currency\Money;
use Zidisha\Generate\LoanGenerator;
use Zidisha\Lender\Lender;
use Zidisha\Loan\Calculator\BidsCalculator;
use Zidisha\Loan\Calculator\InstallmentCalculator;
use Zidisha\Loan\Calculator\RepaymentCalculator;
use Zidisha\Mail\BorrowerMailer;
use Zidisha\Mail\LenderMailer;
use Zidisha\Repayment\Installment;
use Zidisha\Repayment\InstallmentQuery;
use Zidisha\Repayment\RepaymentService;
use Zidisha\Vendor\PropelDB;
use Zidisha\Vendor\SiftScience\SiftScienceService;

class LoanService
{
    /**
     * @var \Zidisha\Balance\TransactionService
     */
    private $transactionService;
    /**
     * @var \Zidisha\Mail\LenderMailer
     */
    private $lenderMailer;
    /**
     * @var MixpanelService
     */
    private $mixpanelService;

    /**
     * @var \Zidisha\Mail\BorrowerMailer
     */
    private $borrowerMailer;

    protected $loanIndex;
    /**
     * @var \Zidisha\Vendor\SiftScience\SiftScienceService
     */
    private $siftScienceService;
    private $repaymentService;

    public function __construct(
        TransactionService $transactionService,
        LenderMailer $lenderMailer,
        MixpanelService $mixpanelService,
        BorrowerMailer $borrowerMailer,
        SiftScienceService $siftScienceService,
        RepaymentService $repaymentService
    )
    {
        $this->transactionService = $transactionService;
        $this->lenderMailer = $lenderMailer;
        $this->mixpanelService = $mixpanelService;
        $this->borrowerMailer = $borrowerMailer;
        $this->siftScienceService = $siftScienceService;
        $this->repaymentService = $repaymentService;
    }

    public function applyForLoan(Borrower $borrower, $data)
    {
        $loan = $this->createLoan($borrower, $data);
        
        $borrower
            ->setActiveLoan($loan)
            ->setLoanStatus(Loan::OPEN);
        
        PropelDB::transaction(function($con) use ($loan, $borrower) {
            $loan->save($con);
            $borrower->save($con);

            $this->changeLoanStage($con, $loan, null, Loan::OPEN, $loan->getAppliedAt());
        });

        $this->borrowerMailer->sendLoanConfirmationMail($borrower, $loan);
        // TODO send mail to previous lenders
        
        $this->addToLoanIndex($loan);
        
        return $loan;
    }

    public function createLoan(Borrower $borrower, $data)
    {
        if (!isset($data['date'])) {
            $data['date'] = new \DateTime();
            $data['exchangeRate'] = ExchangeRateQuery::create()->findCurrent($borrower->getCountry()->getCurrency());
        }

        /** @var ExchangeRate $exchangeRate */
        $exchangeRate = $data['exchangeRate'];
        
        $currencyCode = $borrower->getCountry()->getCurrencyCode();
        $data['usdAmount'] = Converter::toUSD(
            Money::create($data['amount'], $currencyCode),
            $exchangeRate
        )->getAmount();

        $siftScienceScore = $this->siftScienceService->getSiftScore($borrower->getUser());

        $isFirstLoan = LoanQuery::create()
            ->filterByBorrower($borrower)
            ->filterByStatus(Loan::REPAID) // TODO correct? verify database
            ->count();
        $registrationFee = $isFirstLoan ? $borrower->getCountry()->getRegistrationFee() : 0;
        
        $loan = new Loan();
        $loan
            ->setSummary($data['summary'])
            ->setProposal($data['proposal'])
            ->setCurrencyCode($currencyCode)
            ->setAmount(Money::create($data['amount'], $currencyCode))
            ->setUsdAmount(Money::create($data['usdAmount'], 'USD'))
            ->setInstallmentPeriod($borrower->getCountry()->getInstallmentPeriod())
            ->setMaxInterestRate(Setting::get('loan.maximumLenderInterestRate') + Setting::get('loan.transactionFeeRate'))
            ->setServiceFeeRate(Setting::get('loan.transactionFeeRate'))
            ->setInstallmentDay($data['installmentDay'])
            ->setAppliedAt($data['date'])
            ->setCategoryId($data['categoryId'])
            ->setBorrower($borrower)
            ->setRegistrationFee($registrationFee)
            ->setStatus(Loan::OPEN)
            ->setSiftScienceScore($siftScienceScore);

        $calculator = new InstallmentCalculator($loan);
        $installmentAmount = Money::create($data['installmentAmount'], $currencyCode);
        $period = $calculator->period($installmentAmount);
        
        $loan->setPeriod($period);
        
        return $loan;
    }

    protected function getLoanIndex()
    {
        if ($this->loanIndex) {
            return $this->loanIndex;
        }

        $elasticaClient = new \Elastica\Client();

        $loanIndex = $elasticaClient->getIndex('loans');

        if (!$loanIndex->exists()) {
            $loanIndex->create(
                array(
                    'number_of_shards' => 1,
                    'number_of_replicas' => 1,
                    'analysis' => array(
                        'analyzer' => array(
                            'default_index' => array(
                                'type' => 'custom',
                                'tokenizer' => 'standard',
                                'filter' => array('lowercase')
                            ),
                            'default_search' => array(
                                'type' => 'custom',
                                'tokenizer' => 'standard',
                                'filter' => array('standard', 'lowercase')
                            )
                        ),
                    )
                )
            );
        }

        $this->loanIndex = $loanIndex;

        return $loanIndex;
    }

    public function searchLoans($conditions = array(), $page = 1, $limit = 18)
    {
        $conditions += ['search' => false];
        $search = $conditions['search'];
        unset($conditions['search']);

        $queryString = new \Elastica\Query\QueryString();

        $loanIndex = $this->getLoanIndex();

        $query = new \Elastica\Query();

        if ($search) {
            $queryString->setDefaultOperator('AND');
            $queryString->setQuery($search);
            $query->setQuery($queryString);
        }

        $filterAnd = new \Elastica\Filter\BoolAnd();
        foreach ($conditions as $field => $value) {
            $termFilter = new \Elastica\Filter\Term();
            $termFilter->setTerm($field, $value);

            $filterAnd->addFilter($termFilter);
        }
        if ($conditions) {
            $query->setFilter($filterAnd);
        }

        $query->setFrom(($page - 1) * $limit);
        $query->setSize($page * $limit);

        $results = $loanIndex->search($query);

        $ids = [];

        foreach ($results as $result) {
            $data = $result->getData();
            $ids[$data['id']] = $data['id'];
        }

        $loans = LoanQuery::create()->filterById($ids)->find();
        $sortedLoans = $ids;

        foreach ($loans as $loan) {
            $sortedLoans[$loan->getId()] = $loan;
        }
        $sortedLoans = array_filter(
            $sortedLoans,
            function ($l) {
                return !is_scalar($l);
            }
        );

        $paginatorFactory = \App::make('paginator');

        return $paginatorFactory->make(
            $sortedLoans,
            $results->getTotalHits(),
            $limit
        );
    }

    public function addToLoanIndex(Loan $loan)
    {
        if (\App::environment("testing")) {
            return;
        }

        $loanIndex = $this->getLoanIndex();

        $loanType = $loanIndex->getType('loan');

        $data = array(
            'id'                => $loan->getId(),
            'category'          => $loan->getCategory()->getName(),
            'categoryId'        => $loan->getCategory()->getId(),
            'countryId'         => $loan->getBorrower()->getCountry()->getId(),
            'country_code'      => $loan->getBorrower()->getCountry()->getCountryCode(),
            'summary'           => $loan->getSummary(),
            'proposal'          => $loan->getProposal(),
            'status'            => $loan->getStatus(),
            'created_at'        => $loan->getCreatedAt()->getTimestamp(),
            'raised_percentage' => $loan->getRaisedPercentage(),
        );

        $loanDocument = new \Elastica\Document($loan->getId(), $data);
        $loanType->addDocument($loanDocument);
        $loanType->getIndex()->refresh();
    }

    public function placeBid(Loan $loan, Lender $lender, $data)
    {
        $data += [
            'bidAt'                => new \DateTime(),
            'isLenderInviteCredit' => false,
            'isAutomatedLending'   => false,
        ];
        
        /** @var Bid $bid */
        list($bid, $changedBids) = PropelDB::transaction(function($con) use($loan, $lender, $data) {
            $oldBids = BidQuery::create()
                ->getOrderedBids($loan)
                ->find();
            
            $bid = $this->createBid($con, $loan, $lender, $data);

            $newBids = BidQuery::create()
                ->getOrderedBids($loan)
                ->find();

            $changedBids = $this->processBids($con, $loan, $oldBids, $newBids);

            $totalBidAmount = BidQuery::create()
                ->filterByLoan($loan)
                ->getTotalBidAmount();
            
            $loan->setRaisedUsdAmount($totalBidAmount);
            $loan->save();
            
            return [$bid, $changedBids];
        });

        // Mixpanel
        // TODO not working for automatic lending
        $this->mixpanelService->trackPlacedBid($bid);

        // Emails
        // First bid placed confirmation
        $isFirstBid = BidQuery::create()
            ->filterByLender($lender)
            ->count() == 1;

        if ($isFirstBid) {
            $this->lenderMailer->sendFirstBidConfirmationMail($bid);
        }

        // Outbid notification
        foreach ($changedBids as $bidId => $changedBid) {
            if ($changedBid['type'] == 'out_bid') {
                $this->lenderMailer->sendOutbidMail($changedBid);
            }
        }
        
        // Fully Funded notifications
        if ($loan->isFullyFunded()) {
            $bids = BidQuery::create()
                ->filterByLoan($loan)
                ->joinWith('Lender')
                ->joinWith('Lender.User')
                ->find();

            foreach ($bids as $_bid) {
                $this->lenderMailer->sendLoanFullyFundedMail($_bid);
            }
        }

        //Todo: refresh elastic search index.

        return $bid;
    }

    protected function createBid(ConnectionInterface $con, Loan $loan, Lender $lender, $data)
    {
        $bidAmount = Money::create($data['amount'], 'USD');

        $bid = new Bid();
        $bid
            ->setLoan($loan)
            ->setLender($lender)
            ->setBorrower($loan->getBorrower())
            ->setBidAmount($bidAmount)
            ->setInterestRate($data['interestRate'])
            ->setIsLenderInviteCredit($data['isLenderInviteCredit'])
            ->setIsAutomatedLending($data['isAutomatedLending'])
            ->setBidAt($data['bidAt']);

        $bid->save($con);

        return $bid;
    }

    protected function processBids($con, Loan $loan, $oldBids, $newBids)
    {
        $bidsCalculator = new BidsCalculator();
        
        $oldAcceptedBids = $bidsCalculator->getAcceptedBids($oldBids, $loan->getUsdAmount());
        $newAcceptedBids = $bidsCalculator->getAcceptedBids($newBids, $loan->getUsdAmount());
        $changedBids = $bidsCalculator->getChangedBids($oldAcceptedBids, $newAcceptedBids);

        foreach ($changedBids as $bidId => $changedBid) {
            if ($changedBid['type'] == 'out_bid') {
                $this->transactionService->addOutBidTransaction(
                    $con,
                    $changedBid['changedAmount'],
                    $loan,
                    $changedBid['bid']
                );
            } elseif ($changedBid['type'] == 'update_bid') {
                $this->transactionService->addUpdateBidTransaction(
                    $con,
                    $changedBid['changedAmount'],
                    $loan,
                    $changedBid['bid']
                );
            } elseif ($changedBid['type'] == 'place_bid') {
                $this->transactionService->addPlaceBidTransaction(
                    $con,
                    $changedBid['acceptedAmount'],
                    $loan,
                    $changedBid['bid']
                );
            }
        }

        return $changedBids;
    }

    public function editBid(Bid $bid, $data)
    {
        $loan = $bid->getLoan();

        PropelDB::transaction(function($con) use ($bid, $loan, $data) {
            $oldBids = BidQuery::create()
                ->getOrderedBids($loan)
                ->find();

            $bid
                ->setBidAmount(Money::create($data['amount'], 'USD'))
                ->setInterestRate($data['interestRate']);
            
            $bid->save();

            $newBids = BidQuery::create()
                ->getOrderedBids($loan)
                ->find();

            $changedBids = $this->processBids($con, $loan, $oldBids, $newBids);

            $totalBidAmount = BidQuery::create()
                ->filterByLoan($loan)
                ->getTotalBidAmount();

            $loan->setRaisedUsdAmount($totalBidAmount);
            
            $loan->save();
        });

        //Todo: refresh elastic search.
        return $bid;
    }

    public function acceptBids(Loan $loan, $data = [])
    {
        $data += [
            'acceptedAt' => new \DateTime(),            
        ];

        $bids = BidQuery::create()
            ->getOrderedBids($loan)
            ->find();

        $bidsCalculator = new BidsCalculator();
        $acceptedBids = $bidsCalculator->getAcceptedBids($bids, $loan->getUsdAmount());

        PropelDB::transaction(function($con) use ($acceptedBids, $loan, $data, $bidsCalculator) {
            /** @var AcceptedBid $acceptedBid */
            foreach ($acceptedBids as $bidId => $acceptedBid) {
                $acceptedAmount = $acceptedBid->getAcceptedAmount();
                $bid = $acceptedBid->getBid();
                
                if ($acceptedAmount->isPositive()) {
                    $bid
                        ->setActive(true)
                        ->setAcceptedAmount($acceptedAmount);
                    $bid->save($con);
                }
            }

            $totalInterest = $bidsCalculator->getLenderInterestRate($acceptedBids, $loan->getUsdAmount());
            $loan
                ->setStatus(Loan::FUNDED)
                ->setAcceptedAt($data['acceptedAt'])
                ->setLenderInterestRate($totalInterest)
                ->save($con);

            $this->changeLoanStage($con, $loan, Loan::OPEN, Loan::FUNDED);

            $loan->getBorrower()->setActiveLoan($loan);
            $loan->save($con);
        });
        
        //TODO send emails

        return $acceptedBids;
    }

    public function expireLoan(Loan $loan, $data = [])
    {
        $data += [
            'expiredAt' => new \DateTime()
        ];

        $loan
            ->setStatus(Loan::EXPIRED)
            ->setExpiredAt($data['expiredAt']);

        $borrower = $loan->getBorrower();
        $borrower
            ->setActiveLoan(null)
            ->setLoanStatus(Loan::NO_LOAN);
        
        $lenderRefunds = PropelDB::transaction(function($con) use ($loan, $borrower) {
            $loan->save($con);
            $borrower->save($con);

            $this->changeLoanStage($con, $loan, Loan::OPEN, Loan::EXPIRED);

            $lenderRefunds = $this->refundLenders($con, $loan);

            if ($loan->getStatus() == Loan::FUNDED) {
                BidQuery::create()
                    ->filterByLoan($loan)
                    ->update(['active' => 0, 'accepted_amount' => null], $con);
            }
            
            return $lenderRefunds;
        });

        foreach ($lenderRefunds as $refundLender) {
            $this->lenderMailer->sendExpiredLoanMail($loan, $refundLender);
        }
        
        $this->borrowerMailer->sendExpiredLoanMail($loan);
        
        return $lenderRefunds;
    }

    public function cancelLoan(Loan $loan, $data = [])
    {
        $data += [
            'canceledAt' => new \DateTime()
        ];

        $loan
            ->setStatus(Loan::CANCELED)
            ->setCanceledAt($data['canceledAt']);

        $borrower = $loan->getBorrower();
        $borrower
            ->setActiveLoan(null)
            ->setLoanStatus(Loan::NO_LOAN);
        
        $lenderRefunds = PropelDB::transaction(function($con) use($loan, $borrower) {
            $loan->save($con);
            $borrower->save($con);
            
            $this->changeLoanStage($con, $loan, Loan::OPEN, Loan::CANCELED);
            
            $lenderRefunds = $this->refundLenders($con, $loan);
            
            return $lenderRefunds;
        });
        
        // TODO emails to refunded lenders

        return $lenderRefunds;
    }

    protected function refundLenders(ConnectionInterface $con, Loan $loan)
    {
        $status = $loan->getStatus();
        
        $transactions = TransactionQuery::create()
            ->filterByLoan($loan)
            ->filterLoanBids()
            ->joinWith('Bid')
            ->joinWith('Bid.Lender')
            ->find();
        
        $lenderRefunds = $this->getLenderRefunds($transactions);
        $totalLenderInviteCredit = Money::create(0);

        /** @var LenderRefund $lenderRefund */
        foreach ($lenderRefunds as $lenderRefund) {
            if ($lenderRefund->getTotalAmount()->isZero()) {
                continue;
            }
            
            if ($lenderRefund->getAmount()->isPositive()) {
                if ($status == Loan::CANCELED) {
                    $this->transactionService->addLoanBidCanceledTransaction(
                        $con,
                        $lenderRefund->getAmount(),
                        $loan,
                        $lenderRefund->getLender()
                    );
                } else {
                    $this->transactionService->addLoanBidExpiredTransaction(
                        $con,
                        $lenderRefund->getAmount(),
                        $loan,
                        $lenderRefund->getLender()
                    );
                }   
            }

            if ($lenderRefund->getLenderInviteCredit()->isPositive()) {
                $totalLenderInviteCredit = $totalLenderInviteCredit->add($lenderRefund->getLenderInviteCredit());
            }
        }
        
        if ($totalLenderInviteCredit->isPositive()) {
            if ($status == Loan::CANCELED) {
                $this->transactionService->addLenderInviteCreditLoanBidCanceledTransaction(
                    $con,
                    $totalLenderInviteCredit,
                    $loan
                );
            } else {
                $this->transactionService->addLenderInviteCreditLoanBidExpiredTransaction(
                    $con,
                    $totalLenderInviteCredit,
                    $loan
                );
            }
        }
        
        return $lenderRefunds;
    }

    private function changeLoanStage(
        ConnectionInterface $con,
        Loan $loan,
        $oldStatus = null,
        $newStatus,
        \DateTime $date = null
    )
    {
        $date = $date ? : new \DateTime();

        $newLoanStage = new Stage();
        $newLoanStage->setLoan($loan)
            ->setBorrower($loan->getBorrower())
            ->setStatus($newStatus)
            ->setStartDate($date);

        if ($oldStatus) {
            $currentLoanStage = StageQuery::create()
                ->filterByLoan($loan)
                ->findOneByStatus($oldStatus);

            if ($currentLoanStage) {
                $currentLoanStage->setEndDate($date);
                $currentLoanStage->save($con);
            }
        }

        $newLoanStageSuccess = $newLoanStage->save($con);

        if (!$newLoanStageSuccess) {
            throw new \Exception();
        }
    }

    public function disburseLoan(Loan $loan, \DateTime $disbursedAt, Money $amount)
    {
        $isDisbursed = TransactionQuery::create()
            ->filterByLoan($loan)
            ->filterDisbursement()
            ->count();

        if ($isDisbursed) {
            // TODO
            return;
        }

        PropelDB::transaction(function($con) use ($loan, $disbursedAt, $amount) {
            $this->transactionService->addDisbursementTransaction($con, $amount, $loan);

            $loans = LoanQuery::create()
                ->filterByBorrower($loan->getBorrower())
                ->count();
            if ($loans == 1) {
                $this->transactionService->addFeeTransaction($con, $amount, $loan);
            }

            $loan
                ->setStatus(Loan::ACTIVE)
                ->setDisbursedAmount($amount)
                ->setDisbursedAt($disbursedAt)
                ->calculateExtraDays($disbursedAt)
                ->setServiceFeeRate(Setting::get('loan.serviceFeeRate'));

            $installments = $this->generateLoanInstallments($loan);

            $totalAmount = Money::create(0, $loan->getCurrency());
            /** @var Installment $installment */
            foreach ($installments as $installment) {
                $totalAmount = $totalAmount->add($installment->getAmount());
                $installment->save($con);
            }

            $loan->setTotalAmount($totalAmount);
            $loan->save($con);

            $this->changeLoanStage($con, $loan, Loan::FUNDED, Loan::ACTIVE);
        });

        //TODO Send email / sift sience event
    }

    protected function getLenderRefunds($transactions)
    {
        $refunds = [];
        $zero = Money::create(0);
        /** @var Transaction $transaction */
        foreach ($transactions as $transaction) {
            $userId = $transaction->getUserId();
            
            if (!isset($refunds[$userId])) {
                $refunds[$userId] = [
                    'lender'             => $transaction->getBid()->getLender(),
                    'amount'             => $zero,
                    'lenderInviteCredit' => $zero,
                ];
            }
            
            if ($transaction->getBid()->getIsLenderInviteCredit()) {
                $refunds[$userId]['lenderInviteCredit'] = $refunds[$userId]['lenderInviteCredit']->subtract($transaction->getAmount());
            } else {
                $refunds[$userId]['amount'] = $refunds[$userId]['amount']->subtract($transaction->getAmount());
            }
        }

        $lenderRefunds = [];
        foreach ($refunds as $id => $refund) {
            if ($refunds[$id]['amount']->isNegative()) {
                $refunds[$id]['amount'] = $zero;
            }
            if ($refunds[$id]['lenderInviteCredit']->isNegative()) {
                $refunds[$id]['lenderInviteCredit'] = $zero;
            }
            
            $lenderRefunds[$id] = new LenderRefund($refund);
        }

        return $lenderRefunds;
    }

    public function generateLoanInstallments(Loan $loan)
    {
        $calculator = new Calculator\InstallmentCalculator($loan);
        $installmentAmount = $calculator->installmentAmount();
        $period = $loan->getPeriod();

        $installments = [];

        $graceInstallment = new Installment();
        $graceInstallment
            ->setLoan($loan)
            ->setBorrower($loan->getBorrower())
            ->setAmount(Money::create(0, $loan->getCurrencyCode()))
            ->setDueDate($calculator->installmentGraceDate());

        $installments[] = $graceInstallment;

        for ($count = 1; $count <= $period; $count++) {
            $installment = new Installment();
            $installment
                ->setLoan($loan)
                ->setBorrower($loan->getBorrower())
                ->setAmount($installmentAmount)
                ->setDueDate($calculator->nthInstallmentDate($count));
            $installments[] = $installment;
        }

        return $installments;
    }

    public function updateLoanCategories(Loan $loan, $data)
    {
        $category = CategoryQuery::create()
            ->findOneById($data['category']);
        $secondaryCategory = CategoryQuery::create()
            ->findOneById($data['secondaryCategory']);

        $loan->setCategory($category)
            ->setSecondaryCategory($secondaryCategory);

        $loan->save();

    }

    public function addTranslations(Loan $loan, $data)
    {
        $profile = $loan->getBorrower()->getProfile();

        $profile->setAboutMeTranslation($data['translateAboutMe'])
            ->setAboutBusinessTranslation($data['translateAboutBusiness']);
        $loan->setProposalTranslation($data['translateProposal']);

        $profile->save();
        $loan->save();
    }

    public function hasFunded(Lender $lender, Borrower $borrower)
    {
        $count = BidQuery::create()
            ->filterByLender($lender)
            ->filterByActive(true)
            ->useLoanQuery()
                ->filterByBorrower($borrower)
            ->endUse()
            ->count();
        
        return $count > 0;
    }

    public function getOnTimeRepaymentScore(Borrower $borrower)
    {
        $BorrowerLoans = LoanQuery::create()
            ->filterByBorrower($borrower)
            ->find();
        $onTimeInstallmentCount=0;
        $totalTodayInstallmentCount=0;

        foreach ($BorrowerLoans as $loan) {
            $repaymentSchedule = $this->repaymentService->getRepaymentSchedule($loan);
            $totalTodayInstallmentCount += $repaymentSchedule->getTodayInstallmentCount();
            $onTimeInstallmentCount += $repaymentSchedule->getPaidOnTimeInstallmentCount();
        }
        if ($totalTodayInstallmentCount == 0) {
            $repaymentScore = 100;
        }else {
            $repaymentRate = ($onTimeInstallmentCount/$totalTodayInstallmentCount)*100;

            if (empty($repaymentRate) || $repaymentRate < 0) {
                $repaymentRate = 0;
            }
            $repaymentScore = number_format($repaymentRate,2, '.', ',');
        }
        return $repaymentScore;
    }

    public function writeOffLoans()
    {
        $sixMonthsAgo = Carbon::now()->subMonths(6);

        $query = "SELECT l.id, l.borrower_id, l.status, rc.max_paid_date, rc.max_due_date, rc.min_due_date, l.disbursed_amount
                FROM loans l JOIN (
                    SELECT r.loan_id, MAX (r.paid_date) AS max_paid_date, MAX (r.due_date) AS max_due_date, MIN (r.due_date) AS min_due_date
                    FROM installments r
                    WHERE r.amount > 0
                    GROUP BY r.loan_id
                ) rc ON l.id = rc.loan_id
                WHERE
                    l.status = :status
                AND (
                    rc.max_due_date < :sixMonthsAgo
                    OR rc.max_paid_date < :sixMonthsAgo
                    OR (
                        rc.min_due_date < :sixMonthsAgo
                        AND rc.max_paid_date IS NULL
                    )
                )";

        $loans = PropelDB::fetchAll(
            $query,
            [
                'status' => Loan::ACTIVE,
                'sixMonthsAgo' => $sixMonthsAgo,
            ]
        );

        foreach ($loans as $loan) {
            $this->defaultLoan($loan);
        }
    }

    public function defaultLoan(Loan $loan)
    {
        PropelDB::transaction(function($con) use ($loan) {
            $loan->setStatus(Loan::DEFAULTED);
            $loan->save($con);
            $this->changeLoanStage($con, $loan, Loan::ACTIVE, Loan::DEFAULTED);
        });

        $lenders = [];
        // TODO
        $calculator = new RepaymentCalculator($loan);
        $bids = BidQuery::create()
            ->filterBidsToRepay($loan)
            ->find();
        //$loanRepayments = $calculator->loanRepayments($exchangeRate, $bids);

        foreach ($lenders as $lender) {
            $this->lenderMailer->sendLoanDefaultedMail($loan, $lender);
        }
    }
    
    public function expireLoans()
    {
        //TODO: 
        $thresholdDays = Setting::get('loan.expireThreshold');
        $expireThreshold = Carbon::create()->subDays($thresholdDays);
                
        $loans = LoanQuery::create()
            ->filterByStatus(Loan::OPEN)
            ->where('loans.applied_at < ?', $expireThreshold)
            ->find();
        
        foreach($loans as $loan) {
            $percentageRaised = $loan->getRaisedPercentage();
            if($percentageRaised < 100) {
                $this->expireLoan($loan);
            }
        }
    }

    public function isRepaidOnTime(Borrower $borrower, Loan $loan)
    {
        $installment = InstallmentQuery::create()
            ->filterByBorrower($borrower)
            ->filterByLoan($loan)
            ->orderById('desc')
            ->findOne();
        $repaymentThreshold = \Config::get('constants.repaymentThreshold');

        return Carbon::instance($installment->getPaidDate())->diffInDays($installment->getDueDate()) <= $repaymentThreshold;
    }
}
