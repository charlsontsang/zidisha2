<?php

use Illuminate\Console\Command;

class ApplicationSetup extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Used to setup environment and database variables.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $databaseConfig = array();

        $environment = $this->ask('What is your current environment? [Default=local] : ', 'local');

        $databaseConfig['databaseHost'] = $this->ask('Enter your database host [Default=localhost] : ', 'localhost');

        $databaseConfig ['databaseName'] = $this->ask('Enter your database name [Default=homestead] : ', 'homestead');

        $databaseConfig['databaseUsername'] = $this->ask(
            'Enter your database username [Default=homestead] : ',
            'homestead'
        );

        $databaseConfig ['databasePassword'] = $this->secret('Enter your database password [Default=secret] : ') ?: 'secret';

        $databaseConfig['databasePortNumber'] = $this->ask('Enter your database port number [Default=3306] : ', '3306');

        $file = new \Illuminate\Filesystem\Filesystem();
        $contents = <<<ENV
<?php
define("LARAVEL_ENV", '$environment');
ENV;

        $file->put(base_path() . '/bootstrap/env.php', $contents);
        
        $databaseConfig['environment'] = $environment;

        $config = View::make('command.runtime-conf', $databaseConfig);

        if (!$file->isDirectory(base_path() . '/app/config/propel/')) {
            $file->makeDirectory(base_path() . '/app/config/propel');
        }

        $file->put(base_path() . '/app/config/propel/runtime-conf.xml', $config);
        $file->put(base_path() . '/app/config/propel/buildtime-conf.xml', $config);

        exec('vendor/bin/propel config:convert-xml --output-dir="app/config/propel" --input-dir="app/config/propel"');


        $dbContent = <<<ENV
<?php

return array(
    'default' => 'mysql',

    'connections' => array(

        'mysql' => array(
            'driver'   => 'mysql',
            'host'     => '{$databaseConfig['databaseHost']}',
            'database' => '{$databaseConfig ['databaseName']}',
            'username' => '{$databaseConfig['databaseUsername']}',
            'password' => '{$databaseConfig ['databasePassword']}',
            'charset'  => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'   => '',
        ),

    ),

);

ENV;

        if (!$file->isDirectory(base_path() . '/app/config/' . $environment)) {
            $file->makeDirectory(base_path() . '/app/config/' . $environment);
        }

        $file->put(base_path() . '/app/config/' . $environment .'/database.php', $dbContent);

        $this->info('You are done.');
    }

}
