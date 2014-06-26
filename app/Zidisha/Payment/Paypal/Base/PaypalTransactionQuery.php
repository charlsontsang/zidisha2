<?php

namespace Zidisha\Payment\Paypal\Base;

use \Exception;
use \PDO;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use Zidisha\Payment\Payment;
use Zidisha\Payment\Paypal\PaypalTransaction as ChildPaypalTransaction;
use Zidisha\Payment\Paypal\PaypalTransactionQuery as ChildPaypalTransactionQuery;
use Zidisha\Payment\Paypal\Map\PaypalTransactionTableMap;

/**
 * Base class that represents a query for the 'paypal_transactions' table.
 *
 *
 *
 * @method     ChildPaypalTransactionQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildPaypalTransactionQuery orderByTransactionId($order = Criteria::ASC) Order by the transaction_id column
 * @method     ChildPaypalTransactionQuery orderByTransactionType($order = Criteria::ASC) Order by the transaction_type column
 * @method     ChildPaypalTransactionQuery orderByAmount($order = Criteria::ASC) Order by the amount column
 * @method     ChildPaypalTransactionQuery orderByDonationAmount($order = Criteria::ASC) Order by the donation_amount column
 * @method     ChildPaypalTransactionQuery orderByPaypalTransactionFee($order = Criteria::ASC) Order by the paypal_transaction_fee column
 * @method     ChildPaypalTransactionQuery orderByTotalAmount($order = Criteria::ASC) Order by the total_amount column
 * @method     ChildPaypalTransactionQuery orderByStatus($order = Criteria::ASC) Order by the status column
 * @method     ChildPaypalTransactionQuery orderByCustom($order = Criteria::ASC) Order by the custom column
 * @method     ChildPaypalTransactionQuery orderByToken($order = Criteria::ASC) Order by the token column
 * @method     ChildPaypalTransactionQuery orderByPaymentId($order = Criteria::ASC) Order by the payment_id column
 * @method     ChildPaypalTransactionQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildPaypalTransactionQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildPaypalTransactionQuery groupById() Group by the id column
 * @method     ChildPaypalTransactionQuery groupByTransactionId() Group by the transaction_id column
 * @method     ChildPaypalTransactionQuery groupByTransactionType() Group by the transaction_type column
 * @method     ChildPaypalTransactionQuery groupByAmount() Group by the amount column
 * @method     ChildPaypalTransactionQuery groupByDonationAmount() Group by the donation_amount column
 * @method     ChildPaypalTransactionQuery groupByPaypalTransactionFee() Group by the paypal_transaction_fee column
 * @method     ChildPaypalTransactionQuery groupByTotalAmount() Group by the total_amount column
 * @method     ChildPaypalTransactionQuery groupByStatus() Group by the status column
 * @method     ChildPaypalTransactionQuery groupByCustom() Group by the custom column
 * @method     ChildPaypalTransactionQuery groupByToken() Group by the token column
 * @method     ChildPaypalTransactionQuery groupByPaymentId() Group by the payment_id column
 * @method     ChildPaypalTransactionQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildPaypalTransactionQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildPaypalTransactionQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildPaypalTransactionQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildPaypalTransactionQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildPaypalTransactionQuery leftJoinPayment($relationAlias = null) Adds a LEFT JOIN clause to the query using the Payment relation
 * @method     ChildPaypalTransactionQuery rightJoinPayment($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Payment relation
 * @method     ChildPaypalTransactionQuery innerJoinPayment($relationAlias = null) Adds a INNER JOIN clause to the query using the Payment relation
 *
 * @method     \Zidisha\Payment\PaymentQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildPaypalTransaction findOne(ConnectionInterface $con = null) Return the first ChildPaypalTransaction matching the query
 * @method     ChildPaypalTransaction findOneOrCreate(ConnectionInterface $con = null) Return the first ChildPaypalTransaction matching the query, or a new ChildPaypalTransaction object populated from the query conditions when no match is found
 *
 * @method     ChildPaypalTransaction findOneById(int $id) Return the first ChildPaypalTransaction filtered by the id column
 * @method     ChildPaypalTransaction findOneByTransactionId(string $transaction_id) Return the first ChildPaypalTransaction filtered by the transaction_id column
 * @method     ChildPaypalTransaction findOneByTransactionType(string $transaction_type) Return the first ChildPaypalTransaction filtered by the transaction_type column
 * @method     ChildPaypalTransaction findOneByAmount(string $amount) Return the first ChildPaypalTransaction filtered by the amount column
 * @method     ChildPaypalTransaction findOneByDonationAmount(string $donation_amount) Return the first ChildPaypalTransaction filtered by the donation_amount column
 * @method     ChildPaypalTransaction findOneByPaypalTransactionFee(string $paypal_transaction_fee) Return the first ChildPaypalTransaction filtered by the paypal_transaction_fee column
 * @method     ChildPaypalTransaction findOneByTotalAmount(string $total_amount) Return the first ChildPaypalTransaction filtered by the total_amount column
 * @method     ChildPaypalTransaction findOneByStatus(string $status) Return the first ChildPaypalTransaction filtered by the status column
 * @method     ChildPaypalTransaction findOneByCustom(string $custom) Return the first ChildPaypalTransaction filtered by the custom column
 * @method     ChildPaypalTransaction findOneByToken(string $token) Return the first ChildPaypalTransaction filtered by the token column
 * @method     ChildPaypalTransaction findOneByPaymentId(int $payment_id) Return the first ChildPaypalTransaction filtered by the payment_id column
 * @method     ChildPaypalTransaction findOneByCreatedAt(string $created_at) Return the first ChildPaypalTransaction filtered by the created_at column
 * @method     ChildPaypalTransaction findOneByUpdatedAt(string $updated_at) Return the first ChildPaypalTransaction filtered by the updated_at column
 *
 * @method     ChildPaypalTransaction[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildPaypalTransaction objects based on current ModelCriteria
 * @method     ChildPaypalTransaction[]|ObjectCollection findById(int $id) Return ChildPaypalTransaction objects filtered by the id column
 * @method     ChildPaypalTransaction[]|ObjectCollection findByTransactionId(string $transaction_id) Return ChildPaypalTransaction objects filtered by the transaction_id column
 * @method     ChildPaypalTransaction[]|ObjectCollection findByTransactionType(string $transaction_type) Return ChildPaypalTransaction objects filtered by the transaction_type column
 * @method     ChildPaypalTransaction[]|ObjectCollection findByAmount(string $amount) Return ChildPaypalTransaction objects filtered by the amount column
 * @method     ChildPaypalTransaction[]|ObjectCollection findByDonationAmount(string $donation_amount) Return ChildPaypalTransaction objects filtered by the donation_amount column
 * @method     ChildPaypalTransaction[]|ObjectCollection findByPaypalTransactionFee(string $paypal_transaction_fee) Return ChildPaypalTransaction objects filtered by the paypal_transaction_fee column
 * @method     ChildPaypalTransaction[]|ObjectCollection findByTotalAmount(string $total_amount) Return ChildPaypalTransaction objects filtered by the total_amount column
 * @method     ChildPaypalTransaction[]|ObjectCollection findByStatus(string $status) Return ChildPaypalTransaction objects filtered by the status column
 * @method     ChildPaypalTransaction[]|ObjectCollection findByCustom(string $custom) Return ChildPaypalTransaction objects filtered by the custom column
 * @method     ChildPaypalTransaction[]|ObjectCollection findByToken(string $token) Return ChildPaypalTransaction objects filtered by the token column
 * @method     ChildPaypalTransaction[]|ObjectCollection findByPaymentId(int $payment_id) Return ChildPaypalTransaction objects filtered by the payment_id column
 * @method     ChildPaypalTransaction[]|ObjectCollection findByCreatedAt(string $created_at) Return ChildPaypalTransaction objects filtered by the created_at column
 * @method     ChildPaypalTransaction[]|ObjectCollection findByUpdatedAt(string $updated_at) Return ChildPaypalTransaction objects filtered by the updated_at column
 * @method     ChildPaypalTransaction[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class PaypalTransactionQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \Zidisha\Payment\Paypal\Base\PaypalTransactionQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'zidisha', $modelName = '\\Zidisha\\Payment\\Paypal\\PaypalTransaction', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildPaypalTransactionQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildPaypalTransactionQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildPaypalTransactionQuery) {
            return $criteria;
        }
        $query = new ChildPaypalTransactionQuery();
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
     * @return ChildPaypalTransaction|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = PaypalTransactionTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(PaypalTransactionTableMap::DATABASE_NAME);
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
     * @return ChildPaypalTransaction A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT ID, TRANSACTION_ID, TRANSACTION_TYPE, AMOUNT, DONATION_AMOUNT, PAYPAL_TRANSACTION_FEE, TOTAL_AMOUNT, STATUS, CUSTOM, TOKEN, PAYMENT_ID, CREATED_AT, UPDATED_AT FROM paypal_transactions WHERE ID = :p0';
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
            /** @var ChildPaypalTransaction $obj */
            $obj = new ChildPaypalTransaction();
            $obj->hydrate($row);
            PaypalTransactionTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildPaypalTransaction|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildPaypalTransactionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(PaypalTransactionTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildPaypalTransactionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(PaypalTransactionTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildPaypalTransactionQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(PaypalTransactionTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(PaypalTransactionTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PaypalTransactionTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the transaction_id column
     *
     * Example usage:
     * <code>
     * $query->filterByTransactionId('fooValue');   // WHERE transaction_id = 'fooValue'
     * $query->filterByTransactionId('%fooValue%'); // WHERE transaction_id LIKE '%fooValue%'
     * </code>
     *
     * @param     string $transactionId The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildPaypalTransactionQuery The current query, for fluid interface
     */
    public function filterByTransactionId($transactionId = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($transactionId)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $transactionId)) {
                $transactionId = str_replace('*', '%', $transactionId);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PaypalTransactionTableMap::COL_TRANSACTION_ID, $transactionId, $comparison);
    }

    /**
     * Filter the query on the transaction_type column
     *
     * Example usage:
     * <code>
     * $query->filterByTransactionType('fooValue');   // WHERE transaction_type = 'fooValue'
     * $query->filterByTransactionType('%fooValue%'); // WHERE transaction_type LIKE '%fooValue%'
     * </code>
     *
     * @param     string $transactionType The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildPaypalTransactionQuery The current query, for fluid interface
     */
    public function filterByTransactionType($transactionType = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($transactionType)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $transactionType)) {
                $transactionType = str_replace('*', '%', $transactionType);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PaypalTransactionTableMap::COL_TRANSACTION_TYPE, $transactionType, $comparison);
    }

    /**
     * Filter the query on the amount column
     *
     * Example usage:
     * <code>
     * $query->filterByAmount(1234); // WHERE amount = 1234
     * $query->filterByAmount(array(12, 34)); // WHERE amount IN (12, 34)
     * $query->filterByAmount(array('min' => 12)); // WHERE amount > 12
     * </code>
     *
     * @param     mixed $amount The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildPaypalTransactionQuery The current query, for fluid interface
     */
    public function filterByAmount($amount = null, $comparison = null)
    {
        if (is_array($amount)) {
            $useMinMax = false;
            if (isset($amount['min'])) {
                $this->addUsingAlias(PaypalTransactionTableMap::COL_AMOUNT, $amount['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($amount['max'])) {
                $this->addUsingAlias(PaypalTransactionTableMap::COL_AMOUNT, $amount['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PaypalTransactionTableMap::COL_AMOUNT, $amount, $comparison);
    }

    /**
     * Filter the query on the donation_amount column
     *
     * Example usage:
     * <code>
     * $query->filterByDonationAmount(1234); // WHERE donation_amount = 1234
     * $query->filterByDonationAmount(array(12, 34)); // WHERE donation_amount IN (12, 34)
     * $query->filterByDonationAmount(array('min' => 12)); // WHERE donation_amount > 12
     * </code>
     *
     * @param     mixed $donationAmount The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildPaypalTransactionQuery The current query, for fluid interface
     */
    public function filterByDonationAmount($donationAmount = null, $comparison = null)
    {
        if (is_array($donationAmount)) {
            $useMinMax = false;
            if (isset($donationAmount['min'])) {
                $this->addUsingAlias(PaypalTransactionTableMap::COL_DONATION_AMOUNT, $donationAmount['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($donationAmount['max'])) {
                $this->addUsingAlias(PaypalTransactionTableMap::COL_DONATION_AMOUNT, $donationAmount['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PaypalTransactionTableMap::COL_DONATION_AMOUNT, $donationAmount, $comparison);
    }

    /**
     * Filter the query on the paypal_transaction_fee column
     *
     * Example usage:
     * <code>
     * $query->filterByPaypalTransactionFee(1234); // WHERE paypal_transaction_fee = 1234
     * $query->filterByPaypalTransactionFee(array(12, 34)); // WHERE paypal_transaction_fee IN (12, 34)
     * $query->filterByPaypalTransactionFee(array('min' => 12)); // WHERE paypal_transaction_fee > 12
     * </code>
     *
     * @param     mixed $paypalTransactionFee The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildPaypalTransactionQuery The current query, for fluid interface
     */
    public function filterByPaypalTransactionFee($paypalTransactionFee = null, $comparison = null)
    {
        if (is_array($paypalTransactionFee)) {
            $useMinMax = false;
            if (isset($paypalTransactionFee['min'])) {
                $this->addUsingAlias(PaypalTransactionTableMap::COL_PAYPAL_TRANSACTION_FEE, $paypalTransactionFee['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($paypalTransactionFee['max'])) {
                $this->addUsingAlias(PaypalTransactionTableMap::COL_PAYPAL_TRANSACTION_FEE, $paypalTransactionFee['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PaypalTransactionTableMap::COL_PAYPAL_TRANSACTION_FEE, $paypalTransactionFee, $comparison);
    }

    /**
     * Filter the query on the total_amount column
     *
     * Example usage:
     * <code>
     * $query->filterByTotalAmount(1234); // WHERE total_amount = 1234
     * $query->filterByTotalAmount(array(12, 34)); // WHERE total_amount IN (12, 34)
     * $query->filterByTotalAmount(array('min' => 12)); // WHERE total_amount > 12
     * </code>
     *
     * @param     mixed $totalAmount The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildPaypalTransactionQuery The current query, for fluid interface
     */
    public function filterByTotalAmount($totalAmount = null, $comparison = null)
    {
        if (is_array($totalAmount)) {
            $useMinMax = false;
            if (isset($totalAmount['min'])) {
                $this->addUsingAlias(PaypalTransactionTableMap::COL_TOTAL_AMOUNT, $totalAmount['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($totalAmount['max'])) {
                $this->addUsingAlias(PaypalTransactionTableMap::COL_TOTAL_AMOUNT, $totalAmount['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PaypalTransactionTableMap::COL_TOTAL_AMOUNT, $totalAmount, $comparison);
    }

    /**
     * Filter the query on the status column
     *
     * Example usage:
     * <code>
     * $query->filterByStatus('fooValue');   // WHERE status = 'fooValue'
     * $query->filterByStatus('%fooValue%'); // WHERE status LIKE '%fooValue%'
     * </code>
     *
     * @param     string $status The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildPaypalTransactionQuery The current query, for fluid interface
     */
    public function filterByStatus($status = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($status)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $status)) {
                $status = str_replace('*', '%', $status);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PaypalTransactionTableMap::COL_STATUS, $status, $comparison);
    }

    /**
     * Filter the query on the custom column
     *
     * Example usage:
     * <code>
     * $query->filterByCustom('fooValue');   // WHERE custom = 'fooValue'
     * $query->filterByCustom('%fooValue%'); // WHERE custom LIKE '%fooValue%'
     * </code>
     *
     * @param     string $custom The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildPaypalTransactionQuery The current query, for fluid interface
     */
    public function filterByCustom($custom = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($custom)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $custom)) {
                $custom = str_replace('*', '%', $custom);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PaypalTransactionTableMap::COL_CUSTOM, $custom, $comparison);
    }

    /**
     * Filter the query on the token column
     *
     * Example usage:
     * <code>
     * $query->filterByToken('fooValue');   // WHERE token = 'fooValue'
     * $query->filterByToken('%fooValue%'); // WHERE token LIKE '%fooValue%'
     * </code>
     *
     * @param     string $token The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildPaypalTransactionQuery The current query, for fluid interface
     */
    public function filterByToken($token = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($token)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $token)) {
                $token = str_replace('*', '%', $token);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PaypalTransactionTableMap::COL_TOKEN, $token, $comparison);
    }

    /**
     * Filter the query on the payment_id column
     *
     * Example usage:
     * <code>
     * $query->filterByPaymentId(1234); // WHERE payment_id = 1234
     * $query->filterByPaymentId(array(12, 34)); // WHERE payment_id IN (12, 34)
     * $query->filterByPaymentId(array('min' => 12)); // WHERE payment_id > 12
     * </code>
     *
     * @see       filterByPayment()
     *
     * @param     mixed $paymentId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildPaypalTransactionQuery The current query, for fluid interface
     */
    public function filterByPaymentId($paymentId = null, $comparison = null)
    {
        if (is_array($paymentId)) {
            $useMinMax = false;
            if (isset($paymentId['min'])) {
                $this->addUsingAlias(PaypalTransactionTableMap::COL_PAYMENT_ID, $paymentId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($paymentId['max'])) {
                $this->addUsingAlias(PaypalTransactionTableMap::COL_PAYMENT_ID, $paymentId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PaypalTransactionTableMap::COL_PAYMENT_ID, $paymentId, $comparison);
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
     * @return $this|ChildPaypalTransactionQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(PaypalTransactionTableMap::COL_CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(PaypalTransactionTableMap::COL_CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PaypalTransactionTableMap::COL_CREATED_AT, $createdAt, $comparison);
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
     * @return $this|ChildPaypalTransactionQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(PaypalTransactionTableMap::COL_UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(PaypalTransactionTableMap::COL_UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PaypalTransactionTableMap::COL_UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \Zidisha\Payment\Payment object
     *
     * @param \Zidisha\Payment\Payment|ObjectCollection $payment The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPaypalTransactionQuery The current query, for fluid interface
     */
    public function filterByPayment($payment, $comparison = null)
    {
        if ($payment instanceof \Zidisha\Payment\Payment) {
            return $this
                ->addUsingAlias(PaypalTransactionTableMap::COL_PAYMENT_ID, $payment->getId(), $comparison);
        } elseif ($payment instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(PaypalTransactionTableMap::COL_PAYMENT_ID, $payment->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByPayment() only accepts arguments of type \Zidisha\Payment\Payment or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Payment relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildPaypalTransactionQuery The current query, for fluid interface
     */
    public function joinPayment($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Payment');

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
            $this->addJoinObject($join, 'Payment');
        }

        return $this;
    }

    /**
     * Use the Payment relation Payment object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \Zidisha\Payment\PaymentQuery A secondary query class using the current class as primary query
     */
    public function usePaymentQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinPayment($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Payment', '\Zidisha\Payment\PaymentQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildPaypalTransaction $paypalTransaction Object to remove from the list of results
     *
     * @return $this|ChildPaypalTransactionQuery The current query, for fluid interface
     */
    public function prune($paypalTransaction = null)
    {
        if ($paypalTransaction) {
            $this->addUsingAlias(PaypalTransactionTableMap::COL_ID, $paypalTransaction->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the paypal_transactions table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(PaypalTransactionTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            PaypalTransactionTableMap::clearInstancePool();
            PaypalTransactionTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(PaypalTransactionTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(PaypalTransactionTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            PaypalTransactionTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            PaypalTransactionTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     $this|ChildPaypalTransactionQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(PaypalTransactionTableMap::COL_UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     $this|ChildPaypalTransactionQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(PaypalTransactionTableMap::COL_UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     $this|ChildPaypalTransactionQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(PaypalTransactionTableMap::COL_UPDATED_AT);
    }

    /**
     * Order by create date desc
     *
     * @return     $this|ChildPaypalTransactionQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(PaypalTransactionTableMap::COL_CREATED_AT);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     $this|ChildPaypalTransactionQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(PaypalTransactionTableMap::COL_CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date asc
     *
     * @return     $this|ChildPaypalTransactionQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(PaypalTransactionTableMap::COL_CREATED_AT);
    }

} // PaypalTransactionQuery
