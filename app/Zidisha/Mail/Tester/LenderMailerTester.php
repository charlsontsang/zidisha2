<?php
namespace Zidisha\Mail\Tester;


use Carbon\Carbon;
use Zidisha\Balance\InviteTransactionQuery;
use Zidisha\Balance\TransactionQuery;
use Zidisha\Borrower\Borrower;
use Zidisha\Comment\BorrowerCommentQuery;
use Zidisha\Currency\Money;
use Zidisha\Lender\Lender;
use Zidisha\Lender\LenderQuery;
use Zidisha\Loan\Bid;
use Zidisha\Loan\BidQuery;
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
        $bid = BidQuery::create()
            ->findOne();
        $lender = $bid->getLender();

        $this->lenderMailer->sendFirstBidConfirmationMail($lender);
    }

    public function sendOutbidMail()
    {
        $bid = BidQuery::create()
            ->findOne();
        $lender = $bid->getLender();

        $this->lenderMailer->sendOutbidMail($lender, $bid);
    }

    public function sendOutbidMailOld()
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
        $currentCredit = Money::create(100);

        $this->lenderMailer->sendExpiredLoanMail($loan, $lender, $amount, $currentCredit);
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

    public function sendLoanDefaultedMail()
    {
        $loan = LoanQuery::create()
            ->findOne();

        $lender = LenderQuery::create()
            ->findOne();

        $this->lenderMailer->sendLoanDefaultedMail($loan, $lender);
    }

    public function sendReceivedRepaymentMail()
    {
        $loan = LoanQuery::create()
        ->findOne();
        $loan
            ->setAmount(Money::create(800, $loan->getCurrencyCode()))
            ->setPaidAmount(Money::create(530, $loan->getCurrencyCode()));
        $lender = LenderQuery::create()
            ->findOne();

        $this->lenderMailer->sendReceivedRepaymentMail($lender, $loan, Money::create(10, 'USD'), Money::create(100, 'USD'));
    }

    public function sendReceivedRepaymentCreditBalanceMail()
    {
        $lender = LenderQuery::create()
            ->findOne();

        $this->lenderMailer->sendReceivedRepaymentCreditBalanceMail($lender, Money::create(240, 'USD'));
    }

    public function sendRepaidLoanMail()
    {
        $loan = LoanQuery::create()
            ->findOne();
        $lender = LenderQuery::create()
            ->findOne();

        $this->lenderMailer->sendRepaidLoanMail($lender, $loan);
    }

    public function sendRepaidLoanGainMail()
    {
        $loan = LoanQuery::create()
            ->findOne();
        $lender = LenderQuery::create()
            ->findOne();
        $loanAmount = Money::create(240, 'USD');
        $repaidAmount = Money::create(240, 'USD');
        $gainAmount = Money::create(20, 'USD');
        $gainPercent = 4;

        $this->lenderMailer->sendRepaidLoanGainMail($lender, $loan, $loanAmount, $repaidAmount, $gainAmount, $gainPercent);
    }

    public function sendBorrowerCommentNotification()
    {
        $lender = LenderQuery::create()
            ->findOne();
        $loan = LoanQuery::create()
            ->findOne();
        $comment = BorrowerCommentQuery::create()
            ->findOne();
        $postedBy = 'dmdm by hddhd on ffjfjfjf';
        $images = '.....';

        $this->lenderMailer->sendBorrowerCommentNotification($lender, $loan, $comment, $postedBy, $images);
    }
}
