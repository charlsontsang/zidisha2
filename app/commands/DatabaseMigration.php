<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Zidisha\Lender\Lender;
use Zidisha\User\User;

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

    protected $con;

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
//        $this->con = DB::connection('zidisha1');

        if ($table == 'all') {
            $this->line('Migrate all tables');

            $this->call('migrateDB', array('table' => 'users'));
            $this->call('migrateDB', array('table' => 'lenders'));
        }

        if ($table == 'users') {
            $this->line('Migrate users table');

            $count = $this->con->table('users')->count();
            $offset = 0;
            $limit = 500;
            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $users = $this->con->table('users')
                    ->join('lenders', 'users.userid', '=', 'lenders.userid')
                    ->join('borrowers', 'users.userid', '=', 'borrowers.userid')
                    ->where($offset)->take($limit)->get();

                $insertArray = [];

                foreach ($users as $user) {
                    $newUser = [
                        'id'                 => $user['users.userid'],
                        'username'           => $user['users.username'],
                        'email'              => $user['lenders.Email'] ? $user['lenders.Email'] : $user['borrowers.Email'],
                        'password'           => $user['users.password'],
                        'profile_picture_id' => 'TODO',
                        'facebook_id'        => $user['users.facebook_id'],
                        'google_id'          => null, // since google login is now added
                        'google_picture'     => null,
                        'remember_token'     => 'TODO',
                        'role'               => null, //TODO , once i know how it's in old db
                        'sub_role'           => null, //TODO , once i know how it's in old db
                        'joined_at'          => $user['users.regdate'],
                        'last_login_at'      => $user['users.last_login'],
                        'active'             => $user['lenders.Active'] ? $user['lenders.Active'] : $user['borrowers.Active']
                    ];

                    array_push($insertArray, $newUser);
                }
                DB::table('users')->insert($insertArray);
            }
        }

        if ($table == 'lenders') {
            $this->line('Migrate lenders table');

            $count = $this->con->table('lenders')->count();
            $offset = 0;
            $limit = 500;
            for ($offset; $offset < $count; $offset = ($offset + $limit)) {

                $lenders = $this->con->table('lenders')
                    ->join('users', 'lenders.userid', '=', 'users.userid')
                    ->join('countries', 'lenders.Country', '=', 'countries.code')
                    ->where($offset)->take($limit)->get();
                $insertArray = [];

                foreach ($lenders as $lender) {
                    $newLender = [
                        'id'                  => $lender['users.userid'],
                        'country_id'          => $lender['countries.id'],
                        'first_name'          => $lender['lenders.FirstName'],
                        'last_name'           => $lender['lenders.LastName'],
                        'admin_donate'        => $lender['lenders.admin_donate'], //TODO check that only boolean values are there in old data
                        'active'              => $lender['lenders.Active'],
                        'last_check_in_email' => $lender['lenders.last_check_in_email']
                    ];

                    array_push($insertArray, $newLender);
                }
                DB::table('users')->insert($insertArray);
            }
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
