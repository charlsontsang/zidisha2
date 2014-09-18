<?php

namespace Zidisha\Mail;

use Illuminate\Http\Request;
use Zidisha\Comment\Comment;
use Zidisha\Currency\Money;
use Zidisha\Loan\Loan;
use Zidisha\User\User;
use Zidisha\User\UserQuery;

class AdminMailer
{
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendBorrowerCommentNotification(Loan $loan, Comment $comment, $postedBy, $images)
    {
        $admin = UserQuery::create()
            ->findOneById(\Setting::get('site.adminId'));
        $borrower = $loan->getBorrower();
        $parameters = [
            'borrowerName' => $borrower->getName(),
            'message'      => nl2br($comment->getMessage()),
            'postedBy'     => $postedBy,
            'images'       => $images,
        ];

        $this->mailer->queue(
            'emails.hero',
            [
                'to'         => $admin->getEmail(),
                'subject'    => \Lang::get('lender.mails.borrower-comment-notification.subject', $parameters, 'en'),
                'content'    => \Lang::get('lender.mails.borrower-comment-notification.body', $parameters, 'en'),
                'templateId' => \Setting::get('sendwithus.comments-template-id'),
            ]
        );
    }

    public function sendErrorMail(\Exception $exception)
    {
        /** @var Request $request */
        $request = \Request::instance();
        $request = array(
            'url'        => $request->fullUrl(),
            'method'     => $request->method(),
            'secure'     => $request->secure(),
            'ip address' => $request->getClientIp(),
            'time'       => date('Y-m-d H:i:s'),
            'ajax'       => $request->ajax(),
        );

        if (PHP_SAPI == 'cli') {
            global $argv;
            $request += array(
                'cli'  => true,
                'file' => $argv[0],
            );
            foreach (array_slice($argv, 1) as $i => $arg) {
                $request['arg' . ($i + 1)] = $arg;
            }
        }

        $session = \Session::all();
        $input = \Input::all();
        $cookies = \Request::cookie();

        $user = [];
        if (\Auth::check()) {
            /** @var User $u */
            $u = \Auth::user();
            $user = [
                'Id'       => $u->getId(),
                'Username' => $u->getUsername(),
                'Role'     => $u->getRole(),
                'SubRole'  => $u->getSubRole(),
            ];
        }

        $exceptions = [$exception];
        $_exception = $exception;
        while ($_exception->getPrevious()) {
            $_exception = $_exception->getPrevious();
            $exceptions[] = $_exception;
        }

        $this->mailer->send(
            'emails.admin.error',
            [
                'to'         => \Config::get('app.developerEmail'),
                'subject'    => 'ERROR: ' . get_class($exception),
                'trace'      => $exception->getTraceAsString(),
                'exceptions' => $exceptions,
                'request'    => $request,
                'session'    => $session,
                'input'      => $input,
                'cookies'    => $cookies,
                'user'       => $user,
            ]
        );
    }

    public function sendWithdrawalRequestMail(Money $money)
    {
        $admin = UserQuery::create()
            ->findOneById(\Setting::get('site.adminId'));
        $parameters = [
            'withdrawAmount' => (string)$money,
        ];

        $this->mailer->queue(
            'emails.label-template',
            [
                'to'      => $admin->getEmail(),
                'content' => \Lang::get('admin.mails.withdraw-request.body', $parameters, 'en'),
                'subject' => \Lang::get('admin.mails.withdraw-request.subject', [], 'en')
            ]
        );
    }
}
