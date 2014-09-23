<?php

use Zidisha\Sms\Tester\BorrowerSmsTester;

class SmsTesterController extends BaseController
{
    /**
     * @var Zidisha\Sms\Tester\BorrowerSmsTester
     */
    private $borrowerSmsTester;

    public function __construct(BorrowerSmsTester $borrowerSmsTester)
    {
        $this->borrowerSmsTester = $borrowerSmsTester;
    }

    public function getAllSms()
    {
        $borrowerSms = get_class_methods($this->borrowerSmsTester);

        return View::make('admin.test.test-sms', compact('borrowerSms'));
    }

    public function postSms()
    {
        $method = Input::get('method');
        $sms = Input::get('sms');

        if ($sms == 'borrower') {
            if (method_exists($this->borrowerSmsTester, $method)){
                $this->borrowerSmsTester->$method();

                \Flash::success('Borrower test sms for '.$method.' sent successfully.');
            }
        }

        return Redirect::route('admin:sms:test-sms');

    }

}
