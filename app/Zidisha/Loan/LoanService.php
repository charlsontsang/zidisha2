<?php

namespace Zidisha\Loan;


use Propel\Tests\Runtime\Util\PropelModelPagerTest;
use Zidisha\Loan\Base\LoanQuery;

class LoanService
{

    protected $loanIndex;

    public function applyForLoan(Loan $loan)
    {
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
        $sortedLoans = array_filter($sortedLoans, function ($l) {
            return !is_scalar($l);
        });

        $paginatorFactory = \App::make('paginator');

        return $paginatorFactory->make(
            $sortedLoans,
            $results->getTotalHits(),
            2
        );
    }

    protected function addToLoanIndex(Loan $loan)
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
            'created_at' => $loan->getCreatedAt()->getTimestamp(),
        );

        $loanDocument = new \Elastica\Document($loan->getId(), $data);

        $loanType->addDocument($loanDocument);

        $loanType->getIndex()->refresh();
    }

} 