<?php
return [
    'mode' => \Setting::get('paypal.mode'),
    'acct1.UserName' => \Setting::get('paypal.username'),
    'acct1.Password' => \Setting::get('paypal.password'),
    'acct1.Signature' => \Setting::get('paypal.signature'),
    'currency_code' => 'USD',
    'ipn_url' => '---'
];