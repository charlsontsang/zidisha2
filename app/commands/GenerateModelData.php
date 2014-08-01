<?php

use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Console\Command;
use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\Console\Input\InputArgument;
use Zidisha\Admin\Setting;
use Zidisha\Balance\Transaction;
use Zidisha\Balance\TransactionQuery;
use Zidisha\Balance\WithdrawalRequest;
use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Borrower\JoinLog;
use Zidisha\Borrower\VolunteerMentor;
use Zidisha\Borrower\VolunteerMentorQuery;
use Zidisha\Comment\BorrowerComment;
use Zidisha\Comment\Comment;
use Zidisha\Country\Country;
use Zidisha\Country\CountryQuery;
use Zidisha\Country\Language;
use Zidisha\Currency\Converter;
use Zidisha\Currency\ExchangeRateQuery;
use Zidisha\Currency\Money;

use Zidisha\Lender\GiftCard;
use Zidisha\Lender\LendingGroup;
use Zidisha\Lender\LendingGroupMember;
use Zidisha\Lender\LendingGroupQuery;
use Zidisha\Lender\Invite;
use Zidisha\Lender\LenderQuery;
use Zidisha\Loan\Bid;
use Zidisha\Loan\Category;
use Zidisha\Loan\CategoryQuery;
use Zidisha\Loan\CategoryTranslation;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanQuery;
use Zidisha\Loan\LoanService;
use Zidisha\Lender\LenderService;
use Zidisha\Repayment\BorrowerPayment;
use Zidisha\Repayment\BorrowerPaymentQuery;
use Zidisha\Repayment\InstallmentQuery;
use Zidisha\Repayment\RepaymentService;
use Zidisha\Currency\CurrencyService;
use Zidisha\Loan\Stage;

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        \Config::set('mail.enabled', false);

        try {
            $settings = Setting::getAll();
        } catch (\Exception $e) {
            $settings = [];
        }        
        
        $model = $this->argument('model');
        $size = $this->argument('size');
        $faker = Faker::create();
        $countries = [
            ['KE', 'Kenya', 'KES', '1000',],
            ['BJ', 'Benin', 'XOF', '0',],
            ['BF', 'Burkina Faso', 'XOF', '0',],
            ['GH', 'Ghana', 'GHS', '0',],
            ['ID', 'Indonesia', 'IDR', '0',],
            ['SN', 'Senegal', 'XOF', '0',],
            ['IN', 'India', 'INR', '0',],
        ];
        $temp = true;

        $allCity = [];

        if ($model == 'new') {
            $this->line('Rebuild database');
            DB::statement('drop schema public cascade');
            DB::statement('create schema public');
            exec('rm -rf app/database/migrations');
            exec('./propel diff');
            exec('./propel migrate');
            exec('./propel build');

            $this->line('Delete loans index');
            exec("curl -XDELETE 'http://localhost:9200/loans/' -s");

            $this->line('Generate data');

            Setting::import($settings);

            $this->call('fake', array('model' => 'Language', 'size' => 1));
            $this->call('fake', array('model' => 'Country', 'size' => 10));
            $this->call('fake', array('model' => 'Category', 'size' => 10));
            $this->call('fake', array('model' => 'Admin', 'size' => 1));
            $this->call('fake', array('model' => 'Borrower', 'size' => 80));
            $this->call('fake', array('model' => 'Lender', 'size' => 50));
            $this->call('fake', array('model' => 'ExchangeRate', 'size' => 30));
            //$this->call('fake', array('model' => 'LoanOld', 'size' => 150));
            $this->call('fake', array('model' => 'Loan', 'size' => 80));
            //$this->call('fake', array('model' => 'BidOld', 'size' => 50));
            $this->call('fake', array('model' => 'Bid', 'size' => 200));
            $this->call('fake', array('model' => 'AcceptBid', 'size' => 1));
            $this->call('fake', array('model' => 'DisburseLoan', 'size' => 1));
            $this->call('fake', array('model' => 'Repayment', 'size' => 1));
            $this->call('fake', array('model' => 'Transaction', 'size' => 200));
            $this->call('fake', array('model' => 'Installment', 'size' => 200));
            $this->call('fake', array('model' => 'Invite', 'size' => 200));
            $this->call('fake', array('model' => 'Comment', 'size' => 200));
            $this->call('fake', array('model' => 'GiftCard', 'size' => 100));
            $this->call('fake', array('model' => 'CategoryTranslation', 'size' => 10));
            $this->call('fake', array('model' => 'LenderGroup', 'size' => 50));
            $this->call('fake', array('model' => 'LenderGroupMember', 'size' => 200));
            $this->call('fake', array('model' => 'WithdrawalRequest', 'size' => 200));
//            $this->call('fake', array('model' => 'fakeOneBorrowerRefund', 'size' => 1));

            $this->call('import-translations');

            $this->line('Done!');
            return;
        }

        $randArray = [true, false, false, false, false, true, false, false, false, true, false];

        $allLenders = LenderQuery::create()
            ->orderById()
            ->find();

        $allGroups = LendingGroupQuery::create()
            ->orderById()
            ->find();

        $allLoans = LoanQuery::create()
            ->orderById()
            ->find();

        $allCountries = CountryQuery::create()
            ->find();

        $allCategories = CategoryQuery::create()
            ->filterByAdminOnly(false)
            ->orderByRank()
            ->find()
            ->getData();


        $allLanguages = \Zidisha\Country\LanguageQuery::create()
            ->filterByActive(true)
            ->find()
            ->getData();

        $allBorrowers = BorrowerQuery::create()
            ->orderById()
            ->find()
            ->getData();

        $categories = include(app_path() . '/database/LoanCategories.php');
        /** @var LoanService $loanService */
        $loanService = App::make('\Zidisha\Loan\LoanService');
        $lenderService = App::make('\Zidisha\Lender\LenderService');
        /** @var RepaymentService $repaymentService */
        $repaymentService = App::make('\Zidisha\Repayment\RepaymentService');
        /** @var CurrencyService $currencyService */
        $currencyService = App::make('\Zidisha\Currency\CurrencyService');

        $this->line("Generate $model");

        if ($model == "Loan") {
            $allCategories = CategoryQuery::create()
                ->filterByAdminOnly(false)
                ->orderByRank()
                ->find()
                ->getData();

            $allBorrowers = BorrowerQuery::create()
                ->orderById()
                ->find()
                ->getData();

            if ($allCategories == null || count($allBorrowers) < $size) {
                $this->error("not enough categories or borrowers");
                return;
            }
        }

        if ($model == "Admin") {
            $userName = 'admin';
            $password = '1234567890';
            $email = 'admin@mail.com';

            $user = new \Zidisha\User\User();
            $user->setUsername($userName);
            $user->setPassword($password);
            $user->setEmail($email);
            $user->setRole('admin');
            $user->setLastLoginAt(new Carbon());
            $user->save();

            $user = new \Zidisha\User\User();
            $user->setUsername('YC');
            $user->setPassword('1234567890');
            $user->setEmail('yc@mail.com');
            $user->setLastLoginAt(new Carbon());
            $user->save();
        }

        if($model == "Language") {

            $languages = [
                ['in', 'Bahasa Indonesia', true,],
                ['fr', 'Français', true,],
                ['hi', 'Hindi', false,],
                ['en', 'English', false,],
            ];

            foreach($languages as $language){
                $lang = new Language();
                $lang->setLanguageCode($language[0]);
                $lang->setName($language[1]);
                $lang->setActive($language[2]);
                $lang->save();

            }
        }

        if ($model == "AcceptBid") {
            $raisedLoans = LoanQuery::create()
                ->filterByRaisedPercentage(100)
                ->find();

            foreach ($raisedLoans as $loan) {
                if (rand(1,6) <= 5) {
                    $loanService->acceptBids($loan);
                }
            }
        }

        if ($model == "DisburseLoan") {
            $fundedLoans = LoanQuery::create()
                ->filterByStatus(Loan::FUNDED)
                ->find();

            foreach ($fundedLoans as $loan) {
                if (true || rand(1,5) <= 4) {
                    // todo take application date + (15-30) days
                    $Date = $loan->getAppliedAt();
                    $newDate = Carbon::createFromDate($Date->format('Y'), $Date->format('m'), $Date->format('d'))->addDays(25);
                    $loanService->disburseLoan($loan , $newDate, $loan->getAmount());
                if (rand(1,6) <= 5) {
                    $loanService->disburseLoan($loan , new \DateTime(), $loan->getAmount());
                }
                }
            }
        }

        if ($model == "Repayment") {
            $activeLoans = LoanQuery::create()
                ->filterByStatus(Loan::ACTIVE)
                ->find();

            foreach ($activeLoans as $loan) {
                //to test repayment upload from kenya
                if ( $loan->getBorrower()->getCountryId() == 1) {
                    continue;
                }
                $installments = InstallmentQuery::create()
                    ->filterByLoan($loan)
                    ->orderById()// TODO order due date?
                    ->find();
                $repaid = rand(1,10) <= 1;
                foreach ($installments as $installment) {
                    if (!$installment->getAmount()->isPositive()){
                        continue;
                    }

                    if ($loan->getDisbursedAt() < Carbon::create()->subMonths(6)) {
                        break;
                    }
                    if (!$repaid && rand(1,4) <= 1) {
                        break;
                    }
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
                    if(rand(1,10) <= 3) {
                        if(rand(1,2) <= 1) {
                            $installmentDate = $installment->getDueDate()->modify('+1 week');
                        } else {
                            $installmentDate = $installment->getDueDate()->modify('+2 week');
                        }
                        $repaymentService->addRepayment($loan, $installmentDate, $installmentAmount);
                    } else {
                        $repaymentService->addRepayment($loan, $installment->getDueDate(), $installmentAmount);
                    }
                }
            }
        }

        if ($model == "CategoryTranslation") {

            $allCategories = CategoryQuery::create()
                ->filterByAdminOnly(false)
                ->find();

            $allLanguages = \Zidisha\Country\LanguageQuery::create()
                ->filterByActive(true)
                ->find();

            foreach($allCategories as $Category)
            {
                foreach($allLanguages as $language)
                {
                    $translation = new CategoryTranslation();
                    $translation->setCategory($Category)
                        ->setLanguage($language)
                        ->setTranslation($Category->getName(). $language->getLanguageCode());
                    $translation->save();
                }
            }
        }

        if ($model == "ExchangeRate") {

            foreach (['KES' => 80, 'XOF' => 20, 'GHS' => 50, 'IDR' => 40, 'INR' => 80] as $currencyCode => $rate) {
                $dateMonthAgo = new DateTime();
                $dateMonthAgo->modify('-1 month');
                $dateNow = new DateTime();
                $dateNow->modify('-1 second');

                $exchangeRate = new \Zidisha\Currency\ExchangeRate();
                $exchangeRate
                    ->setCurrencyCode($currencyCode)
                    ->setRate($rate - 5)
                    ->setStartDate($dateMonthAgo)
                    ->setEndDate($dateNow);
                $exchangeRate->save();

                $exchangeRate = new \Zidisha\Currency\ExchangeRate();
                $exchangeRate
                    ->setCurrencyCode($currencyCode)
                    ->setRate($rate)
                    ->setStartDate($dateNow);
                $exchangeRate->save();
            }
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
        }

        for ($i = 1; $i <= $size; $i++) {

            if ($model == "Invite") {

                do {
                    $lender = $allLenders[array_rand($allLenders->getData())];
                    $invitee = $allLenders[array_rand($allLenders->getData())];
                } while ($lender->getId() == $invitee->getId());

                $lenderInvite = new Invite();
                $lenderInvite->setLender($lender);
                if (rand( 1, 10) < 4) {
                    $lenderInvite->setEmail($faker->email);
                } else {
                    $lenderInvite->setInvitee($invitee);
                    $lenderInvite->setInvited(true);
                    $lenderInvite->setEmail($invitee->getUser()->getEmail());
                }
                $lenderInvite->save();
            }

            if ($model == "Lender") {
                $data = array();
                $data['username'] = 'lender' . $i;
                $data['password'] = '1234567890';
                $data['email'] = 'lender' . $i . '@mail.com';
                $oneCountry = $allCountries[array_rand($allCountries->getData())];
                $data['countryId'] = $oneCountry->getId();
                $lenderService->joinLender($data);

//                if($i<5){
//                    $user->setLastLoginAt(new Carbon('first day of July 2013'));
//                }elseif($i<10){
//                    $user->setLastLoginAt(new Carbon('first day of June 2013'));
//                }else{
//                    $user->setLastLoginAt(new Carbon());
//                }
            }

            if ($model == "Borrower") {

                $userName = 'borrower' . $i;
                $password = '1234567890';
                $email = 'borrower' . $i . '@mail.com';

                if ($i <= 40 && rand(1, 4) <= 3) {
                    if (rand(1, 5) <= 4) {
                        $oneCountry = $allCountries[1];
                    } else{
                        $oneCountry = $allCountries[3];
                    }
                } else {
                    $oneCountry = $allCountries[array_rand($allCountries->getData())];
                }

                $user = new \Zidisha\User\User();
                $user->setUsername($userName);
                $user->setPassword($password);
                $user->setEmail($email);
                $user->setLastLoginAt(new Carbon());
                $user->setRole('borrower');

                $firstName = 'borrower' . $i;
                $lastName = 'last' . $i;

                $borrower = new \Zidisha\Borrower\Borrower();
                $borrower->setFirstName($firstName);
                $borrower->setLastName($lastName);
                $borrower->setCountry($oneCountry);
                $borrower->setUser($user);
                $borrower->setVerified($faker->boolean());
                
                foreach (['communityLeader', 'familyMember', 'familyMember', 'familyMember', 'neighbor', 'neighbor', 'neighbor'] as $contactType) {
                    $contact = new \Zidisha\Borrower\Contact();
                    $contact
                        ->setPhoneNumber($faker->numberBetween(100000000, 1000000000))
                        ->setFirstName($faker->firstName)
                        ->setLastName($faker->lastName)
                        ->setDescription($faker->sentence())
                        ->setType($contactType);
                    $borrower->addContact($contact);
                }

                $borrower_profile = new \Zidisha\Borrower\Profile();
                $borrower_profile->setAboutMe($faker->paragraph(7));
                $borrower_profile->setAboutBusiness($faker->paragraph(7));
                $borrower_profile->setAddress($faker->paragraph(3));
                $borrower_profile->setAddressInstructions($faker->paragraph(6));
                if (rand(1, 5) <= 2) {
                    $borrower_profile->setCity("Experimento");
                } elseif ($i <= 20) {
                    $city = $faker->city;
                    array_push($allCity, $city);
                    $borrower_profile->setCity($city);
                } else {
                    $borrower_profile->setCity($allCity[array_rand($allCity)]);
                }
                $borrower_profile->setPhoneNumber($faker->phoneNumber);
                $borrower_profile->setAlternatePhoneNumber($faker->phoneNumber);
                $borrower_profile->setNationalIdNumber($faker->randomNumber(10));
                $borrower_profile->setBorrower($borrower);
                if ($i <= 40) {
                    $user->setSubRole('volunteerMentor');
                    $mentor = new VolunteerMentor();
                    $borrower->setCountry($allCountries[2]);
                    $mentor->setBorrowerVolunteer($borrower)
                        ->setCountry($borrower->getCountry())
                        ->setStatus(1)
                        ->setGrantDate(new \DateTime());
                } else {
                    $allMentors = VolunteerMentorQuery::create()
                        ->find();

                    $oneMentor = $allMentors[array_rand($allMentors->getData())];
                    $borrower->setVolunteerMentor($oneMentor);
                }
                $borrower_profile->save();

                $joinLog = new JoinLog();
                $joinLog
                    ->setIpAddress($faker->ipv4)
                    ->setVerificationCode($faker->randomNumber(20))
                    ->setBorrower($borrower);
                if ($borrower->getVerified()) {
                    $joinLog->setVerifiedAt(new \DateTime());
                }
                $joinLog->save();
            }

            if ($model == "Country") {
                if ($i > sizeof($countries)) {
                    continue;
                }

                $oneCountry = $countries[$i - 1];

                $country = new Country();
                $country->setName($oneCountry[1]);
                $country->setCountryCode($oneCountry[0]);
                $country->setContinentCode('AF');
                $country->setDialingCode('000');
                $country->SetRegistrationFee($oneCountry[3]);
                if ($oneCountry[0] == 'IN') {
                    $country->SetBorrowerCountry(false);
                } else {
                    $country->SetBorrowerCountry(true);
                }
                $country->setCurrencyCode($oneCountry[2]);
                $country->setPhoneNumberLength(9);
                $country->setInstallmentPeriod($faker->randomElement([Loan::WEEKLY_INSTALLMENT, Loan::MONTHLY_INSTALLMENT]));
                if($i<3){
                    $language = \Zidisha\Country\LanguageQuery::create()
                        ->filterByLanguageCode('fr')
                        ->findOne();
                    $country->setLanguage($language);
                }elseif($i>2){
                    $language = \Zidisha\Country\LanguageQuery::create()
                        ->filterByLanguageCode('in')
                        ->findOne();
                    $country->setLanguage($language);
                }
                $country->save();
            }

            if ($model == "Category") {
                if ($i >= 17) {
                    continue;
                }

                $oneCategory = $categories[$i - 1];

                $category = new Category();
                $category->setName($oneCategory[0]);
                $category->setWhatDescription($oneCategory[1]);
                $category->setWhyDescription($oneCategory[2]);
                $category->setHowDescription($oneCategory[3]);
                $category->setAdminOnly($oneCategory[4]);
                $category->save();
            }

            if ($model == "LoanOld") {
                if ($i >= 30) {
                    $installmentDay = $i - (int)(25 - $i);
                    $amount = 30 + ($i * 100);
                } else {
                    $installmentDay = $i;
                    $amount = 30 + ($i * 200);
                }
                $loanCategory = $allCategories[array_rand($allCategories)];
                $status = floatval($size / 7);

                if($i > 50 && $i < 55 ){
                    $borrower = $allBorrowers[50];
                }else{
                    $borrower = $allBorrowers[$i - 1];
                }

                $data = array();
                $data['summary'] = $faker->sentence(8);
                $data['proposal'] = $faker->paragraph(7);
                $data['amount'] = $amount;
                $data['currencyCode'] = 'KES';
                $data['usdAmount'] = $amount / 2;
                $installmentAmount = (int)$data['amount'] / 12;
                $data['installmentAmount'] = $installmentAmount;
                $data['applicationDate'] = new \DateTime();
                $data['installmentDay'] = $installmentDay;
                $data['categoryId'] = $loanCategory->getId();
                $data['raisedUsdAmount'] = $data['usdAmount']/rand(2, 6);

                if ($i < $status) {
                    $loanService->applyForLoan($borrower, $data);
                    continue;
                }

                $Loan = $loanService->createLoan($borrower, $data);
                $Loan->setCategory($loanCategory);
                $Loan->setBorrower($borrower);

                $Stage = new Stage();
                $Stage->setLoan($Loan);
                $Stage->setBorrower($borrower);

                $Loan->setRaisedUsdAmount(Money::create($data['raisedUsdAmount']));

                if ($i < ($status * 3)) {
                    $borrower->setLoanStatus(Loan::FUNDED);
                    $borrower->setActiveLoan($Loan);
                    $Loan->setStatus(Loan::FUNDED);
                    $Loan->setDisbursedAmount($amount);
                    $Loan->setDisbursedAt(new \DateTime());
                    $Stage->setStatus(Loan::FUNDED);
                } elseif ($i < ($status * 4)) {
                    $borrower->setLoanStatus(Loan::ACTIVE);
                    $borrower->setActiveLoan($Loan);
                    $Loan->setStatus(Loan::ACTIVE);
                    $Loan->setDisbursedAt(new \DateTime());
                    $Stage->setStatus(Loan::ACTIVE);
                } elseif ($i < ($status * 5)) {
                    $borrower->setLoanStatus(Loan::REPAID);
                    $borrower->setActiveLoan($Loan);
                    $Loan->setDisbursedAmount($amount);
                    $Loan->setDisbursedAt(strtotime("-1 year"));
                    $Loan->setStatus(Loan::REPAID);
                    $Loan->setRepaidAt(new \DateTime());
                    $Stage->setStatus(Loan::REPAID);
                } elseif ($i < ($status * 6)) {
                    $borrower->setLoanStatus(Loan::DEFAULTED);
                    $borrower->setActiveLoan($Loan);
                    $Loan->setStatus(Loan::DEFAULTED);
                    $Stage->setStatus(Loan::DEFAULTED);
                } elseif ($i < ($status * 7)) {
                    $borrower->setLoanStatus(Loan::CANCELED);
                    $borrower->setActiveLoan($Loan);
                    $Loan->setStatus(Loan::CANCELED);
                    $Stage->setStatus(Loan::CANCELED);
                } else {
                    $borrower->setLoanStatus(Loan::EXPIRED);
                    $borrower->setActiveLoan($Loan);
                    $Loan->setStatus(Loan::EXPIRED);
                    $Stage->setStatus(Loan::EXPIRED);
                }

                $Stage->setStartDate(new \DateTime());
                $Stage->save();
                $borrower->save();

                $loanService->addToLoanIndex($Loan);
            }

            if ($model == "Transaction") {

                $oneLender = $allLenders[array_rand($allLenders->getData())];
                $oneLoan = $allLoans[array_rand($allLoans->getData())];
                $isTrue = $randArray[array_rand($randArray)];

                $transaction = new Transaction();
                $transaction->setUser($oneLender->getUser());
                $transaction->setAmount(Money::create(rand(0, 200), 'USD'));
                $transaction->setDescription('description');
                $transaction->setTransactionDate(new \DateTime());
                $transaction->setType(Transaction::FUND_UPLOAD);
                $transaction->save();

                if ($isTrue || $i < 20) {
//                    $transaction = new Transaction();
//                    $transaction->setUser($oneLender->getUser());
//                    $transaction->setAmount(Money::create(rand(0, 20), 'USD'));
//                    $transaction->setLoan($oneLoan);
//                    $transaction->setDescription('description');
//                    $transaction->setTransactionDate(new \DateTime());
//                    $transaction->setType(Transaction::LOAN_BACK_LENDER);
//                    $transaction->save();

                    $transaction = new Transaction();
                    $transaction->setUser($oneLender->getUser());
                    $transaction->setAmount(Money::create(rand(-100, 0), 'USD'));
                    $transaction->setDescription('description');
                    $transaction->setTransactionDate(new \DateTime());
                    $transaction->setType(Transaction::FUND_WITHDRAW);
                    $transaction->save();
                }

                if ($temp == true) {
                    $yc = \Zidisha\User\UserQuery::create()
                        ->findOneById(2);
                    $transaction = new Transaction();
                    $transaction->setUser($yc);
                    $transaction->setAmount(Money::create(10000, 'USD'));
                    $transaction->setDescription($faker->sentence(4));
                    $transaction->setTransactionDate(new \DateTime());
                    $transaction->setType(Transaction::DONATE_BY_ADMIN);
                    $transaction->save();
                    $temp = false;
                }
            }

            if ($model == "BidOld") {

                $openLoans = LoanQuery::create()
                    ->filterByStatus(0)
                    ->find();
                $oneLoan = $openLoans[array_rand($openLoans->getData())];
                $oneLender = $allLenders[array_rand($allLenders->getData())];

                $oneBid = new Bid();
                $oneBid->setBidAt(new \DateTime());
                $oneBid->setBidAmount(Money::create(rand(0, 30), 'USD'));
                $oneBid->setInterestRate(rand(0, 15));
                $oneBid->setLoan($oneLoan);
                $oneBid->setLender($oneLender);
                $oneBid->setBorrower($oneLoan->getBorrower());
                $oneBid->save();
            }


            if ($model == "Comment") {

                $borrower = $allBorrowers[array_rand($allBorrowers)];
                $user = $allBorrowers[array_rand($allBorrowers)];
                $isTranslated = $randArray[array_rand($randArray)];

                $comment = new BorrowerComment();

                $comment->setBorrower($borrower)
                    ->setUser($user->getUser())
                    ->setMessage($faker->paragraph(3))
                    ->setLevel(0);

                if($isTranslated){
                    $comment->setMessageTranslation($faker->paragraph(3))
                        ->setTranslatorId(1);
                }elseif($i<100){
                    $comment->setUser($borrower->getUser());
                }

                $comment->save();
                $comment->setRootId($comment->getId());
                $comment->save();
            }

            if($model == "GiftCard") {
                $lender = $allLenders[array_rand($allLenders->getData())];
                $amount = Money::create(rand(15, 1000), 'USD');
                $faker = Faker::create();

                $giftCard = new GiftCard();
                $giftCard->setLender($lender)
                    ->setOrderType(array_rand([0,1]))
                    ->setCardAmount($amount)
                    ->setFromName($lender->getName())
                    ->setMessage($faker->sentence(10))
                    ->setRecipientEmail($faker->email)
                    ->setDate(new \DateTime())
                    ->setExpireDate(strtotime('+1 year'))
                    ->setCardCode($faker->creditCardNumber);

                if(rand(1 ,5) <= 3){
                    $recipient = $allLenders[array_rand($allLenders->getData())];
                    $giftCard->setClaimed(1)
                        ->setRecipientName($recipient->getName())
                        ->setRecipient($recipient);
                }

                $giftCard->save();
            }

            if($model == "LenderGroup")
            {
                $leader = $allLenders[array_rand($allLenders->getData())];

                $group = new LendingGroup();
                $group->setCreator($leader)
                    ->setLeader($leader)
                    ->setCreator($leader)
                    ->setAbout($faker->paragraph(2))
                    ->setName($faker->sentence(2));

                $groupMember = new LendingGroupMember();
                $groupMember->setMember($leader)
                    ->setLendingGroup($group);

                $groupMember->save();

            }

            if($model == "LenderGroupMember")
            {
                $member = $allLenders[array_rand($allLenders->getData())];
                $group = $allGroups[array_rand($allGroups->getData())];

                $groupMember = new LendingGroupMember();
                $groupMember->setMember($member)
                    ->setLendingGroup($group);
                $groupMember->save();
            }


            if($model == "WithdrawalRequest")
            {
                $lender = $allLenders[array_rand($allLenders->getData())];
                $isPaid = $randArray[array_rand($randArray)];
                $currentBalance = TransactionQuery::create()
                    ->filterByUserId($lender->getUser()->getId())
                    ->getTotalAmount();

                $withdrawalRequest = new WithdrawalRequest();
                $withdrawalRequest->setLender($lender)
                    ->setAmount(Money::create(rand(1, $currentBalance->getAmount())))
                    ->setPaypalEmail($faker->email);
                if ($isPaid) {
                    $withdrawalRequest->setPaid(true);
                }
                $withdrawalRequest->save();
            }

            if ($model == "Loan") {
                if ($i >= 30) {
                    $installmentDay = $i - (int)(25 - $i);
                    $amount = 30 + ($i * 100);
                } else {
                    $installmentDay = $i;
                    $amount = 30 + ($i * 200);
                }
                $loanCategory = $allCategories[array_rand($allCategories)];

                if($i > 50 && $i < 55 ){
                    $borrower = $allBorrowers[50];
                }else{
                    $borrower = $allBorrowers[$i - 1];
                }

                $data = array();
                $data['summary'] = $faker->sentence(8);
                $data['proposal'] = $faker->paragraph(7);
                $data['amount'] = $amount;
                $installmentAmount = (int)$data['amount'] / 12;
                $data['installmentAmount'] = $installmentAmount;
                $data['currencyCode'] = $borrower->getCountry()->getCurrencyCode();
                $data['
                usdAmount'] = $amount / 2;
                $data['installmentDay'] = $installmentDay;
                // TODO between now and a year ago
                $data['applicationDate'] = new \DateTime();
                $data['categoryId'] = $loanCategory->getId();

                $loanService->applyForLoan($borrower, $data);
            }

            if ($model == "Bid") {

                $oneLoan = LoanQuery::create()
                    ->filterByStatus(0)
                    ->orderByRand()
                    ->findOne();

                if ($oneLoan->getAppliedAt() < Carbon::create()->subMonths(8)) {
                    while ($oneLoan->getRaisedPercentage() < 100) {
                        $oneLender = $allLenders[array_rand($allLenders->getData())];
                        $data['amount'] = rand(5, $oneLoan->getUsdAmount()->divide(2)->getAmount());
                        $data['interestRate'] = rand(0, 15);
                        $loanService->placeBid($oneLoan, $oneLender, $data);
                    }
                    continue;
                }

                $numberOfBids = rand(2,5);
                $data = array();

                for ( $j=0; $j<=$numberOfBids; $j++) {
                    $oneLender = $allLenders[array_rand($allLenders->getData())];
                    $data['amount'] = rand(5, intval($oneLoan->getUsdAmount()->divide(8)->getAmount()) + 5);
                    $data['interestRate'] = rand(0, 15);
                    $loanService->placeBid($oneLoan, $oneLender, $data);
                }
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
}
