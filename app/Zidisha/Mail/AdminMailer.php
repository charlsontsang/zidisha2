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

    //TODO gives blank page in redirect/return
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
        $parameters = [
            'trace'      => $exception->getTraceAsString(),
            'exceptions' => $exceptions,
            'request'    => $request,
            'session'    => $session,
            'input'      => $input,
            'cookies'    => $cookies,
            'user'       => $user,
        ];
        $this->mailer->send(
            'emails.hero',
            [
                'to'         => \Config::get('app.developerEmail'),
                'subject'    => 'ERROR: ' . get_class($exception),
                'content'    => View::make('emails.admin.error', $parameters)->render(),
                'templateId' => \Setting::get('sendwithus.lender-notifications-template-id')
            ]
        );
    }
}
