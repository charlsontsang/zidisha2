<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Zidisha\Balance\Transaction;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Country\Country;
use Zidisha\Country\CountryQuery;
use Zidisha\Lender\LenderQuery;
use Zidisha\Loan\Category;
use Zidisha\Loan\CategoryQuery;
use Zidisha\Loan\Loan;
use Faker\Factory as Faker;
use Zidisha\Loan\LoanQuery;
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
        $model = $this->argument('model');
        $size = $this->argument('size');
        $faker = Faker::create();
        $countries = [
            ['Bolivia', 'SA', 'BO', '591', 't'],
            ['Paraguay', 'SA', 'PY', '595', 't'],
            ['Guyana', 'SA', 'GY', '592', 't'],
            ['French Guiana', 'SA', 'GF', '594', 't'],
            ['Falkland Islands', 'SA', 'FK', '45', 't'],
            ['Equador', 'SA', 'EC', '67', 't'],
            ['Colombia', 'SA', 'CO', '57', 't'],
            ['Chile', 'SA', 'CL', '56', 't'],
            ['Brazil', 'SA', 'BR', '55', 't'],
            ['Argentina', 'SA', 'AR', '54', 't'],
        ];

        $allLenders = LenderQuery::create()
            ->orderById()
            ->find();

        $allLoans = LoanQuery::create()
            ->orderById()
            ->find();

        $categories = include(app_path() . '/database/LoanCategories.php');
        $loanService = App::make('\Zidisha\Loan\LoanService');
        
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
            $this->call('fake', array('model' => 'Country', 'size' => 10));
            $this->call('fake', array('model' => 'Category', 'size' => 10));
            $this->call('fake', array('model' => 'Borrower', 'size' => 30));
            $this->call('fake', array('model' => 'Lender', 'size' => 30));
            $this->call('fake', array('model' => 'Loan', 'size' => 30));
            $this->call('fake', array('model' => 'Transaction', 'size' => 200));
            
            $this->line('Done!');
            return;
        }
        
        $this->line("Generate $model");

        if ($model == "Loan") {
            $allCategories = CategoryQuery::create()
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

        for ($i = 1; $i <= $size; $i++) {
            if ($model == "Lender") {

                $userName = 'lender' . $i;
                $password = '1234567890';
                $email = 'lender' . $i . '@mail.com';

                $user = new \Zidisha\User\User();
                $user->setUsername($userName);
                $user->setPassword($password);
                $user->setEmail($email);
                $user->setRole('lender');

                $firstName = 'lender' . $i;
                $lastName = 'last' . $i;
                $countryId = 1;

                $lender = new \Zidisha\Lender\Lender();
                $lender->setFirstName($firstName);
                $lender->setLastName($lastName);
                $lender->setCountryId($countryId);
                $lender->setUser($user);

                $lender_profile = new \Zidisha\Lender\Profile();
                $lender_profile->setAboutMe($faker->paragraph(7));
                $lender_profile->setLender($lender);
                $lender_profile->save();
            }

            if ($model == "Borrower") {

                $userName = 'borrower' . $i;
                $password = '1234567890';
                $email = 'borrower' . $i . '@mail.com';

                $user = new \Zidisha\User\User();
                $user->setUsername($userName);
                $user->setPassword($password);
                $user->setEmail($email);
                $user->setRole('borrower');

                $firstName = 'borrower' . $i;
                $lastName = 'last' . $i;
                $countryId = 1;

                $borrower = new \Zidisha\Borrower\Borrower();
                $borrower->setFirstName($firstName);
                $borrower->setLastName($lastName);
                $borrower->setCountryId($countryId);
                $borrower->setUser($user);

                $borrower_profile = new \Zidisha\Borrower\Profile();
                $borrower_profile->setAboutMe($faker->paragraph(7));
                $borrower_profile->setAboutBusiness($faker->paragraph(7));
                $borrower_profile->setBorrower($borrower);
                $borrower_profile->save();

            }

            if ($model == "Country") {
                if ($i >= 9) {
                    continue;
                }

                $oneCountry = $countries[$i - 1];

                $country = new Country();
                $country->setName($oneCountry[0]);
                $country->setCountryCode($oneCountry[1]);
                $country->setContinentCode($oneCountry[2]);
                $country->setDialingCode($oneCountry[3]);
                $country->setEnabled($oneCountry[4]);
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

            if ($model == "Loan") {

                if ($i >= 30) {
                    $installmentDay = $i - (int)(25 - $i);
                    $amount = 30 + ($i * 10);
                } else {
                    $installmentDay = $i;
                    $amount = 30 + ($i * 20);
                }
                $installmentAmount = $amount / 12;
                $loanCategory = $allCategories[array_rand($allCategories)];
                $status = floatval($size / 7);
                $borrower = $allBorrowers[$i - 1];

                $Loan = new Loan();
                $Loan->setSummary($faker->sentence(8));
                $Loan->setDescription($faker->paragraph(7));
                $Loan->setAmount($amount);
                $Loan->setInstallmentAmount($installmentAmount);
                $Loan->setInstallmentDay($installmentDay);
                $Loan->setBorrower($borrower);
                $Loan->setCategory($loanCategory);

                if ($i < $status) {
                    $loanService->applyForLoan($Loan);
                    continue;
                }

                $Stage = new Stage();
                $Stage->setLoan($Loan);
                $Stage->setBorrower($borrower);

                if ($i < ($status * 3)) {
                    $Loan->setStatus(Loan::FUNDED);
                    $Stage->setStatus(Loan::FUNDED);
                } elseif ($i < ($status * 4)) {
                    $Loan->setStatus(Loan::ACTIVE);
                    $Stage->setStatus(Loan::ACTIVE);
                } elseif ($i < ($status * 5)) {
                    $Loan->setStatus(Loan::REPAID);
                    $Stage->setStatus(Loan::REPAID);
                } elseif ($i < ($status * 6)) {
                    $Loan->setStatus(Loan::DEFAULTED);
                    $Stage->setStatus(Loan::DEFAULTED);
                } elseif ($i < ($status * 7)) {
                    $Loan->setStatus(Loan::CANCELED);
                    $Stage->setStatus(Loan::CANCELED);
                } else {
                    $Loan->setStatus(Loan::EXPIRED);
                    $Stage->setStatus(Loan::EXPIRED);
                }

                $Stage->setStartDate(new \DateTime());
                $Stage->save();

                $loanService->addToLoanIndex($Loan);
            }

            if ($model == "Transaction") {

                $oneLender = $allLenders[array_rand($allLenders->getData())];
                $oneLoan = $allLoans[array_rand($allLoans->getData())];

                $transaction = new Transaction();
                $transaction->setUser($oneLender->getUser());
                $transaction->setAmount(rand(-100,100));
                $transaction->setLoan($oneLoan);
                $transaction->setDescription($oneLoan->getSummary());
                $transaction->setTransactionDate(new \DateTime());
                $transaction->setType(Transaction::FUND_WITHDRAW);
                $transaction->save();

            }

            if ($model == "Transaction") {

                $oneLender = $allLenders[array_rand($allLenders->getData())];
                $oneLoan = $allLoans[array_rand($allLoans->getData())];

                $transaction = new Transaction();
                $transaction->setUser($oneLender->getUser());
                $transaction->setAmount(rand(5,100));
                $transaction->setLoan($oneLoan);
                $transaction->setDescription($oneLoan->getSummary());
                $transaction->setTransactionDate(new \DateTime());
                $transaction->setType(Transaction::FUND_WITHDRAW);
                $transaction->save();

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
