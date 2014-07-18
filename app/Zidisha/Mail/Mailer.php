<?php
namespace Zidisha\Mail;

use Closure;
use Config;
use Illuminate\Support\SerializableClosure;
use Mail;
use Zidisha\Admin\Setting;

class Mailer
{

    public $driver;

    protected $enabled;

    public function __construct()
    {
        $this->enabled = \Config::get('mail.mailer.enabled');
        $this->driver = \Config::get('mail.mailer.driver');
    }

    public function send($view, $data)
    {
        $data += [
            'from' => Setting::get('site.replyTo'),
        ];
        if ($this->driver == 'laravel' && $this->enabled) {
            \Mail::send(
                $view,
                $data,
                function ($message) use ($data) {
                    $message
                        ->to($data['to'])
                        ->from($data['from'])
                        ->subject($data['subject']);
                }
            );
        }
    }

    /**
     * Queue a new e-mail message for sending on the given queue.
     *
     * @param  string  $queue
     * @param  string|array  $view
     * @param  array   $data
     * @param  Closure|string  $callback
     * @return void
     */
    public function queue($view, array $data, $callback, $queue = null)
    {
        $callback = $this->buildQueueCallable($callback);

        \Queue::push('Zidisha\Mail\mailer@handleQueuedMessage', compact('view', 'data', 'callback'), $queue);
    }

    /**
     * Queue a new e-mail message for sending after (n) seconds.
     *
     * @param  int  $delay
     * @param  string|array  $view
     * @param  array  $data
     * @param  Closure|string  $callback
     * @param  string  $queue
     * @return void
     */
    public function later($delay, $view, array $data, $callback, $queue = null)
    {
        $callback = $this->buildQueueCallable($callback);

        \Queue::later($delay, 'mailer@handleQueuedMessage', compact('view', 'data', 'callback'), $queue);
    }

    /**
     * Build the callable for a queued e-mail job.
     *
     * @param  mixed  $callback
     * @return mixed
     */
    protected function buildQueueCallable($callback)
    {
        if ( ! $callback instanceof Closure) return $callback;

        return serialize(new SerializableClosure($callback));
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
        $this->send($data['view'], $data['data'], $this->getQueuedCallable($data));

        $job->delete();
    }

    /**
     * Get the true callable for a queued e-mail message.
     *
     * @param  array  $data
     * @return mixed
     */
    protected function getQueuedCallable(array $data)
    {
        if (str_contains($data['callback'], 'SerializableClosure'))
        {
            return with(unserialize($data['callback']))->getClosure();
        }

        return $data['callback'];
    }
}
