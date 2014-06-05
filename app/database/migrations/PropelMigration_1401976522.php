<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1401976522.
 * Generated on 2014-06-05 13:55:22 by vagrant
 */
class PropelMigration_1401976522
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
CREATE TABLE password_reminders
(
    email VARCHAR(100) NOT NULL,
    token VARCHAR(100) NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE INDEX email ON password_reminders (email);

CREATE INDEX token ON password_reminders (token);
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
DROP TABLE IF EXISTS password_reminders CASCADE;
',
);
    }

}