<?php

use Zidisha\Mail\Mailer;
use Zidisha\Mail\Tester\AdminMailerTester;
use Zidisha\Mail\Tester\BorrowerMailerTester;
use Zidisha\Mail\Tester\LenderMailerTester;
use Zidisha\Mail\Tester\UserMailerTester;

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
    /**
     * @var Zidisha\Mail\Tester\UserMailerTester
     */
    private $userMailerTester;

    public function __construct(LenderMailerTester $lenderMailerTester, BorrowerMailerTester $borrowerMailerTester, AdminMailerTester $adminMailerTester, UserMailerTester $userMailerTester)
    {
        $this->lenderMailerTester = $lenderMailerTester;
        $this->borrowerMailerTester = $borrowerMailerTester;
        $this->adminMailerTester = $adminMailerTester;
        $this->userMailerTester = $userMailerTester;
    }

    public function getAllMails()
    {
        $lenderMailerMethods = get_class_methods($this->lenderMailerTester);
        $borrowerMailerMethods = get_class_methods($this->borrowerMailerTester);
        $adminMailerMethods = get_class_methods($this->adminMailerTester);
        $userMailerMethods = get_class_methods($this->userMailerTester);

        return View::make('admin.test.test-email', compact('lenderMailerMethods', 'borrowerMailerMethods', 'adminMailerMethods', 'userMailerMethods'));
    }

    public function postMail()
    {
        $mailerAndMethod = Input::get('method');

        $mailerAndMethodArray = explode('#', $mailerAndMethod);
        $mailer = $mailerAndMethodArray[0];
        $method = $mailerAndMethodArray[1];

        $validator = Validator::make(Input::all(), ['email' => 'required|email']);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator);
        }
        $email = trim(Input::get('email'));
        Mailer::enableTestMode($email);

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

        if ($mailer == 'admin') {
            if (method_exists($this->adminMailerTester, $method)){
                $this->adminMailerTester->$method();

                \Flash::success('Admin test mail for '.$method.' sent successfully.');
            }
        }

        if ($mailer == 'user') {
            if (method_exists($this->userMailerTester, $method)){
                $this->userMailerTester->$method();

                \Flash::success('User test mail for '.$method.' sent successfully.');
            }
        }

        return Redirect::route('admin:mail:test-mails')->withInput();
    }
}
