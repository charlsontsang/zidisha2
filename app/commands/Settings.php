<?php

use Illuminate\Console\Command;
use Zidisha\Admin\Setting;
use Zidisha\Admin\SettingQuery;

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
        $groups = Setting::getGroups();
                
        $settings = SettingQuery::create()->find();
        $nameToValue = $settings->toKeyValue('name', 'value');

        foreach ($groups as $group => $groupSettings) {
            foreach ($groupSettings as $name => $options) {
                if (!isset($nameToValue[$name])) {
                    $setting = new Setting();
                    $setting
                        ->setName($name)
                        ->setValue($options['default']);
                    $setting->save();
                }
            }
        }        
    }

}
