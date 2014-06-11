<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

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

        for ($i = 1; $i <= $size; $i++) {
            if ($model == "Lender") {

                $userName = 'lender' . $i;
                $password = '1234567890';
                $email = 'lender' . $i . '@mail.com';

                $user = new \Zidisha\User\User();
                $user->setUsername($userName);
                $user->setPassword($password);
                $user->setEmail($email);

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