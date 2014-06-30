<?php


namespace Zidisha\Vendor;


use Propel\Runtime\Propel;
use Zidisha\User\Map\UserTableMap;

class PropelDB {

    public static function transaction($closure, $retry = 3) {
        $con = Propel::getWriteConnection(UserTableMap::DATABASE_NAME);
        $con->beginTransaction();
        
        $result = null;
        for ($i = 1; $i <= $retry; $i++) {
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
    
}
