<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1401976911.
 * Generated on 2014-06-05 14:01:51 by vagrant
 */
class PropelMigration_1401976911
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
CREATE TABLE loan
(
    id serial NOT NULL,
    summary TEXT,
    description TEXT,
    amount DECIMAL(10,2),
    interest_rate DECIMAL(5,2),
    loan_category_id INTEGER NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    PRIMARY KEY (id)
);
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
DROP TABLE IF EXISTS loan CASCADE;
',
);
    }

}