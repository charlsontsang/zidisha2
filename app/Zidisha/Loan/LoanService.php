<?php

namespace Zidisha\Loan;

use Carbon\Carbon;
use Propel\Runtime\Connection\ConnectionInterface;
use Zidisha\Admin\Setting;
use Zidisha\Analytics\MixpanelService;
use Zidisha\Balance\TransactionQuery;
use Zidisha\Balance\TransactionService;
use Zidisha\Borrower\Borrower;
use Zidisha\Currency\Converter;
use Zidisha\Currency\ExchangeRateQuery;
use Zidisha\Currency\Money;
use Zidisha\Lender\Lender;
use Zidisha\Loan\Calculator\InstallmentCalculator;
use Zidisha\Loan\Calculator\RepaymentCalculator;
use Zidisha\Mail\BorrowerMailer;
use Zidisha\Mail\LenderMailer;
use Zidisha\Repayment\Installment;
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

            $this->changeLoanStage($con, $loan, null, Loan::OPEN);
        });

        $this->borrowerMailer->sendLoanConfirmationMail($borrower, $loan);
        // TODO send mail to previous lenders
        
        $this->addToLoanIndex($loan);
        
        return $loan;
    }

    public function createLoan(Borrower $borrower, $data)
    {
        $exchangeRate = ExchangeRateQuery::create()->findCurrent($borrower->getCountry()->getCurrency());
        $currencyCode = $borrower->getCountry()->getCurrencyCode();
        $data['usdAmount'] = Converter::toUSD(
            Money::create($data['amount'], $currencyCode),
            $exchangeRate
        )->getAmount();

        $siftScienceScore = $this->siftScienceService->getSiftScore($borrower->getUser());

        $isFirstLoan = LoanQuery::create()
            ->filterByBorrower($borrower)
            ->filterByStatus(Loan::REPAID) // TODO correct?
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
            ->setInterestRate(20) // TODO from settings serviceFee or limit
            ->setInstallmentDay($data['installmentDay'])
            ->setAppliedAt(new \DateTime())
            ->setCategoryId($data['categoryId'])
            ->setBorrower($borrower)
            ->setRegistrationFee($registrationFee)
            ->setStatus(Loan::OPEN)
            ->setSiftScienceScore($siftScienceScore);

        $calculator = new InstallmentCalculator($loan);
        $installmentAmount = Money::create($data['installmentAmount'], $currencyCode);
        $installmentCount = $calculator->calculateInstallmentCount($installmentAmount);
        
        $loan->setInstallmentCount($installmentCount);
        
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

    public function searchLoans($conditions = array(), $page = 1, $limit = 20)
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
        //Todo: Lender Invite Credit.

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
            ->setActive(true)
            ->setBidAt(new \DateTime());

        $bid->save($con);

        return $bid;
    }

    protected function getAcceptedBids($bids, Money $loanAmount)
    {
        $zero = Money::create(0, 'USD');
        $totalBidAmount = $zero;
        $acceptedBids = [];

        foreach ($bids as $bid) {
            $bidAmount = $bid->getBidAmount();
            $missingAmount = $loanAmount->subtract($totalBidAmount)->max($zero)->round(3);
            $totalBidAmount = $totalBidAmount->add($bidAmount);
            $acceptedAmount = $missingAmount->min($bidAmount);

            $acceptedBids[$bid->getId()] = compact('bid', 'acceptedAmount');
        }

        // Sort by bid date
        // TODO: why?
        uasort(
            $acceptedBids,
            function ($b1, $b2) {
                return $b1['bid']->getBidAt() <= $b2['bid']->getBidAt();
            }
        );

        return $acceptedBids;
    }

    protected function getChangedBids($oldAcceptedBids, $newAcceptedBids)
    {
        $changedBids = [];

        foreach ($newAcceptedBids as $bidId => $acceptedBid) {
            /** @var Money $acceptedAmount */
            $acceptedAmount = $acceptedBid['acceptedAmount'];
            /** @var Bid $bid */
            $bid = $acceptedBid['bid'];
            if (isset($oldAcceptedBids[$bidId])) {
                /** @var Money $oldAcceptedAmount */
                $oldAcceptedAmount = $oldAcceptedBids[$bidId]['acceptedAmount'];
                if ($oldAcceptedAmount->greaterThan($acceptedAmount)) {
                    $changedBids[$bidId] = [
                        'bid'            => $bid,
                        'acceptedAmount' => $acceptedAmount,
                        'type'           => 'out_bid',
                        'changedAmount'  => $oldAcceptedAmount->subtract($acceptedAmount),
                    ];
                } else {
                    if ($oldAcceptedAmount->lessThan($acceptedAmount)) {
                        $changedBids[$bidId] = [
                            'bid'            => $bid,
                            'acceptedAmount' => $acceptedAmount,
                            'type'           => 'update_bid',
                            'changedAmount'  => $acceptedAmount->subtract($oldAcceptedAmount),
                        ];
                    }
                }
            } elseif ($acceptedAmount->greaterThan(Money::create(0))) {
                $changedBids[$bidId] = [
                    'bid'            => $bid,
                    'acceptedAmount' => $acceptedAmount,
                    'type'           => 'place_bid',
                    'changedAmount'  => $acceptedAmount,
                ];
            }
        }

        return $changedBids;
    }

    private function processBids($con, Loan $loan, $oldBids, $newBids)
    {
        $oldAcceptedBids = $this->getAcceptedBids($oldBids, $loan->getUsdAmount());
        $newAcceptedBids = $this->getAcceptedBids($newBids, $loan->getUsdAmount());
        $changedBids = $this->getChangedBids($oldAcceptedBids, $newAcceptedBids);

        foreach ($changedBids as $bidId => $changedBid) {
            if ($changedBid['type'] == 'out_bid') {
                $this->transactionService->addOutBidTransaction(
                    $con,
                    $changedBid['changedAmount'],
                    $loan,
                    $changedBid['bid']->getLender()
                );
            } elseif ($changedBid['type'] == 'update_bid') {
                $this->transactionService->addUpdateBidTransaction(
                    $con,
                    $changedBid['changedAmount'],
                    $loan,
                    $changedBid['bid']->getLender()
                );
            } elseif ($changedBid['type'] == 'place_bid') {
                $this->transactionService->addPlaceBidTransaction(
                    $con,
                    $changedBid['acceptedAmount'],
                    $loan,
                    $changedBid['bid']->getLender()
                );
            }
        }

        return $changedBids;
    }

    public function editBid(Bid $bid, $data)
    {
        $loan = $bid->getLoan();

        $totalBidAmount = BidQuery::create()
            ->filterByLoan($loan)
            ->getTotalBidAmount();

        $bid
            ->setBidAmount(Money::create($data['amount'], 'USD'))
            ->setInterestRate($data['interestRate']);

        $loan->calculateAmountRaised($totalBidAmount);

        PropelDB::transaction(function($con) use ($bid, $loan) {
            $bid->save($con);

            $loan->save();
        });

        //Todo: refresh elastic search.
        return $bid;
    }

    public function acceptBids(Loan $loan)
    {
        $bids = BidQuery::create()
            ->getOrderedBids($loan)
            ->find();

        $acceptedBids = $this->getAcceptedBids($bids, $loan->getUsdAmount());

        PropelDB::transaction(function($con) use ($acceptedBids, $loan) {
            $totalAmount = Money::create(0);

            foreach ($acceptedBids as $bidId => $acceptedBid) {
                /** @var Money $acceptedAmount */
                $acceptedAmount = $acceptedBid['acceptedAmount'];
                /** @var Bid $bid */
                $bid = $acceptedBid['bid'];
                if ($acceptedAmount->greaterThan(Money::create(0))) {
                    $bid
                        ->setActive(true)
                        ->setAcceptedAmount($acceptedAmount);
                    $bid->save($con);
                    $totalAmount = $totalAmount->add($acceptedAmount->multiply(0.01 * $bid->getInterestRate()));
                }
            }

            $totalInterest = $totalAmount->divide($loan->getAmount()->getAmount())->round(2)->getAmount();
            $loan
                ->setStatus(Loan::FUNDED)
                ->setInterestRate($totalInterest)
                ->setAcceptedAt(new \DateTime())
                ->setFinalInterestRate($totalInterest)
                ->save($con);

            $this->changeLoanStage($con, $loan, Loan::OPEN, Loan::FUNDED);

            $loan->getBorrower()->setActiveLoan($loan);
            $loan->save($con);
        });
        
        //TODO send emails

        return true;
    }

    public function expireLoan(Loan $loan)
    {
        PropelDB::transaction(function($con) use ($loan) {
            $loan->setStatus(Loan::EXPIRED)
                ->setExpiredAt(new \DateTime());
            $loan->save($con);

            $loan->getBorrower()
                ->setActiveLoan(null)
                ->setLoanStatus(Loan::NO_LOAN);
            $loan->save($con);

            $this->changeLoanStage($con, $loan, Loan::OPEN, Loan::EXPIRED);

            $refunds = $this->refundLenders($con, $loan, Loan::EXPIRED);

            if ($loan->getStatus() == Loan::FUNDED) {
                BidQuery::create()
                    ->filterByLoan($loan)
                    ->update(['active' => 0, 'accepted_amount' => null], $con);
            }
        });

        // Todo: send emails to notify lenders use $refunds
        
        return true;
    }

    public function cancelLoan(Loan $loan)
    {
        PropelDB::transaction(function($con) use($loan) {
            $loan
                ->setStatus(Loan::CANCELED)
                ->setExpiredAt(new \DateTime());
            $loan->save($con);

            $borrower = $loan->getBorrower();
            $borrower
                ->setActiveLoan(null)
                ->setLoanStatus(Loan::NO_LOAN);
            $borrower->save($con);

            $this->changeLoanStage($con, $loan, Loan::OPEN, Loan::CANCELED);
            $this->refundLenders($con, $loan, Loan::CANCELED);
        });
        
        // TODO emails to refunded lenders

        return true;
    }

    protected function refundLenders(ConnectionInterface $con, Loan $loan, $status = Loan::EXPIRED)
    {
        $transactions = TransactionQuery::create()
            ->filterByLoan($loan)
            ->filterLoanBids()
            ->find();

        $refunds = $this->getLenderRefunds($transactions);

        foreach ($refunds as $refund) {
            if (!$refund['refundAmount']->greaterThan(Money::create(0))) {
                continue;
            }

            if ($status == Loan::CANCELED) {
                $this->transactionService->addLoanBidCanceledTransaction(
                    $con,
                    $refund['refundAmount'],
                    $loan,
                    $refund['lender']
                );
            } else {
                $this->transactionService->addLoanBidExpiredTransaction(
                    $con,
                    $refund['refundAmount'],
                    $loan,
                    $refund['lender']
                );
            }
        }
        // TODO: lender invite

        return $refunds;
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

    public function disburseLoan(Loan $loan, \DateTime $disbursedAt, Money $nativeAmount)
    {
        $isDisbursed = TransactionQuery::create()
            ->filterByLoan($loan)
            ->filterDisbursement()
            ->count();

        if ($isDisbursed) {
            // TODO
            return;
        }

        PropelDB::transaction(function($con) use ($loan, $disbursedAt, $nativeAmount) {
            $this->transactionService->addDisbursementTransaction($con, $nativeAmount, $loan);

            $loans = LoanQuery::create()
                ->filterByBorrower($loan->getBorrower())
                ->count();
            if ($loans == 1) {
                $this->transactionService->addFeeTransaction($con, $nativeAmount, $loan);
            }

            $installments = $this->generateLoanInstallments($loan);

            $totalAmount = Money::create(0, $loan->getCurrency());
            /** @var Installment $installment */
            foreach ($installments as $installment) {
                $totalAmount = $totalAmount->add($installment->getAmount());
                $installment->save($con);
            }

            $loan
                ->setStatus(Loan::ACTIVE)
                ->setDisbursedAmount($nativeAmount)
                ->setTotalAmount($totalAmount)
                ->setDisbursedAt($disbursedAt)
                ->calculateExtraDays($disbursedAt)
                ->setServiceFeeRate(Setting::get('loan.serviceFeeRate'));
            $loan->save($con);

            $this->changeLoanStage($con, $loan, Loan::FUNDED, Loan::ACTIVE);
        });

        //TODO Send email / sift sience event
    }

    protected function getLenderRefunds($transactions)
    {
        $refunds = [];
        $zero = Money::create(0);
        foreach ($transactions as $transaction) {
            $userId = $transaction->getUserId();
            $refunds[$userId] = [
                'lender' => $transaction->getUser()->getLender(),
                'refundAmount' => array_get($refunds, "$userId.refundAmount", $zero)->subtract(
                        $transaction->getAmount()
                    ),
            ];
        }

        foreach ($refunds as $id => $refund) {
            if ($refunds[$id]['refundAmount']->lessThan(Money::create(0))) {
                $refunds[$id]['refundAmount'] = $zero;
            }
        }

        return $refunds;
    }

    public function generateLoanInstallments(Loan $loan)
    {
        $calculator = new Calculator\InstallmentCalculator($loan);
        $installmentAmount = $calculator->installmentAmount();
        $installmentCount = $loan->getInstallmentCount();

        $installments = [];

        $graceInstallment = new Installment();
        $graceInstallment
            ->setLoan($loan)
            ->setBorrower($loan->getBorrower())
            ->setAmount(Money::create(0, $loan->getCurrencyCode()))
            ->setDueDate($calculator->installmentGraceDate());

        $installments[] = $graceInstallment;

        for ($count = 1; $count <= $installmentCount; $count++) {
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
}


