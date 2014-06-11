<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1402471504.
 * Generated on 2014-06-11 07:25:04 by vagrant
 */
class PropelMigration_1402471504
{

    public function preUp($manager)
    {
        // add the pre-migration code here
    }

    public function postUp($manager)
    {
        // add the post-migration code here
    }

    public function preDown($manager)
    {
        // add the pre-migration code here
    }

    public function postDown($manager)
    {
        // add the post-migration code here
    }

    /**
     * Get the SQL statements for the Up migration
     *
     * @return array list of the SQL strings to execute for the Up migration
     *               the keys being the datasources
     */
    public function getUpSQL()
    {
        return array (
  'zidisha' => '
CREATE UNIQUE INDEX users_u_ce4c89 ON users (email);

CREATE UNIQUE INDEX users_u_f86ef3 ON users (username);
',
);
    }

    /**
     * Get the SQL statements for the Down migration
     *
     * @return array list of the SQL strings to execute for the Down migration
     *               the keys being the datasources
     */
    public function getDownSQL()
    {
        return array (
  'zidisha' => '
    ALTER TABLE users DROP CONSTRAINT users_u_ce4c89;
    
    ALTER TABLE users DROP CONSTRAINT users_u_f86ef3;
',
);
    }

}