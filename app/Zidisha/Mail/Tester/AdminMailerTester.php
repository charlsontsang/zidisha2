<?php
namespace Zidisha\Mail\Tester;

use Zidisha\Borrower\Borrower;
use Zidisha\Comment\BorrowerComment;
use Zidisha\Comment\BorrowerCommentQuery;
use Zidisha\Country\CountryQuery;
use Zidisha\Currency\Money;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanQuery;
use Zidisha\Mail\AdminMailer;
use Zidisha\User\User;
use Zidisha\User\UserQuery;

class AdminMailerTester
{
    private $adminMailer;
    private $borrowerCountry;

    public function __construct(AdminMailer $adminMailer)
    {
        $this->adminMailer = $adminMailer;
        $this->borrowerCountry = CountryQuery::create()
            ->filterByBorrowerCountry(true)
            ->findOne();
    }

    public function sendErrorMail()
    {
        $this->adminMailer->sendErrorMail(new \Exception('This is the error message.'));
    }
}
