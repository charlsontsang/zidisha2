<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1402465288.
 * Generated on 2014-06-11 05:41:28 by vagrant
 */
class PropelMigration_1402465288
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
CREATE TABLE borrowers
(
    id serial NOT NULL,
    user_id INTEGER NOT NULL,
    country_id INTEGER NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    PRIMARY KEY (id)
);
ALTER TABLE borrowers ADD CONSTRAINT borrowers_fk_69bd79
    FOREIGN KEY (user_id)
    REFERENCES users (id);

ALTER TABLE borrowers ADD CONSTRAINT borrowers_fk_b1f482
    FOREIGN KEY (country_id)
    REFERENCES countries (id);
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
DROP TABLE IF EXISTS borrowers CASCADE;
',
);
    }

}