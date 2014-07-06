<?php
namespace Zidisha\Borrower\Form\Activation;


use Zidisha\Borrower\Borrower;
use Zidisha\Form\AbstractForm;

class ReviewForm extends AbstractForm
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
            'isAddressLocatable'     => 'required|in:0;1',
            'isAddressLocatableNote' => '',
        ];
    }

    public function getDefaultData()
    {
        $review = $this->borrower->getReview();
        
        return [
            'isAddressLocatable'     => $review ? $review->getIsAddressLocatable() : true,
            'isAddressLocatableNote' => $review ? $review->getIsAddressLocatableNote() : ''
        ];
    }
}