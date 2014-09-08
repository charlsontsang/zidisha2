<?php

namespace Zidisha\Loan;

use Carbon\Carbon;
use Propel\Runtime\Connection\ConnectionInterface;
use Zidisha\Admin\Setting;
use Zidisha\Analytics\MixpanelService;
use Zidisha\Balance\InviteTransactionQuery;
use Zidisha\Balance\Transaction;
use Zidisha\Balance\TransactionQuery;
use Zidisha\Balance\TransactionService;
use Zidisha\Borrower\Borrower;
use Zidisha\Comment\BorrowerCommentService;
use Zidisha\Currency\Converter;
use Zidisha\Currency\ExchangeRate;
use Zidisha\Currency\ExchangeRateQuery;
use Zidisha\Currency\Money;
use Zidisha\Lender\Lender;
use Zidisha\Lender\LenderQuery;
use Zidisha\Loan\Calculator\BidsCalculator;
use Zidisha\Loan\Calculator\InstallmentCalculator;
use Zidisha\Loan\Calculator\RepaymentCalculator;
use Zidisha\Loan\Calculator\RescheduleCalculator;
use Zidisha\Mail\BorrowerMailer;
use Zidisha\Mail\LenderMailer;
use Zidisha\Repayment\Installment;
use Zidisha\Repayment\InstallmentQuery;
use Zidisha\Repayment\RepaymentSchedule;
use Zidisha\Repayment\RepaymentScheduleInstallment;
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
    
    /**
     * @var \Zidisha\Comment\BorrowerCommentService
     */
    private $borrowerCommentService;

    public function __construct(
        TransactionService $transactionService,
        LenderMailer $lenderMailer,
        MixpanelService $mixpanelService,
        BorrowerMailer $borrowerMailer,
        SiftScienceService $siftScienceService,
        RepaymentService $repaymentService,
        BorrowerCommentService $borrowerCommentService
    )
    {
        $this->transactionService = $transactionService;
        $this->lenderMailer = $lenderMailer;
        $this->mixpanelService = $mixpanelService;
        $this->borrowerMailer = $borrowerMailer;
        $this->siftScienceService = $siftScienceService;
        $this->repaymentService = $repaymentService;
        $this->borrowerCommentService = $borrowerCommentService;
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

        $this->addToLoanIndex($loan);

        $this->borrowerMailer->sendLoanConfirmationMail($borrower, $loan);

        $lastLoan = LoanQuery::create()
            ->findLastCompletedLoan($borrower);

        if ($lastLoan) {
            $lenders = $this->getLendersForNewLoanNotificationMail($loan);
            $parameters = [
                'borrowerName' => $loan->getBorrower()->getName(),
                'loanUrl'      => route('loan:index', ['loanId' => $loan->getId()]),
                'repayDate'    => $lastLoan->getRepaidAt()->format('F j, Y')
            ];
            $subject = \Lang::get('lender.mails.new-loan-notification.subject', $parameters);
            foreach($lenders as $lender) {
                if ($lender->isFollowing($borrower)) {
                    $this->lenderMailer->sendFollowerNewLoanNotificationMail($lender, $parameters, $subject);
                } else {
                    $this->lenderMailer->sendNewLoanNotificationMail($lender, $parameters, $subject);
                }
            }
        }
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
            ->filterCompleted() // TODO correct? verify database
            ->count();
        $registrationFee = $isFirstLoan ? $borrower->getCountry()->getRegistrationFee() : Money::create(0, $currencyCode);
        
        $loan = new Loan();
        $loan = $this->setLoanDetails($borrower, $loan, $data, $currencyCode, $registrationFee, $siftScienceScore);
        
        return $loan;
    }

    protected function setLoanDetails(
        Borrower $borrower,
        Loan $loan,
        $data,
        $currencyCode = null,
        $registrationFee = null,
        $siftScienceScore = null
    ) {
        $loan
            ->setSummary($data['summary'])
            ->setProposal($data['proposal'])
            ->setUsdAmount(Money::create($data['usdAmount'], 'USD'))
            ->setInstallmentPeriod($borrower->getCountry()->getInstallmentPeriod())
            ->setMaxInterestRate(Setting::get('loan.maximumLenderInterestRate') + Setting::get('loan.transactionFeeRate'))
            ->setServiceFeeRate(Setting::get('loan.transactionFeeRate'))
            ->setInstallmentDay($data['installmentDay'])
            ->setCategoryId($data['categoryId'])
            ->setBorrower($borrower);

        if ($registrationFee != null) {
            $loan->setRegistrationFee($registrationFee)
                ->setAppliedAt($data['date'])
                ->setCurrencyCode($currencyCode)
                ->setStatus(Loan::OPEN)
                ->setSiftScienceScore($siftScienceScore)
                ->setAmount(Money::create($data['amount'], $currencyCode));
            $installmentAmount = Money::create($data['installmentAmount'], $currencyCode);
        }else {
            $loan
                ->setAmount(Money::create($data['amount'], $loan->getCurrencyCode()));
            $installmentAmount = Money::create($data['installmentAmount'], $loan->getCurrencyCode());

        }

        $calculator = new InstallmentCalculator($loan);
        $period = $calculator->period($installmentAmount);

        $loan->setPeriod($period);

        return $loan;
    }

    public function updateLoanApplication(Borrower $borrower,Loan $loan, $data)
    {
        $data['exchangeRate'] = ExchangeRateQuery::create()->findCurrent($borrower->getCountry()->getCurrency());
        $exchangeRate = $data['exchangeRate'];
        $data['usdAmount'] = Converter::toUSD(Money::create($data['amount'], $loan->getCurrencyCode()),$exchangeRate)->getAmount();

        $loan = $this->setLoanDetails($borrower, $loan, $data);
        $loan->save();

        $this->sendLoanFullyFundedNotification($loan);

        $this->updateLoanIndex($loan);

        return $loan;
    }

    protected function getLendersForNewLoanNotificationMail(Loan $loan)
    {
        $query = "SELECT id
            FROM lenders AS l
            JOIN lender_preferences AS p
            ON (l.id = p.lender_id)
            WHERE
               ((p.notify_loan_application = TRUE AND l.id IN (SELECT lender_id FROM loan_bids AS b WHERE b.loan_id = :loanId AND active = TRUE AND lender_id NOT IN (SELECT lender_id FROM followers AS f WHERE f.borrower_id = :borrowerId)))
                OR
                (l.id IN (SELECT lender_id FROM followers as ff WHERE ff.borrower_id = :borrowerId AND ff.notify_loan_application = TRUE AND active= TRUE )))
               AND active = TRUE ";

        $lenderIds = PropelDB::fetchAll(
            $query,
            [
                'loanId'     => $loan->getId(),
                'borrowerId' => $loan->getBorrowerId(),
            ]
        );

        $lenders = LenderQuery::create()
            ->filterById($lenderIds)
            ->find();

        return $lenders;
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

    /**
     * @param $conditions
     * @param $page
     * @param $limit
     * @return \Elastica\Query
     */
    protected function createSearchQuery($conditions, $page, $limit)
    {
        $conditions += ['search' => false, 'sortBy' => 'id', 'sortByOrder' => 'asc'];
        $search = $conditions['search'];
        $sortBy = $conditions['sortBy'];
        $sortByOrder = $conditions['sortByOrder'];
        unset($conditions['search']);
        unset($conditions['sortBy']);
        unset($conditions['sortByOrder']);

        $queryString = new \Elastica\Query\QueryString();

        $query = new \Elastica\Query();
        $sorting = [
            $sortBy => [
                'order' => $sortByOrder
            ]
        ];
        $query->setSort($sorting);

        if ($search) {
            $queryString->setDefaultOperator('AND');
            $queryString->setQuery($search);
            $query->setQuery($queryString);
        }

        $filterAnd = new \Elastica\Filter\BoolAnd();

        if (isset($conditions['categoryId'])) {
            $filterOr = new \Elastica\Filter\BoolOr();
            
            $termFilter = new \Elastica\Filter\Term();
            $termFilter->setTerm('categoryId', $conditions['categoryId']);
            $filterOr->addFilter($termFilter);

            $termFilter = new \Elastica\Filter\Term();
            $termFilter->setTerm('secondaryCategoryId', $conditions['categoryId']);
            $filterOr->addFilter($termFilter);
            
            $filterAnd->addFilter($filterOr);
            
            unset($conditions['categoryId']);
        }

        foreach ($conditions as $field => $value) {
            $termFilter = new \Elastica\Filter\Term();
            $termFilter->setTerm($field, $value);

            $filterAnd->addFilter($termFilter);
        }
        if ($conditions) {
            $query->setPostFilter($filterAnd);
        }

        $query->setFrom(($page - 1) * $limit);
        $query->setSize($page * $limit);

        return $query;
    }
    
    public function searchLoans($conditions = array(), $page = 1, $limit = 18)
    {
        $query = $this->createSearchQuery($conditions, $page, $limit);

        $loanIndex = $this->getLoanIndex();
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

    public function countLoans($conditions = array(), $page = 1, $limit = 18)
    {
        $query = $this->createSearchQuery($conditions, $page, $limit);

        $loanIndex = $this->getLoanIndex();
        
        return $loanIndex->count($query);
    }

    /**
     * @param Loan $loan
     * @return array
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function createLoanDocument(Loan $loan)
    {
        $loanIndex = $this->getLoanIndex();

        $loanType = $loanIndex->getType('loan');

        $data = [
            'id'                  => $loan->getId(),
            'category'            => $loan->getCategory()->getName(),
            'categoryId'          => $loan->getCategory()->getId(),
            'secondaryCategoryId' => $loan->getSecondaryCategory() ? $loan->getSecondaryCategory()->getId() : '',
            'secondaryCategory'   => $loan->getSecondaryCategory() ? $loan->getSecondaryCategory()->getName() : '',
            'countryId'           => $loan->getBorrower()->getCountry()->getId(),
            'country_code'        => $loan->getBorrower()->getCountry()->getCountryCode(),
            'summary'             => $loan->getSummary(),
            'proposal'            => $loan->getProposal(),
            'status'              => $loan->getStatus(),
            'created_at'          => $loan->getCreatedAt()->getTimestamp(),
            'raised_percentage'   => $loan->getRaisedPercentage(),
            'applied_at'          => $loan->getAppliedAt()->getTimestamp()
        ];

        $loanDocument = new \Elastica\Document($loan->getId(), $data);
        
        return [$loanType, $loanDocument];
    }

    public function addToLoanIndex(Loan $loan)
    {
        if (\App::environment("testing")) {
            return;
        }

        list($loanType, $loanDocument) = $this->createLoanDocument($loan);
        
        $loanType->addDocument($loanDocument);
        $loanType->getIndex()->refresh();
    }

    public function updateLoanIndex(Loan $loan)
    {
        if (\App::environment("testing")) {
            return;
        }

        list($loanType, $loanDocument) = $this->createLoanDocument($loan);
        $loanDocument->setDocAsUpsert(true);
        
        $loanType->updateDocument($loanDocument);
        $loanType->getIndex()->refresh();   
    }

    public function placeBid(Loan $loan, Lender $lender, $data)
    {
        $data += [
            'bidAt'                => new \DateTime(),
            'isLenderInviteCredit' => false,
            'isAutomatedLending'   => false,
        ];
        
        $isFullyFundedBeforeBid = $loan->isFullyFunded();
        
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

        $this->updateLoanIndex($loan);

        // Mixpanel
        // TODO not working for automatic lending
        $this->mixpanelService->trackPlacedBid($bid);

        // Emails
        // First bid placed confirmation
        $isFirstBid = BidQuery::create()
            ->filterByLender($lender)
            ->count() == 1;

        if ($isFirstBid) {
            $this->lenderMailer->sendFirstBidConfirmationMail($lender);
        }

        // Outbid notification
        foreach ($changedBids as $bidId => $changedBid) {
            if ($changedBid['type'] == 'out_bid') {
                /** @var Bid $bid*/
                $bid = $changedBid['bid'];
                /** @var Money $acceptedAmount */
                $acceptedAmount = $changedBid['acceptedAmount'];
                /** @var Money $changedAmount */
                $changedAmount = $changedBid['changedAmount'];
                if ($acceptedAmount->isZero()) {
                    $this->lenderMailer->sendOutbidMail($lender, $bid);
                } else {
                    $outBidAmount = $bid->getBidAmount()->subtract($acceptedAmount);
                    $this->lenderMailer->sendDownBidMail($lender, $bid, $acceptedAmount, $outBidAmount);
                }
            }
        }
        
        if (!$isFullyFundedBeforeBid) {
            $this->sendLoanFullyFundedNotification($loan);
        }

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

        $this->updateLoanIndex($loan);
        
        //TODO send emails (move lenders part from sendLoanFullyFundedNotification)

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

        $this->updateLoanIndex($loan);

        $lenderIds = [];
        /** @var LenderRefund $lenderRefund */
        foreach ($lenderRefunds as $lenderRefund) {
            $lenderIds[] = $lenderRefund->getLender()->getId();
        }
        $currentCreditArray = TransactionQuery::create()
            ->getCurrentBalance($lenderIds);
        $inviteCreditArray = InviteTransactionQuery::create()
            ->getTotalInviteCreditAmount($lenderIds);
        /** @var LenderRefund $lenderRefund */
        foreach ($lenderRefunds as $lenderRefund) {
            if ($lenderRefund->getAmount()->isPositive()) {
                $this->lenderMailer->sendExpiredLoanMail($loan, $lenderRefund->getLender(), $lenderRefund->getAmount(), $currentCreditArray[$lenderRefund->getLender()->getId()]);
            }

            if ($lenderRefund->getLenderInviteCredit()->isPositive()) {
                $this->lenderMailer->sendExpiredLoanWithLenderInviteCreditMail($loan, $lenderRefund->getLender(), $lenderRefund->getLenderInviteCredit(), $inviteCreditArray[$lenderRefund->getLender()->getId()]);
            }
        }
        
        $this->borrowerMailer->sendExpiredLoanMail($borrower);
        
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

        $this->updateLoanIndex($loan);
        
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

    public function changeLoanStage(
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

    public function authorizeLoan(Loan $loan, $data)
    {
        $data += [
            'authorizedAt' => new \DateTime(),
        ];

        $loan
            ->setAuthorizedAt($data['authorizedAt'])
            ->setAuthorizedAmount($data['authorizedAmount']);
        $loan->save();
    }

    public function disburseLoan(Loan $loan, $data)
    {
        $data += [
           'disbursedAt' => new \DateTime(),
        ];
        
        $isDisbursed = TransactionQuery::create()
            ->filterByLoan($loan)
            ->filterDisbursement()
            ->count();

        if ($isDisbursed) {
            // TODO
            return;
        }

        PropelDB::transaction(function($con) use ($loan, $data) {
            $disbursedAmount = $data['disbursedAmount'];
            
            $this->transactionService->addDisbursementTransaction($con, $disbursedAmount, $loan);

            $loans = LoanQuery::create()
                ->filterByBorrower($loan->getBorrower())
                ->count();
            if ($loans == 1) {
                $this->transactionService->addFeeTransaction($con, $disbursedAmount, $loan);
            }

            $loan
                ->setStatus(Loan::ACTIVE)
                ->setDisbursedAmount($disbursedAmount)
                ->setDisbursedAt($data['disbursedAt'])
                ->calculateExtraDays($data['disbursedAt'])
                ->setServiceFeeRate(Setting::get('loan.serviceFeeRate'));

            if ($loan->getRegistrationFee()->isPositive()) {
                $loan->setRegistrationFee($data['registrationFee']);
            }

            $calculator = new InstallmentCalculator($loan);
            $installments = $calculator->generateLoanInstallments($loan);

            $totalAmount = Money::create(0, $loan->getCurrency());
            /** @var Installment $installment */
            foreach ($installments as $installment) {
                $totalAmount = $totalAmount->add($installment->getAmount());
            }

            $loan->setTotalAmount($totalAmount);
            $loan->save($con);

            $this->changeLoanStage($con, $loan, Loan::FUNDED, Loan::ACTIVE);
        });

        $this->updateLoanIndex($loan);

        // TODO, lenders + bid amount
        $lenders = [];
        $borrower = $loan->getBorrower();
        $parameters = [
            'borrowerName'    => $borrower->getName(),
            'borrowFirstName' => $borrower->getFirstName(),
            'disbursedDate'   => date('F d, Y',  time()),
            'loanPage'        => route('loan:index', $loan->getId()),
            'giftCardPage'    => route('lender:gift-cards')
        ];

        $data['image_src'] = $borrower->getUser()->getProfilePictureUrl();
        $message = \Lang::get('lender.mails.loan-disbursed.message', $parameters);
        $data['header'] = $message;
        $body = \Lang::get('lender.mails.loan-disbursed.body', $parameters);
        $data['content'] = $body;
        foreach ($lenders as $lender) { 
            $this->lenderMailer->sendDisbursedLoanMail($lender, $parameters, $data);
        }
        $this->borrowerMailer->sendDisbursedLoanMail($loan);
        
        // TODO sift science event
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
            ->filterByBorrower($borrower)
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
        } else {
            $repaymentRate = $onTimeInstallmentCount / $totalTodayInstallmentCount * 100;

            if ($repaymentRate < 0) { // TODO why?
                $repaymentRate = 0;
            }
            $repaymentScore = round($repaymentRate, 2);
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

        $this->updateLoanIndex($loan);

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
            ->orderByDueDate('desc')
            ->findOne();
        $repaymentThreshold = \Config::get('constants.repaymentThreshold');

        $paidDate = Carbon::instance($installment->getPaidDate());
        $lastDueDate = Carbon::instance($installment->getDueDate());
        
        return $lastDueDate->diffInDays($paidDate, false) <= $repaymentThreshold;
    }

    public function allowLoanForgiveness(Loan $loan, $data)
    {
        /** @var ForgivenessLoan $forgivenessLoan */
        $forgivenessLoan = PropelDB::transaction(function($con) use($loan, $data) {
                $alreadyForgivenLoan = ForgivenessLoanQuery::create()
                    ->filterByLoan($loan)
                    ->findOne($con);

                $verificationCode = md5(mt_rand(0, 32).time());
                if ($alreadyForgivenLoan) {
                    if (!$alreadyForgivenLoan->getVerificationCode()) {
                        $alreadyForgivenLoan->setVerificationCode($verificationCode);
                        $alreadyForgivenLoan->save($con);
                    }
                    return $alreadyForgivenLoan;
                } else {
                    $forgivenessLoan = new ForgivenessLoan();
                    $forgivenessLoan
                        ->setLoan($loan)
                        ->setComment($data['comment'])
                        ->setVerificationCode($verificationCode)
                        ->setBorrowerId ($loan->getBorrowerId())
                        ->save($con);
                    return $forgivenessLoan;
                }
            });

        $inactiveLenders = LenderQuery::create()
            ->getInactiveLendersForLoan($loan);

        foreach ($inactiveLenders as $lender) {
            $this->forgiveLoanShare($loan, $lender);
        }

        // TODO lendersDenied

        $lendersForForgive = LenderQuery::create()
            ->getLendersForForgive($loan);
        $parameters = [
            'borrowerName'      => $loan->getBorrower()->getName(),
            'disbursedDate'     => $loan->getDisbursedAt()->format('d-m-Y'),
            'message'           => trim($forgivenessLoan->getComment()),
            'outstandingAmount' => $loan->getUsdAmount()->multiply($loan->getPaidPercentage())->divide(100),
            'loanLink'          => route('loan:index', $loan->getId()),
            'yesLink'           => route('loan:index', $loan->getId()).'?v='.$forgivenessLoan->getVerificationCode(),
            'yesImage'          => '/assets/images/loan-forgive/yes.png',
            'noImage'           => '/assets/images/loan-forgive.no.png',
        ];
        $subject = \Lang::get('lender.mails.allow-loan-forgiveness.subject', $parameters);

        foreach ($lendersForForgive as $lender) {
            $this->lenderMailer->sendAllowLoanForgivenessMail($forgivenessLoan, $lender, $parameters, $subject);
        }

        return $forgivenessLoan;
    }
    
    public function getPrincipalRatio($loanId, $date=0)
    {
        $sql = "SELECT sum(amount) amt from installments where loan_id = $loanId";
        $all = PropelDB::fetchNumber($sql);
        $forgiveAmount = 0;
        if ($date) {
            //TODO
        } else {
            $s = "SELECT sum(amount) from forgiveness_loan_shares where loan_id = :loanId";
            $forgiveAmount = PropelDB::fetchNumber($s, ['loanId' => $loanId]);
        }

        if ($forgiveAmount) {
            $all += $forgiveAmount;
        }
        $q = "SELECT disbursed_amount from loans where id = $loanId ";
        $disbursedAmount = PropelDB::fetchNumber($q);
        $ratio = $disbursedAmount / $all;
        return round($ratio, 4);

    }
    
    public function forgiveLoanShare(Loan $loan, Lender $lender)
    {
        $forgivenLoanShareCount = ForgivenessLoanShareQuery::create()
            ->filterByLender($lender)
            ->filterByLoan($loan)
            ->count();
        
        if ($forgivenLoanShareCount) {
            return true; 
        }
        
        $acceptedBids = BidQuery::create()
            ->filterByLender($lender)
            ->filterByLoan($loan)
            ->filterByActive(true)
            ->find();

        $bidCalculator = new BidsCalculator();
        $lenderInterestAmount = $bidCalculator->getLenderInterestRate($acceptedBids, $loan->getUsdAmount());
        
        
        $repaymentCalculator = new RepaymentCalculator($loan);
        $lenderRepaymentAmount = $repaymentCalculator->repaymentAmountForLenders();
        
        $totalAmountForRepayment = $lenderRepaymentAmount->add($lenderInterestAmount, 'USD');
        
        PropelDB::transaction(function () use ($loan, $lender, $totalAmountForRepayment) {
                $forgiveLoanShare = new ForgivenessLoanShare();
                
                $forgiveLoanShare->setLoan($loan)
                    ->setBorrower($loan->getBorrower())
                    ->setLender($lender)
                    ->setAmount($totalAmountForRepayment)
                    ->setUsdAmount($totalAmountForRepayment)
                    ->setIsAccepted(true);
                
                $forgiveLoanShare->save();
        });
        
        $con = PropelDB::getConnection();
        $this->transactionService->lenderLoanForgivenessTransaction($con, $lender, Money::create($lenderInterestAmount, 'USD'));
        
        //TODO: updateScheduleAfterForgive
        
        $allLenderForgiven = $this->checkAllLenderForgiven($loan);
        
        if ($allLenderForgiven) {
            $installments = InstallmentQuery::create()
                ->withColumn('SUM(installments.paid_amount)', 'TotalPaidAmount')
                ->withColumn('SUM(installments.amount)', 'TotalAmount')
                ->filterByLoan($loan)
                ->find();
            
            foreach ($installments as $installment) {
                $totalAmount = $installment->getTotalAmount();
                $totalPaidAmount = $installment->getTotalPaidAmount();
                break;
            }
            
            $amountToRepay = $totalAmount - $totalPaidAmount;
            
            if ($amountToRepay >0) {
                //TODO: forgive Zidisha fees
                
                $loan->setStatus(Loan::REPAID);
                $loan->save();
            }
        }
    }

    public function rejectForgiveLoanShare(Loan $loan, Lender $lender)
    {
        $forgivenLoanShareCount = ForgivenessLoanShareQuery::create()
            ->filterByLender($lender)
            ->filterByLoan($loan)
            ->count();

        if ($forgivenLoanShareCount) {
            return true;
        }
        
        $acceptedBids = BidQuery::create()
            ->filterByLender($lender)
            ->filterByLoan($loan)
            ->filterByActive(true)
            ->find();

        $bidCalculator = new BidsCalculator();
        $lenderTotalAmount = $bidCalculator->getLenderInterestRate($acceptedBids, $loan->getUsdAmount());
        
        PropelDB::transaction(function () use ($loan, $lender, $lenderTotalAmount) {
                $forgiveLoanShare = new ForgivenessLoanShare();
                $forgiveLoanShare->setLoan($loan)
                    ->setBorrower($loan->getBorrower())
                    ->setLender($lender)
                    ->setAmount($lenderTotalAmount)
                    ->setUsdAmount($lenderTotalAmount)
                    ->setIsAccepted(false);
                
                $forgiveLoanShare->save();
            });
    }

    private function checkAllLenderForgiven(Loan $loan)
    {
        
        //TODO: fix this method.
        
        $bids = BidQuery::create()
            ->filterByLoan($loan)
            ->find();
        
        foreach ($bids as $bid) {
            $forgivenLoanShare = ForgivenessLoanShareQuery::create()
                ->filterByLender($bid->getLender())
                ->find();
            
            if (!$forgivenLoanShare) {
                return false;
            }
        }
        
        return true;        
    }

    /**
     * @param Loan $loan
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function sendLoanFullyFundedNotification(Loan $loan)
    {
        // Fully Funded notifications
        if ($loan->isFullyFunded()) {
            $bids = BidQuery::create()
                ->filterByLoan($loan)
                ->joinWith('Lender')
                ->joinWith('Lender.User')
                ->find();

            foreach ($bids as $bid) {
                $this->lenderMailer->sendLoanFullyFundedMail($bid);
            }
            
            $this->borrowerMailer->sendLoanFullyFundedMail($loan);
        }
    }

    public function rescheduleLoan(Loan $loan, array $data, $simulate = false)
    {
        $data += [
            'rescheduledAt' => new \Datetime(),
        ];
        
        $oldRepaymentSchedule = $this->repaymentService->getRepaymentSchedule($loan);
        $rescheduleCalculator = new RescheduleCalculator($loan, $oldRepaymentSchedule);

        $repaymentScheduleInstallments = $rescheduleCalculator->repaymentScheduleInstallments(
            Money::create($data['installmentAmount'], $loan->getCurrencyCode()),
            $data['rescheduledAt']
        );
        
        $repaymentSchedule = new RepaymentSchedule($loan, $repaymentScheduleInstallments['new']);
        
        if ($simulate) {
            return $repaymentSchedule;
        }
        
        $deleteIds = [];
        /** @var RepaymentScheduleInstallment $repaymentScheduleInstallment */
        foreach ($repaymentScheduleInstallments['delete'] as $repaymentScheduleInstallment) {
            $deleteIds[] = $repaymentScheduleInstallment->getInstallment()->getId();
        }
        
        PropelDB::transaction(function($con) use ($loan, $data, $repaymentSchedule, $deleteIds) {
            $deletedInstallmentCount = InstallmentQuery::create()
                ->filterById($deleteIds)
                ->delete($con);

            $loan->clearInstallments();
            /** @var RepaymentScheduleInstallment $repaymentScheduleInstallment */
            foreach($repaymentSchedule as $repaymentScheduleInstallment) {
                $repaymentScheduleInstallment->getInstallment()->save();
            }

            $reschedule = new Reschedule();
            $reschedule
                ->setLoan($loan)
                ->setBorrower($loan->getBorrower())
                ->setPeriod($repaymentSchedule->getPeriod())
                ->setReason($data['reason']);

            $loan
                ->setTotalAmount($repaymentSchedule->getTotalAmountDue())
                ->setOriginalPeriod($loan->getPeriod())
                ->setPeriod($repaymentSchedule->getPeriod());

            $loan->clearInstallments();
            $loan->save($con);
        });
        
        // TODO email
        $borrower = $loan->getBorrower();
        
        $this->borrowerCommentService->postComment(
            [
                'message' => $data['reason'],
                'isReschedulingReason' => true
            ],
            $borrower->getUser(),
            $borrower
        );
        
        return $repaymentSchedule;
    }
}
