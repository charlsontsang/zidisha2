<?php
namespace Zidisha\Mail;


use Carbon\Carbon;
use Zidisha\Balance\InviteTransactionQuery;
use Zidisha\Balance\TransactionQuery;
use Zidisha\Balance\WithdrawalRequest;
use Zidisha\Borrower\Borrower;
use Zidisha\Comment\Comment;
use Zidisha\Currency\Money;
use Zidisha\Lender\GiftCard;
use Zidisha\Lender\Invite;
use Zidisha\Lender\InviteQuery;
use Zidisha\Lender\Lender;
use Zidisha\Lender\LenderQuery;
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
        $this->mailer->queue(
            'emails.hero',
            [
                'to'         => $lender->getUser()->getEmail(),
                'subject'    => '',
                'templateId' => \Setting::get('sendwithus.lender-loan-first-bid-confirmation-template-id'),
                'username'   => $lender->getUser()->getUsername(),
            ]
        );
    }

    public function sendOutbidMail(Lender $lender, Bid $bid)
    {
        $borrower = $bid->getLoan()->getBorrower();
        $data = [
            'parameters' => [
                'borrowerLink' => route('borrower:public-profile', $borrower->getUser()->getUsername()),
                'borrowerName' => ucwords(strtolower($borrower->getName())),
                'bidInterest'  => $bid->getInterestRate(),
                'bidAmount'    => $bid->getBidAmount(),
                'outBidAmount' => $bid->getBidAmount(),
            ],
        ];

        $this->mailer->queue(
            'emails.label-template',
            $data + [
                'to'      => $lender->getUser()->getEmail(),
                'label'   => 'lender.mails.out-bid-notification.body',
                'subject' => \Lang::get('lender.mails.out-bid-notification.subject'),
            ]
        );
    }

    public function sendDownBidMail(Lender $lender, Bid $bid, Money $acceptedAmount, Money $outBidAmount)
    {
        $borrower = $bid->getLoan()->getBorrower();
        $data = [
            'parameters' => [
                'borrowerLink'      => route('borrower:public-profile', $borrower->getUser()->getUsername()),
                'borrowerName'      => ucwords(strtolower($borrower->getName())),
                'bidInterest'       => $bid->getInterestRate(),
                'bidAmount'         => $bid->getBidAmount(),
                'outBidAmount'      => $outBidAmount,
                'remainedBidAmount' => $acceptedAmount,
            ],
        ];

        $this->mailer->queue(
            'emails.label-template',
            $data + [
                'to'      => $lender->getUser()->getEmail(),
                'label'   => 'lender.mails.down-bid-notification.body',
                'subject' => \Lang::get('lender.mails.down-bid-notification.subject'),
            ]
        );
    }

    public function sendLoanFullyFundedMail(Bid $bid)
    {
        $email = $bid->getLender()->getUser()->getEmail();

        $subject = \Lang::get('lender.mails.loan-fully-funded.subject', ['borrowerName' => $bid->getBorrower()->getName()]);
        $data['header'] = \Lang::get(
            'lender.mails.loan-fully-funded.accept-message-1',
            ['borrowerName' => $bid->getBorrower()->getName()]
        );

        $data['content'] = \Lang::get(
            'lender.mails.loan-fully-funded.accept-message-2',
            [
                'borrowerName'        => $bid->getBorrower()->getName(),
                'borrowerProfileLink' => route(
                    'borrower:public-profile',
                    ['username' => $bid->getBorrower()->getUser()->getUsername()]
                ),
                'lendingGroupLink'    => route('lender:groups')
            ]
        );

        $this->mailer->queue(
            'emails.hero',
            $data + [
                'to'         => $email,
                'from'       => 'service@zidisha.com',
                'subject'    => $subject,
                'templateId' => \Setting::get('sendwithus.lender-loan-fully-funded-template-id'),
            ]
        );
    }

    public function sendLenderInvite(Lender $lender, Invite $lender_invite, $subject, $customMessage)
    {
        $email = $lender_invite->getEmail();
        $data['header'] = \Lang::get('lender.mails.lender-invite.header');
        $data['footer'] = \Lang::get('lender.mails.lender-invite.footer');
        $data['button_text'] = \Lang::get('lender.mails.lender-invite.button-text');
        $data['button_url'] = route('lender:invitee', $lender->getUser()->getUsername()) . '?h=' . $lender_invite->getHash();
        $profilePicture = $lender->getUser()->getProfilePictureUrl();

        $table = '<table cellspacing="0" cellpadding="10" border="0">';
        $table .= "<tr>";
        $table = $profilePicture ? $table . '<td width="50"><img width="50" style="width:50px;" src="' . $profilePicture . '" /></td>' : $table . "<td><b>Note</b>:</td>";
        $table .= '<td>' . $customMessage . '</td>';
        $table .= "</tr>";
        $table .= "</table>";
        $customMessage = $table;

        $subject = $subject ?: \Lang::get('lender.mails.lender-invite.subject', ['lenderName' => $lender->getName()]);
        $message = \Lang::get(
            'lender.mails.lender-invite.body',
            ['lenderName' => $lender->getName(), 'customMessage' => $customMessage]
        );
        $data['content'] = $message;

        $this->mailer->queue(
            'emails.hero',
            $data + [
                'to'         => $email,
                'subject'    => $subject,
                'templateId' => \Setting::get('sendwithus.lender-invite-credit-template-id')
            ]
        );
    }

    public function sendLenderInviteCredit(Invite $invite)
    {
        $email = $invite->getLender()->getUser()->getEmail();
        $inviteeEmail = $invite->getEmail();

        $subject = \Lang::get('lender.mails.lender-invite-credit.subject');
        $message = \Lang::get(
            'lender.mails.lender-invite-credit.body',
            ['inviteeMail' => $inviteeEmail, 'lendingPage' => route('loan:index')]
        );
        $data['content'] = $message;

        $data['footer'] = \Lang::get('lender.mails.lender-invite-credit.footer');
        $data['button_text'] = \Lang::get('lender.mails.lender-invite-credit.button-text');
        $data['button_url'] = route('loan:index');

        $this->mailer->queue(
            'emails.hero',
            $data + [
                'to'         => $email,
                'subject'    => $subject,
                'templateId' => \Setting::get('sendwithus.lender-invite-credit-template-id')
            ]
        );
    }

    public function sendWelcomeMail(Lender $lender)
    {
        $data = [
            'parameters' => [
                'lendLink' => route('lend:index'),
            ],
        ];

        $this->mailer->queue(
            'emails.label-template',
            $data + [
                'to'      => $lender->getUser()->getEmail(),
                'label'   => 'lender.mails.register-welcome.body',
                'subject' => \Lang::get('lender.mails.register-welcome.subject'),
            ]
        );
    }

    public function sendIntroductionMail(Lender $lender)
    {
        $this->mailer->send(
            'emails.hero',
            [
                'to'         => $lender->getUser()->getEmail(),
                'subject'    => '',
                'templateId' => \Setting::get('sendwithus.introduction-template-id'),
            ]
        );
    }

    //TODO
    public function sendGiftCardMailToSender(GiftCard $giftCard)
    {
        $email = $giftCard->getLender()->getUser()->getEmail();

        $this->mailer->queue(
            'emails.lender.gift-card-sender',
            [
                'card'    => $giftCard,
                'to'      => $email,
                'from'    => 'service@zidisha.com',
                'subject' => 'Your Gift Card Order.'
            ]
        );
    }

    //TODO
    public function sendGiftCardMailToRecipient(GiftCard $giftCard)
    {
        $email = $giftCard->getRecipientEmail();

        $this->mailer->queue(
            'emails.lender.gift-card-receiver',
            [
                'card'    => $giftCard,
                'to'      => $email,
                'from'    => 'service@zidisha.com',
                'subject' => 'Your Gift Card Order.'
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

        $this->mailer->queue(
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
                'borrowerName'     => $loan->getBorrower()->getName(),
                'loanUrl'          => route('loan:index', $loan->getId()),
                'repaidPercentage' => $loan->getRepaidPercent(),
                'requestedAmount'  => $loan->getUsdAmount()
            ],
        ];

        $subject = \Lang::get('lender.mails.loan-defaulted.subject');

        $this->mailer->queue(
            'emails.label-template',
            $data + [
                'to'      => $lender->getUser()->getEmail(),
                'label'   => 'lender.mails.loan-defaulted.body',
                'subject' => $subject
            ]
        );
    }

    public function sendExpiredLoanMail(Loan $loan, Lender $lender, Money $amount, Money $currentBalance)
    {
        $borrower = $loan->getBorrower();
        $parameters = [
            'borrowerName'  => $borrower->getName(),
            'bidAmount'     => $amount,
            'creditBalance' => $currentBalance,
            'lendLink'      => route('lend:index')
        ];
        $message = \Lang::get('lender.mails.loan-expired.body', $parameters);
        $data['content'] = $message;

        $this->mailer->queue(
            'emails.hero',
            $data + [
                'to'         => $lender->getUser()->getEmail(),
                'subject'    => \Lang::get('lender.mails.loan-expired.subject', $parameters),
                'templateId' => \Setting::get('sendwithus.lender-expired-loan-template-id'),
            ]
        );
    }

    public function sendExpiredLoanWithLenderInviteCreditMail(
        Loan $loan,
        Lender $lender,
        Money $amount,
        Money $inviteCreditBalance
    ) {
        $borrower = $loan->getBorrower();
        $parameters = [
            'borrowerName'              => $borrower->getName(),
            'bidAmount'                 => $amount,
            'lenderInviteCreditBalance' => $inviteCreditBalance,
            'lendLink'                  => route('lend:index')
        ];
        $message = \Lang::get('lender.mails.loan-expired-invite.body', $parameters);
        $data['content'] = $message;

        $this->mailer->queue(
            'emails.hero',
            $data + [
                'to'         => $lender->getUser()->getEmail(),
                'subject'    => \Lang::get('lender.mails.loan-expired-invite.subject', $parameters),
                'templateId' => \Setting::get('sendwithus.lender-expired-loan-template-id'),
            ]
        );
    }

    public function sendAbandonedUserMail(Lender $lender)
    {
        $parameters = [
            'lenderName' => $lender->getName(),
            'siteLink'   => route('home'),
            'expireDate' => Carbon::now()->addMonth()->format('d-m-Y'),
        ];
        $message = \Lang::get('lender.mails.abandoned-user-mail.body', $parameters);
        $data['content'] = $message;

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $lender->getUser()->getEmail(),
                'subject'    => \Lang::get('lender.mails.abandoned-user-mail.subject', $parameters),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id'),
            ]
        );
    }

    public function sendUnusedFundsNotification(Lender $lender)
    {
        $currentBalance = TransactionQuery::create()
            ->getCurrentBalance($lender->getId());

        $data = $this->getFeaturedLoansForMail();

        $data['footer'] = \Lang::get('lender.mails.lender-unused-fund.footer');
        $data['available_amount'] = $currentBalance;
        $data['header'] = (string)$currentBalance;

        $data['extra'] = \Lang::get(
            'lender.mails.lender-unused-fund.body',
            ['automaticLendingLink' => route('lender:auto-lending')]
        );

        $subject = \Lang::get('lender.mails.lender-unused-fund.subject');

        $message = \Lang::get('lender.mails.lender-unused-fund.body', ['lenderBalance' => $currentBalance]);
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

    public function sendLoanAboutToExpireMail(Bid $bid)
    {
        $lender = $bid->getLender();
        $loan = $bid->getLoan();
        $parameters = [
            'amountStillNeeded' => Money::create('46', 'USD'),
            'borrowerName'      => ucwords(strtolower($loan->getBorrower()->getName())),
            'loanLink'          => route('loan:index', $loan->getId()),
            'inviteLink'        => route('lender:invite'),
            'recentBidDate'     => $bid->getBidAt()->format('d-m-Y'),
        ];

        $body = \Lang::get('lender.mails.loan-about-to-expire.body', $parameters);
        $data['content'] = $body;

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $lender->getUser()->getEmail(),
                'subject'    => \Lang::get('lender.mails.loan-about-to-expire.subject', $parameters),
                'templateId' => \Setting::get('sendwithus.loan-about-to-expire-mail-template-id')
            ]
        );
    }

    public function sendAllowLoanForgivenessMail(Loan $loan, ForgivenessLoan $forgivenessLoan, Lender $lender)
    {
        $parameters = [
            'borrowerName'      => $loan->getBorrower()->getName(),
            'disbursedDate'     => $loan->getDisbursedAt()->format('d-m-Y'),
            'message'           => trim($forgivenessLoan->getComment()),
            'outstandingAmount' => $loan->getUsdAmount()->multiply($loan->getPaidPercentage())->divide(100),
            'loanLink'          => route('loan:index', $loan->getId()),
            'yesLink'           => route('loan:index', $loan->getId()) . '?v=' . $forgivenessLoan->getVerificationCode(),
            'yesImage'          => '/assets/images/loan-forgive/yes.png',
            'noImage'           => '/assets/images/loan-forgive.no.png',
            'noLink'            => route(
                    'loan:index',
                    $forgivenessLoan->getLoanId()
                ) . '?v=' . $forgivenessLoan->getVerificationCode() . '&lid=' . $lender->getId() . "&dntfrg=1",
        ];
        $subject = \Lang::get('lender.mails.allow-loan-forgiveness.subject', $parameters);
        $message = \Lang::get('lender.mails.allow-loan-forgiveness.body', $parameters);
        $data['content'] = $message;

        $this->mailer->queue(
            'emails.hero',
            $data + [
                'to'         => $lender->getUser()->getEmail(),
                'subject'    => $subject,
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id'),
            ]
        );
    }

    public function sendNewLoanNotificationMail(Lender $lender, Loan $loan, Loan $lastLoan)
    {
        $parameters = [
            'borrowerName' => $loan->getBorrower()->getName(),
            'loanUrl'      => route('loan:index', ['loanId' => $loan->getId()]),
            'repayDate'    => $lastLoan->getRepaidAt()->format('F j, Y')
        ];

        $this->mailer->queue(
            'emails.label-template',
            [
                'to'         => $lender->getUser()->getEmail(),
                'subject'    => \Lang::get('lender.mails.new-loan-notification.subject', $parameters),
                'label'      => 'lender.mails.new-loan-notification.lender-body',
                'parameters' => $parameters,
            ]
        );
    }

    public function sendFollowerNewLoanNotificationMail(Lender $lender, Loan $loan)
    {
        $parameters = [
            'borrowerName' => $loan->getBorrower()->getName(),
            'loanUrl'      => route('loan:index', ['loanId' => $loan->getId()]),
        ];

        $this->mailer->queue(
            'emails.label-template',
            [
                'to'         => $lender->getUser()->getEmail(),
                'label'      => 'lender.mails.new-loan-notification.follower-body',
                'subject'    => \Lang::get('lender.mails.new-loan-notification.subject', $parameters),
                'parameters' => $parameters,
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
        if ($count < 3) {
            return false;
        }


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
                "text$n" => route('loan:index', ['loanId' => $loan->getId()]),
                "url$n"  => route('loan:index', ['loanId' => $loan->getId()])
            ];

            $i += 1;
        }

        return $data;
    }

    public function sendDisbursedLoanMail(Lender $lender, Loan $loan)
    {
        $borrower = $loan->getBorrower();
        $parameters = [
            'borrowerName'    => $borrower->getName(),
            'borrowFirstName' => $borrower->getFirstName(),
            'disbursedDate'   => date('F d, Y', time()),
            'loanPage'        => route('loan:index', $loan->getId()),
            'giftCardPage'    => route('lender:gift-cards')
        ];

        $data['image_src'] = $borrower->getUser()->getProfilePictureUrl();
        $message = \Lang::get('lender.mails.loan-disbursed.message', $parameters);
        $data['header'] = $message;
        $body = \Lang::get('lender.mails.loan-disbursed.body', $parameters);
        $data['content'] = $body;

        $this->mailer->queue(
            'emails.hero',
            $data + [
                'to'         => $lender->getUser()->getEmail(),
                'subject'    => \Lang::get('lender.mails.loan-disbursed.subject', $parameters),
                'templateId' => \Setting::get('sendwithus.lender-loan-disbursed-template-id'),
            ]
        );
    }

    public function sendReceivedRepaymentMail(Lender $lender, Loan $loan, Money $amount, Money $currentCredit)
    {
        $parameters = [
            'borrowerName'         => $loan->getBorrower()->getName(),
            'amount'               => $amount,
            'loanUrl'              => route('loan:index', $loan->getId()),
            'currentCredit'        => $currentCredit,
            'lendUrl'              => route('lend:index'),
            'autoLendingUrl'       => route('lender:auto-lending'),
            'accountPreferenceUrl' => route('lender:preference')
        ];

        $body = \Lang::get('lender.mails.loan-repayment-received.body', $parameters);
        $data['content'] = $body;

        $this->mailer->queue(
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
            'currentCredit' => $currentCredit,
            'lendUrl'       => route('lend:index'),
        ];

        $body = \Lang::get('lender.mails.loan-repayment-received-balance.body', $parameters);
        $data['content'] = $body;

        $this->mailer->queue(
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
                'borrowerName' => $loan->getBorrower()->getName(),
                'reviewUrl'    => route('loan:index', $loan->getId()) . '#feedback',
            ],
        ];

        $this->mailer->queue(
            'emails.label-template',
            $data + [
                'to'      => $lender->getUser()->getEmail(),
                'header'  => \Lang::get(
                    'lender.mails.loan-repayment-feedback.body.header',
                    ['borrowerName' => $loan->getBorrower()->getName()]
                ),
                'label'   => 'lender.mails.loan-repayment-feedback.body',
                'subject' => \Lang::get(
                    'lender.mails.loan-repayment-feedback.subject',
                    ['borrowerName' => $loan->getBorrower()->getName()]
                )
            ]
        );
    }

    public function sendRepaidLoanGainMail(
        Lender $lender,
        Loan $loan,
        Money $loanAmount,
        Money $repaidAmount,
        Money $gainAmount,
        $gainPercent
    ) {
        $borrower = $loan->getBorrower();
//        $loanAmount = $loan->getUsdAmount();
//        $repaidAmount = $loan->getUsdAmount()->multiply($loan->getRepaidPercent())->divide(100);
//        $gainAmount = $repaidAmount->subtract($loanAmount);
//        $gainPercent = $gainAmount->multiply(100)->divide($loanAmount);
        $parameters = [
            'gainAmount'   => $gainAmount,
            'gainPercent'  => $gainPercent,
            'borrowerName' => $borrower->getName(),
            'loanUrl'      => route('loan:index', $loan->getId()),
            'purpose'      => $loan->getProposal(),
            'loanAmount'   => $loanAmount,
            'repaidAmount' => $repaidAmount,
            'lendUrl'      => route('lend:index'),
            'myStatsUrl'   => route('lender:loans')
        ];

        $body = \Lang::get('lender.mails.loan-repaid-gain.body', $parameters);
        $data['content'] = $body;

        $this->mailer->queue(
            'emails.hero',
            $data + [
                'to'         => $lender->getUser()->getEmail(),
                'subject'    => \Lang::get('lender.mails.loan-repaid-gain.subject', $parameters),
                'templateId' => \Setting::get('sendwithus.lender-loan-repayment-template-id'), //TODO template for REPAID_GAIN_TAG
            ]
        );
    }

    public function sendPaypalWithdrawMail(Lender $lender, Money $withdrawnAmount)
    {
        $data = [
            'parameters' => [
                'lenderName'      => $lender->getName(),
                'withdrawnAmount' => (string)$withdrawnAmount,
            ],
        ];

        $this->mailer->queue(
            'emails.label-template',
            $data + [
                'to'      => $lender->getUser()->getEmail(),
                'label'   => 'lender.mails.paypal-withdraw.body',
                'subject' => \Lang::get('lender.mails.paypal-withdraw.subject')
            ]
        );
    }

    public function sendFundUploadMail(Lender $lender, Money $uploadAmount)
    {
        $data = [
            'parameters' => [
                'uploadAmount' => $uploadAmount,
                'lendUrl'      => route('lend:index'),
            ],
        ];

        $this->mailer->queue(
            'emails.label-template',
            $data + [
                'to'      => $lender->getUser()->getEmail(),
                'label'   => 'lender.mails.fund-upload.body',
                'subject' => \Lang::get('lender.mails.fund-upload.subject')
            ]
        );
    }

    public function sendDonationMail(Lender $lender, Money $donationAmount)
    {
        $data = [
            'parameters' => [
                'donationAmount' => $donationAmount,
                'donationDate'   => Carbon::now()->format('d-m-Y'),
            ],
        ];

        $this->mailer->queue(
            'emails.label-template',
            $data + [
                'to'      => $lender->getUser()->getEmail(),
                'label'   => 'lender.mails.lender-donation.body',
                'subject' => \Lang::get('lender.mails.lender-donation.subject')
            ]
        );
    }

    public function sendLoanForgivenessConfirmationMail(Lender $lender, Loan $loan, Money $reducedAmount)
    {
        $data = [
            'parameters' => [
                'borrowerUrl'   => route('loan:index', $loan->getId()),
                'borrowerName'  => $loan->getBorrower()->getName(),
                'reducedAmount' => $reducedAmount,
            ],
        ];

        $this->mailer->queue(
            'emails.label-template',
            $data + [
                'to'      => $lender->getUser()->getEmail(),
                'label'   => 'lender.mails.loan-forgiveness-confirmation.body',
                'subject' => \Lang::get('lender.mails.loan-forgiveness-confirmation.subject')
            ]
        );
    }

    public function sendInviteeOwnFundsMail(User $user, Invite $invite)
    {
        $parameters = [
            'inviterUsername' => $invite->getLender()->getUser()->getUsername(),
        ];

        $data = $this->getFeaturedLoansForMail();
        $data['footer'] = \Lang::get('lender.mails.invitee-own-funds.footer');
        $body = \Lang::get('lender.mails.invitee-own-funds.body', $parameters);
        $data['content'] = $body;

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $user->getEmail(),
                'subject'    => \Lang::get('lender.mails.invitee-own-funds.subject'),
                'templateId' => \Setting::get('sendwithus.inactive-invitee-template-id'),
            ]
        );
    }

    public function sendWithdrawalRequestMail(lender $lender, WithdrawalRequest $withdrawalRequest)
    {
        $parameters = [
            'date'           => Carbon::now()->format('d-m-Y'),
            'withdrawAmount' => (string)$withdrawalRequest->getAmount(),
            'paypalEmail'    => $withdrawalRequest->getPaypalEmail(),
        ];

        $body = \Lang::get('lender.mails.withdraw-request.body', $parameters);
        $data['content'] = $body;

        $this->mailer->send(
            'emails.hero',
            $data + [
                'to'         => $lender->getUser()->getEmail(),
                'subject'    => \Lang::get('lender.mails.withdraw-request.subject'),
                'templateId' => \Setting::get('sendwithus.borrower-notifications-template-id'),
            ]
        );
    }
}
