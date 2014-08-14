<?php

use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Propel;
use Zidisha\User\Map\UserTableMap;

class IntegrationTestCase extends TestCase {

    /**
     * @var ConnectionInterface
     */
    protected $con;

    public function setup()
    {
        parent::setUp();
        
        if (!$this->con) {
            $this->con = Propel::getWriteConnection(UserTableMap::DATABASE_NAME);
        }

        $this->con->beginTransaction();
    }

    public function tearDown()
    {
        parent::tearDown();
        
        $this->con->rollBack();
    }
    
}
