<?php
namespace Zidisha\Mail;


use Carbon\Carbon;
use Zidisha\Balance\InviteTransactionQuery;
use Zidisha\Balance\TransactionQuery;
use Zidisha\Comment\Comment;
use Zidisha\Currency\Money;
use Zidisha\Lender\GiftCard;
use Zidisha\Lender\Invite;
use Zidisha\Lender\Lender;
use Zidisha\Loan\Bid;
use Zidisha\Loan\ForgivenessLoan;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanService;
use Zidisha\User\User;

/**
 * Class LenderMailer
 * @package Zidisha\Mail
 */
class LenderMailer
{

    /**
     * @var mailer
     */
    private $mailer;

    /**
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendFirstBidConfirmationMail(Lender $lender)
    {
        $this->mailer->send(
            'emails.hero',
            [
                'to'         => $lender->getUser()->getEmail(),
                'subject'    => '',
                'templateId' => \Setting::get('sendwithus.lender-loan-first-bid-confirmation-template-id'),
                'username'   => $lender->getUser()->getUsername(),
            ]
        );
    }

    public function sendOutbidMail($changedBid)
    {
        /** @var Bid $bid*/
        $bid = $changedBid['bid'];
        /** @var Money $acceptedAmount */
        $acceptedAmount = $changedBid['acceptedAmount'];
        /** @var Money $changedAmount */
        $changedAmount = $changedBid['changedAmount'];
        $email = $bid->getLender()->getUser()->getEmail();

        $this->mailer->send(
            $acceptedAmount->isZero() ? 'emails.lender.loan.fully-outbid' : 'emails.lender.loan.partially-outbid',
            [
                'to'              => $email,
                'from'            => 'service@zidisha.com',
                'subject'         => 'Outbid Notification.',
                'bidAmount'       => $bid->getBidAmount()->round(2)->getAmount(),
                'bidInterestRate' => $bid->getInterestRate(),
                'outbidAmount'    => $changedAmount->round(2)->getAmount(),
                'acceptedAmount'  => $acceptedAmount->round(2)->getAmount(),
                'borrowerLink'    => $bid->getLoan()->getBorrower()->getUser()->getProfileUrl(),
                'borrowerName'    => $bid->getLoan()->getBorrower()->getName(),
                'loanLink'        => route('loan:index', ['loanId' => $bid->getLoan()->getBorrower()->getActiveLoanId()]),
            ]
        );
    }

    /**
     * @param Bid $bid
     */
    public function sendLoanFullyFundedMail(Bid $bid)
    {
        $email = $bid->getLender()->getUser()->getEmail();

        $subject = \Lang::get('lender.mails.loan-fully-funded.subject', ['borrowerName' => $bid->getBorrower()->getName()]);
        $data['header'] = \Lang::get('lender.mails.loan-fully-funded.accept-message-1', ['borrowerName' => $bid->getBorrower()->getName()]);
        
        //TODO: confirm 
        $message = \Lang::get(
            'lender.mails.loan-fully-funded.accept-message-2',
            [
                'borrowerName' => $bid->getBorrower()->getName(),
                'borrowerProfileLink' => route(
                    'borrower:public-profile',
                    ['username' => $bid->getBorrower()->getUser()->getUsername()]
                ),
                'lendingGroupLink' => route('lender:groups')
            ]
        );
        
        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'      => $email,
                'from'    => 'service@zidisha.com',
                'subject' => $subject,
                'templateId' => \Setting::get('sendwithus.lender-loan-fully-funded-template-id'),
            ]
        );
    }

    public function sendLenderInvite(Lender $lender, Invite $lender_invite, $subject, $customMessage)
    {
        $email = $lender_invite->getEmail();

        $data = array();
        $data['header'] = \Lang::get('lender.mails.lender-invite.header');
        $data['footer'] = \Lang::get('lender.mails.lender-invite.footer');

        $data['button_text'] = "View Projects";
        
        $params['button_url'] = route('lender:invite');
        $profilePicture = $lender->getUser()->getProfilePictureUrl();

        $table = '<table cellspacing="0" cellpadding="10" border="0">';
        $table .= "<tr>";
        $table = $profilePicture ? $table . '<td width="50"><img width="50" style="width:50px;" src="' . $profilePicture . '" /></td>' : $table . "<td><b>Note</b>:</td>";
        $table .= '<td>' . $customMessage . '</td>';
        $table .= "</tr>";
        $table .= "</table>";
        $customMessage = $table;

        $subject = \Lang::get('lender.mails.lender-invite.subject', ['lenderName' => $lender->getName()]);
        $message = \Lang::get('lender.mails.lender-invite.body', ['lenderName' => $lender->getName(), 'customMessage' => $customMessage]);
        $data['content'] = $message;
        
        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'      => $email,
                'from'    => 'service@zidisha.com',
                'subject' => $subject,
                'templateId' => \Setting::get('sendwithus.lender-invite-template-id')
            ]
        );

    }

    public function sendLenderInviteCredit(Invite $invite)
    {
        $email = $invite->getLender()->getUser()->getEmail();
        $inviteeEmail = $invite->getEmail();
        
        $subject = \Lang::get('lender.mails.lender-invite-credit.subject');
        $message = \Lang::get('lender.mails.lender-invite-credit.body', ['inviteeMail' => $inviteeEmail, 'lendingPage' => route('loan:index')]);
        $data['content'] = $message;

        $data['footer'] = \Lang::get('lender.mails.lender-invite-credit.footer');
        $data['button_text'] = \Lang::get('lender.mails.lender-invite-credit.button-text');
        $data['button_url'] = route('loan:index');
        
        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'      => $email,
                'from'    => 'service@zidisha.com',
                'subject' => $subject,
                'templateId' => \Setting::get('sendwithus.lender-invite-credit-template-id')
            ]
        );
    }

    public function sendWelcomeMail(Lender $lender)
    {
        $email = $lender->getUser()->getEmail();

        $this->mailer->send(
            'emails.lender.welcome',
            [
                'to'      => $email,
                'from'    => 'service@zidisha.com',
                'subject' => 'Welcome to Zidisha!'
            ]
        );
    }

    public function sendIntroductionMail(Lender $lender)
    {
        $this->mailer->send(
            'emails.hero',
            [
                'to'         => $lender->getUser()->getEmail(),
                'subject'    => 'Welcome to Zidisha!',
                'templateId' => \Setting::get('sendwithus.introduction-template-id'),
            ]
        );
    }

    public function sendGiftCardMailToSender(GiftCard $giftCard)
    {
        $email = $giftCard->getLender()->getUser()->getEmail();

        $this->mailer->send(
            'emails.lender.gift-card-sender',
            [
                'card'    => $giftCard,
                'to'      => $email,
                'from'    => 'service@zidisha.com',
                'subject' => 'Your Gift Card Order.'
            ]
        );
    }

    public function sendGiftCardMailToRecipient(GiftCard $giftCard)
    {
        $email = $giftCard->getRecipientEmail();

        $this->mailer->send(
            'emails.lender.gift-card-receiver',
            [
                'card'    => $giftCard,
                'to'      => $email,
                'from'    => 'service@zidisha.com',
                'subject' => 'Your Gift Card Order.'
            ]
        );
    }

    public function sendAbandonedMail(Lender $lender)
    {
        $subject = \Lang::get('lender.mails.lender-account-abandoned.subject');
        $message = \Lang::get('lender.mails.lender-account-abandoned.body', ['lenderName' => $lender->getName(), 'siteLink' => \URL::to('/'), 'expiryDate' => Carbon::now()->addMonth()->format('F j, Y')]);
        $data['header'] = $message;

        $this->mailer->send(
            'emails.lender.abandoned',
            [
                'to'         => $lender->getUser()->getEmail(),
                'from'       => 'service@zidisha.com',
                'subject'    => $subject,
                'templateId' => \setting::get('sendwithus.lender-account-abandoned-template-id')
            ]
        );
    }

    public function sendBorrowerCommentNotification(Lender $lender, Loan $loan, Comment $comment, $postedBy, $images)
    {
        $borrower = $loan->getBorrower();
        $parameters = [
            'borrowerName' => $borrower->getName(),
            'message'      => nl2br($comment->getMessage()),
            'postedBy'     => $postedBy,
            'images'       => $images,
        ];
        $message = \Lang::get('lender.mails.borrower-comment-notification.body', $parameters);
        $data['content'] = $message;

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $lender->getUser()->getEmail(),
                'subject'    => \Lang::get('lender.mails.borrower-comment-notification.subject', $parameters),
                'templateId' => \Setting::get('sendwithus.comments-template-id'),
            ]
        );
    }

    public function sendLoanDefaultedMail(Loan $loan, Lender $lender)
    {
        $data = [
            'parameters' => [
                'borrowerName'       => $loan->getBorrower()->getName(),
                'loanUrl'            => route('loan:index', ['loanId' => $loan->getId()]),
                'repaidPercentage'   => $loan->getRepaidPercent(),
                'requestedAmount'    => $loan->getUsdAmount()->getAmount()
            ],
        ];

        $subject = \Lang::get('lender.mails.loan-defaulted.subject');

        $this->mailer->send(
            'emails.label-template',
            $data + [
                'to'         => $lender->getUser()->getEmail(),
                'label'      => 'lender.mails.loan-defaulted.body',
                'subject'    => $subject
            ]
        );
    }
    
    public function sendExpiredLoanMail(Loan $loan, Lender $lender, Money $amount, Money $currentBalance)
    {
        $borrower = $loan->getBorrower();
        $parameters = [
            'borrowerName'  => $borrower->getName(),
            'bidAmount'     => $amount->getAmount(),
            'creditBalance' => $currentBalance->getAmount(),
            'lendLink'      => route('lend:index')
        ];
        $message = \Lang::get('lender.mails.loan-expired.body', $parameters);
        $data['content'] = $message;

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $lender->getUser()->getEmail(),
                'subject'    => \Lang::get('lender.mails.loan-expired.subject', $parameters),
                'templateId' => \Setting::get('sendwithus.lender-expired-loan-template-id'),
            ]
        );        
    }

    public function sendExpiredLoanWithLenderInviteCreditMail(Loan $loan, Lender $lender, Money $amount, Money $inviteCreditBalance)
    {
        $borrower = $loan->getBorrower();
        $parameters = [
            'borrowerName'              => $borrower->getName(),
            'bidAmount'                 => $amount->getAmount(),
            'lenderInviteCreditBalance' => $inviteCreditBalance->getAmount(),
            'lendLink'                  => route('lend:index')
        ];
        $message = \Lang::get('lender.mails.loan-expired-invite.body', $parameters);
        $data['content'] = $message;

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $lender->getUser()->getEmail(),
                'subject'    => \Lang::get('lender.mails.loan-expired-invite.subject', $parameters),
                'templateId' => \Setting::get('sendwithus.lender-expired-loan-template-id'),
            ]
        );
    }

    public function sendAbandonedUserMail(User $user)
    {
        $this->mailer->send(
            'emails.hero',
            [
                'to'         => $user->getEmail(),
                'subject'    => 'Abandoned User Notification',
            ]
        );
    }

    public function sendUnusedFundsNotification(Lender $lender)
    {
        $currentBalance = TransactionQuery::create()
            ->getCurrentBalance($lender->getId());
        
        $data = $this->getFeaturedLoansForMail();
        
        $data['footer'] = \Lang::get('lender.mails.lender-unused-fund.footer');
        
        $data['extra'] = \Lang::get(
            'lender.mails.lender-unused-fund.body',
            ['automaticLendingLink' => route('lender:auto-lending')]
        );

        $subject = \Lang::get('lender.mails.lender-unused-fund.subject');

        $message = \Lang::get('lender.mails.lender-unused-fund.body', ['lenderBalance' => $currentBalance->getAmount()]);
        $data['content'] = $message;

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $lender->getUser()->getEmail(),
                'subject'    => $subject,
                'templateId' => \Setting::get('sendwithus.lender-unused-funds-template-id'),
            ]
        );
    }

    public function sendLoanAboutToExpireMail(Lender $lender)
    {
        $this->mailer->send(
            'emails.hero',
            [
                'to'         => $lender->getUser()->getEmail(),
                'subject'    => 'Loan About to Expire Notification',
                'templateId' => \Setting::get('sendwithus.loan-about-to-expire-mail-template-id'),
            ]
        );                
    }

    public function sendAllowLoanForgivenessMail(Loan $loan, ForgivenessLoan $forgivenessLoan, Lender $lender)
    {        
        //TODO generate links for forgive and reject loan.
        $this->mailer->send(
            'emails.hero',
            [
                'to'         => $lender->getUser()->getEmail(),
                'subject'    => 'Borrower account notifications',
                'templateId' => \Setting::get('sendwithus.lender-loan-forgiveness-mail-template-id'),
            ]
        );
    }

    public function sendNewLoanNotificationMail(Loan $loan, Lender $lender)
    {
        $data = [
            'parameters' => [
                'borrowerName' => $loan->getBorrower()->getName(),
                'loanUrl'      => route('loan:index', ['loanId' => $loan->getId()]),
                'repayDate'    => $loan->getRepaidAt()->format('F j, Y')
            ],
        ];
        
        $subject = \Lang::get('lender.mails.new-loan-notification.subject', ['borrowerName' => $loan->getBorrower()->getName()]);
        
        $this->mailer->send(
            'emails.label-template',
            $data + [
                'to'         => $lender->getUser()->getEmail(),
                'label'      => 'lender.mails.new-loan-notification.lender-body',
                'subject'    => $subject
            ]
        );
    }

    public function sendFollowerNewLoanNotificationMail(Loan $loan, Lender $lender)
    {
        $data = [
            'parameters' => [
                'borrowerName' => $loan->getBorrower()->getName(),
                'loanUrl'      => route('loan:index', ['loanId' => $loan->getId()]),
            ]
        ];

        $subject = \Lang::get('lender.mails.follower-new-loan-notification.subject', ['borrowerName' => $loan->getBorrower()->getName()]);

        $this->mailer->send(
            'emails.lender.new-loan-notification.follower-body',
            $data + [
                'to'         => $lender->getUser()->getEmail(),
                'label'      => 'lenders.mails.new-loan-notification.body',
                'subject'    => $subject
            ]
        );
    }

    public function getFeaturedLoansForMail()
    {
        $conditions = [
            'status'     => Loan::OPEN,
            'categoryId' => '18',
            'sortBy'     => 'raised_percentage',
        ];
        
        /** @var LoanService $loanService */
        $loanService = \App::make('Zidisha\Loan\LoanService');
        
        $featuredLoans = $loanService->searchLoans($conditions);

        $loans = $loansExtra = array();
        /** @var Loan $loan */
        foreach ($featuredLoans as $loan) {
            if ($loan->getRaisedPercentage() < 75) {
                $loans[] = $loan;
            } else {
                $loansExtra[] = $loan;
            }
        }

        shuffle($loans);
        $loans = array_slice($loans, 0, 3);
        $count = count($loans);
        if ($count < 3) return false;


        $data = array();
        $i = 1;
        foreach ($loans as $loan) {
            $n = $i > 1 ? $i : '';
            
            if ($loan->getSummaryTranslation()) {
                $data["content$n"] = $loan->getSummaryTranslation();
            } else {
                $data["content$n"] = $loan->getSummary();
            }
            
            $data["heading$n"] = $loan->getBorrower()->getCountry()->getName();
            $data["title$n"] = $loan->getBorrower()->getName();
            $data["percent$n"] = $loan->getRaisedPercentage() . '%';
            $data["image_src$n"] = $loan->getBorrower()->getUser()->getProfilePictureUrl();
            $data["link$n"] = [
                "text$n" => route('loan:index', [ 'loanId' => $loan->getId() ]),
                "url$n"  => route('loan:index', [ 'loanId' => $loan->getId() ])
            ];
            
            $i += 1;
        }

        return $data;
    }

    public function sendDisbursedLoanMail(Loan $loan, Lender $lender)
    {
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

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $lender->getUser()->getEmail(),
                'subject'    => \Lang::get('lender.mails.loan-disbursed.subject', $parameters),
                'templateId' => \Setting::get('sendwithus.lender-loan-disbursed-template-id'),
            ]
        );
    }

    public function sendReceivedRepaymentMail(Lender $lender, Loan $loan, Money $amount,  Money $currentCredit)
    {
        $parameters = [
            'borrowerName'         => $loan->getBorrower()->getName(),
            'amount'               => $amount,
            'loanUrl'              => route('loan:index', $loan->getId()),
            'currentCredit'        => $currentCredit->getAmount(),
            'lendUrl'              => route('lend:index'),
            'autoLendingUrl'       => route('lender:auto-lending'),
            'accountPreferenceUrl' => route('lender:preference')
        ];

        $body = \Lang::get('lender.mails.loan-repayment-received.body', $parameters);
        $data['content'] = $body;
        $content2 = \Lang::get('lender.mails.loan-repayment-received.message2', $parameters);
        $data['content2'] = $content2;

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $lender->getUser()->getEmail(),
                'subject'    => \Lang::get('lender.mails.loan-repayment-received.subject', $parameters),
                'templateId' => \Setting::get('sendwithus.lender-loan-repayment-template-id'),
            ]
        );
    }

    public function sendReceivedRepaymentCreditBalanceMail(Lender $lender, Money $currentCredit)
    {
        $parameters = [
            'currentCredit'        => $currentCredit->getAmount(),
            'lendUrl'              => route('lend:index'),
        ];

        $body = \Lang::get('lender.mails.loan-repayment-received-balance.body', $parameters);
        $data['content'] = $body;

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $lender->getUser()->getEmail(),
                'subject'    => \Lang::get('lender.mails.loan-repayment-received-balance.subject', $parameters),
                'templateId' => \Setting::get('sendwithus.lender-loan-repayment-template-id'),
            ]
        );
    }

    public function sendRepaidLoanMail(Lender $lender, Loan $loan)
    {
        $data = [
            'parameters' => [
                'borrowerName'     => $loan->getBorrower()->getName(),
                'reviewUrl'          => route('loan:index', $loan->getId()).'#feedback',
            ],
        ];

        $this->mailer->send(
            'emails.label-template',
            $data + [
                'to'         => $lender->getUser()->getEmail(),
                'header'     => \Lang::get('lender.mails.loan-repayment-feedback.body.header', ['borrowerName' => $loan->getBorrower()->getName()]),
                'label'      => 'lender.mails.loan-repayment-feedback.body',
                'subject'    => \Lang::get('lender.mails.loan-repayment-feedback.subject', ['borrowerName' => $loan->getBorrower()->getName()])
            ]
        );
    }

    public function sendRepaidLoanGainMail(Lender $lender, Loan $loan, Money $loanAmount, Money $repaidAmount, Money $gainAmount, $gainPercent)
    {
        $borrower = $loan->getBorrower();
//        $loanAmount = $loan->getUsdAmount();
//        $repaidAmount = $loan->getUsdAmount()->multiply($loan->getRepaidPercent())->divide(100);
//        $gainAmount = $repaidAmount->subtract($loanAmount);
//        $gainPercent = $gainAmount->multiply(100)->divide($loanAmount);
        $parameters = [
            'gainAmount'   => $gainAmount->getAmount(),
            'gainPercent'  => $gainPercent,
            'borrowerName' => $borrower->getName(),
            'loanUrl'      => route('loan:index', $loan->getId()),
            'purpose'      => $loan->getProposal(),
            'loanAmount'   => $loanAmount->getAmount(),
            'repaidAmount' => $repaidAmount->getAmount(),
            'lendUrl'      => route('lend:index'),
            'myStatsUrl'   => route('lender:loans')
        ];

        $body = \Lang::get('lender.mails.loan-repaid-gain.body', $parameters);
        $data['content'] = $body;

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $lender->getUser()->getEmail(),
                'subject'    => \Lang::get('lender.mails.loan-repaid-gain.subject', $parameters),
                'templateId' => \Setting::get('sendwithus.lender-loan-repayment-template-id'), //TODO template for REPAID_GAIN_TAG
            ]
        );
    }
}
