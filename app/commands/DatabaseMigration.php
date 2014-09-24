<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Zidisha\Balance\Transaction;
use Zidisha\Currency\Currency;
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

            $this->call('migrate-zidisha1', array('table' => 'languages'));
            $this->call('migrate-zidisha1', array('table' => 'countries'));
            $this->call('migrate-zidisha1', array('table' => 'users'));
            $this->call('migrate-zidisha1', array('table' => 'lenders'));
            $this->call('migrate-zidisha1', array('table' => 'borrowers'));
            $this->call('migrate-zidisha1', array('table' => 'volunteer_mentors'));
            $this->call('migrate-zidisha1', array('table' => 'loan_categories'));
            $this->call('migrate-zidisha1', array('table' => 'loans'));
            $this->call('migrate-zidisha1', array('table' => 'loan_stages'));
            $this->call('migrate-zidisha1', array('table' => 'exchange_rates'));
            $this->call('migrate-zidisha1', array('table' => 'installments'));
            $this->call('migrate-zidisha1', array('table' => 'installment_payments'));
            $this->call('migrate-zidisha1', array('table' => 'facebook_users'));
            $this->call('migrate-zidisha1', array('table' => 'borrower_invites'));
            $this->call('migrate-zidisha1', array('table' => 'reschedules'));
            $this->call('migrate-zidisha1', array('table' => 'borrower_comments'));
            dd('TODO: other models');
            $this->call('migrate-zidisha1', array('table' => 'loan_bids'));
            $this->call('migrate-zidisha1', array('table' => 'admin_notes'));
            $this->call('migrate-zidisha1', array('table' => 'transactions'));
            $this->call('migrate-zidisha1', array('table' => 'borrower_payments'));
            $this->call('migrate-zidisha1', array('table' => 'lender_invites'));
            $this->call('migrate-zidisha1', array('table' => 'lender_invite_visits'));
            $this->call('migrate-zidisha1', array('table' => 'lender_invite_transactions'));
            $this->call('migrate-zidisha1', array('table' => 'paypal_ipn_log'));
            $this->call('migrate-zidisha1', array('table' => 'paypal_transactions'));
            $this->call('migrate-zidisha1', array('table' => 'gift_cards'));
            $this->call('migrate-zidisha1', array('table' => 'gift_card_transaction'));
            $this->call('migrate-zidisha1', array('table' => 'forgiveness_loan_shares'));
            $this->call('migrate-zidisha1', array('table' => 'forgiveness_loans'));
            $this->call('migrate-zidisha1', array('table' => 'borrower_refunds'));
            $this->call('migrate-zidisha1', array('table' => 'borrower_feedback_messages'));
            $this->call('migrate-zidisha1', array('table' => 'borrower_reviews'));
            $this->call('migrate-zidisha1', array('table' => 'lending_groups'));
            $this->call('migrate-zidisha1', array('table' => 'lending_group_members'));
            $this->call('migrate-zidisha1', array('table' => 'notifications'));
            $this->call('migrate-zidisha1', array('table' => 'withdrawal_requests'));
            $this->call('migrate-zidisha1', array('table' => 'followers'));
            $this->call('migrate-zidisha1', array('table' => 'credit_settings'));
            $this->call('migrate-zidisha1', array('table' => 'credits_earned'));
            $this->call('migrate-zidisha1', array('table' => 'auto_lending_settings'));
            $this->call('migrate-zidisha1', array('table' => 'statistics'));
            $this->call('migrate-zidisha1', array('table' => 'bulk_emails'));
            $this->call('migrate-zidisha1', array('table' => 'bulk_email_recipients'));

        }

        if ($table == 'users') {
            $this->line('Migrate users table');

            $count = $this->con->table('users')->count();
            
            $limit = 500;
            for ($offset = 0; $offset < $count; $offset += $limit) {
                $users = $this->con->table('users as u')
                    ->select(
                        'u.*',
                        'l.userid as lUserid',
                        'l.Email as lEmail',
                        'l.Active as lActive',
                        'l.isTranslator as isTranslator',
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

                $sub_role = null;
                foreach ($users as $user) {
                    // lender
                    if ($user->lUserid !== null) {
                        $active = $user->lActive ? true : false;
                        $email = $user->lEmail;
                        $role = 0;
                        $sub_role = $user->isTranslator ? 0 : null; // volunteer
                    }
                    // borrower
                    elseif ($user->bUserid !== null) {
                        $active = $user->lActive ? true : false;
                        $email = $user->bEmail;
                        $role = 1;
                    }
                    elseif ($user->userid == 92) {
                        $active = true;
                        $email = 'admin@zidisha.org';
                        $role = 3;
                    }
                    elseif ($user->userid == 1690) { // user adminread, only user with read-only sublevel
                        $active = false;
                        $email = '';
                        $role = 3;
                    }
                    // partner
                    elseif ($user->pUserid !== null) {
                        $active = false; //$user->pActive ? true : false;
                        $email = $user->pEmail;
                        $role = 2;
                    }
                    else {
                        $active = false;
                        $email = '';
                        $role = $roles[$user->userlevel];
                    }
                                        
                    $newUser = [
                        'id'                 => $user->userid,
                        'username'           => $user->username,
                        'email'              => $email,
                        'password'           => $user->password, // TODO old password, salt column
                        'profile_picture_id' => null, // TODO
                        'facebook_id'        => $user->facebook_id ?: null,
                        'google_id'          => null, // since google login is now added
                        'google_picture'     => null,
                        'remember_token'     => null, // this cannot be shared between old and new codebase
                        'role'               => $role,
                        'sub_role'           => $sub_role,
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
            //$this->line('Migrate lending_group_notifications table');

            $count = $this->con->table('lenders')->count();
            $limit = 500;
            for ($offset = 0; $offset < $count; $offset += $limit) {

                $lenders = $this->con->table('lenders')
                    ->select('lenders.*', 'countries.id as country_id')
                    ->leftJoin('countries', 'lenders.Country', '=', 'countries.code')
                    ->skip($offset)->take($limit)->get();
                $lenderArray = [];
                $profileArray = [];
                $preferenceArray = [];
                $lendingGroupNotificationArray = [];

                foreach ($lenders as $lender) {
                    $newLender = [
                        'id'                  => $lender->userid,
                        'country_id'          => $lender->country_id ?: 202, // TODO default if country missing
                        'first_name'          => $lender->FirstName,
                        'last_name'           => $lender->LastName,
                        'admin_donate'        => $lender->admin_donate,
                        'active'              => $lender->Active,
                        'last_check_in_email' => $lender->last_check_in_email,
                        'created_at'          => date("Y-m-d H:i:s"),
                        'updated_at'          => date("Y-m-d H:i:s"),
                    ];

                    $profile = [
                        'lender_id'  => $lender->userid,
                        'city'       => $lender->City,
                        'about_me'   => $lender->About,
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s"),
                    ];
                    $preference = [
                        'lender_id'                   => $lender->userid,
                        'hide_lending_activity'       => $lender->hide_Amount,
                        'hide_karma'                  => $lender->hide_karma,
                        'notify_loan_fully_funded'    => $lender->loan_fully_funded_notify,
                        'notify_loan_about_to_expire' => $lender->loan_about_to_expire_notify,
                        'notify_loan_expired'         => $lender->loan_expired_notify,
                        'notify_loan_disbursed'       => $lender->loan_disbursed_notify,
                        'notify_comment'              => $lender->emailcomment,
                        'notify_loan_application'     => $lender->loan_app_notify,
                        'notify_invite_accepted'      => $lender->invite_notify,
                        'notify_loan_repayment'       => $lender->email_loan_repayment,
                        'created_at'                  => date("Y-m-d H:i:s"),
                        'updated_at'                  => date("Y-m-d H:i:s"),
                    ];

                    array_push($lenderArray, $newLender);
                    array_push($profileArray, $profile);
                    array_push($preferenceArray, $preference);

                    $groupIds = explode(',',$lender->groupmsg_notify);
                    foreach ($groupIds as $groupId) {
                        $newGroupNotification = [
                            'lending_group_id' => $groupId,
                            'user_id'          => $lender->userid
                        ];

                        array_push($lendingGroupNotificationArray, $newGroupNotification);
                    }
                }
                DB::table('lenders')->insert($lenderArray);
                DB::table('lender_profiles')->insert($profileArray);
                DB::table('lender_preferences')->insert($preferenceArray);
                if ($lendingGroupNotificationArray) {
                    // TODO do this when importing lending groups (Foreign key violation)
                    //DB::table('lending_group_notifications')->insert($lendingGroupNotificationArray);
                }
            }
        }

        if ($table == 'borrowers') {
            $this->line('Migrate borrowers table');
            $this->line('Migrate borrower_profiles table');
            $this->line('Migrate borrower_contacts table');
            $this->line('Migrate borrower_join_logs table');

            $userIdToReferrerId = [];
            $_nonExistingReferrers = $this->con->table('borrowers AS b') 
                ->select('b.refer_member_name')
                ->leftJoin('borrowers AS bb', 'b.refer_member_name', '=', 'bb.userid')
                ->where('b.refer_member_name', '>', 0)
                ->whereRaw('bb.userid IS NULL')
                ->get();
            $nonExistingReferrers = [];
            foreach ($_nonExistingReferrers as $_nonExistingReferrer) {
                $nonExistingReferrers[$_nonExistingReferrer->refer_member_name] = $_nonExistingReferrer->refer_member_name;
            }
            
            $count = $this->con->table('borrowers')->count();
            $limit = 500;
            for ($offset = 0; $offset < $count; $offset += $limit) {
                $borrowers = $this->con->table('borrowers')
                    ->select(
                        'borrowers.*',
                        'users.emailVerified',
                        'users.password',
                        'users.salt',
                        'countries.id AS country_id',
                        'borrowers_extn.community_leader_first_name',
                        'borrowers_extn.community_leader_last_name',
                        'borrowers_extn.community_leader_mobile_phone',
                        'borrowers_extn.community_leader_organization_title',
                        'borrowers_extn.family_member1_first_name',
                        'borrowers_extn.family_member1_last_name',
                        'borrowers_extn.family_member1_mobile_phone',
                        'borrowers_extn.family_member1_relationship',
                        'borrowers_extn.family_member2_first_name',
                        'borrowers_extn.family_member2_last_name',
                        'borrowers_extn.family_member2_mobile_phone',
                        'borrowers_extn.family_member2_relationship',
                        'borrowers_extn.family_member3_first_name',
                        'borrowers_extn.family_member3_last_name',
                        'borrowers_extn.family_member3_mobile_phone',
                        'borrowers_extn.family_member3_relationship',
                        'borrowers_extn.neighbor1_first_name',
                        'borrowers_extn.neighbor1_last_name',
                        'borrowers_extn.neighbor1_mobile_phone',
                        'borrowers_extn.neighbor1_relationship',
                        'borrowers_extn.neighbor2_first_name',
                        'borrowers_extn.neighbor2_last_name',
                        'borrowers_extn.neighbor2_mobile_phone',
                        'borrowers_extn.neighbor2_relationship',
                        'borrowers_extn.neighbor3_first_name',
                        'borrowers_extn.neighbor3_last_name',
                        'borrowers_extn.neighbor3_mobile_phone',
                        'borrowers_extn.neighbor3_relationship'
//                        'facebook_info.ip_address AS ip_address'
                    )
                    ->join('users', 'borrowers.userid', '=', 'users.userid')
                    ->join('countries', 'borrowers.Country', '=', 'countries.code')
                    ->join('borrowers_extn', 'borrowers.userid', '=', 'borrowers_extn.userid')
//                    ->join('facebook_info', 'borrowers.userid', '=', 'facebook_info.userid')
                    ->orderBy('borrowers.userid', 'asc')
                    ->skip($offset)
                    ->take($limit)
                    ->get();
                
                $borrowerArray = [];
                $profileArray = [];
                $contactArray = [];
                $borrowerJoinLogArray = [];
                
                $activationStatus = [
                    0 => 0, // pending
                    -1 => 4, // declined
                    -2 => 3, // assigned to partner and approved
//                     => 1, // incomplete
//                     => 3, // reviewed
                    1 => 3, // approved
                    2 => 4, // declined
                ];

                foreach ($borrowers as $borrower) {
                    $referrerId = $borrower->refer_member_name ?: null;
                    if (isset($nonExistingReferrers[$referrerId])) {
                        $referrerId = null;
                    }
                    elseif ($borrower->userid < $referrerId) {
                        $userIdToReferrerId[$borrower->userid] = $referrerId;
                        $referrerId = null;
                    }
                    
                    $newBorrower = [
                        'id'                  => $borrower->userid,
                        'country_id'          => $borrower->country_id,
                        'first_name'          => $borrower->FirstName,
                        'last_name'           => $borrower->LastName,
                        'active_loan_id'      => null, // TODO do in cache step
                        'last_loan_id'        => null, // TODO do in cache step
                        'loan_status'         => null, // TODO in cache step $borrower->ActiveLoan,
                        'active'              => $borrower->Active,
                        'volunteer_mentor_id' => null, // we do it when importing volunteer mentors
                        'referrer_id'         => $referrerId,
                        //'referrer_by'         => '', TODO
                        'verified'            => $borrower->emailVerified,
                        'activation_status'   => $activationStatus[$borrower->Assigned_status],
                        'created_at'          => date("Y-m-d H:i:s", $borrower->Created),
                        'updated_at'          => date("Y-m-d H:i:s", $borrower->LastModified),
                        //'sift_science_score'          => $borrower->sift_score, TODO
                    ];

                    $profile = [
                        'borrower_id'                => $borrower->userid,
                        'about_me'                   => $borrower->About ?: '',
                        'about_me_translation'       => $borrower->tr_About,
                        'about_business'             => $borrower->BizDesc ?: '',
                        'about_business_translation' => $borrower->tr_BizDesc,
                        'address'                    => $borrower->PAddress ?: '',
                        'address_instructions'       => $borrower->home_location ?: '',
                        'city'                       => $borrower->City,
                        'referred_by'                => $borrower->reffered_by,
                        'national_id_number'         => $borrower->nationId,
                        'phone_number'               => $this->FormatNumber($borrower->TelMobile, $borrower->Country),
                        'alternate_phone_number'     => $borrower->AlternateTelMobile ? $this->FormatNumber($borrower->AlternateTelMobile, $borrower->Country) : '',
                        //'old_phone_number'           => $borrower->TelMobile,// TODO
                        //'old_alternate_phone_number' => $borrower->AlternateTelMobile ?: '',// TODO
                        'business_category_id'       => null,
                        'business_years'             => null,
                        'loan_usage'                 => null,
                        'birth_date'                 => null,
                        'created_at'                 => date("Y-m-d H:i:s", $borrower->Created),
                        'updated_at'                 => date("Y-m-d H:i:s", $borrower->LastModified),
                    ];

                    $communityLeader = [
                        'borrower_id'  => $borrower->userid,
                        'first_name'   => $borrower->community_leader_first_name,
                        'last_name'    => $borrower->community_leader_last_name,
                        'phone_number' => $borrower->community_leader_mobile_phone,
                        'description'  => $borrower->community_leader_organization_title,
                        'type'         => 2, // communityLeader,
                        'created_at'   => date("Y-m-d H:i:s", $borrower->Created),
                        'updated_at'   => date("Y-m-d H:i:s", $borrower->LastModified),
                    ];

                    for ($i = 1; $i <= 3; $i++) {
                        $stringFirstName = 'family_member'. $i. '_first_name';
                        $stringLastName = 'family_member'. $i. '_last_name';
                        $stringPhoneNumber = 'family_member'. $i. '_mobile_phone';
                        $stringDescription = 'family_member'. $i. '_relationship';

                        if (!$borrower->$stringFirstName && !$borrower->$stringLastName && !$borrower->$stringPhoneNumber) {
                            continue;
                        }
                        $familyMember = [
                            'borrower_id'  => $borrower->userid,
                            'first_name'   => $borrower->$stringFirstName ?: '',
                            'last_name'    => $borrower->$stringLastName ?: '',
                            'phone_number' => $borrower->$stringPhoneNumber ?: '',
                            'description'  => $borrower->$stringDescription ?: '',
                            'type'         => 0, // familyMember
                            'created_at'   => date("Y-m-d H:i:s", $borrower->Created),
                            'updated_at'   => date("Y-m-d H:i:s", $borrower->LastModified),
                        ];
                        array_push($contactArray, $familyMember);
                    }

                    for ($i = 1; $i <= 3; $i++) {
                        $stringFirstName = 'neighbor'. $i. '_first_name';
                        $stringLastName = 'neighbor'. $i. '_last_name';
                        $stringPhoneNumber = 'neighbor'. $i. '_mobile_phone';
                        $stringDescription = 'neighbor'. $i. '_relationship';
                        
                        $neighbor = [
                            'borrower_id'  => $borrower->userid,
                            'first_name'   => $borrower->$stringFirstName ?: '',
                            'last_name'    => $borrower->$stringLastName ?: '',
                            'phone_number' => $borrower->$stringPhoneNumber ?: '',
                            'description'  => $borrower->$stringDescription ?: '',
                            'type'         => 1, // neighbor
                            'created_at'   => date("Y-m-d H:i:s", $borrower->Created),
                            'updated_at'   => date("Y-m-d H:i:s", $borrower->LastModified),
                        ];
                        array_push($contactArray, $neighbor);
                    }

                    $newJoinLog = [
                        'borrower_id'                => $borrower->userid,
                        'ip_address'                 => '', // TODO do in cache step $borrower->ip_address ?: '',
                        'preferred_loan_amount'      => '',
                        'preferred_interest_rate'    => '',
                        'preferred_repayment_amount' => '',
                        'created_at'                 => date("Y-m-d H:i:s", $borrower->Created),
                        'updated_at'                 => date("Y-m-d H:i:s", $borrower->Created),
                        'verification_code'          => null,
                        'sift_science_score'         => $borrower->sift_score,
                    ];

                    if (!$borrower->emailVerified) {
                        $newJoinLog['verification_code'] = md5(md5($borrower->password).$borrower->salt);
                    }

                    array_push($borrowerArray, $newBorrower);
                    array_push($profileArray, $profile);
                    array_push($contactArray, $communityLeader);
                    array_push($borrowerJoinLogArray, $newJoinLog);
                }
                DB::table('borrowers')->insert($borrowerArray);
                DB::table('borrower_profiles')->insert($profileArray);
                DB::table('borrower_contacts')->insert($contactArray);
                DB::table('borrower_join_logs')->insert($borrowerJoinLogArray);
            }
            
            foreach ($userIdToReferrerId as $userId => $referrerId) {
                DB::table('borrowers')->where('id', $userId)->update(['referrer_id' => $referrerId]);
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
                    'id'               => $category->id,
                    'name'             => $category->name,
                    'slug'             => \Illuminate\Support\Str::slug($category->name),
                    'what_description' => $category->what,
                    'why_description'  => $category->why,
                    'how_description'  => $category->lend,
                    'admin_only'       => $category->admin,
                    'sortable_rank'    => $category->sort_order,
                    'created_at'       => date("Y-m-d H:i:s", time()),
                    'updated_at'       => date("Y-m-d H:i:s", time()),
                ];

                array_push($categoryArray, $newCategory);

                $categoryTranslationFR = [
                    'category_id'   => $category->id,
                    'language_code' => 'fr',
                    'translation'   => $category->name,
                ];
                $categoryTranslationID = [
                    'category_id'   => $category->id,
                    'language_code' => 'in',
                    'translation'   => $category->name,
                ];
                array_push($categoryTranslationArray, $categoryTranslationFR);
                array_push($categoryTranslationArray, $categoryTranslationID);
            }
            DB::table('loan_categories')->insert($categoryArray);
            DB::table('loan_category_translations')->insert($categoryTranslationArray);
        }

        if ($table == 'countries') {
            $this->line('Migrate countries table');

            $countries = $this->con->table('countries')
                ->select(
                    'countries.*',
                    'currency.capital',
                    'currency.Currency',
                    'currency.active',
                    'country_lang.lang_code',
                    'repayment_instructions.description AS repayment_instructions'
                )
                ->leftJoin('currency', 'countries.code' , '=', 'currency.country_code')
                ->leftJoin('country_lang', 'countries.code' , '=', 'country_lang.country_code')
                ->leftJoin('repayment_instructions', 'countries.code' , '=', 'repayment_instructions.country_code')
                ->get();
            
            $registrationFees = $this->con->table('registration_fee')
                ->lists('Amount', 'currency_name');

            $countryArray = [];

            foreach ($countries as $country) {
                $newCountry = [
                    'id'                      => $country->id,
                    'name'                    => $country->name,
                    'slug'                    => \Illuminate\Support\Str::slug($country->name),
                    'capital'                 => $country->capital,
                    'continent_code'          => $country->loc,
                    'country_code'            => $country->code,
                    'dialing_code'            => $country->phone ?: '',
//                    'phone_number_length'     => '',
                    'currency_code'           => $country->Currency,
                    'borrower_country'        => $country->active,
                    'registration_fee'        => isset($registrationFees[$country->Currency]) ? $registrationFees[$country->Currency] : null,
//                    'installment_period'      => null,
//                    'installment_amount_step' => '',
//                    'loan_amount_step'        => '',
                    'repayment_instructions'  => $country->repayment_instructions ?: null,
//                    'accept_bids_note'        => null,
                    'language_code'           => $country->lang_code,
                ];

                array_push($countryArray, $newCountry);
            }
            DB::table('countries')->insert($countryArray);
        }

        if ($table == 'loans') {
            $this->line('Migrate loans table');

            $rows = \Zidisha\Borrower\Base\BorrowerQuery::create()
                ->joinCountry()
                ->select(['Borrower.id', 'Country.currency_code'])
                ->find();
            $borrowerIdToCurrencyCode = [];
            foreach ($rows as $row) {
                $borrowerIdToCurrencyCode[$row['Borrower.id']] = $row['Country.currency_code'];
            }

            $count = $this->con->table('loanapplic')->count();
            $limit = 500;
            $autoIncrement = 0;
            for ($offset = 0; $offset < $count; $offset += $limit) {
                $loans = $this->con->table('loanapplic')
                    ->select('loanapplic.*')
                    ->skip($offset)
                    ->take($limit)
                    ->orderBy('loanid')
                    ->get();
                
                $loanArray = [];
                $ids = [];

                foreach ($loans as $loan) {
                    $loan->loanid = $loan->loanid ?: 100000;
                    $ids[] = $loan->loanid;
                    
                    $newLoan = [
                        'id'                    => $loan->loanid,
                        'borrower_id'           => $loan->borrowerid,
                        'summary'               => $loan->summary,
                        'summary_translation'   => $loan->tr_summary,
                        'proposal'              => $loan->loanuse,
                        'proposal_translation'  => $loan->tr_loanuse,
                        'amount'                => $loan->Amount,
                        'total_amount'          => null, // TODO calculate in new code,
                        'paid_amount'           => null, // TODO calculate in new code
                        'usd_amount'            => $loan->reqdamt,
                        'installment_day'       => $loan->installment_day,
                        'max_interest_rate'     => $loan->interest,
                        'lender_interest_rate'  => $loan->finalrate ?: null,
                        'category_id'           => $loan->loan_category_id ?: null,
                        'secondary_category_id' => $loan->secondary_loan_category_id ?: null,
                        'status'                => $loan->active,
                        'applied_at'            => $loan->applydate ? date("Y-m-d H:i:s", $loan->applydate) : null,
                        'accepted_at'           => $loan->AcceptDate ? date("Y-m-d H:i:s", $loan->AcceptDate) : null,
                        'expired_at'            => $loan->expires ? date("Y-m-d H:i:s", $loan->expires) : null,
                        'canceled_at'           => null, // nobody ever cancelled a loan
                        'repaid_at'             => $loan->RepaidDate ? date("Y-m-d H:i:s", $loan->RepaidDate) : null,
                        'authorized_at'         => $loan->auth_date ? date("Y-m-d H:i:s", $loan->auth_date) : null,
                        'authorized_amount'     => $loan->p_amount ?: null,
                        'disbursed_at'          => null,
                        'disbursed_amount'      => $loan->AmountGot ?: null,
                        'forgiven_amount'       => null, // TODO calculate in new code
                        'registration_fee'      => '0',
                        'raised_usd_amount'     => null, // TODO calculate in new code
                        'raised_percentage'     => null, // TODO calculate in new code
                        'paid_percentage'       => null, // TODO calculate in new code
                        'service_fee_rate'      => $loan->WebFee,
                        'extra_days'            => $loan->extra_days,
                        'currency_code'         => $borrowerIdToCurrencyCode[$loan->borrowerid],
                        'installment_period'    => $loan->weekly_inst ? 1 : 0, // 'weekly' : 'monthly',
                        'period'                => $loan->period,
                        'accept_bids_note'      => $loan->accept_bid_note ?: null,
                        'sift_science_score'    => null, // new
                        'deleted_by_admin'      => $loan->adminDelete,
                        'created_at'            => $loan->applydate ? date("Y-m-d H:i:s", $loan->applydate) : null,
                    ];

                    $loanArray[$loan->loanid] = $newLoan;
                    $autoIncrement = $loan->loanid + 1;
                }
                $rows = $this->con->table('transactions')
                    ->whereIn('loanid', $ids)
                    ->where('txn_type', Transaction::DISBURSEMENT)
                    ->lists('TrDate', 'loanid');
                foreach ($rows as $loanId => $date) {
                    $loanArray[$loanId]['disbursed_at'] = date("Y-m-d H:i:s", $date);
                }

                $rows = $this->con->table('transactions')
                    ->whereIn('loanid', $ids)
                    ->where('txn_type', Transaction::REGISTRATION_FEE)
                    ->lists('amount', 'loanid');
                foreach ($rows as $loanId => $amount) {
                    $loanArray[$loanId]['registration_fee'] = -$amount;
                }
                
                DB::table('loans')->insert($loanArray);
            }

            DB::table('loans')->where('id', '=', 100000)->update(['id' => 0]);
            DB::statement("ALTER TABLE loans AUTO_INCREMENT=$autoIncrement");
        }

        if ($table == 'loan_bids') {
            $this->line('Migrate loan_bids table');

            $count = $this->con->table('loanbids')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $bids = $this->con->table('loanbids')
                    ->select('loanbids.*', 'loanapplic.borrowerid')
                    ->join('loanapplic', 'loanbids.loanid', '=', 'loanapplic.loanid')
                    ->skip($offset)->take($limit)->get();
                $bidArray = [];

                foreach ($bids as $bid) {
                    $newBid = [
                        'id'                      => $bid->bidid,
                        'loan_id'                 => $bid->loanid,
                        'lender_id'               => $bid->lenderid,
                        'borrower_id'             => $bid->borrowerid,
                        'bid_amount'              => $bid->bidamount,
                        'interest_rate'           => '', //TODO
                        'active'                  => $bid->active,
                        'accepted_amount'         => $bid->givenamount,
                        'bid_at'                  => date("Y-m-d H:i:s", $bid->biddate),
                        'is_lender_invite_credit' => $bid->use_lender_invite_credit,
                        'is_automated_lending'    => null, //TODO
                        'updated_at'              => date("Y-m-d H:i:s", $bid->modified), //TODO is necessary?
                    ];

                    array_push($bidArray, $newBid);
                }
                DB::table('loan_bids')->insert($bidArray);
            }
        }

        if ($table == 'admin_notes') {
            $this->line('Migrate admin_notes table');

            $count = $this->con->table('loan_notes')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $adminNotes = $this->con->table('loan_notes')
                    ->skip($offset)->limit($limit)->get();
                $adminNoteArray = [];

                foreach ($adminNotes as $adminNote) {
                    $newAdminNote = [
                        'id'          => $adminNote->id,
                        'user_id'     => $adminNote->userid,
                        'loan_id'     => $adminNote->loanid,
                        'borrower_id' => null,
                        'note'        => $adminNote->disbursement_notes,
                        'type'        => 0, //disbursement
                    ];

                    array_push($adminNoteArray, $newAdminNote);
                }
                DB::table('admin_notes')->insert($adminNoteArray);
            }
        }

        if ($table == 'loan_stages') {
            $this->line('Migrate loan_stages table');

            $count = $this->con->table('loanstage')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $stages = $this->con->table('loanstage')
                    ->join('loanapplic', 'loanapplic.loanid', '=', 'loanstage.loanid')
                    ->skip($offset)->take($limit)->get();
                $stageArray = [];

                foreach ($stages as $stage) {
                    $newStage = [
                        'id'          => $stage->id,
                        'loan_id'     => $stage->loanid,
                        'borrower_id' => $stage->borrowerid,
                        'status'      => $stage->status,
                        'start_date'  => date("Y-m-d H:i:s", $stage->startdate),
                        'end_date'    => $stage->enddate ? date("Y-m-d H:i:s", $stage->enddate) : null,
                        'created_at'  => $stage->created,
                        'updated_at'  => $stage->modified,
                    ];

                    array_push($stageArray, $newStage);
                }
                DB::table('loan_stages')->insert($stageArray);
            }
        }

        if ($table == 'transactions') {
            $this->line('Migrate transactions table');

            $count = $this->con->table('transactions')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $transactions = $this->con->table('transactions')
                    ->skip($offset)->take($limit)->get();
                $transactionArray = [];

                foreach ($transactions as $transaction) {
                    $newTransaction = [
                        'id'               => $transaction->id,
                        'user_id'          => $transaction->userid,
                        'amount'           => $transaction->amount,
                        'description'      => $transaction->txn_desc,
                        'loan_id'          => $transaction->loanid,
                        'transaction_date' => date("Y-m-d H:i:s", $transaction->TrDate),
                        'exchange_rate'    => $transaction->conversionrate,
                        'type'             => $transaction->txn_type,
                        'sub_type'         => $transaction->txn_sub_type,
                        'loan_bid_id'      => $transaction->loanbid_id
                    ];

                    array_push($transactionArray, $newTransaction);
                }
                DB::table('transactions')->insert($transactionArray);
            }
        }

        // TODO all type of comments table , till borrower_uploads table
        if ($table == 'borrower_comments') {
            $this->line('Migrate borrower_comments table');

            $count = $this->con->table('zi_comment')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count ; $offset += $limit) {
                $comments = $this->con->table('zi_comment')
                    ->join('borrowers', 'borrowers.userid', '=', 'zi_comment.receiverid') // TODO missing borrowers
                    ->join('users', 'users.userid', '=', 'zi_comment.senderid') // TODO missing users
                    ->orderBy('id', 'asc')
                    ->skip($offset)
                    ->limit($limit)
                    ->get();
                $commentArray = [];

                foreach ($comments as $comment) {
                    $newComment = [
                        'id'                  => $comment->id,
                        'user_id'             => $comment->senderid,
                        'borrower_id'         => $comment->receiverid,
                        'message'             => $comment->message ? : '',
                        'message_translation' => $comment->tr_message ? : null,
                        'translator_id'       => $comment->tr_user ? : null,
                        'parent_id'           => null, // TODO
                        'root_id'             => null, // TODO
                        'level'               => 0, // TODO
                        'removed'             => false,
                        'published'           => $comment->publish,
                        'created_at'          => date("Y-m-d H:i:s", $comment->pub_date),
                        'updated_at'          => $comment->modified ? date("Y-m-d H:i:s", $comment->modified) : null,
                        // TODO reschedule_id
                    ];
                    
                    $commentArray[] = $newComment;
                }
                DB::table('borrower_comments')->insert($commentArray);
            }
        }

        if ($table == 'exchange_rates') {
            $this->line('Migrate exchange_rates table');

            $count = $this->con->table('excrate')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $rates = $this->con->table('excrate')
                    ->select('excrate.*', 'currency.Currency')
                    ->join('currency', 'excrate.currency', '=', 'currency.id')
                    ->skip($offset)
                    ->limit($limit)
                    ->get();
                $rateArray = [];

                foreach ($rates as $rate) {
                    $newRate = [
                        'id'            => $rate->id,
                        'rate'          => $rate->rate,
                        'start_date'    => date("Y-m-d H:i:s", $rate->start),
                        'end_date'      => $rate->stop ? date("Y-m-d H:i:s", $rate->stop) : null,
                        'currency_code' => $rate->Currency
                    ];

                    array_push($rateArray, $newRate);
                }
                DB::table('exchange_rates')->insert($rateArray);
            }
        }

        if ($table == 'installments') {
            $this->line('Migrate installments table');

            $count = $this->con->table('repaymentschedule')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $installments = $this->con->table('repaymentschedule')
                    ->skip($offset)->limit($limit)->get();
                $installmentArray = [];

                foreach ($installments as $installment) {
                    $newInstallment = [
                        'id'          => $installment->id,
                        'borrower_id' => $installment->userid,
                        'loan_id'     => $installment->loanid,
                        'due_date'    => date("Y-m-d H:i:s", $installment->duedate),
                        'amount'      => $installment->amount,
                        'paid_date'   => $installment->paiddate ? date("Y-m-d H:i:s", $installment->paiddate) : null,
                        'paid_amount' => $installment->paidamt,
                        'created_at'  => null,
                        'updated_at'  => null,
                    ];

                    array_push($installmentArray, $newInstallment);
                }
                DB::table('installments')->insert($installmentArray);
            }
        }

        if ($table == 'installment_payments') {
            $this->line('Migrate installment_payments table');

            $rows = \Zidisha\Borrower\Base\BorrowerQuery::create()
                ->joinCountry()
                ->select(['Borrower.id', 'Country.currency_code'])
                ->find();
            $borrowerIdToCurrencyCode = [];
            foreach ($rows as $row) {
                $borrowerIdToCurrencyCode[$row['Borrower.id']] = $row['Country.currency_code'];
            }

            $count = $this->con->table('repaymentschedule_actual')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $payments = $this->con->table('repaymentschedule_actual')
                    ->select('repaymentschedule_actual.*')
                    ->join('repaymentschedule', 'repaymentschedule.id', '=', 'repaymentschedule_actual.rid')
                    ->where('rid', '>', 0)
                    ->skip($offset)->limit($limit)->get();
                $paymentArray = [];

                foreach ($payments as $payment) {
                    $currencyCode = $borrowerIdToCurrencyCode[$payment->userid];

                    $exchangeRateId = null;
                    if ($payment->paiddate) {
                        $paidDate = new \Datetime();
                        $paidDate->setTimestamp($payment->paiddate);
                        $exchangeRateId = \Zidisha\Currency\ExchangeRateQuery::create()
                            ->filterByDate($paidDate)
                            ->filterByCurrencyCode($currencyCode)
                            ->select('id')
                            ->findOne();

                        if (!$exchangeRateId) {
                            $this->line($payment->id);
                        }
                    }

                    if (!$exchangeRateId) {
                        $exchangeRateId = \Zidisha\Currency\ExchangeRateQuery::create()
                            ->findCurrent(Currency::create($currencyCode))
                            ->getId();
                    }

                    $newPayment = [
                        'id'               => $payment->id,
                        'installment_id'   => $payment->rid,
                        'borrower_id'      => $payment->userid,
                        'loan_id'          => $payment->loanid,
                        'paid_date'        => date("Y-m-d H:i:s", $payment->paiddate),
                        'paid_amount'      => $payment->paidamt,
                        'exchange_rate_id' => $exchangeRateId,
                        'created_at'       => null,
                        'updated_at'       => null,
                    ];

                    array_push($paymentArray, $newPayment);
                }
                DB::table('installment_payments')->insert($paymentArray);
            }
        }

        if ($table == 'borrower_payments') {
            $this->line('Migrate borrower_payments table');

            $count = $this->con->table('borrower_payments')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $borrowerPayments = $this->con->table('borrower_payments')
                    ->skip($offset)->limit($limit)->get();
                $borrowerPaymentArray = [];

                foreach ($borrowerPayments as $borrowerPayment) {
                    $newBorrowerPayment = [
                        'id'           => $borrowerPayment->id,
                        'country_code' => $borrowerPayment->country_code,
                        'receipt'      => $borrowerPayment->receipt,
                        'date'         => date("Y-m-d H:i:s", $borrowerPayment->date),
                        'amount'       => $borrowerPayment->amount,
                        'borrower_id'  => $borrowerPayment->borrower_id,
                        'status'       => $borrowerPayment->status,
                        'phone'        => $borrowerPayment->phone,
                        'details'      => $borrowerPayment->details,
                        'error'        => $borrowerPayment->error
                    ];

                    array_push($borrowerPaymentArray, $newBorrowerPayment);
                }
                DB::table('borrower_payments')->insert($borrowerPaymentArray);
            }
        }

        if ($table == 'lender_invites') {
            $this->line('Migrate lender_invites table');

            $count = $this->con->table('lender_invites')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $lenderInvites = $this->con->table('lender_invites')
                    ->skip($offset)->limit($limit)->get();
                $lenderInviteArray = [];

                foreach ($lenderInvites as $lenderInvite) {
                    $newLenderInvite = [
                        'id'         => $lenderInvite->id,
                        'lender_id'  => $lenderInvite->lender_id,
                        'email'      => $lenderInvite->email,
                        'invited'    => $lenderInvite->invited,
                        'hash'       => $lenderInvite->hash,
                        'invitee_id' => $lenderInvite->invitee_id,
                        'created_at' => $lenderInvite->created // because it's already DateTime in old DB
                    ];

                    array_push($lenderInviteArray, $newLenderInvite);
                }
                DB::table('lender_invites')->insert($lenderInviteArray);
            }
        }

        if ($table == 'lender_invite_visits') {
            $this->line('Migrate lender_invite_visits table');

            $count = $this->con->table('lender_invite_visits')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $inviteVisits = $this->con->table('lender_invite_visits')
                    ->skip($offset)->limit($limit)->get();
                $inviteVisitArray = [];

                foreach ($inviteVisits as $inviteVisit) {
                    $newInviteVisit = [
                        'id'               => $inviteVisit->id,
                        'lender_id'        => $inviteVisit->lender_id,
                        'lender_invite_id' => $inviteVisit->lender_invite_id,
                        'share_type'       => $inviteVisit->share_type,
                        'http_referer'     => $inviteVisit->http_referer,
                        'ip_address'       => $inviteVisit->ip_address,
                        'created_at'       => $inviteVisit->created // because it's already DateTime in old DB
                    ];

                    array_push($inviteVisitArray, $newInviteVisit);
                }
                DB::table('lender_invite_visits')->insert($inviteVisitArray);
            }
        }

        if ($table == 'lender_invite_transactions') {
            $this->line('Migrate lender_invite_transactions table');

            $count = $this->con->table('lender_invite_transactions')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $$offset += $limit) {
                $inviteTransactions = $this->con->table('lender_invite_transactions')
                    ->skip($offset)->limit($limit)->get();
                $inviteTransactionArray = [];

                foreach ($inviteTransactions as $inviteTransaction) {
                    $newInviteTransaction = [
                        'id'               => $inviteTransaction->id,
                        'lender_id'        => $inviteTransaction->lender_id,
                        'amount'           => $inviteTransaction->amount,
                        'description'      => $inviteTransaction->txn_desc,
                        'transaction_date' => $inviteTransaction->created, // because it's already DateTime in old DB
                        'type'             => $inviteTransaction->txn_type,
                        'loan_id'          => $inviteTransaction->loan_id,
                        'loan_bid_id'      => $inviteTransaction->loanbid_id
                    ];

                    array_push($inviteTransactionArray, $newInviteTransaction);
                }
                DB::table('lender_invite_transactions')->insert($inviteTransactionArray);
            }
        }

        if ($table == 'paypal_ipn_log') {
            $this->line('Migrate paypal_ipn_log table');

            $count = $this->con->table('paypal_ipn_raw_log')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $paypalIpnLogs = $this->con->table('paypal_ipn_raw_log')
                    ->skip($offset)->limit($limit)->get();
                $paypalIpnLogArray = [];

                foreach ($paypalIpnLogs as $paypalIpnLog) {
                    $newPaypalIpnLog = [
                        'id'         => $paypalIpnLog->id,
                        'log'        => $paypalIpnLog->ipn_data_serialized,
                        'created_at' => date("Y-m-d H:i:s", $paypalIpnLog->created_timestamp)
                    ];

                    array_push($paypalIpnLogArray, $newPaypalIpnLog);
                }
                DB::table('paypal_ipn_log')->insert($paypalIpnLogArray);
            }
        }

        if ($table == 'paypal_transactions') {
            $this->line('Migrate paypal_transactions table');

            $count = $this->con->table('paypal_txns')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $paypalTransactions = $this->con->table('paypal_txns')
                    ->skip($offset)->limit($limit)->get();
                $paypalTransactionArray = [];

                foreach ($paypalTransactions as $paypalTransaction) {
                    $newPaypalTransaction = [
                        'id'                     => $paypalTransaction->invoiceid,
                        'transaction_id'         => $paypalTransaction->txnid,
                        'transaction_type'       => $paypalTransaction->txn_type,
                        'amount'                 => $paypalTransaction->amount,
                        'donation_amount'        => $paypalTransaction->donation,
                        'paypal_transaction_fee' => $paypalTransaction->paypal_tran_fee,
                        'total_amount'           => $paypalTransaction->total_amount,
                        'status'                 => $paypalTransaction->status,
                        'custom'                 => $paypalTransaction->custom,
                        'token'                  => $paypalTransaction->paypaldata,
                        // TODO check if necessary and if yes then viable with created_at new then updated_at
                        'updated_at'             => date("Y-m-d H:i:s", $paypalTransaction->updateddate)
                    ];

                    array_push($paypalTransactionArray, $newPaypalTransaction);
                }
                DB::table('paypal_transactions')->insert($paypalTransactionArray);
            }
        }

        if ($table == 'gift_cards') {
            $this->line('Migrate gift_cards table');

            $count = $this->con->table('gift_cards')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $giftCards = $this->con->table('gift_cards as g')
                    ->select(
                        'g.*',
                        'gt.userid as userId',
                        'gt.id as giftTransactionId')
                    ->join('gift_transaction as gt', 'g.txn_id', '=', 'gt.txn_id') // TODO cross check(is it gift_transaction.txn_id or gift_transaction.id)
                    ->skip($offset)
                    ->limit($limit)
                    ->orderBy('g.txn_id', 'asc')
                    ->get();
                $giftCardArray = [];

                foreach ($giftCards as $giftCard) {
                    $newGiftCard = [
                        'id'                       => $giftCard->id,
                        'lender_id'                => $giftCard->userId,
                        'template'                 => $giftCard->template, // TODO make sure old and new template ids are same
                        'order_type'               => $giftCard->order_type, // TODO check both string are smame
                        'card_amount'              => $giftCard->card_amount,
                        'recipient_email'          => $giftCard->recipient_email,
                        'confirmation_email'       => $giftCard->sender,
                        'recipient_name'           => $giftCard->to_name,
                        'from_name'                => $giftCard->from_name,
                        'message'                  => $giftCard->message,
                        'date'                     => date("Y-m-d H:i:s", $giftCard->date),
                        'expire_date'              => date("Y-m-d H:i:s", $giftCard->exp_date),
                        'card_code'                => $giftCard->card_code,
                        'status'                   => $giftCard->status,
                        'claimed'                  => $giftCard->claimed,
                        'recipient_id'             => $giftCard->claimed_by,
                        'donated'                  => $giftCard->donated,
                        'gift_card_transaction_id' => $giftCard->giftTransactionId // TODO cross check
                    ];

                    array_push($giftCardArray, $newGiftCard);
                }
                DB::table('gift_cards')->insert($giftCardArray);
            }
        }

        if ($table == 'gift_card_transaction') {
            $this->line('Migrate gift_card_transaction table');

            $count = $this->con->table('gift_transaction')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $giftCardTransactions = $this->con->table('gift_transaction')
                    ->skip($offset)->limit($limit)->get();
                $giftCardTransactionArray = [];

                foreach ($giftCardTransactions as $giftCardTransaction) {
                    $newGiftCardTransaction = [
                        'id'               => $giftCardTransaction->id,
                        'transaction_id'   => $giftCardTransaction->txn_id,
                        'transaction_type' => $giftCardTransaction->txn_type,
                        'lender_id'        => $giftCardTransaction->userid,
                        'invoice_id'       => $giftCardTransaction->invoiceid,
                        'status'           => $giftCardTransaction->status,
                        'total_cards'      => $giftCardTransaction->total_cards,
                        'amount'           => $giftCardTransaction->amount,
                        'donation'         => $giftCardTransaction->donation,
                        'date'             => date("Y-m-d H:i:s", $giftCardTransaction->date),
                    ];

                    array_push($giftCardTransactionArray, $newGiftCardTransaction);
                }
                DB::table('gift_card_transaction')->insert($giftCardTransactionArray);
            }
        }

        if ($table == 'forgiveness_loan_shares') {
            $this->line('Migrate forgiveness_loan_shares table');

            $count = $this->con->table('forgiven_loans')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $forgivenessLoanShares = $this->con->table('forgiven_loans')
                    ->skip($offset)->limit($limit)->get();
                $forgivenessLoanShareArray = [];

                foreach ($forgivenessLoanShares as $forgivenessLoanShare) {
                    $newForgivenessLoanShare = [
                        'id'          => $forgivenessLoanShare->id,
                        'loan_id'     => $forgivenessLoanShare->loan_id,
                        'lender_id'   => $forgivenessLoanShare->lender_id,
                        'borrower_id' => $forgivenessLoanShare->borrower_id,
                        'amount'      => $forgivenessLoanShare->amount,
                        'usdAmount'   => $forgivenessLoanShare->damount,
                        'is_accepted' => $forgivenessLoanShare->tnc,
                        'date'        => date("Y-m-d H:i:s", $forgivenessLoanShare->date)
                    ];

                    array_push($forgivenessLoanShareArray, $newForgivenessLoanShare);
                }
                DB::table('forgiveness_loan_shares')->insert($forgivenessLoanShareArray);
            }
        }

        if ($table == 'forgiveness_loans') {
            $this->line('Migrate forgiveness_loans table');

            $count = $this->con->table('loans_to_forgive')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $forgivenessLoans = $this->con->table('loans_to_forgive')
                    ->skip($offset)->limit($limit)->get();
                $forgivenessLoanArray = [];

                foreach ($forgivenessLoans as $forgivenessLoan) {
                    $newForgivenessLoan = [
                        'loan_id'           => $forgivenessLoan->loanid,
                        'borrower_id'       => $forgivenessLoan->borrowerid,
                        'comment'           => $forgivenessLoan->comment,
                        'verification_code' => $forgivenessLoan->validation_code,
                        'is_reminder_sent'  => $forgivenessLoan->reminder_sent
                    ];

                    array_push($forgivenessLoanArray, $newForgivenessLoan);
                }
                DB::table('forgiveness_loans')->insert($forgivenessLoanArray);
            }
        }

        if ($table == 'borrower_refunds') {
            $this->line('Migrate borrower_refunds table');

            $count = $this->con->table('borrower_refunds')->count();
            $limit = 500;

            for ($offset = 0; $offset <$count; $offset += $limit) {
                $borrowerRefunds = $this->con->table('borrower_refunds')
                    ->skip($offset)->limit($limit)->get();
                $borrowerRefundArray = [];

                foreach ($borrowerRefunds as $borrowerRefund) {
                    $newBorrowerRefund = [
                        'id'                  => $borrowerRefund->id,
                        'amount'              => $borrowerRefund->amount,
                        'borrower_id'         => $borrowerRefund->borrower_id,
                        'loan_id'             => $borrowerRefund->loan_id,
                        'borrower_payment_id' => $borrowerRefund->borrower_payment_id,
                        'refunded'            => $borrowerRefund->refunded,
                        'created_at'          => $borrowerRefund->created // because it's already DateTime in old DB
                    ];

                    array_push($borrowerRefundArray, $newBorrowerRefund);
                }
                DB::table('borrower_refunds')->insert($borrowerRefundArray);
            }
        }

        if ($table == 'volunteer_mentors') {
            $this->line('Migrate volunteer_mentors table');
            $ids = $activeIds = [];
            $menteeCount = [];

            $count = $this->con->table('community_organizers')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $volunteerMentors = $this->con->table('community_organizers')
                    ->select('community_organizers.*', 'countries.id AS country_id', 'borrowers.Active')
                    ->join('countries', 'community_organizers.country', '=', 'countries.code')
                    ->join('borrowers', 'community_organizers.user_id', '=', 'borrowers.userid')
                    ->skip($offset)
                    ->limit($limit)
                    ->get();
                $volunteerMentorArray = [];

                foreach ($volunteerMentors as $volunteerMentor) {
                    $newVolunteerMentor = [
                        'borrower_id' => $volunteerMentor->user_id,
                        'country_id'  => $volunteerMentor->country_id,
                        'grant_date'  => date("Y-m-d H:i:s", $volunteerMentor->grant_date),
                        'created_at'  => date("Y-m-d H:i:s", $volunteerMentor->grant_date),
                        'updated_at'  => date("Y-m-d H:i:s", $volunteerMentor->grant_date),
                        'note'        => $volunteerMentor->note,
                        'active'      => $volunteerMentor->status
                    ];

                    array_push($volunteerMentorArray, $newVolunteerMentor);
                    $ids[] = $volunteerMentor->user_id;
                    if ($volunteerMentor->Active && $volunteerMentor->status) {
                        $activeIds[]  = $volunteerMentor->user_id;
                    }
                    $menteeCount[$volunteerMentor->user_id] = 0;
                }
                DB::table('volunteer_mentors')->insert($volunteerMentorArray);
            }

            $rows = $this->con->table('borrowers_extn AS be')
                ->select('be.userid', 'be.mentor_id')
                ->join('borrowers', 'borrowers.userid', '=', 'be.userid')
                ->whereIn('be.mentor_id', $ids)
                ->where('borrowers.Active', '=', 1)
                ->get();
            
            foreach ($rows as $row) {
                DB::table('borrowers')->where('id', $row->userid)->update(['volunteer_mentor_id' => $row->mentor_id]);
                $menteeCount[$row->mentor_id] += 1;
            }

            foreach ($menteeCount as $userId => $count) {
                DB::table('volunteer_mentors')->where('borrower_id', $userId)->update(['mentee_count' => $count]);
            }

            DB::table('users')->whereIn('id', $activeIds)->update(['sub_role' => 1]);
        }

        if ($table == 'borrower_feedback_messages') {
            $this->line('Migrate borrower_feedback_messages table');

            $count = $this->con->table('borrower_reports')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $feedbackMessages = $this->con->table('borrower_reports')
                    ->skip($offset)->limit($limit)->get();
                $feedbackMessageArray = [];

                foreach ($feedbackMessages as $feedbackMessage) {
                    $newFeedbackMessage = [
                        'borrower_id'    => $feedbackMessage->borrower_id,
                        'type'           => null, //TODO
                        'borrower_email' => '', //TODO
                        'cc'             => $feedbackMessage->cc,
                        'reply_to'       => $feedbackMessage->replyto,
                        'subject'        => $feedbackMessage->subject,
                        'message'        => $feedbackMessage->message,
                        'sent_at'        => date("Y-m-d H:i:s", $feedbackMessage->sent_on),
                        'sender_name'    => '', //TODO
                        'loan_id'        => $feedbackMessage->loanid
                    ];

                    array_push($feedbackMessageArray, $newFeedbackMessage);
                }
                DB::table('borrower_feedback_messages')->insert($feedbackMessageArray);
            }
        }

        if ($table == 'borrower_reviews') {
            $this->line('Migrate borrower_reviews table');

            $count = $this->con->table('borrower_review')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $borrowerReviews = $this->con->table('borrower_review')
                    ->skip($offset)->limit($limit)->get();
                $borrowerReviewArray = [];

                foreach ($borrowerReviews as $borrowerReview) {
                    $newBorrowerReview = [
                        'borrower_id'               => $borrowerReview->borrower_id,
                        'is_photo_clear'            => $borrowerReview->is_photo_clear,
                        'is_desc_clear'             => $borrowerReview->is_desc_clear,
                        'is_address_locatable'      => $borrowerReview->is_addr_locatable,
                        'is_address_locatable_note' => '', //TODO
                        'is_number_provided'        => $borrowerReview->is_number_provided,
                        'is_nat_id_uploaded'        => $borrowerReview->is_nat_id_uploaded,
                        'is_rec_form_uploaded'      => $borrowerReview->is_rec_form_uploaded,
                        'is_rec_form_offcr_name'    => $borrowerReview->is_rec_form_offcr_name,
                        'is_pending_mediation'      => $borrowerReview->is_pending_mediation,
                        'created_by'                => $borrowerReview->created_by,
                        'modified_by'               => $borrowerReview->modified_by
                    ];

                    array_push($borrowerReviewArray, $newBorrowerReview);
                }
                DB::table('borrower_reviews')->insert($borrowerReviewArray);
            }
        }

        if ($table == 'languages') {
            $this->line('Migrate languages table');

            $count = $this->con->table('language')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $languages = $this->con->table('language')
                    ->skip($offset)->limit($limit)->get();
                $languageArray = [];

                foreach ($languages as $language) {
                    $newLanguage = [
                        'language_code' => $language->langcode,
                        'name'          => $language->lang,
                        'active'        => $language->active,
                    ];

                    array_push($languageArray, $newLanguage);
                }
                DB::table('languages')->insert($languageArray);
            }
        }

        if ($table == 'lending_groups') {
            $this->line('Migrate lending_groups table');

            $count = $this->con->table('lender_groups')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $lendingGroups = $this->con->table('lender_groups')
                    ->skip($offset)->limit($limit)->get();
                $lendingGroupArray = [];

                foreach ($lendingGroups as $lendingGroup) {
                    $newLendingGroup = [
                        'id'                       => $lendingGroup->id,
                        'name'                     => $lendingGroup->name,
                        'website'                  => $lendingGroup->website,
                        'group_profile_picture_id' => $lendingGroup->image, //TODO with upload things
                        'about'                    => $lendingGroup->about_grp,
                        'creator_id'               => $lendingGroup->created_by,
                        'leader_id'                => $lendingGroup->grp_leader,
                        'created_at'               => $lendingGroup->created,
                        'updated_at'              => $lendingGroup->modified
                    ];

                    array_push($lendingGroupArray, $newLendingGroup);
                }
                DB::table('lending_groups')->insert($lendingGroupArray);
            }
        }

        if ($table == 'lending_group_members') {
            $this->line('Migrate lending_group_members table');

            $count = $this->con->table('lending_group_members')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $groupMembers = $this->con->table('lending_group_members')
                    ->skip($offset)->limit($limit)->get();
                $groupMemberArray = [];

                foreach ($groupMembers as $groupMember) {
                    $newGroupMember = [
                        'id'          => $groupMember->id,
                        'group_id'    => $groupMember->group_id,
                        'member_id'   => $groupMember->member_id,
                        'leaved'      => $groupMember->leaved,
                        'created_at'  => $groupMember->created,
                        'updated_at' => $groupMember->modified
                    ];

                    array_push($groupMemberArray, $newGroupMember);
                }
                DB::table('lending_group_members')->insert($groupMemberArray);
            }
        }

        if ($table == 'notifications') {
            $this->line('Migrate notifications table');

            $count = $this->con->table('notification_history')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $notifications = $this->con->table('notification_history')
                    ->skip($offset)->limit($limit)->get();
                $notificationArray = [];

                foreach ($notifications as $notification) {
                    $newNotification = [
                        'id'         => $notification->id,
                        'type'       => $notification->type,
                        'user_id'    => $notification->userid,
                        'created_at' => $notification->created
                    ];

                    array_push($notificationArray, $newNotification);
                }
                DB::table('notifications')->insert($notificationArray);
            }
        }

        if ($table == 'withdrawal_requests') {
            $this->line('Migrate withdrawal_requests table');

            $count = $this->con->table('withdraw')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $withdrawalRequests = $this->con->table('withdraw')
                    ->skip($offset)->limit($limit)->get();
                $withdrawalRequestArray = [];

                foreach ($withdrawalRequests as $withdrawalRequest) {
                    $newWithdrawalRequest = [
                        'id'           => $withdrawalRequest->id,
                        'lender_id'    => $withdrawalRequest->userid,
                        'amount'       => $withdrawalRequest->amount,
                        'paid'         => $withdrawalRequest->paid,
                        'paypal_email' => $withdrawalRequest->paypalemail
                    ];

                    array_push($withdrawalRequestArray, $newWithdrawalRequest);
                }
                DB::table('withdrawal_requests')->insert($withdrawalRequestArray);
            }
        }

        if ($table == 'followers') {
            $this->line('Migrate followers table');

            $count = $this->con->table('followers')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $followers = $this->con->table('followers')
                    ->skip($offset)->limit($limit)->get();
                $followerArray = [];

                foreach ($followers as $follower) {
                    $newFollower = [
                        'id'                      => $follower->id,
                        'lender_id'               => $follower->lender_id,
                        'borrower_id'             => $follower->borrower_id,
                        'active'                  => !$follower->deleted,
                        'notify_comment'          => $follower->comment_notify, // TODO cross check, if !$value
                        'notify_loan_application' => $follower->new_loan_notify, // TODO cross check, if !$value
                        'created_at'              => $follower->created,
                        'updated_at'              => $follower->modified
                    ];

                    array_push($followerArray, $newFollower);
                }
                DB::table('followers')->insert($followerArray);
            }
        }

        if ($table == 'borrower_invites') {
            $this->line('Migrate borrower_invites table');

            $count = $this->con->table('invites')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $borrowerInvites = $this->con->table('invites')
                    ->join('borrowers', 'borrowers.userid', '=', 'invites.userid') // TODO missing borrowers, lenders?
                    ->skip($offset)->limit($limit)->get();
                $borrowerInviteArray = [];

                foreach ( $borrowerInvites as $borrowerInvite) {
                    $newBorrowerInvite =  [
                        'id'          => $borrowerInvite->id,
                        'borrower_id' => $borrowerInvite->userid,
                        'email'       => $borrowerInvite->email,
                        'invited'     => $borrowerInvite->visited, // TODO cross check
                        'hash'        => '', // $borrowerInvite->cookie_value, // TODO
                        'invitee_id'  => $borrowerInvite->invitee_id ?: null
                    ];

                    array_push($borrowerInviteArray, $newBorrowerInvite);
                }
                DB::table('borrower_invites')->insert($borrowerInviteArray);
            }
        }

        if ($table == 'credit_settings') {
            $this->line('Migrate credit_settings table');

            $count = $this->con->table('credit_setting')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $creditSettings = $this->con->table('credit_setting')
                    ->skip($offset)->limit($limit)->get();
                $creditSettingArray = [];

                foreach ($creditSettings as $creditSetting) {
                    $newCreditSetting = [
                        'id'                => $creditSetting->id,
                        'country_code'      => $creditSetting->country_code,
                        'loan_amount_limit' => $creditSetting->loanamt_limit,
                        'character_limit'   => $creditSetting->character_limit,
                        'comments_limit'    => $creditSetting->comments_limit,
                        'type'              => $creditSetting->type, // TODO, add comments type?
                        'created_at'        => date("Y-m-d H:i:s", $creditSetting->created),
                        'updated_at'        => date("Y-m-d H:i:s", $creditSetting->modified)
                    ];

                    array_push($creditSettingArray, $newCreditSetting);
                }
                DB::table('credit_settings')->insert($creditSettingArray);
            }
        }

        if ($table == 'credits_earned') {
            $this->line('Migrate credits_earned table');

            $count = $this->con->table('credits_earned')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $creditsEarned = $this->con->table('credits_earned')
                    ->skip($offset)->limit($limit)->get();
                $creditEarnedArray = [];

                foreach ($creditsEarned as $creditEarned) {
                    $newCreditEarned = [
                        'id'          => $creditEarned->id,
                        'borrower_id' => $creditEarned->borrower_id,
                        'loan_id'     => $creditEarned->loan_id,
                        'credit_type' => $creditEarned->credit_type, // TODO, add valueSet in table?
                        'ref_id'      => $creditEarned->ref_id,
                        'credit'      => $creditEarned->credit,
                        'created_at'  => date("Y-m-d H:i:s", $creditEarned->created),
                        'updated_at'  => date("Y-m-d H:i:s", $creditEarned->modified)
                    ];

                    array_push($creditEarnedArray, $newCreditEarned);
                }
                DB::table('credits_earned')->insert($creditEarnedArray);
            }
        }

        if ($table == 'bulk_emails') {
            $this->line('Migrate bulk_emails table');

            $count = $this->con->table('bulk_emails')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $bulkEmails = $this->con->table('bulk_emails')
                    ->skip($offset)->limit($limit)->get();
                $bulkEmailArray = [];

                foreach ($bulkEmails as $bulkEmail) {
                    $newBulkEmail = [
                        'id'           => $bulkEmail->id,
                        'sender_email' => $bulkEmail->sender,
                        'subject'      => $bulkEmail->subject,
                        'header'       => $bulkEmail->header,
                        'message'      => $bulkEmail->message,
                        'template'     => $bulkEmail->template,
                        'html'         => $bulkEmail->html,
                        'tag'          => $bulkEmail->tag,
                        'params'       => $bulkEmail->params,
                        'processed_at' => $bulkEmail->processed,
                        'created_at'   => $bulkEmail->created
                    ];

                    array_push($bulkEmailArray, $newBulkEmail);
                }
                DB::table('bulk_emails')->insert($bulkEmailArray);
            }
        }

        if ($table == 'bulk_email_recipients') {
            $this->line('Migrate bulk_email_recipients table');

            $count = $this->con->table('bulk_email_recipients')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $bulkEmailRecipients = $this->con->table('bulk_email_recipients')
                    ->skip($offset)->limit($limit)->get();
                $bulkEmailRecipientArray = [];

                foreach ($bulkEmailRecipients as $bulkEmailRecipient ) {
                    $newBulkEmailRecipient = [
                        'id'            => $bulkEmailRecipient->id,
                        'bulk_email_id' => $bulkEmailRecipient->bulk_email_id,
                        'email'         => $bulkEmailRecipient->email,
                        'processed_at'  => $bulkEmailRecipient->processed
                    ];

                    array_push($bulkEmailRecipientArray, $newBulkEmailRecipient);
                }
                DB::table('bulk_email_recipients')->insert($bulkEmailRecipientArray);
            }
        }

        if ($table == 'facebook_users') {
            $this->line('Migrate facebook_user_logs table');
            $this->line('Migrate facebook_users table');

            $facebookIdToUserId = \Zidisha\User\UserQuery::create()
                ->filterByFacebookId(null, \Propel\Runtime\ActiveQuery\Criteria::ISNOTNULL)
                ->find()
                ->toKeyValue('facebookId', 'id');

            $count = $this->con->table('facebook_info')->count();
            $limit = 500;

            $facebookUserArray = [];

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $facebookUsers = $this->con->table('facebook_info')
                    ->select('facebook_info.*')
                    ->orderBy('date', 'asc')
                    ->skip($offset)->limit($limit)->get();
                $facebookUserLogArray = [];

                foreach ($facebookUsers as $facebookUser) {
                    $facebookData = unserialize($facebookUser->facebook_data);
                    $facebookData += [
                        'user_profile' => [],
                        'posts' => [],
                    ];
                    if (!is_array($facebookData['posts'])) {
                        $facebookData['posts'] = [];
                    }
                    $facebookData['posts'] += [1 => []];
                    $newFacebookUserLog = [
                        'id'              => $facebookUser->id,
                        'facebook_id'     => $facebookUser->facebook_id,
                        'user_id'         => $facebookUser->userid ?: null,
                        'account_name'    => array_get($facebookData['user_profile'], 'name'),
                        'email'           => array_get($facebookData['user_profile'], 'email'),
                        'birth_date'      => array_get($facebookData['user_profile'], 'birthday'),
                        'city'            => array_get($facebookData['user_profile'], 'hometown', ['name' => ''])['name'],
                        'first_post_date' => array_get($facebookData['posts'][1], 'created_time') ? date("Y-m-d H:i:s", array_get($facebookData['posts'][1], 'created_time')): null,
                        'friends_count'   => count(array_get($facebookData, 'user_friends', [])),
                        'accept'          => $facebookUser->accept,
                        'ip_address'      => $facebookUser->ip_address,
                        'created_at'      => date("Y-m-d H:i:s", $facebookUser->date),
                        'updated_at'      => date("Y-m-d H:i:s", $facebookUser->date)
                    ];
                    
                    array_push($facebookUserLogArray, $newFacebookUserLog);

                    if (isset($facebookIdToUserId[$facebookUser->facebook_id])) {
                        $userId = $facebookIdToUserId[$facebookUser->facebook_id];
                        if ($userId == $newFacebookUserLog['user_id']) {
                            $newFacebookUser = $newFacebookUserLog;
                            $newFacebookUser['id'] = $facebookUser->facebook_id;
                            unset($newFacebookUser['facebook_id']);
                            unset($newFacebookUser['created_at']);
                            unset($newFacebookUser['updated_at']);
                            $facebookUserArray[$facebookUser->facebook_id] = $newFacebookUser;
                        }
                    }
                }
                DB::table('facebook_user_logs')->insert($facebookUserLogArray);
            }
            DB::table('facebook_users')->insert($facebookUserArray);
        }

        if ($table == 'auto_lending_settings') {
            $this->line('Migrate auto_lending_settings table');

            $count = $this->con->table('auto_lending')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $autoLendingSettings = $this->con->table('auto_lending')
                    ->skip($offset)->limit($limit)->get();
                $autoLendingSettingArray = [];

                foreach ($autoLendingSettings as $autoLendingSetting) {
                    $newAutoLendingSetting = [
                        'id'                   => $autoLendingSetting->id,
                        'lender_id'            => $autoLendingSetting->lender_id,
                        'preference'           => $autoLendingSetting->preference,
                        'min_desired_interest' => $autoLendingSetting->desired_interest,
                        'max_desired_interest' => $autoLendingSetting->max_desired_interest,
                        'current_allocated'    => $autoLendingSetting->current_allocated,
                        'lender_credit'        => $autoLendingSetting->lender_credit,
                        'active'               => $autoLendingSetting->Active,
                        'last_processed'       => $autoLendingSetting->last_processed,
                        'created_at'           => $autoLendingSetting->created,
                        'updated_at'           => $autoLendingSetting->modified
                    ];

                    array_push($autoLendingSettingArray, $newAutoLendingSetting);
                }
                DB::table('auto_lending_settings')->insert($autoLendingSettingArray);
            }
        }

        if ($table == 'statistics') {
            $this->line('Migrate statistics table');

            $count = $this->con->table('statistics')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $statistics =$this->con->table('statistics')
                    ->skip($offset)->limit($limit)->get();
                $statisticArray = [];

                foreach ($statistics as $statistic) {
                    $newStatistics = [
                        'id'         => $statistic->id,
                        'name'       => $statistic->Name,
                        'value'      => $statistic->value,
                        //TODO croos check for foreign key bcz some country values in old DB are '' (empty string)
                        'country_id' => $statistic->country,
                        'date'       => date("Y-m-d H:i:s", $statistic->date)
                    ];

                    array_push($statisticArray, $newStatistics);
                }
                DB::table('statistics')->insert($statisticArray);
            }
        }

        if ($table == 'reschedules') {
            $this->line('Migrate reschedules table');

            $count = $this->con->table('reschedule')->count();
            $limit = 500;

            for ($offset = 0; $offset < $count; $offset += $limit) {
                $reschedules = $this->con->table('reschedule')
                    ->skip($offset)
                    ->limit($limit)
                    ->get();
                $rescheduleArray = [];

                foreach ($reschedules as $reschedule) {
                    $newReschedule = [
                        'id'          => $reschedule->id,
                        'loan_id'     => $reschedule->loan_id,
                        'borrower_id' => $reschedule->borrower_id,
                        'reason'      => $reschedule->reschedule_reason ?: '',
                        'period'      => $reschedule->period,
                        'created_at'  => date("Y-m-d H:i:s", $reschedule->date)
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

    protected function FormatNumber($telnumber, $country){
        $result=preg_replace("/[^0-9]+/", "", $telnumber);
        $to_number = "";
        if($country=='KE'){
            $to_number = substr($result, -9);
        }
        if($country=='NE'){
            $to_number = substr($result, -8);
        }
        if($country=='SN'){
            $to_number = substr($result, -9);
        }
        if($country=='ID'){
            $to_number = substr($result, -11);
        }
        if($country=='BF'){
            $to_number = substr($result, -8);
        }
        if($country=='GN'){
            $to_number = substr($result, -8);
        }
        if($country=='BJ'){
            $to_number = substr($result, -8);
        }
        if($country=='GH'){
            $to_number = substr($result, -10);
        }
        if($country=='ZM'){
            $to_number = substr($result, -10);
        }
        return $to_number;
    }

}
