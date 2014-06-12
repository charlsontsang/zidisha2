<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Zidisha\Country\Country;
use Zidisha\Loan\Category;

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
        $countries = [
          ['Bolivia', 'SA', 'BO', '591', 't'],
          ['Paraguay', 'SA', 'PY', '595', 't'],
          ['Guyana', 'SA', 'GY', '592', 't'],
          ['French Guiana', 'SA', 'GF', '594'],
          ['Falkland Islands', 'SA', 'FK'],
          ['Equador', 'SA', 'EC', null, 't'],
          ['Colombia', 'SA', 'CO', '57', 't'],
          ['Chile', 'SA', 'CL', '56', 't'],
          ['Brazil', 'SA', 'BR', '55', 't'],
          ['Argentina', 'SA', 'AR', '54', 't'],
        ];
        $categories = include(app_path() . '/database/LoanCategories.php');

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
                $aboutMe = "Hi, i'm lender" . $i . "!";
                $countryId = 1;

                $lender = new \Zidisha\Lender\Lender();
                $lender->setFirstName($firstName);
                $lender->setLastName($lastName);
                $lender->setCountryId($countryId);
                $lender->setUser($user);

                $lender_profile = new \Zidisha\Lender\Profile();
                $lender_profile->setAboutMe($aboutMe);
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
                $aboutMe = "Hi, i'm a borrower" . $i . "!";
                $countryId = 1;

                $borrower = new \Zidisha\Borrower\Borrower();
                $borrower->setFirstName($firstName);
                $borrower->setLastName($lastName);
                $borrower->setCountryId($countryId);
                $borrower->setUser($user);

                $borrower_profile = new \Zidisha\Borrower\Profile();
                $borrower_profile->setAboutMe($aboutMe);
                $borrower_profile->setAboutBusiness($aboutMe);
                $borrower_profile->setBorrower($borrower);
                $borrower_profile->save();

            }

            if($model == "Country"){

                $oneCountry = $countries[$i-1];

                $country = new Country();
                $country->setName($oneCountry[0]);
                $country->setContinentCode($oneCountry[1]);
                $country->setContinentCode($oneCountry[2]);
                $country->setDialingCode($oneCountry[3]);
                $country->setEnabled($oneCountry[4]);
                $country->save();

                if ($i==9) {
                    exit();
                }
            }

            if($model == "Category"){

                $oneCategory = $categories[$i-1];

                $category = new Category();
                $category->setName($oneCategory[0]);
                $category->setWhatDescription($oneCategory[1]);
                $category->setWhyDescription($oneCategory[2]);
                $category->setHowDescription($oneCategory[3]);
                $category->setAdminOnly($oneCategory[4]);
                $category->save();

                if($i==17){
                    exit();
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
            array('size', InputArgument::REQUIRED, 'Number of entries you want for this model')
        );
    }

}