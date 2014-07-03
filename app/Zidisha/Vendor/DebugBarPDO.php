<?php

namespace Zidisha\Vendor;


use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Connection\ConnectionWrapper;

class DebugBarPDO extends ConnectionWrapper {

    /**
     * Creates a Connection instance.
     *
     * @param \Propel\Runtime\Connection\ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        $connection = new TraceablePDO($connection);
        parent::__construct($connection);
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
