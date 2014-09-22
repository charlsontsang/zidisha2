<?php


namespace Zidisha\Vendor;


use PDO;
use Propel\Runtime\Propel;
use Zidisha\User\Map\UserTableMap;

class PropelDB {

    public static function transaction($closure, $retry = 3) {
        $con = Propel::getWriteConnection(UserTableMap::DATABASE_NAME);
        
        $result = null;
        // tmp disable, problem with $model->alreadyInSave
        $retry = 1;
        for ($i = 1; $i <= $retry; $i++) {
            $con->beginTransaction();
            try {
                $result = $closure($con);
                $con->commit();
                break;
            } catch (\Exception $e) {
                $con->rollBack();
                if ($i < $retry) {
                    usleep($i * 100);
                } else {
                    throw $e;
                }
            }
        }
        
        return $result;
    }
    
    public static function fetchNumber($sql, $parameters = [])
    {
        $con = Propel::getWriteConnection(UserTableMap::DATABASE_NAME);
        $stmt = $con->prepare($sql);
        $stmt->execute($parameters);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        
        return $row[0];
    }

    public static function fetchAll($sql, $parameters = [])
    {
        $con = Propel::getWriteConnection(UserTableMap::DATABASE_NAME);
        $stmt = $con->prepare($sql);
        $stmt->execute($parameters);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    public static function fetchColumn($sql, $parameters = [])
    {
        $con = Propel::getWriteConnection(UserTableMap::DATABASE_NAME);
        $stmt = $con->prepare($sql);
        $stmt->execute($parameters);
        $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return $rows;
    }

    public static function fetchOne($sql, $parameters = [])
    {
        $con = Propel::getWriteConnection(UserTableMap::DATABASE_NAME);
        $stmt = $con->prepare($sql);
        $stmt->execute($parameters);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row;
    }

    public static function getConnection()
    {
        return Propel::getWriteConnection(UserTableMap::DATABASE_NAME);
    }

}
