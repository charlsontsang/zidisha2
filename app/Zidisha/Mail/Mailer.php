<?php
namespace Zidisha\Mail;

use Closure;
use Config;
use Illuminate\Support\SerializableClosure;
use Mail;
use Zidisha\Admin\Setting;
use Zidisha\Utility\Utility;

class Mailer
{

    public $driver;

    protected $enabled;
    /**
     * @var LaravelMailerDriver
     */
    private $laravelMailerDriver;
    /**
     * @var SendwithusDriver
     */
    private $sendwithusDriver;

    protected $useSendWithUs;
    
    protected static $testEmail;

    public function __construct(LaravelMailerDriver $laravelMailerDriver, SendwithusDriver $sendwithusDriver)
    {
        $this->useSendWithUs = \Config::get('services.sendwithus.enabled');
        $this->enabled = \Config::get('mail.enabled');
        $this->driver = \Config::get('mail.mailer.driver');
        $this->laravelMailerDriver = $laravelMailerDriver;
        $this->sendwithusDriver = $sendwithusDriver;
    }
    
    public static function enableTestMode($email)
    {
        static::$testEmail = $email;
    }

    public function send($view, $data)
    {
        if (!$this->enabled) {
            return;
        }
        
        if (static::$testEmail) {
            $data['subject'] = $data['subject'] . ' (' . $data['to'] . ')';
            $data['to'] = static::$testEmail;
        }

        $data += [
            'from'    => Setting::get('site.fromEmailAddress'),
            'replyTo' => Setting::get('site.replyToEmailAddress'),
        ];
        
        $data['to'] = Utility::clearPost($data['to']);
        $data['from'] = Utility::clearPost($data['from']);
        $data['subject'] = stripcslashes(Utility::clearPost($data['subject']));

        if (array_get($data, 'templateId') ) {
            if ($this->useSendWithUs) {
                $this->sendwithusDriver->send($view, $data);
            } else {
                $this->laravelMailerDriver->send('emails.sendwithus', $data);
            }
        } elseif (array_get($data, 'label')) {
            $this->laravelMailerDriver->send('emails.label-template', $data);
        } else {
            $this->laravelMailerDriver->send($view, $data);
        }
    }

    /**
     * Queue a new e-mail message for sending on the given queue.
     *
     * @param  string  $queue
     * @param  string|array  $view
     * @param  array   $data
     * @return void
     */
    public function queue($view, array $data, $queue = null)
    {
        if (!$this->enabled) {
            return;
        }

        \Queue::push('Zidisha\Mail\Mailer@handleQueuedMessage', compact('view', 'data'), $queue);
    }

    /**
     * Queue a new e-mail message for sending after (n) seconds.
     *
     * @param  int  $delay
     * @param  string|array  $view
     * @param  array  $data
     * @param  string  $queue
     * @return void
     */
    public function later($delay, $view, array $data, $queue = null)
    {
        if (!$this->enabled) {
            return;
        }

        \Queue::later($delay, 'Zidisha\Mail\Mailer@handleQueuedMessage', compact('view', 'data'), $queue);
    }


    /**
     * Handle a queued e-mail message job.
     *
     * @param  \Illuminate\Queue\Jobs\Job  $job
     * @param  array  $data
     * @return void
     */
    public function handleQueuedMessage($job, $data)
    {
        $this->send($data['view'], $data['data']);

        $job->delete();
    }
}
