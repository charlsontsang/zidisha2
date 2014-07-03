<?php

namespace Zidisha\Vendor;


use Propel\Runtime\Connection\ConnectionInterface;

class TraceablePDO extends \DebugBar\DataCollector\PDO\TraceablePDO implements ConnectionInterface {

    /**
     * @param string $name The datasource name associated to this connection
     */
    public function setName($name)
    {
        $this->pdo->setName($name);
    }

    /**
     * @return string The datasource name associated to this connection
     */
    public function getName()
    {
        return $this->pdo->getName();
    }

    /**
     * @param $data
     *
     * @return \Propel\Runtime\DataFetcher\DataFetcherInterface
     */
    public function getSingleDataFetcher($data)
    {
        return $this->pdo->getSingleDataFetcher($data);
    }

    /**
     * @param $data
     *
     * @return \Propel\Runtime\DataFetcher\DataFetcherInterface
     */
    public function getDataFetcher($data)
    {
        return $this->pdo->getDataFetcher($data);
    }

    /**
     * Executes the given callable within a transaction.
     * This helper method takes care to commit or rollback the transaction.
     *
     * In case you want the transaction to rollback just throw an Exception of any type.
     *
     * @param callable $callable A callable to be wrapped in a transaction
     *
     * @return mixed Returns the result of the callable.
     *
     * @throws \Exception Re-throws a possible <code>Exception</code> triggered by the callable.
     */
    public function transaction(callable $callable)
    {
        return $this->pdo->transaction($callable);
    }
}
