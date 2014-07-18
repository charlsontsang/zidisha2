<?php


namespace Zidisha\Vendor;


use PDO;
use Propel\Runtime\Propel;
use Zidisha\User\Map\UserTableMap;

class PropelDB {

    public static function transaction($closure, $retry = 3) {
        $con = Propel::getWriteConnection(UserTableMap::DATABASE_NAME);
        
        $result = null;
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

}
