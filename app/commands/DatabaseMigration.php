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
            $this->call('migrateDB', array('table' => 'borrowers'));
            $this->call('migrateDB', array('table' => 'loan_categories'));
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

                $userArray = [];

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

                    array_push($userArray, $newUser);
                }
                DB::table('users')->insert($userArray);
            }
        }

        if ($table == 'lenders') {
            $this->line('Migrate lenders table');
            $this->line('Migrate lender_profiles table');
            $this->line('Migrate lender_preferences table');

            $count = $this->con->table('lenders')->count();
            $offset = 0;
            $limit = 500;
            for ($offset; $offset < $count; $offset = ($offset + $limit)) {

                $lenders = $this->con->table('lenders')
                    ->join('users', 'lenders.userid', '=', 'users.userid')
                    ->join('countries', 'lenders.Country', '=', 'countries.code')
                    ->where($offset)->take($limit)->get();
                $lenderArray = [];
                $profileArray = [];
                $preferenceArray = [];

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

                    $profile = [
                        'lender_id' => $lender['users.userid'],
                        'city'      => $lender['lenders.City'],
                        'about_me'  => $lender['lenders.About']
                    ];
                    $preference = [
                        'lender_id' => $lender['users.userid'],
                    ];

                    array_push($lenderArray, $newLender);
                    array_push($profileArray, $profile);
                    array_push($preferenceArray, $preference);
                }
                DB::table('lenders')->insert($lenderArray);
                DB::table('lender_profiles')->insert($profileArray);
                DB::table('lender_preferences')->insert($preferenceArray);
            }
        }

        if ($table == 'borrowers') {
            $this->line('Migrate borrowers table');
            $this->line('Migrate borrower_profiles table');
            $this->line('Migrate borrower_contacts table');

            $count = $this->con->table('borrowers')->count();
            $offset = 0;
            $limit = 500;
            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $borrowers = $this->con->table('borrowers')
                    ->join('users', 'borrowers.userid', '=', 'users.userid')
                    ->join('countries', 'borrowers.Country', '=', 'countries.code')
                    ->join('borrowers_extn', 'borrowers.userid', '=', 'borrowers_extn.userid')
                    ->where($offset)->take($limit)->get();
                $borrowerArray = [];
                $profileArray = [];
                $contactArray = [];

                foreach ($borrowers as $borrower) {
                    $newBorrower = [
                        'id'                  => $borrower['users.userid'],
                        'country_id'          => $borrower['countries.id'],
                        'first_name'          => $borrower['borrowers.FirstName'],
                        'last_name'           => $borrower['borrowers.LastName'],
                        'active_loan_id'      => $borrower['borrowers.activeLoanID'],
                        'loan_status'         => $borrower['borrowers.ActiveLoan'],
                        'active'              => $borrower['borrowers.Active'],
                        'volunteer_mentor_id' => $borrower['borrowers.Assigned_to'],
                        'referrer_id'         => $borrower['borrowers.refer_member_name'], //TODO cross check
                        'verified'            => 'TODO', // TODO
                        'activation_status'   => null, // TODO
                    ];

                    //TODO facebook_users migration

                    $profile = [
                        'borrower_id'                => $borrower['users.userid'],
                        'about_me'                   => $borrower['borrowers.About'],
                        'about_me_translation'       => $borrower['borrowers.tr_About'],
                        'about_business'             => $borrower['borrowers.BizDesc'],
                        'about_business_translation' => $borrower['borrowers.tr_BizDesc'],
                        'address'                    => $borrower['borrowers.PAddress'],
                        'address_instructions'       => 'TODO',// TODO
                        'city'                       => $borrower['borrowers.City'],
                        'national_id_number'         => $borrower['borrowers.nationId'],
                        'phone_number'               => $borrower['borrowers.TelMobile'],
                        'alternate_phone_number'     => $borrower['borrowers.AlternateTelMobile'], //TODO, though column in both new/old database are required=true, the database sample you gave have value null
                        'business_category_id'       => '',
                        'business_years'             => '',
                        'loan_usage'                 => '',
                        'birth_date'                 => '',
                    ];

                    //TODO, though all values are required in both tables, many are null in sample data
                    $communityLeader = [
                        'borrower_id'  => $borrower['users.userid'],
                        'first_name'   => $borrower['borrowers_extn.community_leader_first_name'],
                        'last_name'    => $borrower['borrowers_extn.community_leader_last_name'],
                        'phone_number' => $borrower['borrowers_extn.community_leader_mobile_phone'],
                        'description'  => $borrower['borrowers_extn.community_leader_organization_title'],
                        'type'         => 'communityLeader'
                    ];

                    for ($i = 1; $i <= 3; $i++) {
                        $stringFirstName = 'borrowers_extn.family_member'. $i. '_first_name';
                        $stringLastName = 'borrowers_extn.family_member'. $i. '_last_name';
                        $stringPhoneNumber = 'borrowers_extn.family_member'. $i. '_mobile_phone';
                        $stringDescription = 'borrowers_extn.family_member'. $i. '_relationship';

                        if (!$borrower[$stringFirstName] && !$borrower[$stringLastName] && !$borrower[$stringPhoneNumber]){
                            continue;
                        }
                        $familyMember = [
                            'borrower_id'  => $borrower['users.userid'],
                            'first_name'   => $borrower[$stringFirstName] ? $borrower[$stringFirstName] : '',
                            'last_name'    => $borrower[$stringLastName] ? $borrower[$stringLastName] : '',
                            'phone_number' => $borrower[$stringPhoneNumber] ? $borrower[$stringPhoneNumber] : '',
                            'description'  => $borrower[$stringDescription] ? $borrower[$stringDescription] : '',
                            'type'         => 'familyMember'
                        ];
                        array_push($contactArray, $familyMember);
                    }

                    for ($i = 1; $i <= 3; $i++) {
                        $stringFirstName = 'borrowers_extn.neighbor'. $i. '_first_name';
                        $stringLastName = 'borrowers_extn.neighbor'. $i. '_last_name';
                        $stringPhoneNumber = 'borrowers_extn.neighbor'. $i. '_mobile_phone';
                        $stringDescription = 'borrowers_extn.neighbor'. $i. '_relationship';

                        if (!$borrower[$stringFirstName] && !$borrower[$stringLastName] && !$borrower[$stringPhoneNumber] && !$stringDescription){
                            continue;
                        }
                        $neighbor = [
                            'borrower_id'  => $borrower['users.userid'],
                            'first_name'   => $borrower[$stringFirstName] ? $borrower[$stringFirstName] : '',
                            'last_name'    => $borrower[$stringLastName] ? $borrower[$stringLastName] : '',
                            'phone_number' => $borrower[$stringPhoneNumber] ? $borrower[$stringPhoneNumber] : '',
                            'description'  => $borrower[$stringDescription] ? $borrower[$stringDescription] : '',
                            'type'         => 'neighbor'
                        ];
                        array_push($contactArray, $neighbor);
                    }

                    //TODO JoinLog migration

                    array_push($borrowerArray, $newBorrower);
                    array_push($profileArray, $profile);
                    array_push($contactArray, $communityLeader);
                }
                DB::table('borrowers')->insert($borrowerArray);
                DB::table('borrowers')->insert($profileArray);
                DB::table('borrower_contacts')->insert($contactArray);
            }
        }

        if ($table == 'loan_categories'){
            $this->line('Migration loan_categories table');

            $categories = $this->con->table('loan_categories')->get();
            $categoryArray = [];

            foreach ($categories as $category) {
                $newCategory = [
                    'name'             => $category['name'],
                    'slug'             => '', // TODO
                    'what_description' => $category['what'],
                    'why_description'  => $category['why'],
                    'how_description'  => $category['lend'], //TODO cross check
                    'admin_only'       => $category['admin'],
                    ''
                ];

                array_push($categoryArray, $newCategory);
            }
            DB::table('loan_categories')->insert($categoryArray);
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
