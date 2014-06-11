<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1402477703.
 * Generated on 2014-06-11 09:08:23 by vagrant
 */
class PropelMigration_1402477703
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
CREATE TABLE lender_profiles
(
    lender_id INTEGER NOT NULL,
    about_me TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    PRIMARY KEY (lender_id)
);
ALTER TABLE lender_profiles ADD CONSTRAINT lender_profiles_fk_7e0a6f
    FOREIGN KEY (lender_id)
    REFERENCES lenders (id);
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
DROP TABLE IF EXISTS lender_profiles CASCADE;
',
);
    }

}