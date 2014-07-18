<?php
namespace Zidisha\Mail\Tester;


use Zidisha\Borrower\Borrower;
use Zidisha\Currency\Money;
use Zidisha\Lender\Lender;
use Zidisha\Loan\Bid;
use Zidisha\Loan\Loan;
use Zidisha\Mail\LenderMailer;
use Zidisha\User\User;

class LenderMailerTester
{
    /**
     * @var \Zidisha\Mail\LenderMailer
     */
    private $lenderMailer;

    public function __construct(LenderMailer $lenderMailer)
    {
        $this->lenderMailer = $lenderMailer;
    }

    public function sendFirstBidConfirmationMail()
    {
        $user = new User();
        $user->setEmail('test@test.com');

        $lender = new Lender();
        $lender->setUser($user);

        $bid = new Bid();
        $bid->setLender($lender);

        $this->lenderMailer->sendFirstBidConfirmationMail($bid);
    }

    public function sendOutbidMail()
    {
        $changedBid = [];

        $changedBid['changedAmount'] = Money::create('15');
        $changedBid['acceptedAmount'] = Money::create('12.333');

        $lenderUser = new User();
        $lenderUser->setRole('lender');
        $lenderUser->setEmail('lender@test.com');

        $lender = new Lender();
        $lender->setUser($lenderUser);

        $borrowerUser = new User();
        $borrowerUser->setRole('borrower');
        $borrowerUser->setEmail('lender@test.com');

        $borrower = new Borrower();
        $borrower->setUser($borrowerUser);
        $borrower->setFirstName('First Name');
        $borrower->setLastName('Last Name');

        $loan = new Loan();
        $loan->setBorrower($borrower);

        $bid = new Bid();
        $bid->setLender($lender);
        $bid->setBidAmount(Money::create('55'));
        $bid->setInterestRate('12');
        $bid->setLoan($loan);

        $changedBid['bid'] = $bid;

        $this->lenderMailer->sendOutbidMail($changedBid);
    }

    public function sendLenderWelcomeMail()
    {
        $user = new User();
        $user->setEmail('lender@test.com');

        $lender = new Lender();
        $lender->setUser($user);

        $this->lenderMailer->sendWelcomeMail($lender);
    }
} 