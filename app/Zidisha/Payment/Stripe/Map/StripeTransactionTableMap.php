<?php

namespace Zidisha\Payment\Stripe\Map;

use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\InstancePoolTrait;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\DataFetcher\DataFetcherInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Map\TableMapTrait;
use Zidisha\Payment\Stripe\StripeTransaction;
use Zidisha\Payment\Stripe\StripeTransactionQuery;


/**
 * This class defines the structure of the 'stripe_transactions' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class StripeTransactionTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Zidisha.Payment.Stripe.Map.StripeTransactionTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'zidisha';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'stripe_transactions';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Zidisha\\Payment\\Stripe\\StripeTransaction';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Zidisha.Payment.Stripe.StripeTransaction';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 10;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 10;

    /**
     * the column name for the ID field
     */
    const COL_ID = 'stripe_transactions.ID';

    /**
     * the column name for the STRIPE_ID field
     */
    const COL_STRIPE_ID = 'stripe_transactions.STRIPE_ID';

    /**
     * the column name for the AMOUNT field
     */
    const COL_AMOUNT = 'stripe_transactions.AMOUNT';

    /**
     * the column name for the DONATION_AMOUNT field
     */
    const COL_DONATION_AMOUNT = 'stripe_transactions.DONATION_AMOUNT';

    /**
     * the column name for the TRANSACTION_FEE field
     */
    const COL_TRANSACTION_FEE = 'stripe_transactions.TRANSACTION_FEE';

    /**
     * the column name for the TOTAL_AMOUNT field
     */
    const COL_TOTAL_AMOUNT = 'stripe_transactions.TOTAL_AMOUNT';

    /**
     * the column name for the STATUS field
     */
    const COL_STATUS = 'stripe_transactions.STATUS';

    /**
     * the column name for the PAYMENT_ID field
     */
    const COL_PAYMENT_ID = 'stripe_transactions.PAYMENT_ID';

    /**
     * the column name for the CREATED_AT field
     */
    const COL_CREATED_AT = 'stripe_transactions.CREATED_AT';

    /**
     * the column name for the UPDATED_AT field
     */
    const COL_UPDATED_AT = 'stripe_transactions.UPDATED_AT';

    /**
     * The default string format for model objects of the related table
     */
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        self::TYPE_PHPNAME       => array('Id', 'StripeId', 'Amount', 'DonationAmount', 'TransactionFee', 'TotalAmount', 'Status', 'PaymentId', 'CreatedAt', 'UpdatedAt', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'stripeId', 'amount', 'donationAmount', 'transactionFee', 'totalAmount', 'status', 'paymentId', 'createdAt', 'updatedAt', ),
        self::TYPE_COLNAME       => array(StripeTransactionTableMap::COL_ID, StripeTransactionTableMap::COL_STRIPE_ID, StripeTransactionTableMap::COL_AMOUNT, StripeTransactionTableMap::COL_DONATION_AMOUNT, StripeTransactionTableMap::COL_TRANSACTION_FEE, StripeTransactionTableMap::COL_TOTAL_AMOUNT, StripeTransactionTableMap::COL_STATUS, StripeTransactionTableMap::COL_PAYMENT_ID, StripeTransactionTableMap::COL_CREATED_AT, StripeTransactionTableMap::COL_UPDATED_AT, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID', 'COL_STRIPE_ID', 'COL_AMOUNT', 'COL_DONATION_AMOUNT', 'COL_TRANSACTION_FEE', 'COL_TOTAL_AMOUNT', 'COL_STATUS', 'COL_PAYMENT_ID', 'COL_CREATED_AT', 'COL_UPDATED_AT', ),
        self::TYPE_FIELDNAME     => array('id', 'stripe_id', 'amount', 'donation_amount', 'transaction_fee', 'total_amount', 'status', 'payment_id', 'created_at', 'updated_at', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'StripeId' => 1, 'Amount' => 2, 'DonationAmount' => 3, 'TransactionFee' => 4, 'TotalAmount' => 5, 'Status' => 6, 'PaymentId' => 7, 'CreatedAt' => 8, 'UpdatedAt' => 9, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'stripeId' => 1, 'amount' => 2, 'donationAmount' => 3, 'transactionFee' => 4, 'totalAmount' => 5, 'status' => 6, 'paymentId' => 7, 'createdAt' => 8, 'updatedAt' => 9, ),
        self::TYPE_COLNAME       => array(StripeTransactionTableMap::COL_ID => 0, StripeTransactionTableMap::COL_STRIPE_ID => 1, StripeTransactionTableMap::COL_AMOUNT => 2, StripeTransactionTableMap::COL_DONATION_AMOUNT => 3, StripeTransactionTableMap::COL_TRANSACTION_FEE => 4, StripeTransactionTableMap::COL_TOTAL_AMOUNT => 5, StripeTransactionTableMap::COL_STATUS => 6, StripeTransactionTableMap::COL_PAYMENT_ID => 7, StripeTransactionTableMap::COL_CREATED_AT => 8, StripeTransactionTableMap::COL_UPDATED_AT => 9, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID' => 0, 'COL_STRIPE_ID' => 1, 'COL_AMOUNT' => 2, 'COL_DONATION_AMOUNT' => 3, 'COL_TRANSACTION_FEE' => 4, 'COL_TOTAL_AMOUNT' => 5, 'COL_STATUS' => 6, 'COL_PAYMENT_ID' => 7, 'COL_CREATED_AT' => 8, 'COL_UPDATED_AT' => 9, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'stripe_id' => 1, 'amount' => 2, 'donation_amount' => 3, 'transaction_fee' => 4, 'total_amount' => 5, 'status' => 6, 'payment_id' => 7, 'created_at' => 8, 'updated_at' => 9, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, )
    );

    /**
     * Initialize the table attributes and columns
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('stripe_transactions');
        $this->setPhpName('StripeTransaction');
        $this->setClassName('\\Zidisha\\Payment\\Stripe\\StripeTransaction');
        $this->setPackage('Zidisha.Payment.Stripe');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('stripe_transactions_id_seq');
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('STRIPE_ID', 'StripeId', 'VARCHAR', false, null, null);
        $this->addColumn('AMOUNT', 'Amount', 'DECIMAL', true, 10, null);
        $this->addColumn('DONATION_AMOUNT', 'DonationAmount', 'DECIMAL', true, 10, null);
        $this->addColumn('TRANSACTION_FEE', 'TransactionFee', 'DECIMAL', true, 10, null);
        $this->addColumn('TOTAL_AMOUNT', 'TotalAmount', 'DECIMAL', true, 10, null);
        $this->addColumn('STATUS', 'Status', 'VARCHAR', true, 100, null);
        $this->addForeignKey('PAYMENT_ID', 'PaymentId', 'INTEGER', 'payments', 'ID', false, null, null);
        $this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Payment', '\\Zidisha\\Payment\\Payment', RelationMap::MANY_TO_ONE, array('payment_id' => 'id', ), null, null);
    } // buildRelations()

    /**
     *
     * Gets the list of behaviors registered for this table
     *
     * @return array Associative array (name => parameters) of behaviors
     */
    public function getBehaviors()
    {
        return array(
            'timestampable' => array('create_column' => 'created_at', 'update_column' => 'updated_at', 'disable_created_at' => 'false', 'disable_updated_at' => 'false', ),
        );
    } // getBehaviors()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return string The primary key hash of the row
     */
    public static function getPrimaryKeyHashFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        // If the PK cannot be derived from the row, return NULL.
        if ($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] === null) {
            return null;
        }

        return (string) $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        return (int) $row[
            $indexType == TableMap::TYPE_NUM
                ? 0 + $offset
                : self::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)
        ];
    }

    /**
     * The class that the tableMap will make instances of.
     *
     * If $withPrefix is true, the returned path
     * uses a dot-path notation which is translated into a path
     * relative to a location on the PHP include_path.
     * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
     *
     * @param boolean $withPrefix Whether or not to return the path with the class name
     * @return string path.to.ClassName
     */
    public static function getOMClass($withPrefix = true)
    {
        return $withPrefix ? StripeTransactionTableMap::CLASS_DEFAULT : StripeTransactionTableMap::OM_CLASS;
    }

    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param array  $row       row returned by DataFetcher->fetch().
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                 One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     * @return array           (StripeTransaction object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = StripeTransactionTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = StripeTransactionTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + StripeTransactionTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = StripeTransactionTableMap::OM_CLASS;
            /** @var StripeTransaction $obj */
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            StripeTransactionTableMap::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @param DataFetcherInterface $dataFetcher
     * @return array
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function populateObjects(DataFetcherInterface $dataFetcher)
    {
        $results = array();

        // set the class once to avoid overhead in the loop
        $cls = static::getOMClass(false);
        // populate the object(s)
        while ($row = $dataFetcher->fetch()) {
            $key = StripeTransactionTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = StripeTransactionTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                /** @var StripeTransaction $obj */
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                StripeTransactionTableMap::addInstanceToPool($obj, $key);
            } // if key exists
        }

        return $results;
    }
    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param Criteria $criteria object containing the columns to add.
     * @param string   $alias    optional table alias
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(StripeTransactionTableMap::COL_ID);
            $criteria->addSelectColumn(StripeTransactionTableMap::COL_STRIPE_ID);
            $criteria->addSelectColumn(StripeTransactionTableMap::COL_AMOUNT);
            $criteria->addSelectColumn(StripeTransactionTableMap::COL_DONATION_AMOUNT);
            $criteria->addSelectColumn(StripeTransactionTableMap::COL_TRANSACTION_FEE);
            $criteria->addSelectColumn(StripeTransactionTableMap::COL_TOTAL_AMOUNT);
            $criteria->addSelectColumn(StripeTransactionTableMap::COL_STATUS);
            $criteria->addSelectColumn(StripeTransactionTableMap::COL_PAYMENT_ID);
            $criteria->addSelectColumn(StripeTransactionTableMap::COL_CREATED_AT);
            $criteria->addSelectColumn(StripeTransactionTableMap::COL_UPDATED_AT);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.STRIPE_ID');
            $criteria->addSelectColumn($alias . '.AMOUNT');
            $criteria->addSelectColumn($alias . '.DONATION_AMOUNT');
            $criteria->addSelectColumn($alias . '.TRANSACTION_FEE');
            $criteria->addSelectColumn($alias . '.TOTAL_AMOUNT');
            $criteria->addSelectColumn($alias . '.STATUS');
            $criteria->addSelectColumn($alias . '.PAYMENT_ID');
            $criteria->addSelectColumn($alias . '.CREATED_AT');
            $criteria->addSelectColumn($alias . '.UPDATED_AT');
        }
    }

    /**
     * Returns the TableMap related to this object.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getServiceContainer()->getDatabaseMap(StripeTransactionTableMap::DATABASE_NAME)->getTable(StripeTransactionTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
        $dbMap = Propel::getServiceContainer()->getDatabaseMap(StripeTransactionTableMap::DATABASE_NAME);
        if (!$dbMap->hasTable(StripeTransactionTableMap::TABLE_NAME)) {
            $dbMap->addTableObject(new StripeTransactionTableMap());
        }
    }

    /**
     * Performs a DELETE on the database, given a StripeTransaction or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or StripeTransaction object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param  ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(StripeTransactionTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Zidisha\Payment\Stripe\StripeTransaction) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(StripeTransactionTableMap::DATABASE_NAME);
            $criteria->add(StripeTransactionTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = StripeTransactionQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) {
            StripeTransactionTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) {
                StripeTransactionTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the stripe_transactions table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return StripeTransactionQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a StripeTransaction or Criteria object.
     *
     * @param mixed               $criteria Criteria or StripeTransaction object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(StripeTransactionTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from StripeTransaction object
        }

        if ($criteria->containsKey(StripeTransactionTableMap::COL_ID) && $criteria->keyContainsValue(StripeTransactionTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.StripeTransactionTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = StripeTransactionQuery::create()->mergeWith($criteria);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        return $con->transaction(function () use ($con, $query) {
            return $query->doInsert($con);
        });
    }

} // StripeTransactionTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
StripeTransactionTableMap::buildTableMap();
