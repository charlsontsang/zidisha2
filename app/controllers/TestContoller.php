<?php

use Zidisha\Loan\Form\BidPaymentForm;

class TestController extends BaseController
{
    /**
     * @var Zidisha\Loan\Form\BidPaymentForm
     */
    private $bidPaymentForm;

    public function __construct(BidPaymentForm $bidPaymentForm)
    {
        $this->bidPaymentForm = $bidPaymentForm;
    }

    public function displayBidForm()
    {
        return View::Make('bidform');
    }
    public function bidPayment()
    {
        $form = $this->bidPaymentForm;
        $form->handleRequest(Request::instance());
        if ($form->isValid()) {
            $data = $form->getData();

        }else{
            \Zidisha\Flash\Flash::error('Form Not valid');
            return Redirect::back();
        }
    }
}