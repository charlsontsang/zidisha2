<?php namespace Zidisha\Auth\Reminders;

use Illuminate\Auth\Reminders\PasswordBroker;
use Illuminate\Support\ServiceProvider;
use Zidisha\Auth\Reminders\PropelReminderRepository;

class ReminderServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerPasswordBroker();

        $this->registerReminderRepository();
    }

    /**
     * Register the password broker instance.
     *
     * @return void
     */
    protected function registerPasswordBroker()
    {
        $this->app->bindShared(
            'auth.reminder',
            function ($app) {
                // The reminder repository is responsible for storing the user e-mail addresses
                // and password reset tokens. It will be used to verify the tokens are valid
                // for the given e-mail addresses. We will resolve an implementation here.
                $reminders = $app['auth.reminder.repository'];

                $users = $app['auth']->driver()->getProvider();

                $view = $app['config']['auth.reminder.email'];

                // The password broker uses the reminder repository to validate tokens and send
                // reminder e-mails, as well as validating that password reset process as an
                // aggregate service of sorts providing a convenient interface for resets.
                return new PasswordBroker(

                    $reminders, $users, $app['mailer'], $view

                );
            }
        );
    }

    /**
     * Register the reminder repository implementation.
     *
     * @return void
     */
    protected function registerReminderRepository()
    {
        $this->app->bindShared(
            'auth.reminder.repository',
            function ($app) {
                $expire = $app['config']->get('auth.reminder.expire', 60);
                $key = $app['config']['app.key'];

                return new PropelReminderRepository($key, $expire);
            }
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('auth.reminder', 'auth.reminder.repository');
    }

}
