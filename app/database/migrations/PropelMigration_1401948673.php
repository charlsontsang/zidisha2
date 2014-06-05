<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1401948673.
 * Generated on 2014-06-05 06:11:13 by vagrant
 */
class PropelMigration_1401948673
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
CREATE TABLE loan_categories
(
    id serial NOT NULL,
    name VARCHAR(100) NOT NULL,
    what_description TEXT,
    why_description TEXT,
    how_description TEXT,
    admin_only BOOLEAN DEFAULT \'f\',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    sortable_rank INTEGER,
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
DROP TABLE IF EXISTS loan_categories CASCADE;
',
);
    }

}