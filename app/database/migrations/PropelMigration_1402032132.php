<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1402032132.
 * Generated on 2014-06-06 05:22:12 by vagrant
 */
class PropelMigration_1402032132
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
CREATE TABLE lenders
(
    id serial NOT NULL,
    user_id INTEGER NOT NULL,
    country_id INTEGER NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    about_me TEXT,
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
DROP TABLE IF EXISTS lenders CASCADE;
',
);
    }

}