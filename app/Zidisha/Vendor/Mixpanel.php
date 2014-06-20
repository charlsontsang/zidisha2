<?php
namespace Zidisha\Vendor;

class Mixpanel
{

    private static $snippet = <<<SNIPPET
    (function(e,b){if(!b.__SV){var a,f,i,g;window.mixpanel=b;b._i=[];b.init=function(a,e,d){function f(b,h){var a=h.split(".");2==a.length&&(b=b[a[0]],h=a[1]);b[h]=function(){b.push([h].concat(Array.prototype.slice.call(arguments,0)))}}var c=b;"undefined"!==typeof d?c=b[d]=[]:d="mixpanel";c.people=c.people||[];c.toString=function(b){var a="mixpanel";"mixpanel"!==d&&(a+="."+d);b||(a+=" (stub)");return a};c.people.toString=function(){return c.toString(1)+".people (stub)"};i="disable track track_pageview track_links track_forms register register_once alias unregister identify name_tag set_config people.set people.set_once people.increment people.append people.track_charge people.clear_charges people.delete_user".split(" ");
        for(g=0;g<i.length;g++)f(c,i[g]);b._i.push([a,e,d])};b.__SV=1.2;a=e.createElement("script");a.type="text/javascript";a.async=!0;a.src=("https:"===e.location.protocol?"https:":"http:")+'//cdn.mxpnl.com/libs/mixpanel-2.2.min.js';f=e.getElementsByTagName("script")[0];f.parentNode.insertBefore(a,f)}})(document,window.mixpanel||[]);
SNIPPET;

    private static function addHeadScript($script)
    {
        $old = Session::get('mixpanel_head_script', '');
        \Session::put('mixpanel_head_script', $old . $script);
    }

    private static function addBodyScript($script)
    {
        $old = Session::get('mixpanel_body_script', '');
        \Session::put('mixpanel_body_script', $old . $script);
    }

    public static function headScript()
    {
        if (\Config::get('mixpanel-token')) {
            echo '<script type="text/javascript">';
            echo static::$snippet;
            echo "mixpanel.init('" . \Config::get('mixpanel-token') . "');";
            echo \Session::pull('mixpanel_head_script');
            echo '</script>';
        }
    }

    public static function bodyScript()
    {
        if (\Config::get('mixpanel-token')) {
            echo '<script type="text/javascript">';
            echo \Session::pull('mixpanel_body_script');
            echo '</script>';
        }
    }

    public static function identify($userID, $properties = array())
    {
        static::addHeadScript("mixpanel.identify('$userID');");

        $propertiesJson = json_encode($properties);
        static::addHeadScript("mixpanel.register($propertiesJson);");
    }

    public static function alias($userID)
    {
        static::addHeadScript("mixpanel.alias('$userID');");
    }

    public static function track($eventName, $properties = array())
    {
        $propertiesJson = json_encode($properties);
        static::addBodyScript("mixpanel.track('$eventName', $propertiesJson);");
    }
}
