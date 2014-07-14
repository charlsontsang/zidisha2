<?php

namespace Zidisha\Admin;

use Zidisha\Admin\Base\Setting as BaseSetting;
use Zidisha\Vendor\PropelDB;

class Setting extends BaseSetting
{
    
    protected static $nameToValue;

    public static function getGroups()
    {
        $groups = include(app_path() . '/config/settings.php');

        foreach ($groups as $group => $groupSettings) {
            foreach ($groupSettings as $name => $options) {
                if (strpos($name, '_')) {
                    throw new \Exception("Setting $name should be in camelcase.");
                }
                $groups[$group][$name] += [
                    'type'        => 'text',
                    'default'     => '',
                    'rule'        => '',
                    'description' => '',
                    'label'       => $name,
                    'prepend'     => null,
                    'append'      => null,
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
            // TODO log
            //throw new \Exception("Setting with name $name doesn't exists.");
            return null;
        }
        
        return static::$nameToValue[$name];
    }


    public static function getAll()
    {
        if (static::$nameToValue === null) {
            $settings = SettingQuery::create()->find();
            static::$nameToValue = $settings->toKeyValue('name', 'value');
        }
        
        return static::$nameToValue;
    }

    public static function updateSettings($data)
    {
        static::$nameToValue = null;
        
        $settings = SettingQuery::create()->find();
        PropelDB::transaction(function() use($settings, $data) {
            foreach ($settings as $setting) {
                if (isset($data[$setting->getName()])) {
                    $setting->setValue($data[$setting->getName()]);
                    $setting->save();
                }
            } 
        });
    }
    
    public static function isUpToDate()
    {
        $groups = Setting::getGroups();
        
        $names = [];
        foreach ($groups as $groupSettings) {
            foreach ($groupSettings as $name => $_) {
                $names[] = $name;
            }
        }
        
        $diff = array_diff($names, array_keys(static::getAll()));
        
        return empty($diff);
    }

    public static function import($defaults = [])
    {
        static::$nameToValue = null;
        $groups = Setting::getGroups();

        $settings = SettingQuery::create()->find();
        $nameToValue = $settings->toKeyValue('name', 'value');

        foreach ($groups as $group => $groupSettings) {
            foreach ($groupSettings as $name => $options) {
                if (!isset($nameToValue[$name])) {
                    $setting = new Setting();
                    $setting
                        ->setName($name)
                        ->setValue(array_get($defaults, $name, $options['default']));
                    $setting->save();
                }
            }
        }
    }

}
