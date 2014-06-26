<?php
namespace Zidisha\Payment;


interface PaymentHandler
{
    public function process();

    public function redirect();
}
