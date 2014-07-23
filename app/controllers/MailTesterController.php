<?php

use Zidisha\Mail\Tester\AdminMailerTester;
use Zidisha\Mail\Tester\BorrowerMailerTester;
use Zidisha\Mail\Tester\LenderMailerTester;

class MailTesterController extends BaseController
{
    /**
     * @var Zidisha\Mail\Tester\LenderMailerTester
     */
    private $lenderMailerTester;
    /**
     * @var Zidisha\Mail\Tester\BorrowerMailerTester
     */
    private $borrowerMailerTester;
    /**
     * @var Zidisha\Mail\Tester\AdminMailerTester
     */
    private $adminMailerTester;

    public function __construct(LenderMailerTester $lenderMailerTester, BorrowerMailerTester $borrowerMailerTester, AdminMailerTester $adminMailerTester)
    {
        $this->lenderMailerTester = $lenderMailerTester;
        $this->borrowerMailerTester = $borrowerMailerTester;
        $this->adminMailerTester = $adminMailerTester;
    }

    public function getAllMails()
    {
        $lenderMailerMethods = get_class_methods($this->lenderMailerTester);
        $borrowerMailerMethods = get_class_methods($this->borrowerMailerTester);
        $adminMailerMethods = get_class_methods($this->adminMailerTester);

        return View::make('admin.testmails.index', compact('lenderMailerMethods', 'borrowerMailerMethods', 'adminMailerMethods'));
    }

    public function postMail()
    {
        $method = Input::get('method');
        $mailer = Input::get('mailer');

        if ($mailer == 'lender') {
            if (method_exists($this->lenderMailerTester, $method)){
                $this->lenderMailerTester->$method();

                \Flash::success('Lender test mail for '.$method.' sent successfully.');
            }
        }

        if ($mailer == 'borrower') {
            if (method_exists($this->borrowerMailerTester, $method)){
                $this->borrowerMailerTester->$method();

                \Flash::success('Borrower test mail for '.$method.' sent successfully.');
            }
        }

        return Redirect::route('admin:mail:test-mails');
    }
}
