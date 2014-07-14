<?php

namespace Zidisha\Loan;

use Propel\Runtime\Connection\ConnectionInterface;
use Zidisha\Analytics\MixpanelService;
use Zidisha\Balance\Map\TransactionTableMap;
use Zidisha\Balance\TransactionQuery;
use Zidisha\Balance\TransactionService;
use Zidisha\Borrower\Borrower;
use Zidisha\Currency\Converter;
use Zidisha\Currency\ExchangeRateQuery;
use Zidisha\Currency\Money;
use Zidisha\Lender\Lender;
use Zidisha\Mail\BorrowerMailer;
use Zidisha\Mail\LenderMailer;
use Zidisha\Repayment\Installment;
use Zidisha\Vendor\PropelDB;

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

    public function __construct(
        TransactionService $transactionService,
        LenderMailer $lenderMailer,
        MixpanelService $mixpanelService,
        BorrowerMailer $borrowerMailer
    )
    {
        $this->transactionService = $transactionService;
        $this->lenderMailer = $lenderMailer;
        $this->mixpanelService = $mixpanelService;
        $this->borrowerMailer = $borrowerMailer;
    }

    public function applyForLoan(Borrower $borrower, $data)
    {
        $exchangeRate = ExchangeRateQuery::create()->findCurrent($borrower->getCountry()->getCurrency());
        
        $data['currencyCode'] = $borrower->getCountry()->getCurrencyCode();

        $loanCategory = CategoryQuery::create()
            ->findOneById($data['categoryId']);

        $data['nativeAmount'] = $data['amount']; // TODO
        $data['amount'] = Converter::toUSD(
            Money::create($data['nativeAmount'], $data['currencyCode']),
            $exchangeRate
        )->getAmount();

        $loan = Loan::createFromData($data);

        $loan
            ->setCategory($loanCategory)
            ->setBorrower($borrower)
            ->setStatus(Loan::OPEN);
        
        $borrower
            ->setActiveLoan($loan)
            ->setLoanStatus(Loan::OPEN);
        
        PropelDB::transaction(function($con) use ($loan, $borrower) {
            $loan->save($con);
            $borrower->save();

            $this->changeLoanStage($con, $loan, null, Loan::OPEN);
        });

        $this->borrowerMailer->sendLoanConfirmation($borrower, $loan);
        // TODO send mail to lenders
        
        $this->addToLoanIndex($loan);
        
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
        $bid = PropelDB::transaction(function($con) use($loan, $lender, $data) {
            $bid = $this->createBid($con, $loan, $lender, $data);

            $totalBidAmount = BidQuery::create()
                ->filterByLoan($loan)
                ->getTotalBidAmount();
            
            $loan->setRaisedAmount($totalBidAmount);
            $loan->save();
            
            return $bid;
        });

        if ($bid->isFirstBid()) {
            $this->lenderMailer->sendFirstBidConfirmationMail($bid);
        }

        $this->mixpanelService->trackPlacedBid($bid);

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
                return $b1['bid']->getBidDate() <= $b2['bid']->getBidDate();
            }
        );

        return $acceptedBids;
    }

    protected function getChangedBids($oldAcceptedBids, $newAcceptedBids)
    {
        $changedBids = [];

        foreach ($newAcceptedBids as $bidId => $acceptedBid) {
            $acceptedAmount = $acceptedBid['acceptedAmount'];
            $bid = $acceptedBid['bid'];
            if (isset($oldAcceptedBids[$bidId])) {
                $oldAcceptedAmount = $oldAcceptedBids[$bidId]['acceptedAmount'];
                if ($oldAcceptedAmount->greaterThan($acceptedAmount)) {
                    $changedBids[$bidId] = [
                        'bid' => $bid,
                        'acceptedAmount' => $acceptedAmount,
                        'type' => 'out_bid',
                        'changedAmount' => $oldAcceptedAmount->subtract($acceptedAmount),
                    ];
                } else {
                    if ($oldAcceptedAmount->lessThan($acceptedAmount)) {
                        $changedBids[$bidId] = [
                            'bid' => $bid,
                            'acceptedAmount' => $acceptedAmount,
                            'type' => 'update_bid',
                            'changedAmount' => $acceptedAmount->subtract($oldAcceptedAmount),
                        ];
                    }
                }
            } elseif ($acceptedAmount->greaterThan(Money::create(0))) {
                $changedBids[$bidId] = [
                    'bid' => $bid,
                    'acceptedAmount' => $acceptedAmount,
                    'type' => 'place_bid',
                    'changedAmount' => $acceptedAmount,
                ];
            }
        }

        return $changedBids;
    }

    protected function createBid(ConnectionInterface $con, Loan $loan, Lender $lender, $data)
    {
        $bidAmount = Money::create($data['amount'], 'USD');

        $oldBids = BidQuery::create()
            ->getOrderedBids($loan)
            ->find();

        $bid = new Bid();
        $bid
            ->setLoan($loan)
            ->setLender($lender)
            ->setBorrower($loan->getBorrower())
            ->setBidAmount($bidAmount)
            ->setInterestRate($data['interestRate'])
            ->setActive(true)
            ->setBidDate(new \DateTime());

        $bidSuccess = $bid->save($con);

        if (!$bidSuccess) {
            throw new \Exception();
        }

        $newBids = BidQuery::create()
            ->getOrderedBids($loan)
            ->find();

        $oldAcceptedBids = $this->getAcceptedBids($oldBids, $loan->getAmount());
        $newAcceptedBids = $this->getAcceptedBids($newBids, $loan->getAmount());
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

        return $bid;
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
        $newBids = BidQuery::create()
            ->getOrderedBids($loan)
            ->find();

        $newAcceptedBids = $this->getAcceptedBids($newBids, $loan->getAmount());

        PropelDB::transaction(function($con) use ($newAcceptedBids, $loan) {
            $totalAmount = Money::create(0);

            foreach ($newAcceptedBids as $bidId => $acceptedBid) {
                $acceptedAmount = $acceptedBid['acceptedAmount'];
                $bid = $acceptedBid['bid'];
                if ($acceptedAmount->greaterThan(Money::create(0))) {
                    $bid->setActive(0)
                        ->setAcceptedAmount($acceptedAmount);
                    $success = $bid->save($con);
                    if (!$success) {
                        // Todo: Notify admin.
                        throw new \Exception();
                    }
                    $totalAmount = $totalAmount->add($acceptedAmount->multiply(0.01 * $bid->getInterestRate()));
                }
            }

            $totalInterest = $totalAmount->divide($loan->getAmount())->round(2)->getAmount();
            $loan->setStatus(Loan::FUNDED)
                ->setInterestRate($totalInterest)
                ->setAcceptedDate(new \DateTime())
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
                ->setExpiredDate(new \DateTime());
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
                ->setExpiredDate(new \DateTime());
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

    public function disburseLoan(Loan $loan, \DateTime $disbursedDate, Money $nativeAmount)
    {
        $isDisbursed = TransactionQuery::create()
            ->filterByLoan($loan)
            ->filterDisbursement()
            ->count();

        if ($isDisbursed) {
            // TODO
            return;
        }

        PropelDB::transaction(function($con) use ($loan, $disbursedDate, $nativeAmount) {
            $this->transactionService->addDisbursementTransaction($con, $nativeAmount, $loan);

            $loans = LoanQuery::create()
                ->filterByBorrower($loan->getBorrower())
                ->count();
            if ($loans == 1) {
                $this->transactionService->addFeeTransaction($con, $nativeAmount, $loan);
            }

            //TODO service fee rate
            $loan
                ->setStatus(Loan::ACTIVE)
                ->setNativeDisbursedAmount($nativeAmount)
                ->setDisbursedDate($disbursedDate)
                ->calculateExtraDays($disbursedDate)
                ->setServiceFeeRate(2.5);
            $loan->save($con);

            $this->changeLoanStage($con, $loan, Loan::FUNDED, Loan::ACTIVE);
            
            $installments = $this->generateLoanInstallments($loan);
            
            foreach ($installments as $installment) {
                $installment->save($con);
            }
        });

        //TODO Add repayment schedule
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

    protected function generateLoanInstallments(Loan $loan)
    {
        $calculator = new Calculator\InstallmentCalculator($loan);
        $nativeInstallmentAmount = $calculator->installmentAmount();
        $installmentCount = $loan->getInstallmentCount();

        $installments = [];

        $graceInstallment = new Installment();
        $graceInstallment
            ->setLoan($loan)
            ->setBorrower($loan->getBorrower())
            ->setNativeAmount(Money::create(0))
            ->setDueDate($calculator->installmentGraceDate());

        $installments[] = $graceInstallment;

        for ($count = 1; $count <= $installmentCount; $count++) {
            $installment = new Installment();
            $installment
                ->setLoan($loan)
                ->setBorrower($loan->getBorrower())
                ->setNativeAmount($nativeInstallmentAmount)
                ->setDueDate($calculator->nthInstallmentDate($count));
            $installments[] = $installment;
        }

        return $installments;
    }

    public function calculateTotalInterest(Loan $loan)
    {
        if($loan->getInstallmentPeriod() == 0){

            $totalInterest = ($loan->getNativeDisbursedAmount())*($loan->getInterestRate()/100)*($loan->getInstallmentCount()
                    /12);
        }else{
            $totalInterest = ($loan->getNativeDisbursedAmount())*($loan->getInterestRate()/100)*($loan->getInstallmentCount()
                    /52);
        }
        //TODO. change nativeAmount to USD

        return $totalInterest;
    }

    public function calculateTransactionFee(Loan $loan)
    {
        return $loan->getNativeDisbursedAmount()*(5/100);
        //TODO. change nativeAmount to USD
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

}


