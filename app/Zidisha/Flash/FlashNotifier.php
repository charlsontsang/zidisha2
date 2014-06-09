<?php namespace Zidisha\Flash;

use Illuminate\Session\Store;

class FlashNotifier
{

    private $session;
    private $notifications = [];

    function __construct(Store $session)
    {
        $this->session = $session;
    }

    public function success($message)
    {
        $this->message($message, 'success');
    }

    public function error($message)
    {
        $this->message($message, 'danger');
    }

    public function danger($message)
    {
        $this->message($message, 'danger');
    }

    public function info($message)
    {
        $this->message($message, 'info');
    }

    public function warning($message)
    {
        $this->message($message, 'warning');
    }

    public function modal($message, $title = null)
    {
        $type = 'modal';
        $this->notifications[] = compact('message', 'title', 'type');
        $this->session->flash('flash_notifications', $this->notifications);
    }

    public function message($message, $level = 'info')
    {
        $type = 'message';
        $this->notifications[] = compact('message', 'level', 'type');
        $this->session->flash('flash_notifications', $this->notifications);
    }

}