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
            if ($model == "User") {

                $userName = 'user' . $i;
                $password = '1234567890';
                $email = 'user' . $i . '@mail.com';

                $user = new User();
                $user->setUsername($userName);
                $user->setPassword($password);
                $user->setEmail($email);
                $user->save();
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