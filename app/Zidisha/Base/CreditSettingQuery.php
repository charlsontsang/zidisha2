<?php

namespace Zidisha\Base;

use \Exception;
use \PDO;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use Zidisha\CreditSetting as ChildCreditSetting;
use Zidisha\CreditSettingQuery as ChildCreditSettingQuery;
use Zidisha\Country\Country;
use Zidisha\Map\CreditSettingTableMap;

/**
 * Base class that represents a query for the 'credit_settings' table.
 *
 *
 *
 * @method     ChildCreditSettingQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildCreditSettingQuery orderByCountryCode($order = Criteria::ASC) Order by the country_code column
 * @method     ChildCreditSettingQuery orderByLoanAmountLimit($order = Criteria::ASC) Order by the loan_amount_limit column
 * @method     ChildCreditSettingQuery orderByCharacterLimit($order = Criteria::ASC) Order by the character_limit column
 * @method     ChildCreditSettingQuery orderByCommentsLimit($order = Criteria::ASC) Order by the comments_limit column
 * @method     ChildCreditSettingQuery orderByType($order = Criteria::ASC) Order by the type column
 * @method     ChildCreditSettingQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildCreditSettingQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildCreditSettingQuery groupById() Group by the id column
 * @method     ChildCreditSettingQuery groupByCountryCode() Group by the country_code column
 * @method     ChildCreditSettingQuery groupByLoanAmountLimit() Group by the loan_amount_limit column
 * @method     ChildCreditSettingQuery groupByCharacterLimit() Group by the character_limit column
 * @method     ChildCreditSettingQuery groupByCommentsLimit() Group by the comments_limit column
 * @method     ChildCreditSettingQuery groupByType() Group by the type column
 * @method     ChildCreditSettingQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildCreditSettingQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildCreditSettingQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildCreditSettingQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildCreditSettingQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildCreditSettingQuery leftJoinCountry($relationAlias = null) Adds a LEFT JOIN clause to the query using the Country relation
 * @method     ChildCreditSettingQuery rightJoinCountry($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Country relation
 * @method     ChildCreditSettingQuery innerJoinCountry($relationAlias = null) Adds a INNER JOIN clause to the query using the Country relation
 *
 * @method     \Zidisha\Country\CountryQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildCreditSetting findOne(ConnectionInterface $con = null) Return the first ChildCreditSetting matching the query
 * @method     ChildCreditSetting findOneOrCreate(ConnectionInterface $con = null) Return the first ChildCreditSetting matching the query, or a new ChildCreditSetting object populated from the query conditions when no match is found
 *
 * @method     ChildCreditSetting findOneById(int $id) Return the first ChildCreditSetting filtered by the id column
 * @method     ChildCreditSetting findOneByCountryCode(string $country_code) Return the first ChildCreditSetting filtered by the country_code column
 * @method     ChildCreditSetting findOneByLoanAmountLimit(int $loan_amount_limit) Return the first ChildCreditSetting filtered by the loan_amount_limit column
 * @method     ChildCreditSetting findOneByCharacterLimit(int $character_limit) Return the first ChildCreditSetting filtered by the character_limit column
 * @method     ChildCreditSetting findOneByCommentsLimit(int $comments_limit) Return the first ChildCreditSetting filtered by the comments_limit column
 * @method     ChildCreditSetting findOneByType(int $type) Return the first ChildCreditSetting filtered by the type column
 * @method     ChildCreditSetting findOneByCreatedAt(string $created_at) Return the first ChildCreditSetting filtered by the created_at column
 * @method     ChildCreditSetting findOneByUpdatedAt(string $updated_at) Return the first ChildCreditSetting filtered by the updated_at column
 *
 * @method     ChildCreditSetting[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildCreditSetting objects based on current ModelCriteria
 * @method     ChildCreditSetting[]|ObjectCollection findById(int $id) Return ChildCreditSetting objects filtered by the id column
 * @method     ChildCreditSetting[]|ObjectCollection findByCountryCode(string $country_code) Return ChildCreditSetting objects filtered by the country_code column
 * @method     ChildCreditSetting[]|ObjectCollection findByLoanAmountLimit(int $loan_amount_limit) Return ChildCreditSetting objects filtered by the loan_amount_limit column
 * @method     ChildCreditSetting[]|ObjectCollection findByCharacterLimit(int $character_limit) Return ChildCreditSetting objects filtered by the character_limit column
 * @method     ChildCreditSetting[]|ObjectCollection findByCommentsLimit(int $comments_limit) Return ChildCreditSetting objects filtered by the comments_limit column
 * @method     ChildCreditSetting[]|ObjectCollection findByType(int $type) Return ChildCreditSetting objects filtered by the type column
 * @method     ChildCreditSetting[]|ObjectCollection findByCreatedAt(string $created_at) Return ChildCreditSetting objects filtered by the created_at column
 * @method     ChildCreditSetting[]|ObjectCollection findByUpdatedAt(string $updated_at) Return ChildCreditSetting objects filtered by the updated_at column
 * @method     ChildCreditSetting[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class CreditSettingQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \Zidisha\Base\CreditSettingQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'zidisha', $modelName = '\\Zidisha\\CreditSetting', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildCreditSettingQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildCreditSettingQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildCreditSettingQuery) {
            return $criteria;
        }
        $query = new ChildCreditSettingQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildCreditSetting|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CreditSettingTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(CreditSettingTableMap::DATABASE_NAME);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildCreditSetting A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT ID, COUNTRY_CODE, LOAN_AMOUNT_LIMIT, CHARACTER_LIMIT, COMMENTS_LIMIT, TYPE, CREATED_AT, UPDATED_AT FROM credit_settings WHERE ID = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            /** @var ChildCreditSetting $obj */
            $obj = new ChildCreditSetting();
            $obj->hydrate($row);
            CreditSettingTableMap::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildCreditSetting|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, ConnectionInterface $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return $this|ChildCreditSettingQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CreditSettingTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildCreditSettingQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CreditSettingTableMap::COL_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildCreditSettingQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(CreditSettingTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(CreditSettingTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditSettingTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the country_code column
     *
     * Example usage:
     * <code>
     * $query->filterByCountryCode('fooValue');   // WHERE country_code = 'fooValue'
     * $query->filterByCountryCode('%fooValue%'); // WHERE country_code LIKE '%fooValue%'
     * </code>
     *
     * @param     string $countryCode The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildCreditSettingQuery The current query, for fluid interface
     */
    public function filterByCountryCode($countryCode = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($countryCode)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $countryCode)) {
                $countryCode = str_replace('*', '%', $countryCode);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CreditSettingTableMap::COL_COUNTRY_CODE, $countryCode, $comparison);
    }

    /**
     * Filter the query on the loan_amount_limit column
     *
     * Example usage:
     * <code>
     * $query->filterByLoanAmountLimit(1234); // WHERE loan_amount_limit = 1234
     * $query->filterByLoanAmountLimit(array(12, 34)); // WHERE loan_amount_limit IN (12, 34)
     * $query->filterByLoanAmountLimit(array('min' => 12)); // WHERE loan_amount_limit > 12
     * </code>
     *
     * @param     mixed $loanAmountLimit The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildCreditSettingQuery The current query, for fluid interface
     */
    public function filterByLoanAmountLimit($loanAmountLimit = null, $comparison = null)
    {
        if (is_array($loanAmountLimit)) {
            $useMinMax = false;
            if (isset($loanAmountLimit['min'])) {
                $this->addUsingAlias(CreditSettingTableMap::COL_LOAN_AMOUNT_LIMIT, $loanAmountLimit['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($loanAmountLimit['max'])) {
                $this->addUsingAlias(CreditSettingTableMap::COL_LOAN_AMOUNT_LIMIT, $loanAmountLimit['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditSettingTableMap::COL_LOAN_AMOUNT_LIMIT, $loanAmountLimit, $comparison);
    }

    /**
     * Filter the query on the character_limit column
     *
     * Example usage:
     * <code>
     * $query->filterByCharacterLimit(1234); // WHERE character_limit = 1234
     * $query->filterByCharacterLimit(array(12, 34)); // WHERE character_limit IN (12, 34)
     * $query->filterByCharacterLimit(array('min' => 12)); // WHERE character_limit > 12
     * </code>
     *
     * @param     mixed $characterLimit The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildCreditSettingQuery The current query, for fluid interface
     */
    public function filterByCharacterLimit($characterLimit = null, $comparison = null)
    {
        if (is_array($characterLimit)) {
            $useMinMax = false;
            if (isset($characterLimit['min'])) {
                $this->addUsingAlias(CreditSettingTableMap::COL_CHARACTER_LIMIT, $characterLimit['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($characterLimit['max'])) {
                $this->addUsingAlias(CreditSettingTableMap::COL_CHARACTER_LIMIT, $characterLimit['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditSettingTableMap::COL_CHARACTER_LIMIT, $characterLimit, $comparison);
    }

    /**
     * Filter the query on the comments_limit column
     *
     * Example usage:
     * <code>
     * $query->filterByCommentsLimit(1234); // WHERE comments_limit = 1234
     * $query->filterByCommentsLimit(array(12, 34)); // WHERE comments_limit IN (12, 34)
     * $query->filterByCommentsLimit(array('min' => 12)); // WHERE comments_limit > 12
     * </code>
     *
     * @param     mixed $commentsLimit The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildCreditSettingQuery The current query, for fluid interface
     */
    public function filterByCommentsLimit($commentsLimit = null, $comparison = null)
    {
        if (is_array($commentsLimit)) {
            $useMinMax = false;
            if (isset($commentsLimit['min'])) {
                $this->addUsingAlias(CreditSettingTableMap::COL_COMMENTS_LIMIT, $commentsLimit['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($commentsLimit['max'])) {
                $this->addUsingAlias(CreditSettingTableMap::COL_COMMENTS_LIMIT, $commentsLimit['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditSettingTableMap::COL_COMMENTS_LIMIT, $commentsLimit, $comparison);
    }

    /**
     * Filter the query on the type column
     *
     * @param     mixed $type The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildCreditSettingQuery The current query, for fluid interface
     */
    public function filterByType($type = null, $comparison = null)
    {
        $valueSet = CreditSettingTableMap::getValueSet(CreditSettingTableMap::COL_TYPE);
        if (is_scalar($type)) {
            if (!in_array($type, $valueSet)) {
                throw new PropelException(sprintf('Value "%s" is not accepted in this enumerated column', $type));
            }
            $type = array_search($type, $valueSet);
        } elseif (is_array($type)) {
            $convertedValues = array();
            foreach ($type as $value) {
                if (!in_array($value, $valueSet)) {
                    throw new PropelException(sprintf('Value "%s" is not accepted in this enumerated column', $value));
                }
                $convertedValues []= array_search($value, $valueSet);
            }
            $type = $convertedValues;
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditSettingTableMap::COL_TYPE, $type, $comparison);
    }

    /**
     * Filter the query on the created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $createdAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildCreditSettingQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(CreditSettingTableMap::COL_CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(CreditSettingTableMap::COL_CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditSettingTableMap::COL_CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the updated_at column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $updatedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildCreditSettingQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(CreditSettingTableMap::COL_UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(CreditSettingTableMap::COL_UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditSettingTableMap::COL_UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \Zidisha\Country\Country object
     *
     * @param \Zidisha\Country\Country|ObjectCollection $country The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditSettingQuery The current query, for fluid interface
     */
    public function filterByCountry($country, $comparison = null)
    {
        if ($country instanceof \Zidisha\Country\Country) {
            return $this
                ->addUsingAlias(CreditSettingTableMap::COL_COUNTRY_CODE, $country->getCountryCode(), $comparison);
        } elseif ($country instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CreditSettingTableMap::COL_COUNTRY_CODE, $country->toKeyValue('PrimaryKey', 'CountryCode'), $comparison);
        } else {
            throw new PropelException('filterByCountry() only accepts arguments of type \Zidisha\Country\Country or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Country relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildCreditSettingQuery The current query, for fluid interface
     */
    public function joinCountry($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Country');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Country');
        }

        return $this;
    }

    /**
     * Use the Country relation Country object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \Zidisha\Country\CountryQuery A secondary query class using the current class as primary query
     */
    public function useCountryQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCountry($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Country', '\Zidisha\Country\CountryQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildCreditSetting $creditSetting Object to remove from the list of results
     *
     * @return $this|ChildCreditSettingQuery The current query, for fluid interface
     */
    public function prune($creditSetting = null)
    {
        if ($creditSetting) {
            $this->addUsingAlias(CreditSettingTableMap::COL_ID, $creditSetting->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the credit_settings table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CreditSettingTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            CreditSettingTableMap::clearInstancePool();
            CreditSettingTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    /**
     * Performs a DELETE on the database based on the current ModelCriteria
     *
     * @param ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public function delete(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CreditSettingTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(CreditSettingTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            CreditSettingTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            CreditSettingTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     $this|ChildCreditSettingQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(CreditSettingTableMap::COL_UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     $this|ChildCreditSettingQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(CreditSettingTableMap::COL_UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     $this|ChildCreditSettingQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(CreditSettingTableMap::COL_UPDATED_AT);
    }

    /**
     * Order by create date desc
     *
     * @return     $this|ChildCreditSettingQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(CreditSettingTableMap::COL_CREATED_AT);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     $this|ChildCreditSettingQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(CreditSettingTableMap::COL_CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date asc
     *
     * @return     $this|ChildCreditSettingQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(CreditSettingTableMap::COL_CREATED_AT);
    }

} // CreditSettingQuery
