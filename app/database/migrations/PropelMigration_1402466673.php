<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1402466673.
 * Generated on 2014-06-11 06:04:33 by vagrant
 */
class PropelMigration_1402466673
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
CREATE TABLE borrower_profiles
(
    id serial NOT NULL,
    borrower_id INTEGER NOT NULL,
    about_me TEXT,
    about_business  TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    PRIMARY KEY (id)
);
ALTER TABLE borrower_profiles ADD CONSTRAINT borrower_profiles_fk_1d2ce7
    FOREIGN KEY (borrower_id)
    REFERENCES borrowers (id);
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
DROP TABLE IF EXISTS borrower_profiles CASCADE;
',
);
    }

}