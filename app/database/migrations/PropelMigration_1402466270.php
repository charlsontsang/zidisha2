<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1402466270.
 * Generated on 2014-06-11 05:57:50 by vagrant
 */
class PropelMigration_1402466270
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
ALTER TABLE lenders

  ADD created_at TIMESTAMP,

  ADD updated_at TIMESTAMP;
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
ALTER TABLE lenders

  DROP COLUMN created_at,

  DROP COLUMN updated_at;
',
);
    }

}