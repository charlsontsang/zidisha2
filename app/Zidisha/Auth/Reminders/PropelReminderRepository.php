<?php namespace Zidisha\Auth\Reminders;


use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Auth\Reminders\ReminderRepositoryInterface;


class PropelReminderRepository implements ReminderRepositoryInterface
{

    /**
     * The hashing key.
     *
     * @var string
     */
    protected $hashKey;

    /**
     * The number of seconds a reminder should last.
     *
     * @var int
     */
    protected $expires;


    public function __construct($key, $expires = 60)
    {
        $this->hashKey = $key;
        $this->expires = $expires * 60;
    }


    /**
     * Create a new reminder record and token.
     *
     * @param  \Illuminate\Auth\Reminders\RemindableInterface $user
     * @return string
     */
    public function create(RemindableInterface $user)
    {
        $email = $user->getReminderEmail();

        // We will create a new, random token for the user so that we can e-mail them
        // a safe link to the password reset form. Then we will insert a record in
        // the database so that we can verify the token within the actual reset.
        $token = $this->createNewToken($user);

        $passwordReminder = new \PasswordReminder();
        $passwordReminder->setEmail($email)->setToken($token)->save();

        return $token;
    }

    /**
     * Determine if a reminder record exists and is valid.
     *
     * @param  \Illuminate\Auth\Reminders\RemindableInterface $user
     * @param  string $token
     * @return bool
     */
    public function exists(RemindableInterface $user, $token)
    {
        $email = $user->getReminderEmail();

        $reminder = \PasswordReminderQuery::create()
            ->filterByEmail($email)
            ->filterByToken($token)
            ->findOne();

        return $reminder && !$this->reminderExpired($reminder);
    }

    /**
     * Delete a reminder record by token.
     *
     * @param  string $token
     * @return void
     */
    public function delete($token)
    {
        \PasswordReminderQuery::create()->filterByToken($token)->delete();
    }

    /**
     * Delete expired reminders.
     *
     * @return void
     */
    public function deleteExpired()
    {
        $expired = \Carbon::now()->subSeconds($this->expires);

        \PasswordReminderQuery::create()->where('PasswordReminder.created_at < ?', $expired)->delete();
    }

    /**
     * Create a new token for the user.
     *
     * @param  \Illuminate\Auth\Reminders\RemindableInterface $user
     * @return string
     */
    public function createNewToken(RemindableInterface $user)
    {
        $email = $user->getReminderEmail();

        $value = str_shuffle(sha1($email . spl_object_hash($this) . microtime(true)));

        return hash_hmac('sha1', $value, $this->hashKey);
    }

    /**
     * Determine if the reminder has expired.
     *
     * @param  array $reminder
     * @return bool
     */
    protected function reminderExpired($reminder)
    {
        $createdPlusHour = $reminder->getCreatedAt()->getTimestamp() + $this->expires;

        return $createdPlusHour < $this->getCurrentTime();
    }

    /**
     * Get the current UNIX timestamp.
     *
     * @return int
     */
    protected function getCurrentTime()
    {
        return time();
    }
}
