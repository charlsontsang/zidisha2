<?php
namespace Zidisha\Payment\Handler\UploadFund;

use Zidisha\Payment\PaymentHandler;

class PendingHandler extends PaymentHandler
{
    public function redirect()
    {
        //Todo: paste a proper line from trello. (see paypalService line:153)
        \Flash::error('common.validation.error');
        return \Redirect::route('lender:funds');
    }
}