<?php
namespace Zidisha\Borrower\Form\Activation;


use Zidisha\Borrower\Base\Borrower;
use Zidisha\Form\AbstractForm;

class FeedbackForm extends AbstractForm
{
    /**
     * @var Borrower
     */
    private $borrower;

    public function __construct(Borrower $borrower)
    {
        $this->borrower = $borrower;
    }

    public function getRules($data)
    {
        return [
            'borrowerEmail' => 'required|email',
            'cc'            => 'Emails',
            'replyTo'       => 'required|email',
            'subject'       => 'required',
            'message'       => 'required',
            'senderName'    => 'required',
        ];
    }

    public function getDefaultData()
    {
        return [
            'borrowerEmail' => $this->borrower->getUser()->getEmail(),
            'cc'            => '',
            'replyTo'       => 'service@zidisha.org',// TODO
            'subject'       =>  \Lang::get('borrowerActivation.feedback.default-subject'),
            'message'       => \Lang::get('borrowerActivation.feedback.default-message', array('borrowerName' => $this->borrower->getName())),
            'senderName'    => \Auth::user()->getUsername(), // TODO
        ];
    }
}
