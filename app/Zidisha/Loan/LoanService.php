<?php

namespace Zidisha\Loan;


use Propel\Runtime\Propel;
use SupremeNewMedia\Finance\Core\Currency;
use SupremeNewMedia\Finance\Core\Money;
use Zidisha\Analytics\MixpanelService;
use Zidisha\Balance\Map\TransactionTableMap;
use Zidisha\Balance\Transaction;
use Zidisha\Balance\TransactionQuery;
use Zidisha\Borrower\Borrower;
use Zidisha\Currency\CurrencyService;
use Zidisha\Lender\Exceptions\InsufficientLenderBalanceException;
use Zidisha\Lender\Lender;
use Zidisha\Loan\BidQuery;
use Zidisha\Mail\LenderMailer;

class LoanService
{
    /**
     * @var CurrencyService
     */
    private $currencyService;
    /**
     * @var \Zidisha\Mail\LenderMailer
     */
    private $lenderMailer;
    /**
     * @var MixpanelService
     */
    private $mixpanelService;

    public function __construct(CurrencyService $currencyService, LenderMailer $lenderMailer, MixpanelService $mixpanelService)
    {
        $this->currencyService = $currencyService;
        $this->lenderMailer = $lenderMailer;
        $this->mixpanelService = $mixpanelService;
    }

    protected $loanIndex;

    public function applyForLoan(Borrower $borrower, $data)
    {
        $data['currencyCode'] = $borrower->getCountry()->getCurrencyCode();

        $loanCategory = CategoryQuery::create()
            ->findOneById($data['categoryId']);

        $data['usdAmount'] = $this->currencyService->convertToUSD(
            Money::valueOf($data['amount'], Currency::valueOf($data['currencyCode']))
        )->getAmount();

        $loan = Loan::createFromData($data);

        $loan->setCategory($loanCategory);
        $loan->setBorrower($borrower);
        $loan->setStatus(Loan::OPEN);

        $stage = new Stage();
        $stage->setBorrower($loan->getBorrower());
        $stage->setStatus(Loan::OPEN);
        $stage->setStartDate(new \DateTime());
        $loan->addStage($stage);

        $loan->save();

        $this->addToLoanIndex($loan);
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

    public function searchLoans($conditions = array(), $page = 1)
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

        $query->setFrom(($page - 1) * 2);
        $query->setSize($page * 2);

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
            2
        );
    }

    public function addToLoanIndex(Loan $loan)
    {
        $loanIndex = $this->getLoanIndex();

        $loanType = $loanIndex->getType('loan');

        $data = array(
            'id' => $loan->getId(),
            'category' => $loan->getCategory()->getName(),
            'categoryId' => $loan->getCategory()->getId(),
            'countryId' => $loan->getBorrower()->getCountry()->getId(),
            'country_code' => $loan->getBorrower()->getCountry()->getCountryCode(),
            'summary' => $loan->getSummary(),
            'description' => $loan->getDescription(),
            'status' => $loan->getStatus(),
            'created_at' => $loan->getCreatedAt()->getTimestamp(),
        );

        $loanDocument = new \Elastica\Document($loan->getId(), $data);

        $loanType->addDocument($loanDocument);

        $loanType->getIndex()->refresh();
    }

    public function placeBid(Loan $loan, Lender $lender, $data)
    {
        $currentBalance = TransactionQuery::create()
            ->filterByUser($lender->getUser())
            ->getTotalBalance();

        if ($data['amount'] >= $currentBalance) {
            throw new InsufficientLenderBalanceException();
        }

        $con = Propel::getWriteConnection(TransactionTableMap::DATABASE_NAME);
        $con->beginTransaction();

        //TODO: calculate the accepted amount.

        $amount = Money::valueOf($data['amount'], Currency::valueOf('USD'));
        try {
            $bid = new Bid();
            $bid
                ->setLoan($loan)
                ->setLender($lender)
                ->setBorrower($loan->getBorrower())
                ->setBidAmount($amount)
                ->setInterestRate($data['interestRate'])
                ->setActive(true)
                ->setBidDate(new \DateTime());

            $bidSuccess = $bid->save($con);

            $bidTransaction = new Transaction();
            $bidTransaction
                ->setUser($lender->getUser())
                ->setAmount($amount)
                ->setDescription('Loan bid')
                ->setLoan($loan)
                ->setTransactionDate(new \DateTime())
                ->setType(Transaction::LOAN_BID)
                ->setSubType(Transaction::PLACE_BID);

            $bidTransactionSuccess = $bidTransaction->save($con);

            //Todo: OutBid Transaction.

            if (!$bidSuccess || !$bidTransactionSuccess) {
                // Todo: Notify admin.
                throw new \Exception();
            }

            $con->commit();
        } catch (\Exception $e) {
            $con->rollBack();
            throw $e;
        }

        // Send bid confirmation mail
        $this->lenderMailer->bidPlaceMail($bid);

        // Check if this lender is palcing his first bid, if so send him a mail
        $hasLenderBidEarlier = BidQuery::create()
            ->filterByLender($lender)
            ->findOne();

        if (!$hasLenderBidEarlier) {
            $this->lenderMailer->sendPlaceBidMail($bid);
        }

        $this->mixpanelService->trackPlacedBid($bid);

        $totalBidAmount = BidQuery::create()
            ->filterByLoan($loan)
            ->getTotalBidAmount();

        if ($totalBidAmount->compare($loan->getAmount()) != -1) {

            $bids = BidQuery::create()
                ->filterByLoan($loan)
                ->find();

            foreach ($bids as $oneBid) {
                $this->lenderMailer->loanCompletionMail($oneBid);
            }
        }

        //Todo: Lender Invite Credit.

        return $bid;
    }

}
