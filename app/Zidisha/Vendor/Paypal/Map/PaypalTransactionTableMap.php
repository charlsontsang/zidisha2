<?php

namespace Zidisha\Vendor\Paypal\Map;

use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\InstancePoolTrait;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\DataFetcher\DataFetcherInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Map\TableMapTrait;
use Zidisha\Vendor\Paypal\PaypalTransaction;
use Zidisha\Vendor\Paypal\PaypalTransactionQuery;


/**
 * This class defines the structure of the 'paypal_transactions' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class PaypalTransactionTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Zidisha.Vendor.Paypal.Map.PaypalTransactionTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'zidisha';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'paypal_transactions';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Zidisha\\Vendor\\Paypal\\PaypalTransaction';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Zidisha.Vendor.Paypal.PaypalTransaction';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 11;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 11;

    /**
     * the column name for the ID field
     */
    const COL_ID = 'paypal_transactions.ID';

    /**
     * the column name for the TRANSACTION_ID field
     */
    const COL_TRANSACTION_ID = 'paypal_transactions.TRANSACTION_ID';

    /**
     * the column name for the TRANSACTION_TYPE field
     */
    const COL_TRANSACTION_TYPE = 'paypal_transactions.TRANSACTION_TYPE';

    /**
     * the column name for the AMOUNT field
     */
    const COL_AMOUNT = 'paypal_transactions.AMOUNT';

    /**
     * the column name for the DONATION_AMOUNT field
     */
    const COL_DONATION_AMOUNT = 'paypal_transactions.DONATION_AMOUNT';

    /**
     * the column name for the PAYPAL_TRANSACTION_FEE field
     */
    const COL_PAYPAL_TRANSACTION_FEE = 'paypal_transactions.PAYPAL_TRANSACTION_FEE';

    /**
     * the column name for the TOTAL_AMOUNT field
     */
    const COL_TOTAL_AMOUNT = 'paypal_transactions.TOTAL_AMOUNT';

    /**
     * the column name for the STATUS field
     */
    const COL_STATUS = 'paypal_transactions.STATUS';

    /**
     * the column name for the CUSTOM field
     */
    const COL_CUSTOM = 'paypal_transactions.CUSTOM';

    /**
     * the column name for the CREATED_AT field
     */
    const COL_CREATED_AT = 'paypal_transactions.CREATED_AT';

    /**
     * the column name for the UPDATED_AT field
     */
    const COL_UPDATED_AT = 'paypal_transactions.UPDATED_AT';

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
        self::TYPE_PHPNAME       => array('Id', 'TransactionId', 'TransactionType', 'Amount', 'DonationAmount', 'PaypalTransactionFee', 'TotalAmount', 'Status', 'Custom', 'CreatedAt', 'UpdatedAt', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'transactionId', 'transactionType', 'amount', 'donationAmount', 'paypalTransactionFee', 'totalAmount', 'status', 'custom', 'createdAt', 'updatedAt', ),
        self::TYPE_COLNAME       => array(PaypalTransactionTableMap::COL_ID, PaypalTransactionTableMap::COL_TRANSACTION_ID, PaypalTransactionTableMap::COL_TRANSACTION_TYPE, PaypalTransactionTableMap::COL_AMOUNT, PaypalTransactionTableMap::COL_DONATION_AMOUNT, PaypalTransactionTableMap::COL_PAYPAL_TRANSACTION_FEE, PaypalTransactionTableMap::COL_TOTAL_AMOUNT, PaypalTransactionTableMap::COL_STATUS, PaypalTransactionTableMap::COL_CUSTOM, PaypalTransactionTableMap::COL_CREATED_AT, PaypalTransactionTableMap::COL_UPDATED_AT, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID', 'COL_TRANSACTION_ID', 'COL_TRANSACTION_TYPE', 'COL_AMOUNT', 'COL_DONATION_AMOUNT', 'COL_PAYPAL_TRANSACTION_FEE', 'COL_TOTAL_AMOUNT', 'COL_STATUS', 'COL_CUSTOM', 'COL_CREATED_AT', 'COL_UPDATED_AT', ),
        self::TYPE_FIELDNAME     => array('id', 'transaction_id', 'transaction_type', 'amount', 'donation_amount', 'paypal_transaction_fee', 'total_amount', 'status', 'custom', 'created_at', 'updated_at', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'TransactionId' => 1, 'TransactionType' => 2, 'Amount' => 3, 'DonationAmount' => 4, 'PaypalTransactionFee' => 5, 'TotalAmount' => 6, 'Status' => 7, 'Custom' => 8, 'CreatedAt' => 9, 'UpdatedAt' => 10, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'transactionId' => 1, 'transactionType' => 2, 'amount' => 3, 'donationAmount' => 4, 'paypalTransactionFee' => 5, 'totalAmount' => 6, 'status' => 7, 'custom' => 8, 'createdAt' => 9, 'updatedAt' => 10, ),
        self::TYPE_COLNAME       => array(PaypalTransactionTableMap::COL_ID => 0, PaypalTransactionTableMap::COL_TRANSACTION_ID => 1, PaypalTransactionTableMap::COL_TRANSACTION_TYPE => 2, PaypalTransactionTableMap::COL_AMOUNT => 3, PaypalTransactionTableMap::COL_DONATION_AMOUNT => 4, PaypalTransactionTableMap::COL_PAYPAL_TRANSACTION_FEE => 5, PaypalTransactionTableMap::COL_TOTAL_AMOUNT => 6, PaypalTransactionTableMap::COL_STATUS => 7, PaypalTransactionTableMap::COL_CUSTOM => 8, PaypalTransactionTableMap::COL_CREATED_AT => 9, PaypalTransactionTableMap::COL_UPDATED_AT => 10, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID' => 0, 'COL_TRANSACTION_ID' => 1, 'COL_TRANSACTION_TYPE' => 2, 'COL_AMOUNT' => 3, 'COL_DONATION_AMOUNT' => 4, 'COL_PAYPAL_TRANSACTION_FEE' => 5, 'COL_TOTAL_AMOUNT' => 6, 'COL_STATUS' => 7, 'COL_CUSTOM' => 8, 'COL_CREATED_AT' => 9, 'COL_UPDATED_AT' => 10, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'transaction_id' => 1, 'transaction_type' => 2, 'amount' => 3, 'donation_amount' => 4, 'paypal_transaction_fee' => 5, 'total_amount' => 6, 'status' => 7, 'custom' => 8, 'created_at' => 9, 'updated_at' => 10, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, )
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
        $this->setName('paypal_transactions');
        $this->setPhpName('PaypalTransaction');
        $this->setClassName('\\Zidisha\\Vendor\\Paypal\\PaypalTransaction');
        $this->setPackage('Zidisha.Vendor.Paypal');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('paypal_transactions_id_seq');
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('TRANSACTION_ID', 'TransactionId', 'VARCHAR', false, 255, '0');
        $this->addColumn('TRANSACTION_TYPE', 'TransactionType', 'VARCHAR', false, 255, null);
        $this->addColumn('AMOUNT', 'Amount', 'DECIMAL', true, 10, null);
        $this->addColumn('DONATION_AMOUNT', 'DonationAmount', 'DECIMAL', true, 10, null);
        $this->addColumn('PAYPAL_TRANSACTION_FEE', 'PaypalTransactionFee', 'DECIMAL', true, 10, null);
        $this->addColumn('TOTAL_AMOUNT', 'TotalAmount', 'DECIMAL', true, 10, null);
        $this->addColumn('STATUS', 'Status', 'VARCHAR', true, 100, null);
        $this->addColumn('CUSTOM', 'Custom', 'VARCHAR', true, 255, null);
        $this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
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
        return $withPrefix ? PaypalTransactionTableMap::CLASS_DEFAULT : PaypalTransactionTableMap::OM_CLASS;
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
     * @return array           (PaypalTransaction object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = PaypalTransactionTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = PaypalTransactionTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + PaypalTransactionTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = PaypalTransactionTableMap::OM_CLASS;
            /** @var PaypalTransaction $obj */
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            PaypalTransactionTableMap::addInstanceToPool($obj, $key);
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
            $key = PaypalTransactionTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = PaypalTransactionTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                /** @var PaypalTransaction $obj */
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                PaypalTransactionTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(PaypalTransactionTableMap::COL_ID);
            $criteria->addSelectColumn(PaypalTransactionTableMap::COL_TRANSACTION_ID);
            $criteria->addSelectColumn(PaypalTransactionTableMap::COL_TRANSACTION_TYPE);
            $criteria->addSelectColumn(PaypalTransactionTableMap::COL_AMOUNT);
            $criteria->addSelectColumn(PaypalTransactionTableMap::COL_DONATION_AMOUNT);
            $criteria->addSelectColumn(PaypalTransactionTableMap::COL_PAYPAL_TRANSACTION_FEE);
            $criteria->addSelectColumn(PaypalTransactionTableMap::COL_TOTAL_AMOUNT);
            $criteria->addSelectColumn(PaypalTransactionTableMap::COL_STATUS);
            $criteria->addSelectColumn(PaypalTransactionTableMap::COL_CUSTOM);
            $criteria->addSelectColumn(PaypalTransactionTableMap::COL_CREATED_AT);
            $criteria->addSelectColumn(PaypalTransactionTableMap::COL_UPDATED_AT);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.TRANSACTION_ID');
            $criteria->addSelectColumn($alias . '.TRANSACTION_TYPE');
            $criteria->addSelectColumn($alias . '.AMOUNT');
            $criteria->addSelectColumn($alias . '.DONATION_AMOUNT');
            $criteria->addSelectColumn($alias . '.PAYPAL_TRANSACTION_FEE');
            $criteria->addSelectColumn($alias . '.TOTAL_AMOUNT');
            $criteria->addSelectColumn($alias . '.STATUS');
            $criteria->addSelectColumn($alias . '.CUSTOM');
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
        return Propel::getServiceContainer()->getDatabaseMap(PaypalTransactionTableMap::DATABASE_NAME)->getTable(PaypalTransactionTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
        $dbMap = Propel::getServiceContainer()->getDatabaseMap(PaypalTransactionTableMap::DATABASE_NAME);
        if (!$dbMap->hasTable(PaypalTransactionTableMap::TABLE_NAME)) {
            $dbMap->addTableObject(new PaypalTransactionTableMap());
        }
    }

    /**
     * Performs a DELETE on the database, given a PaypalTransaction or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or PaypalTransaction object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(PaypalTransactionTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Zidisha\Vendor\Paypal\PaypalTransaction) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(PaypalTransactionTableMap::DATABASE_NAME);
            $criteria->add(PaypalTransactionTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = PaypalTransactionQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) {
            PaypalTransactionTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) {
                PaypalTransactionTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the paypal_transactions table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return PaypalTransactionQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a PaypalTransaction or Criteria object.
     *
     * @param mixed               $criteria Criteria or PaypalTransaction object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(PaypalTransactionTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from PaypalTransaction object
        }

        if ($criteria->containsKey(PaypalTransactionTableMap::COL_ID) && $criteria->keyContainsValue(PaypalTransactionTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.PaypalTransactionTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = PaypalTransactionQuery::create()->mergeWith($criteria);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        return $con->transaction(function () use ($con, $query) {
            return $query->doInsert($con);
        });
    }

} // PaypalTransactionTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
PaypalTransactionTableMap::buildTableMap();
