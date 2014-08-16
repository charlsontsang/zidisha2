<?php

use Illuminate\Database\ConnectionInterface;
use Propel\Runtime\Propel;
use Zidisha\User\Map\UserTableMap;

class TestCase extends Illuminate\Foundation\Testing\TestCase {

    /**
     * @var ConnectionInterface
     */
    protected $con;

    /**
     * Creates the application.
     *
     * @return \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication()
    {
        $unitTesting = true;

        $testEnvironment = 'testing';

        return require __DIR__.'/../../bootstrap/testing.php';
    }

    public function setup()
    {
        parent::setUp();

        \Config::set('mail.enabled', false);

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

    public static function setupBeforeClass()
    {
        @session_start();
        parent::setupBeforeClass();
    }
}
