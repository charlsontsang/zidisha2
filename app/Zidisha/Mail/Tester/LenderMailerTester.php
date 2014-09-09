<?php
namespace Zidisha\Mail\Tester;


use Carbon\Carbon;
use Propel\Runtime\ActiveQuery\Criteria;
use Zidisha\Balance\InviteTransactionQuery;
use Zidisha\Balance\TransactionQuery;
use Zidisha\Borrower\Borrower;
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

    public function sendDownBidMail()
    {
        $bid = BidQuery::create()
            ->findOne();
        $lender = $bid->getLender();
        $acceptedAmount = $bid->getBidAmount()->divide(2);
        $outBidAmount = $bid->getBidAmount()->subtract($acceptedAmount);

        $this->lenderMailer->sendDownBidMail($lender, $bid, $acceptedAmount, $outBidAmount);
    }

    public function sendLenderInvite()
    {
        $lender = LenderQuery::create()
            ->findOne();
        $invite = InviteQuery::create()
            ->findOne();
        $invite->setHash('hshshshs');
        $subject = '';
        $message = 'ahdhdhdhd ddjdjd jeeje jdddjdjdd';

        $this->lenderMailer->sendLenderInvite($lender, $invite, $subject, $message);
    }

    public function sendLenderWelcomeMail()
    {
        $lender = LenderQuery::create()
            ->findOne();

        $this->lenderMailer->sendWelcomeMail($lender);
    }

    public function sendIntroductionMail()
    {
        $lender = LenderQuery::create()
            ->findOne();

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
        $inviteCredit = Money::create(100);

        $this->lenderMailer->sendExpiredLoanWithLenderInviteCreditMail($loan, $lender, $amount, $inviteCredit);
    }

    public function sendUnusedFundsNotification()
    {
        $lender = LenderQuery::create()
            ->findOne();

        $this->lenderMailer->sendUnusedFundsNotification($lender);
    }

    public function sendLoanAboutToExpireMail()
    {
        $bid = BidQuery::create()
            ->findOne();
        $loan = $bid->getLoan();
        $params = array(
            'amountStillNeeded' => Money::create('46', 'USD'),
            'borrowerName'      => ucwords(strtolower($loan->getBorrower()->getName())),
            'loanLink'          => route('loan:index', $loan->getId()),
            'inviteLink'        => route('lender:invite'),
        );

        $this->lenderMailer->sendLoanAboutToExpireMail($bid, $params);
    }

    public function sendAllowLoanForgivenessMail()
    {
        $loan = LoanQuery::create()
            ->filterByDisbursedAt(null, Criteria::NOT_EQUAL)
            ->findOne();
        $forgivenessLoan = new ForgivenessLoan();
        $forgivenessLoan->setLoan($loan)
            ->setComment('Forgive this loan comment!')
            ->setVerificationCode(md5(mt_rand(0, 32).time()));
        $lender = LenderQuery::create()
            ->findOne();

        $parameters = [
            'borrowerName'      => $loan->getBorrower()->getName(),
            'disbursedDate'     => $loan->getDisbursedAt()->format('d-m-Y'),
            'message'           => trim($forgivenessLoan->getComment()),
            'outstandingAmount' => $loan->getUsdAmount()->multiply($loan->getPaidPercentage())->divide(100),
            'loanLink'          => route('loan:index', $loan->getId()),
            'yesLink'           => route('loan:index', $loan->getId()).'?v='.$forgivenessLoan->getVerificationCode(),
            'yesImage'          => '/assets/images/loan-forgive/yes.png',
            'noImage'           => '/assets/images/loan-forgive.no.png',
        ];
        $subject = \Lang::get('lender.mails.allow-loan-forgiveness.subject', $parameters);
            
        $this->lenderMailer->sendAllowLoanForgivenessMail($forgivenessLoan, $lender, $parameters, $subject);
    }

    public function sendNewLoanNotificationMail()
    {
        $lastLoan = new Loan();
        $lastLoan->setRepaidAt(Carbon::create(2014, 5, 1));
        
        $loan = LoanQuery::create()
            ->findOne();
        $lender = LenderQuery::create()
            ->findOne();
        
        $this->lenderMailer->sendNewLoanNotificationMail($lender, $loan, $lastLoan);
    }

    public function sendFollowerNewLoanNotificationMail()
    {
        $loan = LoanQuery::create()
            ->findOne();
     
        $lender = LenderQuery::create()
            ->findOne();   

        $this->lenderMailer->sendFollowerNewLoanNotificationMail($lender, $loan);
    }

    public function sendDisbursedLoanMail()
    {
        $loan = LoanQuery::create()
            ->filterByDisbursedAt(null, Criteria::NOT_EQUAL)
            ->filterByRepaidAt(null)
            ->findOne();
        $bid = BidQuery::create()
            ->filterByLoan($loan)
            ->filterByAcceptedAmount(null, Criteria::NOT_EQUAL)
            ->findOne();
        $lender = $bid->getLender();
        $borrower = $loan->getBorrower();
        $parameters = [
            'borrowerName'    => $borrower->getName(),
            'borrowFirstName' => $borrower->getFirstName(),
            'disbursedDate'   => date('F d, Y',  time()),
            'loanPage'        => route('loan:index', $loan->getId()),
            'giftCardPage'    => route('lender:gift-cards')
        ];

        $data['image_src'] = $borrower->getUser()->getProfilePictureUrl();
        $message = \Lang::get('lender.mails.loan-disbursed.message', $parameters);
        $data['header'] = $message;
        $body = \Lang::get('lender.mails.loan-disbursed.body', $parameters);
        $data['content'] = $body;
        $subject = \Lang::get('lender.mails.loan-disbursed.subject', $parameters);
        $this->lenderMailer->sendDisbursedLoanMail($lender, $parameters, $data, $subject);
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

    public function sendAbandonedUserMail()
    {
        $lender = LenderQuery::create()
            ->findOne();

        $this->lenderMailer->sendAbandonedUserMail($lender->getUser());
    }
}
