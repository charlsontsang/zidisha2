<?php

use Illuminate\Console\Command;
use Zidisha\Admin\Setting;

class Settings extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'settings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Used to import the settings.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        Setting::import();       
    }

}
