<?php
namespace Zidisha\Payment\Form;


use Zidisha\Currency\Money;
use Zidisha\Loan\Bid;
use Zidisha\Payment\BidPayment;

class EditBidForm extends AbstractPaymentForm
{

    /**
     * @var \Zidisha\Loan\Loan
     */
    private $loan;
    
    /**
     * @var \Zidisha\Loan\Bid
     */
    private $bid;

    public function __construct(Bid $bid)
    {
        $this->bid = $bid;
        $this->loan = $bid->getLoan();
    }
    
    public function getRules($data)
    {
        return [
            'interestRate' => 'max:' . $this->bid->getInterestRate(), // TODO
            'amount'       => 'min:' . $this->bid->getBidAmount()->getAmount() . '|max:' . $this->loan->getAmount(), // TODO
        ] + parent::getRules($data);
    }

    public function getPayment()
    {
        if (!\Auth::user()) {
            \App::abort(404, 'Fatal Error');
        }

        $lender = \Auth::user()->getLender();

        $data = $this->getData();

        $placeBidPayment = new BidPayment();
        $placeBidPayment
            ->setCreditAmount(Money::create($data['creditAmount']))
            ->setDonationAmount(Money::create($data['donationAmount']))
            ->setDonationCreditAmount(Money::create($data['donationCreditAmount']))
            ->setTransactionFee(Money::create($data['transactionFee']))
            ->setTotalAmount(Money::create($data['totalAmount']))
            ->setLoan($this->loan)
            ->setAmount(Money::create($data['amount']))
            ->setInterestRate($data['interestRate'])
            // TODO edit bid
            ->setLender($lender);

        return $placeBidPayment;
    }

    public function getRates()
    {
        $keys = range(0, 15);
        $values = array_map(
            function ($a) {
                return "$a%";
            },
            $keys
        );

        return array_combine($keys, $values);
    }
}
