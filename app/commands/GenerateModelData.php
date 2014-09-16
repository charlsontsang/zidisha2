<?php

use Carbon\Carbon;
use Faker\Factory as Faker;
use Faker\Generator;
use Illuminate\Console\Command;
use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\Console\Input\InputArgument;
use Zidisha\Admin\Setting;
use Zidisha\Balance\Transaction;
use Zidisha\Balance\TransactionQuery;
use Zidisha\Balance\TransactionService;
use Zidisha\Balance\WithdrawalRequest;
use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Comment\BorrowerComment;
use Zidisha\Comment\Comment;
use Zidisha\Country\Country;
use Zidisha\Country\CountryQuery;
use Zidisha\Country\Language;
use Zidisha\Credit\CreditSetting;
use Zidisha\Currency\Converter;
use Zidisha\Currency\ExchangeRateQuery;
use Zidisha\Currency\Money;

use Zidisha\Lender\GiftCard;
use Zidisha\Lender\Lender;
use Zidisha\Lender\LendingGroup;
use Zidisha\Lender\LendingGroupMember;
use Zidisha\Lender\LendingGroupQuery;
use Zidisha\Lender\Invite;
use Zidisha\Lender\LenderQuery;
use Zidisha\Lender\Preferences;
use Zidisha\Lender\Profile;
use Zidisha\Loan\Bid;
use Zidisha\Loan\Calculator\InstallmentCalculator;
use Zidisha\Loan\Category;
use Zidisha\Loan\CategoryQuery;
use Zidisha\Loan\CategoryTranslation;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanQuery;
use Zidisha\Loan\LoanService;
use Zidisha\Lender\LenderService;
use Zidisha\Payment\UploadFundPayment;
use Zidisha\Repayment\BorrowerPayment;
use Zidisha\Repayment\BorrowerPaymentQuery;
use Zidisha\Repayment\Installment;
use Zidisha\Repayment\InstallmentQuery;
use Zidisha\Repayment\RepaymentService;
use Zidisha\Currency\CurrencyService;
use Zidisha\Loan\Stage;
use Zidisha\User\User;
use Zidisha\Vendor\PropelDB;

class GenerateModelData extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'fake';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Used to generate fake data into a model.';

    /**
     * @var Generator
     */
    protected $faker;

    /**
     * @var LenderService
     */
    protected $lenderService;
    /**
     * @var LoanService
     */
    protected $loanService;

    /**
     * @var TransactionService
     */
    protected $transactionService;
    
    /**
     * @var RepaymentService
     */
    protected $repaymentService;
    /**
     * @var CurrencyService
     */
    protected $currencyService;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        \Config::set('mail.enabled', false);

        $model = $this->argument('model');
        $size = $this->argument('size');
        
        $this->faker = Faker::create();

        $this->lenderService = App::make('\Zidisha\Lender\LenderService');
        $this->loanService = App::make('\Zidisha\Loan\LoanService');
        $this->transactionService = App::make('\Zidisha\Balance\TransactionService');
        $this->repaymentService = App::make('\Zidisha\Repayment\RepaymentService');        
        $this->currencyService = App::make('\Zidisha\Currency\CurrencyService');
        
        $temp = true;
        
        if ($model == 'reset') {
            $this->reset();
            return;
        }

        if ($model == 'base') {
            $this->generateBase();
            return;
        }
        
        if ($model == 'test') {
            $this->reset();
            $this->generateBase();
            return;
        }

        if ($model == 'new') {
            $this->reset();

            $this->line('Generate data');

            $this->generateBase();

            $this->call('fake', array('model' => 'Volunteer', 'size' => 20));
            $this->call('fake', array('model' => 'Lender', 'size' => 100));
            $this->call('fake', array('model' => 'VolunteerMentor', 'size' => 20));
            $this->call('fake', array('model' => 'Borrower', 'size' => 80));

            $this->call('fake', array('model' => 'LendingGroup', 'size' => 50));
            $this->call('fake', array('model' => 'LendingGroupMember', 'size' => 200));

            $this->call('fake', array('model' => 'Transaction', 'size' => 200));

            $this->call('fake', array('model' => 'BorrowerInvite', 'size' => 200));
            $this->call('fake', array('model' => 'LenderInvite', 'size' => 200));
            $this->call('fake', array('model' => 'GiftCard', 'size' => 100));
            
            $this->call('fake', array('model' => 'Loan', 'size' => 70));
            $this->call('fake', array('model' => 'Bid', 'size' => 100));
            $this->call('fake', array('model' => 'FullyFundedLoan', 'size' => 40));
            $this->call('fake', array('model' => 'AcceptBid', 'size' => 1));
            $this->call('fake', array('model' => 'DisburseLoan', 'size' => 1));
            $this->call('fake', array('model' => 'RepaidLoan', 'size' => 1));

            $this->call('fake', array('model' => 'Repayment', 'size' => 1));

            $this->call('fake', array('model' => 'Comment', 'size' => 200));
            $this->call('fake', array('model' => 'WithdrawalRequest', 'size' => 200));
            $this->call('fake', array('model' => 'CreditSetting', 'size' => 1));

//            $this->call('fake', array('model' => 'fakeOneBorrowerRefund', 'size' => 1));

            $this->call('import-translations');

            $this->line('Done!');
            return;
        }

        if ($model == "ClearFbData") {
            $this->line('Deleting all Facebook Data');
            $logs = \Zidisha\User\Base\FacebookUserLogQuery::create()
                ->doDeleteAll();
            if ($logs) {
                $Fb = \Zidisha\User\FacebookUserQuery::create()
                    ->doDeleteAll();
            }
            $this->line('done');
        }

        if ($model == "ClearJobs") {
            $this->line('Deleting all Schedule Jobs');

            $logs = \Zidisha\ScheduledJob\ScheduledJobLogQuery::create()
                ->doDeleteAll();
            if ($logs) {
                $jobs = \Zidisha\ScheduledJob\ScheduledJobQuery::create()
                    ->doDeleteAll();
            }
            $this->line('Done');
        }

        if ($model == 'ClearInstallments') {
            $this->line('clearing installments');
            $installments = InstallmentQuery::create()
                ->doDeleteAll();
        }

        $this->line("Generate $model");

        if ($model == "Language") {
            return $this->generateLanguages();
        }
        
        if ($model == "Country") {
            return $this->generateCountries();
        }

        if ($model == "newLender") {
            $data = [
                'googleId'      => null,
                'googlePicture' => null,
                'firstName'     => null,
                'lastName'      => null,
                'aboutMe'       => null,
                'facebookId'    => null,
                'password'      => null,
                'joinedAt'      => new DateTime(),
            ];

            $user = new User();
            $user
                ->setJoinedAt($data['joinedAt'])
                ->setLastLoginAt($data['joinedAt'])
                ->setPassword('1234')
                ->setEmail('lenderfake@fake.com')
                ->setUsername('fakelender')
                ->setRole('lender')
                ->setGoogleId($data['googleId'])
                ->setFacebookId($data['facebookId'])
                ->setGooglePicture($data['googlePicture']);

            $lender = new Lender();
            $lender
                ->setUser($user)
                ->setCountryId(1)
                ->setFirstName('lender first name')
                ->setLastName('lender last name');

            $profile = new Profile();
            $profile->setAboutMe('about me');
            $lender->setProfile($profile);

            $preferences = new Preferences();
            $lender->setPreferences($preferences);

            $lender->save();
    }
        
        if ($model == "ExchangeRate") {
            return $this->generateExchangeRates();
        }

        if ($model == "SpecialUser") {
            return $this->generateSpecialUsers();
        }
        
        if ($model == "Volunteer") {
            return $this->generateVolunteers($size);
        }

        if ($model == "Lender") {
            return $this->generateLenders($size);
        }

        if ($model == "VolunteerMentor") {
            return $this->generateVolunteerMentors($size);
        }
        
        if ($model == "Borrower") {
            return $this->generateBorrowers($size);
        }

        if ($model == "LendingGroup") {
            return $this->generateLendingGroups($size);
        }

        if ($model == "LendingGroupMember") {
            return $this->generateLendingGroupMembers($size);
        }

        if ($model == "LenderInvite") {
            return $this->generateLenderInvites($size);
        }

        if ($model == "GiftCard") {
            return $this->generateGiftCards($size);
        }

        if ($model == "Category") {
            return $this->generateCategories();
        }

        if ($model == "Loan") {
            return $this->generateLoans($size);
        }

        if ($model == "Bid") {
            return $this->generateBids($size);
        }
        
        if ($model == "FullyFundedLoan") {
            return $this->generateFullyFundedLoans($size);
        }

        if ($model == "AcceptBid") {
            return $this->generateAcceptBid();
        }

        if ($model == "DisburseLoan") {
            return $this->generateDisburseLoan();
        }

        if ($model == "RepaidLoan") {
            return $this->generateRepaidLoan();
        }

        if ($model == "CreditSetting") {
            return $this->generateCreditSetting();
        }

        if ($model == "Repayment") {
            return $this->generateRepayments($size);
        }

        if ($model == "Transaction") {
            return $this->generateTransactions($size);
        }

        if ($model == "InviteeOwnFunds") {
            return $this->generateInviteeOwnFunds();
        }

        if ($model == "fakeOneBorrowerRefund") {
            $payments = BorrowerpaymentQuery::create()
                ->findPKs(array(1,3));
            /** @var $payment BorrowerPayment */
            foreach ( $payments as $payment) {
                $payment->setStatus(Borrower::PAYMENT_FAILED)->save();
                $refund = new \Zidisha\Repayment\BorrowerRefund();
                $refund->setAmount($payment->getAmount())
                    ->setBorrower($payment->getBorrower())
                    ->setLoan($payment->getBorrower()->getActiveLoan())
                    ->setBorrowerPayment($payment);
                $refund->save();
            }
            return true;
        }

        if ($model == "UnusedFunds") {
            $data['username'] = 'lender-unusedfund';
            $data['password'] = '1234567890';
            $data['email'] = 'singularityguy@fastmail.fm';
            $data['countryId'] = '1';

            $lender = $this->lenderService->joinLender($data);
            $user = $lender->getUser();

            $user->setLastLoginAt(Carbon::create()->subMonths(3));
            $user->save();

            $con = PropelDB::getConnection();
            $payment = new UploadFundPayment();
            $payment
                ->setLender($lender)
                ->setTotalAmount(Money::create(60, 'USD'))
                ->setTransactionFee(Money::create(3, 'USD'))
                ->setPaymentMethod('paypal');

            $this->transactionService->addUploadFundTransaction($con, $payment);
        }
        
        if ($model == 'AbandonedUser') {
            $data['username'] = 'lender-abandoned';
            $data['password'] = '1234567890';
            $data['email'] = 'lender-abandoned@mail.com';
            $data['countryId'] = '1';
            $data['joinedAt'] = Carbon::create()->subMonths(15);

            $lender = $this->lenderService->joinLender($data);

            $payment = new UploadFundPayment();
            $payment
                ->setLender($lender)
                ->setTotalAmount(Money::create(60, 'USD'))
                ->setTransactionFee(Money::create(3, 'USD'))
                ->setPaymentMethod('paypal');
            $con = PropelDB::getConnection();
            $this->transactionService->addUploadFundTransaction($con, $payment);
        }
        
        if ($model == 'LoanAboutToExpireReminder') {
            $categoryIds = CategoryQuery::create()
                ->filterByAdminOnly(false)
                ->orderByRank()
                ->select('id')
                ->find()
                ->getData();

            $borrowers = BorrowerQuery::create()
                ->joinWith('Country')
                ->orderById()
                ->find()
                ->getData();

            /** @var Borrower $borrower */
            $borrower = $borrowers[0];
            $currency = $borrower->getCountry()->getCurrency();

            $date = $this->faker->dateTimeBetween('-16 months');
            $exchangeRate = $this->currencyService->getExchangeRate($currency, $date);
            $usdAmount = Money::create($this->faker->numberBetween(50, 400));
            $amount = Converter::fromUSD($usdAmount, $currency, $exchangeRate);

            $isWeekly = $borrower->getCountry()->getInstallmentPeriod() == Loan::WEEKLY_INSTALLMENT;

            $data = [
                'summary'           => $this->faker->sentence(8),
                'proposal'          => $this->faker->paragraph(7),
                'amount'            => $amount->getAmount(),
                'installmentAmount' => $amount->divide($this->faker->numberBetween(6, 16))->getAmount(),
                'currencyCode'      => $borrower->getCountry()->getCurrencyCode(),
                'installmentDay'    => $isWeekly ? $this->faker->dayOfWeek : $this->faker->dayOfMonth,
                'date'              => $date,
                'exchangeRate'      => $exchangeRate,
                'categoryId'        => $this->faker->randomElement($categoryIds),
            ];

            $loan = $this->loanService->applyForLoan($borrower, $data);

            $loan->setAppliedAt(Carbon::create()->subDays(\Setting::get('loan.deadline') - 3));
            $loan->save();
            
            $this->info('Loan Generated with id: '.$loan->getId());
        }

        if ($model == 'NewLenderIntro') {
            $data['username'] = 'lender-Newlender';
            $data['password'] = '1234567890';
            $data['email'] = 'lender-NewLenderIntro@mail.com';
            $data['countryId'] = '1';
            $data['joinedAt'] = Carbon::create()->subDay()->subHour(5);

            $lender = $this->lenderService->joinLender($data);
            $user = $lender->getUser();

            $user->setCreatedAt(Carbon::create()->subDay()->subHour(5));
            $user->save();
        }

//        if ($model == 'AbandonedUser') {
//            $data['username'] = 'lender-AbandonedUser';
//            $data['password'] = '1234567890';
//            $data['email'] = 'lender-AbandonedUser@mail.com';
//            $data['countryId'] = '1';
//
//            $lender = $this->lenderService->joinLender($data);
//            $user = $lender->getUser();
//
//            $user->setCreatedAt(Carbon::create()->subDay());
//            $user->save();
//        }
        
        if ($model == 'LoanFinalArrear') {
            $loan = $this->generateLoanArrear('final');
//            $loan = $this->generateLoanForArrear(14, 12);
            $this->info('Loan Generated with id: '.$loan->getId());
        }

        if ($model == 'AgainRepaymentReminder') {
            $loan = $this->generateLoanForArrear(\Setting::get('loan.repaymentReminderDay'), 12);
            $this->info('Loan Generated with id: '.$loan->getId());
        }

        if ($model == 'LoanFirstArrear') {
            $loan = $this->generateLoanArrear();
//            $loan = $this->generateLoanForArrear(4, 12);
            $this->info('Loan Generated with id: '.$loan->getId());
        }

        if ($model == 'RepaymentReminder') {
            $loan = $this->generateLoanForArrear(1,12, 'addition');
            $this->info('Loan Generated with id: '.$loan->getId());
        }

        if ($model == 'MonthlyLoanArrear') {
            $loan = $this->generateLoanForArrear(30, 12);
            $this->info('Loan Generated with id: '.$loan->getId());
        }

        if ($model == 'CronToRepay') {
            $loan = $this->generateLoanArrear('repay');
//            $loan = $this->generateLoanForArrear(60, 12);
            $this->info('Loan Generated with id: '.$loan->getId());
        }
        
        $randArray = [true, false, false, false, false, true, false, false, false, true, false];

        $allLenders = LenderQuery::create()
            ->orderById()
            ->find();

        $allBorrowers = BorrowerQuery::create()
            ->orderById()
            ->find()
            ->getData();
        
        for ($i = 1; $i <= $size; $i++) {
            if ($model == "Comment") {
                $borrower = $allBorrowers[array_rand($allBorrowers)];
                $user = $allBorrowers[array_rand($allBorrowers)];
                $isTranslated = $randArray[array_rand($randArray)];

                $comment = new BorrowerComment();

                $comment
                    ->setBorrower($borrower)
                    ->setUser($user->getUser())
                    ->setPublished($this->faker->boolean())
                    ->setMessage($this->faker->paragraph(3))
                    ->setLevel(0);

                if ($isTranslated) {
                    $comment->setMessageTranslation($this->faker->paragraph(3))
                        ->setTranslatorId(1);
                } elseif($i<100) {
                    $comment->setUser($borrower->getUser());
                }

                $comment->save();
                $comment->setRootId($comment->getId());
                $comment->save();
            }

            if ($model == "WithdrawalRequest") {
                $lender = $allLenders[array_rand($allLenders->getData())];
                $isPaid = $randArray[array_rand($randArray)];
                $currentBalance = TransactionQuery::create()
                    ->filterByUserId($lender->getUser()->getId())
                    ->getTotalAmount();

                $withdrawalRequest = new WithdrawalRequest();
                $withdrawalRequest->setLender($lender)
                    ->setAmount(Money::create(rand(1, $currentBalance->getAmount())))
                    ->setPaypalEmail($this->faker->email);
                if ($isPaid) {
                    $withdrawalRequest->setPaid(true);
                }
                $withdrawalRequest->save();
            }

            if ($model == "BorrowerInvite") {
                do {
                    $borrower = $allBorrowers[array_rand($allBorrowers)];
                    $invitee = $allBorrowers[array_rand($allBorrowers)];
                } while ($borrower->getId() == $invitee->getId());

                $borrowerInvite = new \Zidisha\Borrower\Invite();
                $borrowerInvite->setBorrower($borrower);
                if (rand( 1, 10) < 9) {
                    $borrowerInvite->setEmail($this->faker->email);
                } else {
                    $borrowerInvite->setInvitee($invitee);
                    $borrowerInvite->setInvited(true);
                    $borrowerInvite->setEmail($invitee->getUser()->getEmail());
                }
                $borrowerInvite->save();
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
            array('model', InputArgument::REQUIRED, 'Model in which you want to insert data'),
            array('size', InputArgument::OPTIONAL, 'Number of entries you want for this model', 10)
        );
    }

    protected function reset()
    {
        try {
            $settings = Setting::getAll();
        } catch (\Exception $e) {
            $settings = [];
        }

        $this->line('Rebuild database');
        DB::statement('DROP DATABASE IF EXISTS homestead');
        DB::statement('CREATE DATABASE IF NOT EXISTS homestead');
        exec('rm -rf app/database/migrations');
        exec('./propel diff');
        exec('./propel migrate');
        exec('./propel build');

        $this->line('Delete loans index');
        exec("curl -XDELETE 'http://localhost:9200/loans/' -s");

        $this->line('Import settings');
        Setting::import($settings);
    }

    protected function generateBase()
    {
        $this->call('fake', array('model' => 'Language', 'size' => 1));
        $this->call('fake', array('model' => 'Country', 'size' => 10));
        $this->call('fake', array('model' => 'ExchangeRate', 'size' => 30));

        $this->call('fake', array('model' => 'SpecialUser', 'size' => 1));
        
        $this->call('fake', array('model' => 'Category', 'size' => 10));
        $this->call('fake', array('model' => 'CategoryTranslation', 'size' => 10));
    }

    protected function generateLanguages()
    {
        $languages = [
            ['in', 'Bahasa Indonesia', true,],
            ['fr', 'Français', true,],
            ['hi', 'Hindi', false,],
            ['en', 'English', false,],
        ];
        
        $return = [];

        foreach ($languages as $data) {
            $language = new Language();
            $language
                ->setLanguageCode($data[0])
                ->setName($data[1])
                ->setActive($data[2])
                ->save();
            $return[] = $language;
        }
        
        return $return;
    }
    
    protected function generateCountries()
    {
        $countries = [
            ['KE', 'Kenya', 'KES', '1000', 'en',],
            ['BJ', 'Benin', 'XOF', '0', 'fr',],
            ['BF', 'Burkina Faso', 'XOF', '0', 'fr',],
            ['GH', 'Ghana', 'GHS', '0', 'fr',],
            ['ID', 'Indonesia', 'IDR', '0', 'in',],
            ['SN', 'Senegal', 'XOF', '0', 'fr',],
            ['IN', 'India', 'INR', '0', 'hi',],
        ];

        $return = [];

        foreach ($countries as $data) {
            $country = new Country();
            $country
                ->setName($data[1])
                ->setCountryCode($data[0])
                ->setContinentCode('AF')
                ->setDialingCode(str_pad($this->faker->numberBetween(1, 200), '3', '0'))
                ->setRegistrationFee(Money::create($data[3], $data[2]))
                ->setBorrowerCountry($data[0] != 'IN')
                ->setCurrencyCode($data[2])
                ->setPhoneNumberLength(9)
                ->setInstallmentPeriod($this->faker->randomElement([Loan::WEEKLY_INSTALLMENT, Loan::MONTHLY_INSTALLMENT]))
                ->setInstallmentAmountStep(100)
                ->setLoanAmountStep(200)
                ->setLanguageCode($data[4]);
            
            $country->save();
            $return[] = $country;
        }
        
        return $return;
    }

    protected function generateSpecialUsers()
    {
        $return = [];
        
        $user = new \Zidisha\User\User();
        $user
            ->setUsername('admin')
            ->setPassword('1234567890')
            ->setEmail('admin@mail.com')
            ->setRole('admin')
            ->setLastLoginAt(new Carbon())
            ->setJoinedAt(new Carbon())
            ->save();
        
        $return[] = $user;

        $user = new \Zidisha\User\User();
        $user
            ->setUsername('YC')
            ->setPassword('1234567890')
            ->setEmail('yc@mail.com')
            ->setLastLoginAt(new Carbon())
            ->setJoinedAt(new Carbon())
            ->save();

        $return[] = $user;
        
        return $return;
    }

    protected function generateLenders($count)
    {
        return \Zidisha\Generate\LenderGenerator::create()
            ->size($count)
            ->generate();
    }

    protected function generateVolunteers($count)
    {
        return \Zidisha\Generate\LenderGenerator::create()
            ->size($count)
            ->volunteer(true)
            ->generate();
    }

    protected function generateVolunteerMentors($count)
    {
        return \Zidisha\Generate\BorrowerGenerator::create()
            ->size($count)
            ->volunteerMentor(true)
            ->generate();
    }

    protected function generateBorrowers($count)
    {
        return \Zidisha\Generate\BorrowerGenerator::create()
            ->size($count)
            ->generate();
    }

    protected function generateLendingGroups($count)
    {
        $lenderIds = LenderQuery::create()
            ->select('id')
            ->find();
        $lenderIds = $lenderIds->getData();

        $return = [];
        
        for ($i = 0; $i < $count; $i++) {
            $creatorId = $this->faker->randomElement($lenderIds);
            
            $group = new LendingGroup();
            $group
                ->setCreatorId($creatorId)
                ->setLeaderId($creatorId)
                ->setAbout($this->faker->paragraph(2))
                ->setName($this->faker->sentence(2));

            $groupMember = new LendingGroupMember();
            $groupMember
                ->setMemberId($group->getLeaderId())
                ->setLendingGroup($group);

            $groupMember->save();
            
            $return[] = $group;
        }

        return $return;
    }

    protected function generateLendingGroupMembers($count)
    {
        $lenderIds = LenderQuery::create()
            ->select('id')
            ->find();
        $lenderIds = $lenderIds->getData();
        
        $lendingGroupIds = LendingGroupQuery::create()
            ->select('id')
            ->find();  
        $lendingGroupIds = $lendingGroupIds->getData();
        
        $return = [];
        $i = 0;

        while ($i < $count) {
            $memberId = $this->faker->randomElement($lenderIds);
            $lendingGroupId = $this->faker->randomElement($lendingGroupIds);
            
            if (isset($return["$memberId-$lendingGroupId"])) {
                continue;
            }

            $group = LendingGroupQuery::create()
                ->findOneById($lendingGroupId);

            $groupMember = new LendingGroupMember();
            $groupMember
                ->setMemberId($memberId)
                ->setLendingGroup($group);
            $groupMember->save();
            
            $return["$memberId-$lendingGroupId"] = $groupMember;
            $i += 1;
        }
        
        return $return;
    }

    protected function generateExchangeRates()
    {
        $currencies = ['KES' => 80, 'XOF' => 20, 'GHS' => 50, 'IDR' => 40, 'INR' => 80];
        
        $return = [];
        
        foreach ($currencies as $currencyCode => $baseRate) {
            for ($i = 60; $i > 0; $i--) {
                $startDate = new Carbon();
                $startDate->subMonths($i);
                
                $endDate = null;
                if ($i > 1) {
                    $endDate = $startDate->copy();
                    $endDate->addMonth()->subSecond();
                }

                $rate = $baseRate + $this->faker->numberBetween(-$baseRate/5, $baseRate/5);
                
                $exchangeRate = new \Zidisha\Currency\ExchangeRate();
                $exchangeRate
                    ->setCurrencyCode($currencyCode)
                    ->setRate($rate)
                    ->setStartDate($startDate)
                    ->setEndDate($endDate);
                $exchangeRate->save();

                $return[] = $exchangeRate;
            }
        }
        
        return $return;
    }

    protected function generateLenderInvites($count)
    {
        $lenderIds = LenderQuery::create()
            ->select('id')
            ->find();
        $lenderIds = $lenderIds->getData();

        $invitees = $this->generateLenders(floor($count/10*3));
       
        $return = [];
       
        for ($i = 0; $i < $count; $i++) {
            $lenderId = $this->faker->randomElement($lenderIds);
            $lenderInvite = new Invite();
            $lenderInvite->setLenderId($lenderId);
            
            if (rand(1, 10) < 4 || !$invitees) {
                $lenderInvite->setEmail($this->faker->email);
            } else {
                $invitee = array_pop($invitees);
                
                $lenderInvite
                    ->setInvitee($invitee)
                    ->setInvited(true)
                    ->setEmail($invitee->getUser()->getEmail());
            }
            $lenderInvite->save();

            $return[] = $lenderInvite;
        }

        return $return;
    }

    protected function generateGiftCards($count)
    {
        $lenders = LenderQuery::create()->find()->getData();

        $return = [];

        for ($i = 0; $i < $count; $i++) {
            /** @var Lender $lender */
            $lender = $this->faker->randomElement($lenders);
            $amount = Money::create(rand(15, 1000), 'USD');
            $faker = Faker::create();
            $date = new Carbon();

            $giftCard = new GiftCard();
            $giftCard
                ->setLender($lender)
                ->setOrderType(array_rand([0,1]))
                ->setCardAmount($amount)
                ->setFromName($lender->getName())
                ->setMessage($faker->sentence(10))
                ->setRecipientEmail($faker->email)
                ->setDate($date)
                ->setExpireDate($date->addYear())
                ->setCardCode($faker->creditCardNumber);

            if (rand(1 ,5) <= 3) {
                /** @var Lender $recipient */
                $recipient = $this->faker->randomElement($lenders);
                if ($recipient != $lender) {
                    $giftCard->setClaimed(true)
                        ->setRecipientName($recipient->getName())
                        ->setRecipient($recipient);
                }
            }

            $giftCard->save();
            $return[] = $giftCard;
        }

        return $return;
    }

    protected function generateCategories()
    {
        $categories = include(app_path() . '/database/LoanCategories.php');
        $allLanguages = \Zidisha\Country\LanguageQuery::create()
            ->filterByActive(true)
            ->find();
        
        $return = [];

        foreach ($categories as $data) {
            $category = new Category();
            $category
                ->setName($data[0])
                ->setWhatDescription($data[1])
                ->setWhyDescription($data[2])
                ->setHowDescription($data[3])
                ->setAdminOnly($data[4]);
            $category->save();

            foreach ($allLanguages as $language) {
                $categoryTranslation = new CategoryTranslation();
                $categoryTranslation
                    ->setCategory($category)
                    ->setLanguage($language)
                    ->setTranslation($category->getName() . ' - ' . $language->getLanguageCode());
                $categoryTranslation->save();
            }
            
            $return[] = $category;
        }
        
        return $return;
    }

    protected function generateLoans($count)
    {
        return \Zidisha\Generate\LoanGenerator::create()
            ->size($count)
            ->generate();
    }

    protected function generateBids($count)
    {
        $lenders = LenderQuery::create()
            ->filterByActive(true)
            ->find()
            ->getData();

        $loans = LoanQuery::create()
            ->filterByStatus(0)
            ->orderByRand()
            ->find()
            ->getData();
        
        $bids = [];
        
        foreach ($loans as $loan) {
            $bidCount = $this->faker->numberBetween(0, 5);
            for ($i = 0; $i < $bidCount; $i ++) {
                $lender = $this->faker->randomElement($lenders);
                $bids[] = \Zidisha\Generate\BidGenerator::create()
                    ->setLender($lender)
                    ->setLoan($loan)
                    ->generateOne();
            }
            
            if (count($bids) == $count) {
                break;
            }
        }
        
        return $bids;
    }
    
    protected function generateFullyFundedLoans($count)
    {
        $lenders = LenderQuery::create()
            ->filterByActive(true)
            ->find()
            ->getData();
        
        $loans = \Zidisha\Generate\LoanGenerator::create()
            ->size($count)
            ->generate();
        
        $bidGenerator = \Zidisha\Generate\BidGenerator::create();
        foreach ($loans as $loan) {
            $bidGenerator->setLoan($loan);
            $bidGenerator->fullyFund($lenders);
        }
        
        return $loans;
    }

    protected function generateAcceptBid()
    {
        $raisedLoans = LoanQuery::create()
            ->filterByRaisedPercentage(100)
            ->find();

        $return = [];
        
        foreach ($raisedLoans as $loan) {
            if (rand(1, 6) <= 5) {
                $acceptedAt = Carbon::instance($loan->getAppliedAt());
                $acceptedAt->addDays($this->faker->numberBetween(15, 20));
                $this->loanService->acceptBids($loan, ['acceptedAt' => $acceptedAt]);
                $return[] = $loan;
            }
        }
        
        return $return;
    }

    protected function generateDisburseLoan()
    {
        $fundedLoans = LoanQuery::create()
            ->filterByStatus(Loan::FUNDED)
            ->find();

        $return = [];

        foreach ($fundedLoans as $loan) {
            if (rand(1, 5) <= 4) {
                $disbursedAt = Carbon::instance($loan->getAcceptedAt());
                $disbursedAt->addDays($this->faker->numberBetween(1, 10));
                $disbursedAmount = $loan->getAmount();
                $this->loanService->disburseLoan($loan, compact('disbursedAt', 'disbursedAmount'));
                $return[] = $loan;
            }
        }
        
        return $return;
    }

    protected function generateRepaidLoan()
    {
//        $disbursedLoans = LoanQuery::create()
//            ->filterByStatus(Loan::ACTIVE)
//            ->find();
//
//        $return = [];
//
//        foreach ($disbursedLoans as $loan) {
//            if (rand(1, 5) <= 4) {
//                $disbursedAt = Carbon::instance($loan->getAcceptedAt());
//                $disbursedAt->addDays($this->faker->numberBetween(1, 10));
//                $disbursedAmount = $loan->getAmount();
//                $this->loanService->disburseLoan($loan, compact('disbursedAt', 'disbursedAmount'));
//                $return[] = $loan;
//            }
//        }
    }

    protected function generateTransactions($count)
    {
        $con = PropelDB::getConnection();
        
        $lenderIds = LenderQuery::create()
            ->filterByActive(true)
            ->select('id')
            ->find()
            ->getData();

        foreach ($lenderIds as $lenderId) {
            $payment = new UploadFundPayment();
            $payment
                ->setLenderId($lenderId)
                ->setTotalAmount(Money::create(rand(100, 1000), 'USD'))
                ->setTransactionFee(Money::create(rand(5, 10), 'USD'))
                ->setPaymentMethod($this->faker->randomElement(['paypal', 'stripe']));

            $this->transactionService->addUploadFundTransaction($con, $payment);
        }

//        for ($i = 0; $i < $count; $i++) {
//            $lenderId = $this->faker->randomElement($lenderIds);
//
//            $payment = new UploadFundPayment();
//            $payment
//                ->setLenderId($lenderId)
//                ->setTotalAmount(Money::create(rand(1000, 1000), 'USD'))
//                ->setTransactionFee(Money::create(rand(5, 10), 'USD'))
//                ->setPaymentMethod($this->faker->randomElement(['paypal', 'stripe']));
//
//            $this->transactionService->addUploadFundTransaction($con, $payment);
//            if (rand(1, 5) == 1) {
//            }

//            if ($temp == true) {
//                $yc = \Zidisha\User\UserQuery::create()
//                    ->findOneById(2);
//                $transaction = new Transaction();
//                $transaction->setUser($yc);
//                $transaction->setAmount(Money::create(10000, 'USD'));
//                $transaction->setDescription($faker->sentence(4));
//                $transaction->setTransactionDate(new \DateTime());
//                $transaction->setType(Transaction::DONATE_BY_ADMIN);
//                $transaction->save();
//                $temp = false;
//            }
//        }
        
        return true;
    }

    protected function generateRepayments($count)
    {
        $activeLoans = LoanQuery::create()
            ->filterByStatus(Loan::ACTIVE)
            ->find();
        
        $i = 0;

        foreach ($activeLoans as $loan) {
            //to test repayment upload from kenya
            if ($loan->getBorrower()->getCountryId() == 1) {
                continue;
            }
            $installments = InstallmentQuery::create()
                ->filterByLoan($loan)
                ->orderById()// TODO order due date?
                ->find();
            
            foreach ($installments as $installment) {
//                if ($i == $count) {
//                    break;
//                }
                
                if (!$installment->getAmount()->isPositive()){
                    continue;
                }

//                    if ($loan->getDisbursedAt() < Carbon::create()->subMonths(6)) {
//                        break;
//                    }

                if (rand(1,4) <= 1) {
//                    if (!$repaid && rand(1,6) <= 1) {
//                        break;
//                    }
                }
                if (rand(1,5) <= 4) {
                    $installmentAmount = $installment->getAmount();
                } else {
                    $installmentAmount = Money::create(rand($installment->getAmount()->subtract($installment->getAmount()->divide
                            (2))
                            ->getAmount(),
                        $installment->getAmount()->add($installment->getAmount()->divide(2))->getAmount()), $loan->getCurrency()) ;
                }
                if (rand(1,10) <= 2) {
                    if(rand(1,5) <= 4) {
                        $installmentDate = $installment->getDueDate()->modify('+1 week');
                    } else {
                        $installmentDate = $installment->getDueDate()->modify('+2 week');
                    }
                    $this->repaymentService->addRepayment($loan, [
                        'date'   => $installmentDate,
                        'amount' => $installmentAmount->getAmount(),
                    ]);
                } else {
                    $this->repaymentService->addRepayment($loan, [
                        'date'   => $installment->getDueDate(),
                        'amount' => $installmentAmount->getAmount(),
                    ]);
                }
                $i += 1;
            }
        }
        
        return true;
    }

    protected function generateCreditSetting()
    {
        $values = [
            ['KE', 0, 300, 50, 1],
            ['SN', 0, 300, 50, 1],
            ['BF', 0, 300, 50, 1],
            ['ID', 0, 300, 50, 1],
//            ['NE', 0, 300, 50, 1],
            ['BJ', 0, 300, 50, 1],
//            ['GN', 0, 300, 50, 1],
            ['KE', 0, 0, 0, 2],
            ['BF', 0, 0, 0, 2],
            ['SN', 0, 0, 0, 2],
            ['ID', 0, 0, 0, 2],
            ['KE', 2000, 0, 0, 3],
            ['ID', 300000, 0, 0, 3],
            ['SN', 10000, 0, 0, 3],
            ['BF', 10000, 0, 0, 3],
//            ['NE', 10000, 0, 0, 3],
            ['BJ', 10000, 0, 0, 3],
//            ['GN', 200000, 0, 0, 3],
            ['GH', 200, 0, 0, 3],
//            ['ZM', 150, 0, 0, 3],
        ];

        foreach ($values as $value) {
            $creditSetting = new CreditSetting();
            $creditSetting
                ->setCountryCode($value[0])
                ->setLoanAmountLimit($value[1])
                ->setCharacterLimit($value[2])
                ->setCommentsLimit($value[3])
                ->setType($value[4]);
            $creditSetting->save();
        }

        return 1;
    }

    private function generateLoanForArrear($days, $hours = null, $operator = 'subtract')
    {
        $categoryIds = CategoryQuery::create()
            ->filterByAdminOnly(false)
            ->orderByRank()
            ->select('id')
            ->find()
            ->getData();

        $borrowers = BorrowerQuery::create()
            ->joinWith('Country')
            ->orderById()
            ->find()
            ->getData();

        /** @var Borrower $borrower */
        $borrower = $borrowers[0];
        $currency = $borrower->getCountry()->getCurrency();

        $date = $this->faker->dateTimeBetween('-16 months');
        $exchangeRate = $this->currencyService->getExchangeRate($currency, $date);
        $usdAmount = Money::create(100);
        $amount = Converter::fromUSD($usdAmount, $currency, $exchangeRate);

        $isWeekly = $borrower->getCountry()->getInstallmentPeriod() == Loan::WEEKLY_INSTALLMENT;

        $data = [
            'summary'           => $this->faker->sentence(8),
            'proposal'          => $this->faker->paragraph(7),
            'amount'            => $amount->getAmount(),
            'installmentAmount' => $amount->divide($this->faker->numberBetween(6, 16))->getAmount(),
            'currencyCode'      => $borrower->getCountry()->getCurrencyCode(),
            'installmentDay'    => $isWeekly ? $this->faker->dayOfWeek : $this->faker->dayOfMonth,
            'date'              => $date,
            'exchangeRate'      => $exchangeRate,
            'categoryId'        => $this->faker->randomElement($categoryIds),
        ];

        $loan = $this->loanService->applyForLoan($borrower, $data);
        $loan->save();
        $acceptedAt = Carbon::instance($loan->getAppliedAt());
        $acceptedAt->addDays($this->faker->numberBetween(15, 20));
//        $this->loanService->acceptBids($loan, ['acceptedAt' => $acceptedAt]);

        $disbursedAt = Carbon::instance($loan->getCreatedAt())->subMonths(3);
        $disbursedAt->addDays($this->faker->numberBetween(1, 10));
//        $disbursedAmount = $loan->getAmount();
//        $this->loanService->disburseLoan($loan, compact('disbursedAt', 'disbursedAmount'));

        $loan
            ->setStatus(Loan::ACTIVE)
            ->setDisbursedAmount($amount)
            ->setDisbursedAt($disbursedAt)
            ->calculateExtraDays($disbursedAt)
            ->setServiceFeeRate(Setting::get('loan.serviceFeeRate'));

        $calculator = new InstallmentCalculator($loan);
        $installmentAmount = $calculator->totalAmount()->divide($loan->getPeriod());
        $period = $loan->getPeriod();

        for ($count = 1; $count <= $period; $count++) {

            if ($count == $period) {
                $installment = new Installment();
                $installment
                    ->setLoan($loan)
                    ->setBorrower($loan->getBorrower())
                    ->setAmount($installmentAmount);
                    if ($operator == 'subtract') {
                        $installment->setDueDate(Carbon::now()->subDays($days)->subHours($hours));
                    } else {
                        $installment->setDueDate(Carbon::now()->addDays($days)->addHours($hours));
                    }
            } else {
                $installment = new Installment();
                $installment
                    ->setLoan($loan)
                    ->setBorrower($loan->getBorrower())
                    ->setAmount($installmentAmount)
                    ->setDueDate($calculator->nthInstallmentDate($count));

            }
            $installment->save();
        }

        return $loan;
    }

    private function generateLoanArrear($type = 'first')
    {
        $categoryIds = CategoryQuery::create()
            ->filterByAdminOnly(false)
            ->orderByRank()
            ->select('id')
            ->find()
            ->getData();

        $borrowers = BorrowerQuery::create()
            ->joinWith('Country')
            ->useCountryQuery()
            ->filterByInstallmentPeriod(0)
            ->endUse()
            ->orderById()
            ->find()
            ->getData();

        /** @var Borrower $borrower */
        $borrower = $borrowers[0];
        $currency = $borrower->getCountry()->getCurrency();

        if ($type == 'final') {
            $date = Carbon::now()->subMonths(3)->subDays(11);
        } elseif ($type == 'repay') {
            $date = Carbon::now()->subMonths(5)->subDays(1);
        } else {
            $date = Carbon::now()->subMonths(2)->subDays(11);
        }
        $exchangeRate = $this->currencyService->getExchangeRate($currency, $date);
        $usdAmount = Money::create(100);
        $amount = Converter::fromUSD($usdAmount, $currency, $exchangeRate);

        $isWeekly =$borrower->getCountry()->getInstallmentPeriod() == Loan::WEEKLY_INSTALLMENT;

        $data = [
            'summary'           => $this->faker->sentence(8),
            'proposal'          => $this->faker->paragraph(7),
            'amount'            => $amount->getAmount(),
            'installmentAmount' => $amount->divide($this->faker->numberBetween(6, 16))->getAmount(),
            'currencyCode'      => $borrower->getCountry()->getCurrencyCode(),
            'installmentDay'    => $isWeekly ? $this->faker->dayOfWeek : $this->faker->dayOfMonth,
            'date'              => $date,
            'exchangeRate'      => $exchangeRate,
            'categoryId'        => $this->faker->randomElement($categoryIds),
        ];

        $loan = $this->loanService->applyForLoan($borrower, $data);
        $loan->save();
        $acceptedAt = Carbon::instance($loan->getAppliedAt());
        $acceptedAt->addDays(5);
        $this->loanService->acceptBids($loan, ['acceptedAt' => $acceptedAt]);

        $disbursedAt = Carbon::instance($loan->getAcceptedAt());
        $disbursedAt->addDays(15);
        $disbursedAmount = $loan->getAmount();
        $this->loanService->disburseLoan($loan, compact('disbursedAt', 'disbursedAmount'));

        $installment = InstallmentQuery::create()
            ->getDueInstallment($loan);
        if ($type == 'final') {
            $installment->setDueDate(Carbon::now()->subDays(14)->subHours(5));
        } elseif ($type == 'repay') {
            $installment->setDueDate(Carbon::now()->subDays(60)->subHours(5));
        } else {
            $installment->setDueDate(Carbon::now()->subDays(4)->subHours(5));
        }
        $installment->save();
        $this->line('installment iD ' . $installment->getId());

        return $loan;
    }

    private function generateInviteeOwnFunds()
    {
        $inviteeLender = "SELECT id from users as u
                          WHERE u.joined_at < :joinedAtMin
                          AND u.joined_at > :joinedAtMax
                          AND u.role = " . User::LENDER_ROLE_ENUM . "
                          AND u.active = TRUE
                          AND (SELECT COUNT(*) FROM loan_bids lb
                                  WHERE lb.lender_id = u.id
                                  AND lb.active = TRUE
                                  AND lb.is_lender_invite_credit = FALSE) = 0
                          LIMIT 1";

        $inviteeLenderId = PropelDB::fetchOne($inviteeLender, [
                'joinedAtMin' =>  Carbon::now()->subMonth(),
                'joinedAtMax' => Carbon::now()->subMonths(3),
            ]);

        $this->line('Invitee Lender iD =' . $inviteeLenderId['id']);

        $lenderInviteTransaction = new \Zidisha\Balance\InviteTransaction();
        $lenderInviteTransaction->setLenderId(247)
            ->setType(Transaction::LENDER_INVITE_INVITEE)
            ->setAmount(Money::create(40))
            ->setDescription('Lender Invitee Transaction')
            ->setTransactionDate(Carbon::now()->subMonths(4));
        $lenderInviteTransaction->save();
//        $invite = new Invite();
//        $invite->setLenderId($lender)

        $this->line('Lender Invitee Transaction iD =' . $lenderInviteTransaction->getId());

        return $inviteeLenderId;
    }
}
