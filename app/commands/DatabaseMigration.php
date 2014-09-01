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
	protected $name = 'migrate-zidisha1';

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
        $this->con = DB::connection('zidisha1');

        if ($table == 'all') {
            $this->line('Migrate all tables');

            $this->call('migrateDB', array('table' => 'users'));
            $this->call('migrateDB', array('table' => 'lenders'));
            $this->call('migrateDB', array('table' => 'borrowers'));
            $this->call('migrateDB', array('table' => 'loan_categories'));
            $this->call('migrateDB', array('table' => 'countries'));
            $this->call('migrateDB', array('table' => 'loans'));
            $this->call('migrateDB', array('table' => 'loan_bids'));
            $this->call('migrateDB', array('table' => 'admin_notes'));
            $this->call('migrateDB', array('table' => 'loan_stages'));
            $this->call('migrateDB', array('table' => 'transactions'));
            $this->call('migrateDB', array('table' => 'comments'));
            $this->call('migrateDB', array('table' => 'exchange_rates'));
            $this->call('migrateDB', array('table' => 'installments'));
            $this->call('migrateDB', array('table' => 'installment_payments'));
            $this->call('migrateDB', array('table' => 'borrower_payments'));
            $this->call('migrateDB', array('table' => 'lender_invites'));
            $this->call('migrateDB', array('table' => 'lender_invite_visits'));
            $this->call('migrateDB', array('table' => 'lender_invite_transactions'));
            $this->call('migrateDB', array('table' => 'paypal_ipn_log'));
            $this->call('migrateDB', array('table' => 'paypal_transactions'));
            $this->call('migrateDB', array('table' => 'gift_cards'));
            $this->call('migrateDB', array('table' => 'gift_card_transaction'));
            $this->call('migrateDB', array('table' => 'forgiveness_loan_shares'));
            $this->call('migrateDB', array('table' => 'forgiveness_loans'));
            $this->call('migrateDB', array('table' => 'borrower_refunds'));
            $this->call('migrateDB', array('table' => 'volunteer_mentors'));
            $this->call('migrateDB', array('table' => 'borrower_feedback_messages'));
            $this->call('migrateDB', array('table' => 'borrower_reviews'));
            $this->call('migrateDB', array('table' => 'languages'));
            $this->call('migrateDB', array('table' => 'lending_groups'));
            $this->call('migrateDB', array('table' => 'lending_group_members'));
            $this->call('migrateDB', array('table' => 'notifications'));
            $this->call('migrateDB', array('table' => 'withdrawal_requests'));
            $this->call('migrateDB', array('table' => 'followers'));
            $this->call('migrateDB', array('table' => 'borrower_invites'));
            $this->call('migrateDB', array('table' => 'credit_settings'));
            $this->call('migrateDB', array('table' => 'credits_earned'));
            $this->call('migrateDB', array('table' => 'facebook_users'));
            $this->call('migrateDB', array('table' => 'auto_lending_settings'));
            $this->call('migrateDB', array('table' => 'statistics'));
            $this->call('migrateDB', array('table' => 'reschedule'));
            $this->call('migrateDB', array('table' => 'bulk_emails'));
            $this->call('migrateDB', array('table' => 'bulk_email_recipients'));

        }

        if ($table == 'users') {
            $this->line('Migrate users table');

            $count = $this->con->table('users')->count();
            $this->line("    $count users");
            
            $limit = 500;
            for ($offset = 0; $offset < $count; $offset += $limit) {
                $this->line($offset);
                $users = $this->con->table('users as u')
                    ->select(
                        'u.*',
                        'l.userid as lUserid',
                        'l.Email as lEmail',
                        'l.Active as lActive',
                        'b.userid as bUserid',
                        'b.Email as bEmail',
                        'b.Active as bActive',
                        'p.userid as pUserid',
                        'p.Email as pEmail',
                        'p.Active as pActive'
                    )
                    ->leftJoin('lenders as l', 'u.userid', '=', 'l.userid')
                    ->leftJoin('borrowers as b', 'u.userid', '=', 'b.userid')
                    ->leftJoin('partners as p', 'u.userid', '=', 'p.userid')
                    ->skip($offset)
                    ->take($limit)
                    ->orderBy('u.userid', 'asc')
                    ->get();

                $userArray = [];

                $roles = [
                    1 => 1, // borrower
                    4 => 0, // lender
                    6 => 2, // partner                  
                    9 => 3, // admin
                ];
                               
                foreach ($users as $user) {
                    // lender
                    if ($user->lUserid !== null) {
                        $active = $user->lActive ? true : false;
                        $email = $user->lEmail;
                        $role = 0;
                    }
                    // borrower
                    elseif ($user->bUserid !== null) {
                        $active = $user->lActive ? true : false;
                        $email = $user->lEmail;
                        $role = 1;
                    }
                    elseif ($user->userid == 92) { // TODO, partner?
                        $active = true;
                        $email = 'admin@zidisha.org';
                        $role = 3;
                    }
                    // partner
                    elseif ($user->pUserid !== null) {
                        $active = $user->pActive ? true : false;
                        $email = $user->pEmail;
                        $role = 2;
                    }
                    else {
                        $active = false;
                        $email = 'fake_email_' . $user->userid . '@zidisha.org';
                        $role = $roles[$user->userlevel];
                    }
                                        
                    $newUser = [
                        'id'                 => $user->userid,
                        'username'           => $user->username,
                        'email'              => $email,
                        'password'           => $user->password,
                        'profile_picture_id' => null, // TODO
                        'facebook_id'        => $user->facebook_id,
                        'google_id'          => null, // since google login is now added
                        'google_picture'     => null,
                        'remember_token'     => null, // this cannot be shared between old and new codebase
                        'role'               => $role,
                        'sub_role'           => null, // TODO
                        'joined_at'          => date("Y-m-d H:i:s", $user->regdate),
                        'last_login_at'      => date("Y-m-d H:i:s", $user->last_login),
                        'created_at'         => date("Y-m-d H:i:s", $user->regdate),
                        'active'             => $active,
                    ];  

                    $userArray[] = $newUser;
                }
                DB::table('users')->insert($userArray);
            }
        }

        if ($table == 'lenders') {
            $this->line('Migrate lenders table');
            $this->line('Migrate lender_profiles table');
            $this->line('Migrate lender_preferences table');
            $this->line('Migrate lending_group_notifications table');

            $count = $this->con->table('lenders')->count();
            $offset = 0;
            $limit = 500;
            for ($offset; $offset < $count; $offset = ($offset + $limit)) {

                $lenders = $this->con->table('lenders')
                    ->join('users', 'lenders.userid', '=', 'users.userid')
                    ->join('countries', 'lenders.Country', '=', 'countries.code')
                    ->skip($offset)->take($limit)->get();
                $lenderArray = [];
                $profileArray = [];
                $preferenceArray = [];
                $lendingGroupNotificationArray = [];

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
                        'lender_id'                   => $lender['users.userid'],
                        'hide_lending_activity'       => $lender['lenders.hide_Amount'],
                        'hide_karma'                  => $lender['lenders.hide_karma'], // TODO do default false in new DB?
                        'notify_loan_fully_funded'    => $lender['lenders.loan_fully_funded_notify'],
                        'notify_loan_about_to_expire' => $lender['lenders.loan_about_to_expire_notify'],
                        'notify_loan_expired'         => $lender['lenders.loan_expired_notify'],
                        'notify_loan_disbursed'       => $lender['lenders.loan_disbursed_notify'],
                        'notify_comment'              => $lender['lenders.emailcomment'], // TODO cross check
                        'notify_loan_application'     => $lender['lenders.loan_app_notify'],
                        'notify_invite_accepted'      => $lender['lenders.invite_notify'],
                        'notify_loan_repayment'       => $lender['lenders.email_loan_repayment'],
                    ];

                    array_push($lenderArray, $newLender);
                    array_push($profileArray, $profile);
                    array_push($preferenceArray, $preference);

                    $groupIds = explode(',',$lender['lenders.groupmsg_notify']);
                    foreach ($groupIds as $groupId) {
                        $newGroupNotification = [
                            'lending_group_id' => $groupId,
                            'user_id'          => $lender['users.userid']
                        ];

                        array_push($lendingGroupNotificationArray, $newGroupNotification);
                    }
                }
                DB::table('lenders')->insert($lenderArray);
                DB::table('lender_profiles')->insert($profileArray);
                DB::table('lender_preferences')->insert($preferenceArray);
                DB::table('lending_group_notifications')->insert($lendingGroupNotificationArray);
            }
        }

        if ($table == 'borrowers') {
            $this->line('Migrate borrowers table');
            $this->line('Migrate borrower_profiles table');
            $this->line('Migrate borrower_contacts table');
            $this->line('Migrate borrower_join_logs table');

            $count = $this->con->table('borrowers')->count();
            $offset = 0;
            $limit = 500;
            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $borrowers = $this->con->table('borrowers')
                    ->join('users', 'borrowers.userid', '=', 'users.userid')
                    ->join('countries', 'borrowers.Country', '=', 'countries.code')
                    ->join('borrowers_extn', 'borrowers.userid', '=', 'borrowers_extn.userid')
                    ->join('facebook_info', 'borrowers.userid', '=', 'facebook_info.userid')
                    ->skip($offset)->take($limit)->get();
                $borrowerArray = [];
                $profileArray = [];
                $contactArray = [];
                $borrowerJoinLogArray = [];

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
                        'birth_date'                 => null,
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

                    $newJoinLog = [
                        'borrower_id' => $borrower['users.userid'],
                        'ip_address' => $borrower['facebook_info.ip_address'] ? $borrower['facebook_info.ip_address'] : '',
                        'preferred_loan_amount' => '',
                        'preferred_interest_rate' => '',
                        'preferred_repayment_amount' => '',
                    ];

                    if (!$borrower['users.emailVerified']) {
                        $newJoinLog = $newJoinLog + ['verification_code' => md5($borrower['users.password'].$borrower['users.salt'])];
                    }

                    array_push($borrowerArray, $newBorrower);
                    array_push($profileArray, $profile);
                    array_push($contactArray, $communityLeader);
                    array_push($borrowerJoinLogArray, $newJoinLog);
                }
                DB::table('borrowers')->insert($borrowerArray);
                DB::table('borrowers')->insert($profileArray);
                DB::table('borrower_contacts')->insert($contactArray);
                DB::table('borrower_join_logs')->insert($borrowerJoinLogArray);
            }
        }

        if ($table == 'loan_categories') {
            $this->line('Migration loan_categories table');
            $this->line('Adding raw data loan_category_translations table');

            $categories = $this->con->table('loan_categories')->get();
            $categoryArray = [];
            $categoryTranslationArray = [];

            foreach ($categories as $category) {
                $newCategory = [
                    'id'               => $category['id'],
                    'name'             => $category['name'],
                    'slug'             => \Illuminate\Support\Str::slug($category['name']),
                    'what_description' => $category['what'],
                    'why_description'  => $category['why'],
                    'how_description'  => $category['lend'], //TODO cross check
                    'admin_only'       => $category['admin'],
                    ''
                ];

                array_push($categoryArray, $newCategory);

                $categoryTranslationFR = [
                    'category_id'   => $category['id'],
                    'language_code' => 'fr',
                    'translation'   => $category['name'],
                ];
                $categoryTranslationID = [
                    'category_id'   => $category['id'],
                    'language_code' => 'id', //TODO, in old DB it seems it's 'in'
                    'translation'   => $category['name'],
                ];
                array_push($categoryTranslationArray, $categoryTranslationFR);
                array_push($categoryTranslationArray, $categoryTranslationID);
            }
            DB::table('loan_categories')->insert($categoryArray);
            DB::table('loan_category_translations')->insert($categoryTranslationArray);
        }

        if ($table == 'countries') {
            $this->line('Migrate countries table');

            $countries = $this->con->table('currency')
                ->join('countries', 'currency.country_code.' , '=', 'countries.code')
                ->join('country_lang', 'currency.country_code.' , '=', 'country_lang.country_code')
                ->join('registration_fee', 'currency.currency_name' , '=', 'registration_fee.currency_name')
                ->join('repayment_instructions', 'currency.country_code' , '=', 'repayment_instructions.country_code')
                ->get();
            $countryArray = [];

            foreach ($countries as $country) {
                $newCountry = [
                    'name'                    => $country['countries.name'],
                    'slug'                    => \Illuminate\Support\Str::slug($country['countries.name']),
                    'capital'                 => $country['currency.capital'],
                    'continent_code'          => $country['countries.loc'], //TODO cross check
                    'country_code'            => $country['countries.code'],
                    'dialing_code'            => $country['countries.phone'] ? $country['countries.phone'] : '',
                    'phone_number_length'     => '', //TODO
                    'currency_code'           => $country['currency.Currency'],
                    'borrower_country'        => $country['currency.active'],
                    'registration_fee'        => $country['registration_fee.Amount'],
                    'installment_period'      => null, //TODO
                    'installment_amount_step' => '', //TODO
                    'loan_amount_step'        => '', //TODO
                    'repayment_instructions'  => $country['repayment_instructions.description'] ? $country['repayment_instructions.description'] : null,
                    'accept_bids_note'        => null, //TODO
                    'language_code'           => $country['country_lang.lang_code'],
                ];

                array_push($countryArray, $newCountry);
            }
            DB:table('countries')->insert($countryArray);
        }

        if ($table == 'loans') {
            $this->line('Migrate loans table');

            $count = $this->con->table('loanapplic')->count();
            $offset = 0;
            $limit = 500;
            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $loans = $this->con->table('loanapplic')
                    ->skip($offset)->take($limit)->get();
                $loanArray = [];

                //TODO check most amount things and fill  unfilled values
                foreach ($loans as $loan) {
                    $newLoan = [
                        'id'                    => $loan['loanid'],
                        'borrower_id'           => $loan['borrowerid'],
                        'summary'               => $loan['summary'],
                        'summary_translation'   => $loan['tr_summary'],
                        'proposal'              => $loan['loanuse'],
                        'proposal_translation'  => $loan['tr_loanuse'],
                        'amount'                => $loan['Amount'],
                        'total_amount'          => '',
                        'paid_amount'           => '',
                        'usd_amount'            => '',
                        'installment_day'       => $loan['installment_day'],
                        'max_interest_rate'     => $loan['finalrate'],
                        'lender_interest_rate'  => '',
                        'category_id'           => $loan['loan_category_id'],
                        'secondary_category_id' => $loan['secondary_loan_category_id'],
                        'status'                => $loan['active'],
                        'applied_at'            => date("Y-m-d H:i:s", $loan['applydate']),
                        'accepted_at'           => date("Y-m-d H:i:s", $loan['AcceptDate']),
                        'expired_at'            => date("Y-m-d H:i:s", $loan['expires']),
                        'canceled_at'           => '',
                        'repaid_at'             => date("Y-m-d H:i:s", $loan['RepaidDate']),
                        'authorized_at'         => date("Y-m-d H:i:s", $loan['auth_date']),
                        'authorized_amount'     => '',
                        'disbursed_at'          => '',
                        'disbursed_amount'      => $loan['AmountGot'],
                        'forgiven_amount'       => '',
                        'registration_fee'      => '',
                        'raised_usd_amount'     => '',
                        'raised_percentage'     => '',
                        'paid_percentage'       => '',
                        'service_fee_rate'      => $loan['WebFee'],
                        'extra_days'            => $loan['extra_days'],
                        'currency_code'         => '',
                        'installment_period'    => $loan['weekly_inst'] ? 'weekly' : 'monthly',
                        'period'                => '',
                        'accept_bids_note'      => $loan['accept_bid_note'],
                        'sift_science_score'    => '',
                        'deleted_by_admin'      => $loan['adminDelete'],
                    ];

                    array_push($loanArray, $newLoan);
                }
                DB::table('loans')->insert($loanArray);
            }
        }

        if ($table == 'loan_bids') {
            $this->line('Migrate loan_bids table');

            $count = $this->con->table('loanbids')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $limit; $count = ($offset + $limit)) {
                $bids = $this->con->table('loanbids')
                    ->join('loanapplic', 'loanbids.loanid', '=', 'loanapplic.loanid')
                    ->skip($offset)->take($limit)->get();
                $bidArray = [];

                foreach ($bids as $bid) {
                    $newBid = [
                        'id'                      => $bid['loanbids.bidid'],
                        'loan_id'                 => $bid['loanbids.loanid'],
                        'lender_id'               => $bid['loanbids.lenderid'],
                        'borrower_id'             => $bid['loanapplic.borrowerid'],
                        'bid_amount'              => $bid['loanbids.bidamount'],
                        'interest_rate'           => '', //TODO
                        'active'                  => $bid['loanbids.active'],
                        'accepted_amount'         => $bid['loanbids.givenamount'],
                        'bid_at'                  => date("Y-m-d H:i:s", $bid['loanbids.biddate']),
                        'is_lender_invite_credit' => $bid['loanbids.use_lender_invite_credit'],
                        'is_automated_lending'    => null, //TODO
                        'updated_at'              => date("Y-m-d H:i:s", $bid['loanbids.modified']), //TODO is necessary?
                    ];

                    array_push($bidArray, $newBid);
                }
                DB::table('loan_bids')->insert($bidArray);
            }
        }

        if ($table = 'admin_notes') {
            $this->line('Migrate admin_notes table');

            $count = $this->con->table('loan_notes')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $adminNotes = $this->con->table('loan_notes')
                    ->skip($offset)->limit($limit)->get();
                $adminNoteArray = [];

                foreach ($adminNotes as $adminNote) {
                    $newAdminNote = [
                        'id'          => $adminNote['id'],
                        'user_id'     => $adminNote['userid'],
                        'loan_id'     => $adminNote['loanid'],
                        'borrower_id' => null,
                        'note'        => $adminNote['disbursement_notes'],
                        'type'        => 'disbursement'
                    ];

                    array_push($adminNoteArray, $newAdminNote);
                }
                DB::table('admin_notes')->insert($adminNoteArray);
            }
        }

        if ($table == 'loan_stages') {
            $this->line('Migrate loan_stages table');

            $count = $this->con->table('loanstage')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $stages = $this->con->table('loanstage')
                    ->skip($offset)->take($limit)->get();
                $stageArray = [];

                foreach ($stages as $stage) {
                    $newStage = [
                        'id'          => $stage['id'],
                        'loan_id'     => $stage['loanid'],
                        'borrower_id' => $stage['borrowerid'],
                        'status'      => $stage['status'],
                        'start_date'  => date("Y-m-d H:i:s", $stage['startdate']),
                        'end_date'    => date("Y-m-d H:i:s", $stage['enddate']),
                        'created_at'  => date("Y-m-d H:i:s", $stage['created']),
                        'updated_at'  => date("Y-m-d H:i:s", $stage['modified']),
                    ];

                    array_push($stageArray, $newStage);
                }
                DB::table('loan_stages')->insert($stageArray);
            }
        }

        if ($table == 'transactions') {
            $this->line('Migrate transactions table');

            $count = $this->con->table('transactions')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $transactions = $this->con->table('transactions')
                    ->skip($offset)->take($limit)->get();
                $transactionArray = [];

                foreach ($transactions as $transaction) {
                    $newTransaction = [
                        'id'               => $transaction['id'],
                        'user_id'          => $transaction['userid'],
                        'amount'           => $transaction['amount'],
                        'description'      => $transaction['txn_desc'],
                        'loan_id'          => $transaction['loanid'],
                        'transaction_date' => date("Y-m-d H:i:s", $transaction['TrDate']),
                        'exchange_rate'    => $transaction['conversionrate'],
                        'type'             => $transaction['txn_type'],
                        'sub_type'         => $transaction['txn_sub_type'],
                        'loan_bid_id'      => $transaction['loanbid_id']
                    ];

                    array_push($transactionArray, $newTransaction);
                }
                DB::table('transactions')->insert($transactionArray);
            }
        }

        // TODO all type of comments table , till borrower_uploads table
        if ($table == 'comments') {
            $this->line('Migrate comments table');

            $count = $this->con->table('comments')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count ; $offset = ($offset + $limit)) {
                $comments = $this->con->table('comments')
                    ->skip($offset)->limit($limit)->get();
                $commentArray = [];

                foreach ($comments as $comment) {
                    $newComment = [
                        'id'      => $comment['id'],
                        'user_id' => $comment['userid'],
                        'message' => ''
                    ];
                }
            }
        }

        if ($table == 'exchange_rates') {
            $this->line('Migrate exchange_rates table');

            $count = $this->con->table('excrate')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $rates = $this->con->table('excrate')
                    ->join('currency', 'excrate.currency', '=', 'currency.id')
                    ->skip($offset)->limit($limit)->get();
                $rateArray = [];

                foreach ($rates as $rate) {
                    $newRate = [
                        'id'            => $rate['excrate.id'],
                        'rate'          => $rate['excrate.rate'],
                        'start_date'    => date("Y-m-d H:i:s", $rate['excrate.start']),
                        'end_date'      => date("Y-m-d H:i:s", $rate['excrate.stop']),
                        'currency_code' => $rate['currency.Currency']
                    ];

                    array_push($rateArray, $newRate);
                }
                DB::table('exchange_rates')->insert($rateArray);
            }
        }

        if ($table == 'installments') {
            $this->line('Migrate installments table');

            $count = $this->con->table('repaymentschedule')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $installments = $this->con->table('repaymentschedule')
                    ->skip($offset)->limit($limit)->get();
                $installmentArray = [];

                foreach ($installments as $installment) {
                    $newInstallment = [
                        'id'          => $installment['id'],
                        'borrower_id' => $installment['userid'],
                        'loan_id'     => $installment['loanid'],
                        'due_date'    => date("Y-m-d H:i:s", $installment['duedate']),
                        'amount'      => $installment['amount'],
                        'paid_date'   => date("Y-m-d H:i:s", $installment['paiddate']),
                        'paid_amount' => $installment['paidamt']
                    ];

                    array_push($installmentArray, $newInstallment);
                }
                DB::table('installments')->insert($installmentArray);
            }
        }

        if ($table == 'installment_payments') {
            $this->line('Migrate installment_payments table');

            $count = $this->con->table('repaymentschedule_actual')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $payments = $this->con->table('repaymentschedule_actual')
                    ->skip($offset)->limit($limit)->get();
                $paymentArray = [];

                foreach ($payments as $payment) {
                    $newPayment = [
                        'id'               => $payment['id'],
                        'installment_id'   => $payment['rid'],
                        'borrower_id'      => $payment['userid'],
                        'loan_id'          => $payment['loanid'],
                        'paid_date'        => date("Y-m-d H:i:s", $payment['paiddate']),
                        'paid_amount'      => $payment['paidamt'],
                        'exchange_rate_id' => 0, //TODO
                    ];

                    array_push($paymentArray, $newPayment);
                }
                DB::table('installment_payments')->insert($paymentArray);
            }
        }

        if ($table == 'borrower_payments') {
            $this->line('Migrate borrower_payments table');

            $count = $this->con->table('borrower_payments')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $borrowerPayments = $this->con->table('borrower_payments')
                    ->skip($offset)->limit($limit)->get();
                $borrowerPaymentArray = [];

                foreach ($borrowerPayments as $borrowerPayment) {
                    $newBorrowerPayment = [
                        'id'           => $borrowerPayment['id'],
                        'country_code' => $borrowerPayment['country_code'],
                        'receipt'      => $borrowerPayment['receipt'],
                        'date'         => date("Y-m-d H:i:s", $borrowerPayment['date']),
                        'amount'       => $borrowerPayment['amount'],
                        'borrower_id'  => $borrowerPayment['borrower_id'],
                        'status'       => $borrowerPayment['status'],
                        'phone'        => $borrowerPayment['phone'],
                        'details'      => $borrowerPayment['details'],
                        'error'        => $borrowerPayment['error']
                    ];

                    array_push($borrowerPaymentArray, $newBorrowerPayment);
                }
                DB::table('borrower_payments')->insert($borrowerPaymentArray);
            }
        }

        if ($table == 'lender_invites') {
            $this->line('Migrate lender_invites table');

            $count = $this->con->table('lender_invites')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $lenderInvites = $this->con->table('lender_invites')
                    ->skip($offset)->limit($limit)->get();
                $lenderInviteArray = [];

                foreach ($lenderInvites as $lenderInvite) {
                    $newLenderInvite = [
                        'id'         => $lenderInvite['id'],
                        'lender_id'  => $lenderInvite['lender_id'],
                        'email'      => $lenderInvite['email'],
                        'invited'    => $lenderInvite['invited'],
                        'hash'       => $lenderInvite['hash'],
                        'invitee_id' => $lenderInvite['invitee_id'],
                        'created_at' => $lenderInvite['created'] // because it's already DateTime in old DB
                    ];

                    array_push($lenderInviteArray, $newLenderInvite);
                }
                DB::table('lender_invites')->insert($lenderInviteArray);
            }
        }

        if ($table == 'lender_invite_visits') {
            $this->line('Migrate lender_invite_visits table');

            $count = $this->con->table('lender_invite_visits')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $inviteVisits = $this->con->table('lender_invite_visits')
                    ->skip($offset)->limit($limit)->get();
                $inviteVisitArray = [];

                foreach ($inviteVisits as $inviteVisit) {
                    $newInviteVisit = [
                        'id'               => $inviteVisit['id'],
                        'lender_id'        => $inviteVisit['lender_id'],
                        'lender_invite_id' => $inviteVisit['lender_invite_id'],
                        'share_type'       => $inviteVisit['share_type'],
                        'http_referer'     => $inviteVisit['http_referer'],
                        'ip_address'       => $inviteVisit['ip_address'],
                        'created_at'       => $inviteVisit['created'] // because it's already DateTime in old DB
                    ];

                    array_push($inviteVisitArray, $newInviteVisit);
                }
                DB::table('lender_invite_visits')->insert($inviteVisitArray);
            }
        }

        if ($table == 'lender_invite_transactions') {
            $this->line('Migrate lender_invite_transactions table');

            $count = $this->con->table('lender_invite_transactions')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $$offset = ($offset + $limit)) {
                $inviteTransactions = $this->con->table('lender_invite_transactions')
                    ->skip($offset)->limit($limit)->get();
                $inviteTransactionArray = [];

                foreach ($inviteTransactions as $inviteTransaction) {
                    $newInviteTransaction = [
                        'id'               => $inviteTransaction['id'],
                        'lender_id'        => $inviteTransaction['lender_id'],
                        'amount'           => $inviteTransaction['amount'],
                        'description'      => $inviteTransaction['txn_desc'],
                        'transaction_date' => $inviteTransaction['created'], // because it's already DateTime in old DB
                        'type'             => $inviteTransaction['txn_type'],
                        'loan_id'          => $inviteTransaction['loan_id'],
                        'loan_bid_id'      => $inviteTransaction['loanbid_id']
                    ];

                    array_push($inviteTransactionArray, $newInviteTransaction);
                }
                DB::table('lender_invite_transactions')->insert($inviteTransactionArray);
            }
        }

        if ($table == 'paypal_ipn_log') {
            $this->line('Migrate paypal_ipn_log table');

            $count = $this->con->table('paypal_ipn_raw_log')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $paypalIpnLogs = $this->con->table('paypal_ipn_raw_log')
                    ->skip($offset)->limit($limit)->get();
                $paypalIpnLogArray = [];

                foreach ($paypalIpnLogs as $paypalIpnLog) {
                    $newPaypalIpnLog = [
                        'id'         => $paypalIpnLog['id'],
                        'log'        => $paypalIpnLog['ipn_data_serialized'],
                        'created_at' => date("Y-m-d H:i:s", $paypalIpnLog['created_timestamp'])
                    ];

                    array_push($paypalIpnLogArray, $newPaypalIpnLog);
                }
                DB::table('paypal_ipn_log')->insert($paypalIpnLogArray);
            }
        }

        if ($table == 'paypal_transactions') {
            $this->line('Migrate paypal_transactions table');

            $count = $this->con->table('paypal_txns')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $paypalTransactions = $this->con->table('paypal_txns')
                    ->skip($offset)->limit($limit)->get();
                $paypalTransactionArray = [];

                foreach ($paypalTransactions as $paypalTransaction) {
                    $newPaypalTransaction = [
                        'id'                     => $paypalTransaction['invoiceid'],
                        'transaction_id'         => $paypalTransaction['txnid'],
                        'transaction_type'       => $paypalTransaction['txn_type'],
                        'amount'                 => $paypalTransaction['amount'],
                        'donation_amount'        => $paypalTransaction['donation'],
                        'paypal_transaction_fee' => $paypalTransaction['paypal_tran_fee'],
                        'total_amount'           => $paypalTransaction['total_amount'],
                        'status'                 => $paypalTransaction['status'],
                        'custom'                 => $paypalTransaction['custom'],
                        'token'                  => $paypalTransaction['paypaldata'],
                        // TODO check if necessary and if yes then viable with created_at new then updated_at
                        'updated_at'             => date("Y-m-d H:i:s", $paypalTransaction['updateddate'])
                    ];

                    array_push($paypalTransactionArray, $newPaypalTransaction);
                }
                DB::table('paypal_transactions')->insert($paypalTransactionArray);
            }
        }

        if ($table == 'gift_cards') {
            $this->line('Migrate gift_cards table');

            $count = $this->con->table('gift_cards')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $giftCards = $this->con->table('gift_cards')
                    ->join('gift_transaction', 'gift_cards.txn_id', '=', 'gift_transaction.txn_id') // TODO cross check(is it gift_transaction.txn_id or gift_transaction.id)
                    ->skip($offset)->limit($limit)->get();
                $giftCardArray = [];

                foreach ($giftCards as $giftCard) {
                    $newGiftCard = [
                        'id'                       => $giftCard['gift_cards.id'],
                        'lender_id'                => $giftCard['gift_transaction.userid'],
                        'template'                 => $giftCard['gift_cards.template'], // TODO make sure old and new template ids are same
                        'order_type'               => $giftCard['gift_cards.order_type'], // TODO check both string are smame
                        'card_amount'              => $giftCard['gift_cards.card_amount'],
                        'recipient_email'          => $giftCard['gift_cards.recipient_email'],
                        'confirmation_email'       => $giftCard['gift_cards.sender'],
                        'recipient_name'           => $giftCard['gift_cards.to_name'],
                        'from_name'                => $giftCard['gift_cards.from_name'],
                        'message'                  => $giftCard['gift_cards.message'],
                        'date'                     => date("Y-m-d H:i:s", $giftCard['gift_cards.date']),
                        'expire_date'              => date("Y-m-d H:i:s", $giftCard['gift_cards.exp_date']),
                        'card_code'                => $giftCard['gift_cards.card_code'],
                        'status'                   => $giftCard['gift_cards.status'],
                        'claimed'                  => $giftCard['gift_cards.claimed'],
                        'recipient_id'             => $giftCard['gift_cards.claimed_by'],
                        'donated'                  => $giftCard['gift_cards.donated'],
                        'gift_card_transaction_id' => $giftCard['gift_transaction.id'] // TODO cross check
                    ];

                    array_push($giftCardArray, $newGiftCard);
                }
                DB::table('gift_cards')->insert($giftCardArray);
            }
        }

        if ($table == 'gift_card_transaction') {
            $this->line('Migrate gift_card_transaction table');

            $count = $this->con->table('gift_transaction')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $giftCardTransactions = $this->con->table('gift_transaction')
                    ->skip($offset)->limit($limit)->get();
                $giftCardTransactionArray = [];

                foreach ($giftCardTransactions as $giftCardTransaction) {
                    $newGiftCardTransaction = [
                        'id'               => $giftCardTransaction['id'],
                        'transaction_id'   => $giftCardTransaction['txn_id'],
                        'transaction_type' => $giftCardTransaction['txn_type'],
                        'lender_id'        => $giftCardTransaction['userid'],
                        'invoice_id'       => $giftCardTransaction['invoiceid'],
                        'status'           => $giftCardTransaction['status'],
                        'total_cards'      => $giftCardTransaction['total_cards'],
                        'amount'           => $giftCardTransaction['amount'],
                        'donation'         => $giftCardTransaction['donation'],
                        'date'             => date("Y-m-d H:i:s", $giftCardTransaction['date']),
                    ];

                    array_push($giftCardTransactionArray, $newGiftCardTransaction);
                }
                DB::table('gift_card_transaction')->insert($giftCardTransactionArray);
            }
        }

        if ($table == 'forgiveness_loan_shares') {
            $this->line('Migrate forgiveness_loan_shares table');

            $count = $this->con->table('forgiven_loans')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $forgivenessLoanShares = $this->con->table('forgiven_loans')
                    ->skip($offset)->limit($limit)->get();
                $forgivenessLoanShareArray = [];

                foreach ($forgivenessLoanShares as $forgivenessLoanShare) {
                    $newForgivenessLoanShare = [
                        'id'          => $forgivenessLoanShare['id'],
                        'loan_id'     => $forgivenessLoanShare['loan_id'],
                        'lender_id'   => $forgivenessLoanShare['lender_id'],
                        'borrower_id' => $forgivenessLoanShare['borrower_id'],
                        'amount'      => $forgivenessLoanShare['amount'],
                        'usdAmount'   => $forgivenessLoanShare['damount'],
                        'is_accepted' => $forgivenessLoanShare['tnc'],
                        'date'        => date("Y-m-d H:i:s", $forgivenessLoanShare['date'])
                    ];

                    array_push($forgivenessLoanShareArray, $newForgivenessLoanShare);
                }
                DB::table('forgiveness_loan_shares')->insert($forgivenessLoanShareArray);
            }
        }

        if ($table == 'forgiveness_loans') {
            $this->line('Migrate forgiveness_loans table');

            $count = $this->con->table('loans_to_forgive')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $forgivenessLoans = $this->con->table('loans_to_forgive')
                    ->skip($offset)->limit($limit)->get();
                $forgivenessLoanArray = [];

                foreach ($forgivenessLoans as $forgivenessLoan) {
                    $newForgivenessLoan = [
                        'loan_id'           => $forgivenessLoan['loanid'],
                        'borrower_id'       => $forgivenessLoan['borrowerid'],
                        'comment'           => $forgivenessLoan['comment'],
                        'verification_code' => $forgivenessLoan['validation_code'],
                        'is_reminder_sent'  => $forgivenessLoan['reminder_sent']
                    ];

                    array_push($forgivenessLoanArray, $newForgivenessLoan);
                }
                DB::table('forgiveness_loans')->insert($forgivenessLoanArray);
            }
        }

        if ($table == 'borrower_refunds') {
            $this->line('Migrate borrower_refunds table');

            $count = $this->con->table('borrower_refunds')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset <$count; $offset = ($offset + $limit)) {
                $borrowerRefunds = $this->con->table('borrower_refunds')
                    ->skip($offset)->limit($limit)->get();
                $borrowerRefundArray = [];

                foreach ($borrowerRefunds as $borrowerRefund) {
                    $newBorrowerRefund = [
                        'id'                  => $borrowerRefund['id'],
                        'amount'              => $borrowerRefund['amount'],
                        'borrower_id'         => $borrowerRefund['borrower_id'],
                        'loan_id'             => $borrowerRefund['loan_id'],
                        'borrower_payment_id' => $borrowerRefund['borrower_payment_id'],
                        'refunded'            => $borrowerRefund['refunded'],
                        'created_at'          => $borrowerRefund['created'] // because it's already DateTime in old DB
                    ];

                    array_push($borrowerRefundArray, $newBorrowerRefund);
                }
                DB::table('borrower_refunds')->insert($borrowerRefundArray);
            }
        }

        if ($table == 'volunteer_mentors') {
            $this->line('Migrate volunteer_mentors table');

            $count = $this->con->table('community_organizers')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $volunteerMentors = $this->con->table('community_organizers')
                    ->skip($offset)->limit($limit)->get();
                $volunteerMentorArray = [];

                foreach ($volunteerMentors as $volunteerMentor) {
                    $newVolunteerMentor = [
                        'borrower_id' => $volunteerMentor['user_id'],
                        'country_id'  => $volunteerMentor['country'],
                        'grant_date'  => date("Y-m-d H:i:s", $volunteerMentor['grant_date']),
                        'note'        => $volunteerMentor['note'],
                        'status'      => $volunteerMentor['status']
                    ];
                    //TODO, will mentee_count get calculated here?

                    array_push($volunteerMentorArray, $newVolunteerMentor);
                }
                DB::table('volunteer_mentors')->insert($volunteerMentorArray);
            }
        }

        if ($table == 'borrower_feedback_messages') {
            $this->line('Migrate borrower_feedback_messages table');

            $count = $this->con->table('borrower_reports')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $feedbackMessages = $this->con->table('borrower_reports')
                    ->skip($offset)->limit($limit)->get();
                $feedbackMessageArray = [];

                foreach ($feedbackMessages as $feedbackMessage) {
                    $newFeedbackMessage = [
                        'borrower_id'    => $feedbackMessage['borrower_id'],
                        'type'           => null, //TODO
                        'borrower_email' => '', //TODO
                        'cc'             => $feedbackMessage['cc'],
                        'reply_to'       => $feedbackMessage['replyto'],
                        'subject'        => $feedbackMessage['subject'],
                        'message'        => $feedbackMessage['message'],
                        'sent_at'        => date("Y-m-d H:i:s", $feedbackMessage['sent_on']),
                        'sender_name'    => '', //TODO
                        'loan_id'        => $feedbackMessage['loanid']
                    ];

                    array_push($feedbackMessageArray, $newFeedbackMessage);
                }
                DB::table('borrower_feedback_messages')->insert($feedbackMessageArray);
            }
        }

        if ($table == 'borrower_reviews') {
            $this->line('Migrate borrower_reviews table');

            $count = $this->con->table('borrower_review')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $borrowerReviews = $this->con->table('borrower_review')
                    ->skip($offset)->limit($limit)->get();
                $borrowerReviewArray = [];

                foreach ($borrowerReviews as $borrowerReview) {
                    $newBorrowerReview = [
                        'borrower_id'               => $borrowerReview['borrower_id'],
                        'is_photo_clear'            => $borrowerReview['is_photo_clear'],
                        'is_desc_clear'             => $borrowerReview['is_desc_clear'],
                        'is_address_locatable'      => $borrowerReview['is_addr_locatable'],
                        'is_address_locatable_note' => '', //TODO
                        'is_number_provided'        => $borrowerReview['is_number_provided'],
                        'is_nat_id_uploaded'        => $borrowerReview['is_nat_id_uploaded'],
                        'is_rec_form_uploaded'      => $borrowerReview['is_rec_form_uploaded'],
                        'is_rec_form_offcr_name'    => $borrowerReview['is_rec_form_offcr_name'],
                        'is_pending_mediation'      => $borrowerReview['is_pending_mediation'],
                        'created_by'                => $borrowerReview['created_by'],
                        'modified_by'               => $borrowerReview['modified_by']
                    ];

                    array_push($borrowerReviewArray, $newBorrowerReview);
                }
                DB::table('borrower_reviews')->insert($borrowerReviewArray);
            }
        }

        if ($table == 'languages') {
            $this->line('Migrate languages table');

            $count = $this->con->table('language')->get();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $languages = $this->con->table('language')
                    ->skip($offset)->limit($limit)->get();
                $languageArray = [];

                foreach ($languages as $language) {
                    $newLanguage = [
                        'language_code' => $language['langcode`'],
                        'name'          => $language['lang'],
                        'active'        => $language['is_active_for_country'] // TODO, active or is_active_for_country
                    ];

                    array_push($languageArray, $newLanguage);
                }
                DB::table('languages')->insert($languageArray);
            }
        }

        if ($table == 'lending_groups') {
            $this->line('Migrate lending_groups table');

            $count = $this->con->table('lender_groups')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $lendingGroups = $this->con->table('lender_groups')
                    ->skip($offset)->limit($limit)->get();
                $lendingGroupArray = [];

                foreach ($lendingGroups as $lendingGroup) {
                    $newLendingGroup = [
                        'id'                       => $lendingGroup['id'],
                        'name'                     => $lendingGroup['name'],
                        'website'                  => $lendingGroup['website'],
                        'group_profile_picture_id' => $lendingGroup['image'], //TODO with upload things
                        'about'                    => $lendingGroup['about_grp'],
                        'creator_id'               => $lendingGroup['created_by'],
                        'leader_id'                => $lendingGroup['grp_leader'],
                        'created_at'               => $lendingGroup['created'],
                        'updated_at'              => $lendingGroup['modified']
                    ];

                    array_push($lendingGroupArray, $newLendingGroup);
                }
                DB::table('lending_groups')->insert($lendingGroupArray);
            }
        }

        if ($table == 'lending_group_members') {
            $this->line('Migrate lending_group_members table');

            $count = $this->con->table('lending_group_members')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $groupMembers = $this->con->table('lending_group_members')
                    ->skip($offset)->limit($limit)->get();
                $groupMemberArray = [];

                foreach ($groupMembers as $groupMember) {
                    $newGroupMember = [
                        'id'          => $groupMember['id'],
                        'group_id'    => $groupMember['group_id'],
                        'member_id'   => $groupMember['member_id'],
                        'leaved'      => $groupMember['leaved'],
                        'created_at'  => $groupMember['created'],
                        'updated_at' => $groupMember['modified']
                    ];

                    array_push($groupMemberArray, $newGroupMember);
                }
                DB::table('lending_group_members')->insert($groupMemberArray);
            }
        }

        if ($table == 'notifications') {
            $this->line('Migrate notifications table');

            $count = $this->con->table('notification_history')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $notifications = $this->con->table('notification_history')
                    ->skip($offset)->limit($limit)->get();
                $notificationArray = [];

                foreach ($notifications as $notification) {
                    $newNotification = [
                        'id'         => $notification['id'],
                        'type'       => $notification['type'],
                        'user_id'    => $notification['userid'],
                        'created_at' => $notification['created']
                    ];

                    array_push($notificationArray, $newNotification);
                }
                DB::table('notifications')->insert($notificationArray);
            }
        }

        if ($table == 'withdrawal_requests') {
            $this->line('Migrate withdrawal_requests table');

            $count = $this->con->table('withdraw')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $withdrawalRequests = $this->con->table('withdraw')
                    ->skip($offset)->limit($limit)->get();
                $withdrawalRequestArray = [];

                foreach ($withdrawalRequests as $withdrawalRequest) {
                    $newWithdrawalRequest = [
                        'id'           => $withdrawalRequest['id'],
                        'lender_id'    => $withdrawalRequest['userid'],
                        'amount'       => $withdrawalRequest['amount'],
                        'paid'         => $withdrawalRequest['paid'],
                        'paypal_email' => $withdrawalRequest['paypalemail']
                    ];

                    array_push($withdrawalRequestArray, $newWithdrawalRequest);
                }
                DB::table('withdrawal_requests')->insert($withdrawalRequestArray);
            }
        }

        if ($table == 'followers') {
            $this->line('Migrate followers table');

            $count = $this->con->table('followers')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $followers = $this->con->table('followers')
                    ->skip($offset)->limit($limit)->get();
                $followerArray = [];

                foreach ($followers as $follower) {
                    $newFollower = [
                        'id'                      => $follower['id'],
                        'lender_id'               => $follower['lender_id'],
                        'borrower_id'             => $follower['borrower_id'],
                        'active'                  => !$follower['deleted'],
                        'notify_comment'          => $follower['comment_notify'], // TODO cross check, if !$value
                        'notify_loan_application' => $follower['new_loan_notify'], // TODO cross check, if !$value
                        'created_at'              => $follower['created'],
                        'updated_at'              => $follower['modified']
                    ];

                    array_push($followerArray, $newFollower);
                }
                DB::table('followers')->insert($followerArray);
            }
        }

        if ($table == 'borrower_invites') {
            $this->line('Migrate borrower_invites table');

            $count = $this->con->table('invites')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $borrowerInvites = $this->con->table('invites')
                    ->skip($offset)->limit($limit)->get();
                $borrowerInviteArray = [];

                foreach ( $borrowerInvites as $borrowerInvite) {
                    $newBorrowerInvite =  [
                        'id'          => $borrowerInvite['id'],
                        'borrower_id' => $borrowerInvite['userid'],
                        'email'       => $borrowerInvite['email'],
                        'invited'     => $borrowerInvite['visited'], // TODO cross check
                        'hash'        => $borrowerInvite['cookie_value'],
                        'invitee_id'  => $borrowerInvite['invitee_id']
                    ];

                    array_push($borrowerInviteArray, $newBorrowerInvite);
                }
                DB::table('borrower_invites')->insert($borrowerInviteArray);
            }
        }

        if ($table == 'credit_settings') {
            $this->line('Migrate credit_settings table');

            $count = $this->con->table('credit_setting')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $creditSettings = $this->con->table('credit_setting')
                    ->skip($offset)->limit($limit)->get();
                $creditSettingArray = [];

                foreach ($creditSettings as $creditSetting) {
                    $newCreditSetting = [
                        'id'                => $creditSetting['id'],
                        'country_code'      => $creditSetting['country_code'],
                        'loan_amount_limit' => $creditSetting['loanamt_limit'],
                        'character_limit'   => $creditSetting['character_limit'],
                        'comments_limit'    => $creditSetting['comments_limit'],
                        'type'              => $creditSetting['type'], // TODO, add comments type?
                        'created_at'        => date("Y-m-d H:i:s", $creditSetting['created']),
                        'updated_at'        => date("Y-m-d H:i:s", $creditSetting['modified'])
                    ];

                    array_push($creditSettingArray, $newCreditSetting);
                }
                DB::table('credit_settings')->insert($creditSettingArray);
            }
        }

        if ($table == 'credits_earned') {
            $this->line('Migrate credits_earned table');

            $count = $this->con->table('credits_earned')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $creditsEarned = $this->con->table('credits_earned')
                    ->skip($offset)->limit($limit)->get();
                $creditEarnedArray = [];

                foreach ($creditsEarned as $creditEarned) {
                    $newCreditEarned = [
                        'id'          => $creditEarned['id'],
                        'borrower_id' => $creditEarned['borrower_id'],
                        'loan_id'     => $creditEarned['loan_id'],
                        'credit_type' => $creditEarned['credit_type'], // TODO, add valueSet in table?
                        'ref_id'      => $creditEarned['ref_id'],
                        'credit'      => $creditEarned['credit'],
                        'created_at'  => date("Y-m-d H:i:s", $creditEarned['created']),
                        'updated_at'  => date("Y-m-d H:i:s", $creditEarned['modified'])
                    ];

                    array_push($creditEarnedArray, $newCreditEarned);
                }
                DB::table('credits_earned')->insert($creditEarnedArray);
            }
        }

        if ($table == 'bulk_emails') {
            $this->line('Migrate bulk_emails table');

            $count = $this->con->table('bulk_emails')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $bulkEmails = $this->con->table('bulk_emails')
                    ->skip($offset)->limit($limit)->get();
                $bulkEmailArray = [];

                foreach ($bulkEmails as $bulkEmail) {
                    $newBulkEmail = [
                        'id'           => $bulkEmail['id'],
                        'sender_email' => $bulkEmail['sender'],
                        'subject'      => $bulkEmail['subject'],
                        'header'       => $bulkEmail['header'],
                        'message'      => $bulkEmail['message'],
                        'template'     => $bulkEmail['template'],
                        'html'         => $bulkEmail['html'],
                        'tag'          => $bulkEmail['tag'],
                        'params'       => $bulkEmail['params'],
                        'processed_at' => $bulkEmail['processed'],
                        'created_at'   => $bulkEmail['created']
                    ];

                    array_push($bulkEmailArray, $newBulkEmail);
                }
                DB::table('bulk_emails')->insert($bulkEmailArray);
            }
        }

        if ($table == 'bulk_email_recipients') {
            $this->line('Migrate bulk_email_recipients table');

            $count = $this->con->table('bulk_email_recipients')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $bulkEmailRecipients = $this->con->table('bulk_email_recipients')
                    ->skip($offset)->limit($limit)->get();
                $bulkEmailRecipientArray = [];

                foreach ($bulkEmailRecipients as $bulkEmailRecipient ) {
                    $newBulkEmailRecipient = [
                        'id'            => $bulkEmailRecipient['id'],
                        'bulk_email_id' => $bulkEmailRecipient['bulk_email_id'],
                        'email'         => $bulkEmailRecipient['email'],
                        'processed_at'  => $bulkEmailRecipient['processed']
                    ];

                    array_push($bulkEmailRecipientArray, $newBulkEmailRecipient);
                }
                DB::table('bulk_email_recipients')->insert($bulkEmailRecipientArray);
            }
        }

        if ($table == 'facebook_users') {
            $this->line('Migrate facebook_users table');

            $count = $this->con->table('facebook_info')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $facebookUsers = $this->con->table('facebook_info')
                    ->skip($offset)->limit($limit)->get();
                $facebookUserArray = [];

                foreach ($facebookUsers as $facebookUser) {
                    $facebookData = unserialize($facebookUser['facebook_data']);
                    $newFacebookUser = [
                        'id'              => $facebookUser['id'],
                        'user_id'         => $facebookUser['userid'],
                        'account_name'    => $facebookData['user_profile']['name'],
                        'email'           => $facebookData['user_profile']['email'],
                        'birth_date'      => $facebookData['user_profile']['birthday'],
                        'city'            => $facebookData['user_profile']['hometown'],
                        'first_post_date' => $facebookData['posts'][1]['created_time'],
                        'friends_count'   => $facebookData['user_friends']
                    ];

                    array_push($facebookUserArray, $newFacebookUser);
                }
                DB::table('facebook_users')->insert($facebookUserArray);
            }
        }

        if ($table == 'auto_lending_settings') {
            $this->line('Migrate auto_lending_settings table');

            $count = $this->con->table('auto_lending')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $autoLendingSettings = $this->con->table('auto_lending')
                    ->skip($offset)->limit($limit)->get();
                $autoLendingSettingArray = [];

                foreach ($autoLendingSettings as $autoLendingSetting) {
                    $newAutoLendingSetting = [
                        'id'                   => $autoLendingSetting['id'],
                        'lender_id'            => $autoLendingSetting['lender_id'],
                        'preference'           => $autoLendingSetting['preference'],
                        'min_desired_interest' => $autoLendingSetting['desired_interest'],
                        'max_desired_interest' => $autoLendingSetting['max_desired_interest'],
                        'current_allocated'    => $autoLendingSetting['current_allocated'],
                        'lender_credit'        => $autoLendingSetting['lender_credit'],
                        'active'               => $autoLendingSetting['Active'],
                        'last_processed'       => $autoLendingSetting['last_processed'],
                        'created_at'           => $autoLendingSetting['created'],
                        'updated_at'           => $autoLendingSetting['modified']
                    ];

                    array_push($autoLendingSettingArray, $newAutoLendingSetting);
                }
                DB::table('auto_lending_settings')->insert($autoLendingSettingArray);
            }
        }

        if ($table == 'statistics') {
            $this->line('Migrate statistics table');

            $count = $this->con->table('statistics')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $statistics =$this->con->table('statistics')
                    ->skip($offset)->limit($limit)->get();
                $statisticArray = [];

                foreach ($statistics as $statistic) {
                    $newStatistics = [
                        'id'         => $statistic['id'],
                        'name'       => $statistic['Name'],
                        'value'      => $statistic['value'],
                        //TODO croos check for foreign key bcz some country values in old DB are '' (empty string)
                        'country_id' => $statistic['country'],
                        'date'       => date("Y-m-d H:i:s", $statistic['date'])
                    ];

                    array_push($statisticArray, $newStatistics);
                }
                DB::table('statistics')->insert($statisticArray);
            }
        }

        if ($table == 'reschedules') {
            $this->line('Migrate reschedule table');

            $count = $this->con->table('reschedule')->count();
            $offset = 0;
            $limit = 500;

            for ($offset; $offset < $count; $offset = ($offset + $limit)) {
                $reschedules = $this->con->table('reschedule')
                    ->skip($offset)->limit($limit)->get();
                $rescheduleArray = [];

                foreach ($reschedules as $reschedule) {
                    $newReschedule = [
                        'id'          => $reschedule['id'],
                        'loan_id'     => $reschedule['loan_id'],
                        'borrower_id' => $reschedule['borrower_id'],
                        'reason'      => $reschedule['reschedule_reason'],
                        'period'      => $reschedule['period'],
                        'created_at'  => date("Y-m-d H:i:s", $reschedule['date'])
                    ];

                    array_push($rescheduleArray, $newReschedule);
                }
                DB::table('reschedules')->insert($rescheduleArray);
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
