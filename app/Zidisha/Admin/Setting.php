<?php

namespace Zidisha\Admin;

use Zidisha\Admin\Base\Setting as BaseSetting;

class Setting extends BaseSetting
{
    
    protected static $nameToValue;

    public static function getGroups()
    {
        $groups = include(app_path() . '/config/settings.php');

        foreach ($groups as $group => $groupSettings) {
            foreach ($groupSettings as $name => $options) {
                $groups[$group][$name] += [
                    'type' => 'text',
                    'default' => '',
                    'rule' => '',
                    'description' => '',
                    'label' => $name,
                ];
            }
        }
        
        return $groups;
    }
    
    public static function get($name)
    {
        if (static::$nameToValue === null) {
            $settings = SettingQuery::create()->find();
            static::$nameToValue = $settings->toKeyValue('name', 'value');
        }
        
        if (!isset(static::$nameToValue[$name])) {
            throw new \Exception("Setting with name $name doesn't exists.");
        }
        
        return static::$nameToValue[$name];
    }
    
}
