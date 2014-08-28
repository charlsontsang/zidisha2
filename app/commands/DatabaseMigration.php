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
                    ->where($offset)->take($limit)->get();

                $insertArray = [];

                foreach ($users as $user) {
                    $newUser = [
                        'id' => $user['users.userid'],
                        'username' => $user['users.username'],
                        'email' => $user['lenders.Email'],
                        'password' => $user['users.password'],
                        'profile_picture_id' => 'TODO',
                        'facebook_id' => $user['users.facebook_id'],
                        'google_id' => null, // since google login is now added
                        'google_picture' => null,
                        'remember_token' =>  'TODO',
                        'role' => null, //TODO , once i know how it's in old db
                        'sub_role' => null, //TODO , once i know how it's in old db
                        'joined_at' => $user['users.regdate'],
                        'last_login_at' => $user['users.last_login']
                    ];

                        //TODO
//                      `salt`              VARCHAR(100)         DEFAULT NULL,
//                      `userlevel`         INT(1)      NOT NULL DEFAULT '0',
//                      `tnc`               TINYINT(1)  NOT NULL DEFAULT '0',
//                      `lang`              VARCHAR(10)          DEFAULT 'en',
//                      `emailVerified`     INT(1)      NOT NULL DEFAULT '0',
//                      `accountExpiedMail` TINYINT(4)  NOT NULL DEFAULT '0',
//                      `sublevel`          INT(11)              DEFAULT NULL,
//                      `fb_post`           VARCHAR(100)         DEFAULT NULL,

                    array_push($insertArray, $newUser);
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
