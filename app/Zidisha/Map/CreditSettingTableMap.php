<?php

namespace Zidisha\Map;

use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\InstancePoolTrait;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\DataFetcher\DataFetcherInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Map\TableMapTrait;
use Zidisha\CreditSetting;
use Zidisha\CreditSettingQuery;


/**
 * This class defines the structure of the 'credit_settings' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class CreditSettingTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Zidisha.Map.CreditSettingTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'zidisha';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'credit_settings';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Zidisha\\CreditSetting';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Zidisha.CreditSetting';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 8;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 8;

    /**
     * the column name for the ID field
     */
    const COL_ID = 'credit_settings.ID';

    /**
     * the column name for the COUNTRY_CODE field
     */
    const COL_COUNTRY_CODE = 'credit_settings.COUNTRY_CODE';

    /**
     * the column name for the LOAN_AMOUNT_LIMIT field
     */
    const COL_LOAN_AMOUNT_LIMIT = 'credit_settings.LOAN_AMOUNT_LIMIT';

    /**
     * the column name for the CHARACTER_LIMIT field
     */
    const COL_CHARACTER_LIMIT = 'credit_settings.CHARACTER_LIMIT';

    /**
     * the column name for the COMMENTS_LIMIT field
     */
    const COL_COMMENTS_LIMIT = 'credit_settings.COMMENTS_LIMIT';

    /**
     * the column name for the TYPE field
     */
    const COL_TYPE = 'credit_settings.TYPE';

    /**
     * the column name for the CREATED_AT field
     */
    const COL_CREATED_AT = 'credit_settings.CREATED_AT';

    /**
     * the column name for the UPDATED_AT field
     */
    const COL_UPDATED_AT = 'credit_settings.UPDATED_AT';

    /**
     * The default string format for model objects of the related table
     */
    const DEFAULT_STRING_FORMAT = 'YAML';

    /** The enumerated values for the TYPE field */
    const COL_TYPE_1 = '1';
    const COL_TYPE_2 = '2';
    const COL_TYPE_3 = '3';

    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        self::TYPE_PHPNAME       => array('Id', 'CountryCode', 'LoanAmountLimit', 'CharacterLimit', 'CommentsLimit', 'Type', 'CreatedAt', 'UpdatedAt', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'countryCode', 'loanAmountLimit', 'characterLimit', 'commentsLimit', 'type', 'createdAt', 'updatedAt', ),
        self::TYPE_COLNAME       => array(CreditSettingTableMap::COL_ID, CreditSettingTableMap::COL_COUNTRY_CODE, CreditSettingTableMap::COL_LOAN_AMOUNT_LIMIT, CreditSettingTableMap::COL_CHARACTER_LIMIT, CreditSettingTableMap::COL_COMMENTS_LIMIT, CreditSettingTableMap::COL_TYPE, CreditSettingTableMap::COL_CREATED_AT, CreditSettingTableMap::COL_UPDATED_AT, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID', 'COL_COUNTRY_CODE', 'COL_LOAN_AMOUNT_LIMIT', 'COL_CHARACTER_LIMIT', 'COL_COMMENTS_LIMIT', 'COL_TYPE', 'COL_CREATED_AT', 'COL_UPDATED_AT', ),
        self::TYPE_FIELDNAME     => array('id', 'country_code', 'loan_amount_limit', 'character_limit', 'comments_limit', 'type', 'created_at', 'updated_at', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'CountryCode' => 1, 'LoanAmountLimit' => 2, 'CharacterLimit' => 3, 'CommentsLimit' => 4, 'Type' => 5, 'CreatedAt' => 6, 'UpdatedAt' => 7, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'countryCode' => 1, 'loanAmountLimit' => 2, 'characterLimit' => 3, 'commentsLimit' => 4, 'type' => 5, 'createdAt' => 6, 'updatedAt' => 7, ),
        self::TYPE_COLNAME       => array(CreditSettingTableMap::COL_ID => 0, CreditSettingTableMap::COL_COUNTRY_CODE => 1, CreditSettingTableMap::COL_LOAN_AMOUNT_LIMIT => 2, CreditSettingTableMap::COL_CHARACTER_LIMIT => 3, CreditSettingTableMap::COL_COMMENTS_LIMIT => 4, CreditSettingTableMap::COL_TYPE => 5, CreditSettingTableMap::COL_CREATED_AT => 6, CreditSettingTableMap::COL_UPDATED_AT => 7, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID' => 0, 'COL_COUNTRY_CODE' => 1, 'COL_LOAN_AMOUNT_LIMIT' => 2, 'COL_CHARACTER_LIMIT' => 3, 'COL_COMMENTS_LIMIT' => 4, 'COL_TYPE' => 5, 'COL_CREATED_AT' => 6, 'COL_UPDATED_AT' => 7, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'country_code' => 1, 'loan_amount_limit' => 2, 'character_limit' => 3, 'comments_limit' => 4, 'type' => 5, 'created_at' => 6, 'updated_at' => 7, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, )
    );

    /** The enumerated values for this table */
    protected static $enumValueSets = array(
                CreditSettingTableMap::COL_TYPE => array(
                            self::COL_TYPE_1,
            self::COL_TYPE_2,
            self::COL_TYPE_3,
        ),
    );

    /**
     * Gets the list of values for all ENUM columns
     * @return array
     */
    public static function getValueSets()
    {
      return static::$enumValueSets;
    }

    /**
     * Gets the list of values for an ENUM column
     * @param string $colname
     * @return array list of possible values for the column
     */
    public static function getValueSet($colname)
    {
        $valueSets = self::getValueSets();

        return $valueSets[$colname];
    }

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
        $this->setName('credit_settings');
        $this->setPhpName('CreditSetting');
        $this->setClassName('\\Zidisha\\CreditSetting');
        $this->setPackage('Zidisha');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('credit_settings_id_seq');
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('COUNTRY_CODE', 'CountryCode', 'VARCHAR', 'countries', 'COUNTRY_CODE', true, 2, null);
        $this->addColumn('LOAN_AMOUNT_LIMIT', 'LoanAmountLimit', 'INTEGER', true, null, null);
        $this->addColumn('CHARACTER_LIMIT', 'CharacterLimit', 'INTEGER', true, null, null);
        $this->addColumn('COMMENTS_LIMIT', 'CommentsLimit', 'INTEGER', true, null, null);
        $this->addColumn('TYPE', 'Type', 'ENUM', true, null, null);
        $this->getColumn('TYPE', false)->setValueSet(array (
  0 => '1',
  1 => '2',
  2 => '3',
));
        $this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Country', '\\Zidisha\\Country\\Country', RelationMap::MANY_TO_ONE, array('country_code' => 'country_code', ), null, null);
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
        return $withPrefix ? CreditSettingTableMap::CLASS_DEFAULT : CreditSettingTableMap::OM_CLASS;
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
     * @return array           (CreditSetting object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = CreditSettingTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = CreditSettingTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + CreditSettingTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = CreditSettingTableMap::OM_CLASS;
            /** @var CreditSetting $obj */
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            CreditSettingTableMap::addInstanceToPool($obj, $key);
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
            $key = CreditSettingTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = CreditSettingTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                /** @var CreditSetting $obj */
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                CreditSettingTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(CreditSettingTableMap::COL_ID);
            $criteria->addSelectColumn(CreditSettingTableMap::COL_COUNTRY_CODE);
            $criteria->addSelectColumn(CreditSettingTableMap::COL_LOAN_AMOUNT_LIMIT);
            $criteria->addSelectColumn(CreditSettingTableMap::COL_CHARACTER_LIMIT);
            $criteria->addSelectColumn(CreditSettingTableMap::COL_COMMENTS_LIMIT);
            $criteria->addSelectColumn(CreditSettingTableMap::COL_TYPE);
            $criteria->addSelectColumn(CreditSettingTableMap::COL_CREATED_AT);
            $criteria->addSelectColumn(CreditSettingTableMap::COL_UPDATED_AT);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.COUNTRY_CODE');
            $criteria->addSelectColumn($alias . '.LOAN_AMOUNT_LIMIT');
            $criteria->addSelectColumn($alias . '.CHARACTER_LIMIT');
            $criteria->addSelectColumn($alias . '.COMMENTS_LIMIT');
            $criteria->addSelectColumn($alias . '.TYPE');
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
        return Propel::getServiceContainer()->getDatabaseMap(CreditSettingTableMap::DATABASE_NAME)->getTable(CreditSettingTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
        $dbMap = Propel::getServiceContainer()->getDatabaseMap(CreditSettingTableMap::DATABASE_NAME);
        if (!$dbMap->hasTable(CreditSettingTableMap::TABLE_NAME)) {
            $dbMap->addTableObject(new CreditSettingTableMap());
        }
    }

    /**
     * Performs a DELETE on the database, given a CreditSetting or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or CreditSetting object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(CreditSettingTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Zidisha\CreditSetting) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(CreditSettingTableMap::DATABASE_NAME);
            $criteria->add(CreditSettingTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = CreditSettingQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) {
            CreditSettingTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) {
                CreditSettingTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the credit_settings table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return CreditSettingQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a CreditSetting or Criteria object.
     *
     * @param mixed               $criteria Criteria or CreditSetting object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CreditSettingTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from CreditSetting object
        }

        if ($criteria->containsKey(CreditSettingTableMap::COL_ID) && $criteria->keyContainsValue(CreditSettingTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.CreditSettingTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = CreditSettingQuery::create()->mergeWith($criteria);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        return $con->transaction(function () use ($con, $query) {
            return $query->doInsert($con);
        });
    }

} // CreditSettingTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
CreditSettingTableMap::buildTableMap();
