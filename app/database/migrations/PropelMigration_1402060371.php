<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1402060371.
 * Generated on 2014-06-06 13:12:51 by vagrant
 */
class PropelMigration_1402060371
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
ALTER TABLE users

  ALTER COLUMN password TYPE VARCHAR(60) USING NULL,

  ALTER COLUMN password DROP NOT NULL,

  ADD facebook_id INT8
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
ALTER TABLE users

  ALTER COLUMN password TYPE VARCHAR(60) USING NULL,

  ALTER COLUMN password SET NOT NULL,

  ADD remember_token VARCHAR(100),

  DROP COLUMN facebook_id
',
);
    }

}