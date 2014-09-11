<?php
namespace Zidisha\Mail\Tester;


use Carbon\Carbon;
use Propel\Runtime\ActiveQuery\Criteria;
use Zidisha\Balance\InviteTransactionQuery;
use Zidisha\Balance\TransactionQuery;
use Zidisha\Borrower\Borrower;
use Zidisha\Comment\BorrowerComment;
use Zidisha\Comment\BorrowerCommentQuery;
use Zidisha\Currency\Money;
use Zidisha\Lender\Invite;
use Zidisha\Lender\InviteQuery;
use Zidisha\Lender\Lender;
use Zidisha\Lender\LenderQuery;
use Zidisha\Loan\Bid;
use Zidisha\Loan\BidQuery;
use Zidisha\Loan\ForgivenessLoan;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanQuery;
use Zidisha\Loan\LenderRefund;
use Zidisha\Mail\LenderMailer;
use Zidisha\User\User;
use Zidisha\User\UserQuery;

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
        $user->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $lender = new Lender();
        $lender->setUser($user);

        $this->lenderMailer->sendFirstBidConfirmationMail($lender);
    }

    public function sendOutbidMail()
    {
        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $borrower = new Borrower();
        $borrower->setUser($userBorrower)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName');
        $loan = new Loan();
        $loan->setBorrower($borrower);
        $user = new User();
        $user->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $lender = new Lender();
        $lender->setUser($user);
        $bid = new Bid();
        $bid->setInterestRate(5)
            ->setBidAmount(Money::create(20))
            ->setLender($lender)
            ->setLoan($loan);
        $lender = $bid->getLender();

        $this->lenderMailer->sendOutbidMail($lender, $bid);
    }

    public function sendDownBidMail()
    {
        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $borrower = new Borrower();
        $borrower->setUser($userBorrower)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName');
        $loan = new Loan();
        $loan->setBorrower($borrower);
        $user = new User();
        $user->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $lender = new Lender();
        $lender->setUser($user);
        $bid = new Bid();
        $bid->setInterestRate(5)
            ->setBidAmount(Money::create(20))
            ->setLender($lender)
            ->setLoan($loan);
        $lender = $bid->getLender();
        $acceptedAmount = $bid->getBidAmount()->divide(2);
        $outBidAmount = $bid->getBidAmount()->subtract($acceptedAmount);

        $this->lenderMailer->sendDownBidMail($lender, $bid, $acceptedAmount, $outBidAmount);
    }

    public function sendLoanFullyFundedMail()
    {
        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $borrower = new Borrower();
        $borrower->setUser($userBorrower)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName');
        $loan = new Loan();
        $loan->setBorrower($borrower);
        $user = new User();
        $user->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $lender = new Lender();
        $lender->setUser($user);
        $bid = new Bid();
        $bid->setInterestRate(5)
            ->setBidAmount(Money::create(20))
            ->setLender($lender)
            ->setBorrower($borrower)
            ->setLoan($loan);

        $this->lenderMailer->sendLoanFullyFundedMail($bid);
    }

    public function sendLenderInvite()
    {
        $user = new User();
        $user->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $lender = new Lender();
        $lender->setFirstName('lenderFirstName')
            ->setLastName('lenderLastName');
        $lender->setUser($user);
        $invite = new Invite();
        $invite->setHash('invitehash')
            ->setEmail('lenderinvite@mail.com');
        $subject = '';
        $message = 'I\'m inviting you as lender at Zidisha';

        $this->lenderMailer->sendLenderInvite($lender, $invite, $subject, $message);
    }

    public function sendLenderInviteCredit()
    {
        $user = new User();
        $user->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $lender = new Lender();
        $lender->setFirstName('lenderFirstName')
            ->setLastName('lenderLastName');
        $lender->setUser($user);
        $invite = new Invite();
        $invite->setHash('invitehash')
            ->setEmail('lenderinvite@mail.com')
            ->setLender($lender);

        $this->lenderMailer->sendLenderInviteCredit($invite);
    }

    public function sendLenderWelcomeMail()
    {
        $user = new User();
        $user->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $lender = new Lender();
        $lender->setFirstName('lenderFirstName')
            ->setLastName('lenderLastName');
        $lender->setUser($user);

        $this->lenderMailer->sendWelcomeMail($lender);
    }

    public function sendIntroductionMail()
    {
        $user = new User();
        $user->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $lender = new Lender();
        $lender->setFirstName('lenderFirstName')
            ->setLastName('lenderLastName')
            ->setUser($user);

        $this->lenderMailer->sendIntroductionMail($lender);
    }

    public function sendBorrowerCommentNotification()
    {
        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $borrower = new Borrower();
        $borrower->setUser($userBorrower)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName');
        $loan = new Loan();
        $loan->setBorrower($borrower);
        $user = new User();
        $user->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $lender = new Lender();
        $lender->setFirstName('lenderFirstName')
            ->setLastName('lenderLastName')
            ->setUser($user);
        $comment = new BorrowerComment();
        $comment->setMessage('this is comment for borrower!!');
        $postedBy = 'dmdm by hddhd on ffjfjfjf';
        $images = '.....';

        $this->lenderMailer->sendBorrowerCommentNotification($lender, $loan, $comment, $postedBy, $images);
    }

    public function sendLoanDefaultedMail()
    {
        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $borrower = new Borrower();
        $borrower->setUser($userBorrower)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName');
        $loan = new Loan();
        $loan->setBorrower($borrower)
            ->setUsdAmount(Money::create('400'));
        $user = new User();
        $user->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $lender = new Lender();
        $lender->setFirstName('lenderFirstName')
            ->setLastName('lenderLastName')
            ->setUser($user);

        $this->lenderMailer->sendLoanDefaultedMail($loan, $lender);
    }

    public function sendExpiredLoanMail()
    {
        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $borrower = new Borrower();
        $borrower->setUser($userBorrower)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName');
        $loan = new Loan();
        $loan->setBorrower($borrower)
            ->setUsdAmount(Money::create('400'));
        $user = new User();
        $user->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $lender = new Lender();
        $lender->setFirstName('lenderFirstName')
            ->setLastName('lenderLastName')
            ->setUser($user);

        $amount = Money::create(25);
        $currentCredit = Money::create(100);

        $this->lenderMailer->sendExpiredLoanMail($loan, $lender, $amount, $currentCredit);
    }

    public function sendExpiredLoanWithLenderInviteCreditMail()
    {
        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $borrower = new Borrower();
        $borrower->setUser($userBorrower)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName');
        $loan = new Loan();
        $loan->setBorrower($borrower)
            ->setUsdAmount(Money::create('400'));
        $user = new User();
        $user->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $lender = new Lender();
        $lender->setFirstName('lenderFirstName')
            ->setLastName('lenderLastName')
            ->setUser($user);

        $amount = Money::create(25);
        $inviteCredit = Money::create(100);

        $this->lenderMailer->sendExpiredLoanWithLenderInviteCreditMail($loan, $lender, $amount, $inviteCredit);
    }

    public function sendAbandonedUserMail()
    {
        $user = new User();
        $user->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $lender = new Lender();
        $lender->setFirstName('lenderFirstName')
            ->setLastName('lenderLastName')
            ->setUser($user);

        $this->lenderMailer->sendAbandonedUserMail($lender);
    }

    public function sendUnusedFundsNotification()
    {
        $user = new User();
        $user->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $lender = new Lender();
        $lender->setFirstName('lenderFirstName')
            ->setLastName('lenderLastName')
            ->setUser($user);

        $this->lenderMailer->sendUnusedFundsNotification($lender);
    }

    public function sendLoanAboutToExpireMail()
    {
        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $borrower = new Borrower();
        $borrower->setUser($userBorrower)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName');
        $loan = new Loan();
        $loan->setBorrower($borrower);
        $user = new User();
        $user->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $lender = new Lender();
        $lender->setFirstName('lenderFirstName')
            ->setLastName('lenderLastName')
            ->setUser($user);
        $bid = new Bid();
        $bid->setInterestRate(5)
            ->setBidAmount(Money::create(20))
            ->setLender($lender)
            ->setBorrower($borrower)
            ->setBidAt(new \DateTime())
            ->setLoan($loan);

        $this->lenderMailer->sendLoanAboutToExpireMail($bid);
    }

    public function sendAllowLoanForgivenessMail()
    {
        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $borrower = new Borrower();
        $borrower->setUser($userBorrower)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName');
        $loan = new Loan();
        $loan->setBorrower($borrower)
            ->setDisbursedAt(Carbon::now()->subMonths(6));
        $user = new User();
        $user->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $lender = new Lender();
        $lender->setFirstName('lenderFirstName')
            ->setLastName('lenderLastName')
            ->setUser($user);
        $forgivenessLoan = new ForgivenessLoan();
        $forgivenessLoan->setLoan($loan)
            ->setComment('Forgive this loan comment!')
            ->setVerificationCode(md5(mt_rand(0, 32).time()));

        $this->lenderMailer->sendAllowLoanForgivenessMail($loan, $forgivenessLoan, $lender);
    }

    public function sendNewLoanNotificationMail()
    {
        $lastLoan = new Loan();
        $lastLoan->setRepaidAt(Carbon::create(2014, 5, 1));

        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $borrower = new Borrower();
        $borrower->setUser($userBorrower)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName');
        $loan = new Loan();
        $loan->setBorrower($borrower)
            ->setDisbursedAt(Carbon::now()->subMonths(6));
        $user = new User();
        $user->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $lender = new Lender();
        $lender->setFirstName('lenderFirstName')
            ->setLastName('lenderLastName')
            ->setUser($user);
        
        $this->lenderMailer->sendNewLoanNotificationMail($lender, $loan, $lastLoan);
    }

    public function sendFollowerNewLoanNotificationMail()
    {
        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $borrower = new Borrower();
        $borrower->setUser($userBorrower)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName');
        $loan = new Loan();
        $loan->setBorrower($borrower)
            ->setDisbursedAt(Carbon::now()->subMonths(6));
        $user = new User();
        $user->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $lender = new Lender();
        $lender->setFirstName('lenderFirstName')
            ->setLastName('lenderLastName')
            ->setUser($user);

        $this->lenderMailer->sendFollowerNewLoanNotificationMail($lender, $loan);
    }

    public function sendDisbursedLoanMail()
    {
        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $borrower = new Borrower();
        $borrower->setUser($userBorrower)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName');
        $loan = new Loan();
        $loan->setBorrower($borrower)
            ->setDisbursedAt(Carbon::now()->subMonths(6));
        $user = new User();
        $user->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $lender = new Lender();
        $lender->setFirstName('lenderFirstName')
            ->setLastName('lenderLastName')
            ->setUser($user);

        $this->lenderMailer->sendDisbursedLoanMail($lender, $loan);
    }



    public function sendReceivedRepaymentMail()
    {
        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $borrower = new Borrower();
        $borrower->setUser($userBorrower)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName');
        $loan = new Loan();
        $loan->setBorrower($borrower)
            ->setDisbursedAt(Carbon::now()->subMonths(6));
        $user = new User();
        $user->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $lender = new Lender();
        $lender->setFirstName('lenderFirstName')
            ->setLastName('lenderLastName')
            ->setUser($user);

        $this->lenderMailer->sendReceivedRepaymentMail($lender, $loan, Money::create(10, 'USD'), Money::create(100, 'USD'));
    }

    public function sendReceivedRepaymentCreditBalanceMail()
    {
        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $borrower = new Borrower();
        $borrower->setUser($userBorrower)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName');
        $loan = new Loan();
        $loan->setBorrower($borrower)
            ->setDisbursedAt(Carbon::now()->subMonths(6));
        $user = new User();
        $user->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $lender = new Lender();
        $lender->setFirstName('lenderFirstName')
            ->setLastName('lenderLastName')
            ->setUser($user);

        $this->lenderMailer->sendReceivedRepaymentCreditBalanceMail($lender, Money::create(240, 'USD'));
    }

    public function sendRepaidLoanMail()
    {
        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $borrower = new Borrower();
        $borrower->setUser($userBorrower)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName');
        $loan = new Loan();
        $loan->setBorrower($borrower)
            ->setDisbursedAt(Carbon::now()->subMonths(6));
        $user = new User();
        $user->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $lender = new Lender();
        $lender->setFirstName('lenderFirstName')
            ->setLastName('lenderLastName')
            ->setUser($user);

        $this->lenderMailer->sendRepaidLoanMail($lender, $loan);
    }

    public function sendRepaidLoanGainMail()
    {
        $userBorrower = new User();
        $userBorrower->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $borrower = new Borrower();
        $borrower->setUser($userBorrower)
            ->setFirstName('borrowerFirstName')
            ->setLastName('borrowerLastName');
        $loan = new Loan();
        $loan->setBorrower($borrower)
            ->setDisbursedAt(Carbon::now()->subMonths(6));
        $user = new User();
        $user->setUsername('LenderTest')
            ->setEmail('lendertest@gmail.com');
        $lender = new Lender();
        $lender->setFirstName('lenderFirstName')
            ->setLastName('lenderLastName')
            ->setUser($user);

        $loanAmount = Money::create(240, 'USD');
        $repaidAmount = Money::create(240, 'USD');
        $gainAmount = Money::create(20, 'USD');
        $gainPercent = 4;

        $this->lenderMailer->sendRepaidLoanGainMail($lender, $loan, $loanAmount, $repaidAmount, $gainAmount, $gainPercent);
    }
}
