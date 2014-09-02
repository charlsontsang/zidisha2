<?php
namespace Zidisha\Mail\Tester;


use Carbon\Carbon;
use Zidisha\Balance\InviteTransactionQuery;
use Zidisha\Balance\TransactionQuery;
use Zidisha\Borrower\Borrower;
use Zidisha\Currency\Money;
use Zidisha\Lender\Lender;
use Zidisha\Lender\LenderQuery;
use Zidisha\Loan\Bid;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanQuery;
use Zidisha\Loan\LenderRefund;
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
        $borrowerUser->setUsername('borrowerUsername');

        $borrower = new Borrower();
        $borrower->setUser($borrowerUser);
        $borrower->setFirstName('First Name');
        $borrower->setLastName('Last Name');
        $borrower->setActiveLoanId('12');

        $loan = new Loan();
        $loan->setBorrower($borrower);

        $bid = new Bid();
        $bid->setLender($lender);
        $bid->setBidAmount(Money::create('55'));
        $bid->setInterestRate('12');
        $bid->setLoan($loan);

        $changedBid['bid'] = $bid;

        $this->lenderMailer->sendOutbidMail($changedBid);

        $changedBid['acceptedAmount'] = Money::create('0');
        $changedBid['changedAmount'] = Money::create('20');
        
        $bid = new Bid();
        $bid->setLender($lender);
        $bid->setBidAmount(Money::create('20'));
        $bid->setInterestRate('15');
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

    public function sendIntroductionMail()
    {
        $user = new User();
        $user->setEmail('email@test.com');

        $lender = new Lender();
        $lender->setUser($user);

        $this->lenderMailer->sendIntroductionMail($lender);
    }

    public function sendExpiredLoanMail()
    {
        $loan = LoanQuery::create()
            ->findOne();
        $lender = LenderQuery::create()
            ->findOne();

        $amount = Money::create(25);

        $this->lenderMailer->sendExpiredLoanMail($loan, $lender, $amount);
    }

    public function sendExpiredLoanWithLenderInviteCreditMail()
    {
        $loan = LoanQuery::create()
            ->findOne();
        $lender = LenderQuery::create()
            ->findOne();

        $amount = Money::create(25);

        $this->lenderMailer->sendExpiredLoanWithLenderInviteCreditMail($loan, $lender, $amount);
    }

    public function sendAllowLoanForgivenessMail()
    {
        $loan = new Loan();

        $bid = new Bid();
        $bid->setLender(new Lender);
        $bid->getLender()->setUser(new User());
        $bid->getLender()->getUser()->setEmail('test@mail.com');
            
        $this->lenderMailer->sendAllowLoanForgivenessMail($loan, $bid);
    }

    public function sendNewLoanNotificationMail()
    {
        $lenderUser = new User();
        $lenderUser->setRole('lender');
        $lenderUser->setEmail('lender@test.com');

        $lender = new Lender();
        $lender->setUser($lenderUser);

        $borrowerUser = new User();
        $borrowerUser
            ->setRole('borrower')
            ->setEmail('lender@test.com');

        $borrower = new Borrower();
        $borrower
            ->setUser($borrowerUser)
            ->setFirstName('First Name')
            ->setLastName('Last Name');

        $loan = new Loan();
        $loan
            ->setId(1)
            ->setBorrower($borrower)
            ->setRepaidAt(Carbon::now()->subMonth())
            ->setInstallmentDay('12');
        
        $this->lenderMailer->sendNewLoanNotificationMail($loan, $lender);
    }

    public function sendDisbursedLoanMail()
    {
        $lenderUser = new User();
        $lenderUser->setRole('lender');
        $lenderUser->setEmail('lender@test.com');

        $lender = new Lender();
        $lender->setUser($lenderUser);

        $borrowerUser = new User();
        $borrowerUser
            ->setRole('borrower')
            ->setEmail('lender@test.com');

        $borrower = new Borrower();
        $borrower
            ->setUser($borrowerUser)
            ->setFirstName('First Name')
            ->setLastName('Last Name');

        $loan = new Loan();
        $loan
            ->setId(1)
            ->setBorrower($borrower);

        $this->lenderMailer->sendDisbursedLoanMail($loan, $lender);
    }
}
