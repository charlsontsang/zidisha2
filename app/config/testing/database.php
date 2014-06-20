<?php

return array(
    'default' => 'pgsql',

    'connections' => array(

        'pgsql' => array(
            'driver'   => 'pgsql',
            'host'     => 'localhost',
            'database' => 'homestead',
            'username' => 'homestead',
            'password' => 'secret',
            'charset'  => 'utf8',
            'prefix'   => '',
            'schema'   => 'public',
        ),

    ),

);
