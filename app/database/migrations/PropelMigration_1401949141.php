<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1401949141.
 * Generated on 2014-06-05 06:19:01 by vagrant
 */
class PropelMigration_1401949141
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
CREATE TABLE countries
(
    id serial NOT NULL,
    name VARCHAR(100),
    continent_code VARCHAR(2),
    country_code VARCHAR(2),
    dialing_code VARCHAR(4),
    enabled BOOLEAN,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    PRIMARY KEY (id)
);

CREATE INDEX name ON countries (name);

CREATE INDEX code ON countries (country_code);
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
DROP TABLE IF EXISTS countries CASCADE;
',
);
    }

}