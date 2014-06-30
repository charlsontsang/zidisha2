<?php
namespace Zidisha\Payment\Handler\Bid;

use Zidisha\Payment\PaymentHandler;

class PendingHandler extends PaymentHandler
{
    public function redirect()
    {
        //Todo: paste a proper line from trello. (see paypalService line:153)
        \Flash::error($this->paymentError->getMessage());
        return \Redirect::route('loan:index', ['id' => $this->payment->getLoanId()]);
    }
}
