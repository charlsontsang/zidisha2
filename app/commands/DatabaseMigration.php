<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DatabaseMigration extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'migrateDB';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Runs migration of old database to the new one.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        $table = $this->argument('table');

        if ($table == 'users') {
            $this->line('Migrate users');
        } else {
            $this->line('Migrate all tables');
        }
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('table', InputArgument::REQUIRED, 'Table name which you wants to migrate'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}
