<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1402481359.
 * Generated on 2014-06-11 10:09:19 by vagrant
 */
class PropelMigration_1402481359
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
ALTER TABLE borrower_profiles

  DROP CONSTRAINT borrower_profiles_pkey,

  DROP COLUMN id,

  ADD PRIMARY KEY (borrower_id);
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
ALTER TABLE borrower_profiles

  DROP CONSTRAINT borrower_profiles_pkey,

  ADD id serial NOT NULL,

  ADD PRIMARY KEY (id);
',
);
    }

}