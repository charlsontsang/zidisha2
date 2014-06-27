<?php
namespace Zidisha\Payment\Handler\UploadFund;

use Zidisha\Payment\PaymentHandler;

class PendingHandler extends PaymentHandler
{
    public function redirect()
    {
        //Todo: paste a proper line from trello. (see paypalService line:153)
        \Flash::error('Thank you! The transaction is not yet complete. We will update your Account when the transaction is completed by Paypal.');
        return \Redirect::route('lender:funds');
    }
}