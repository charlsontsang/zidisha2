<?php
namespace Zidisha\Payment\Form;


use Zidisha\Balance\InviteTransactionQuery;
use Zidisha\Currency\Money;
use Zidisha\Loan\Loan;
use Zidisha\Payment\BidPayment;

class PlaceBidForm extends AbstractPaymentForm
{

    /**
     * @var \Zidisha\Loan\Loan
     */
    private $loan;
    
    protected $lenderInviteCredit;

    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
    }
    
    public function getRules($data)
    {
        return [
            'interestRate'          => '', // TODO
            'amount'                => '', // TODO
            'useLenderInviteCredit' => '', // TODO
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
            ->setUseLenderInviteCredit($data['useLenderInviteCredit'])
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

    public function getDefaultData()
    {
        $max = 30;
        if ($this->getLenderInviteCredit()->isPositive()) {
            $max = $this->getLenderInviteCredit()->getAmount();
        }
        
        $defaults = [
            'interestRate' => 3,
            'amount' => min($max, max(10, $this->loan->getStillNeededUsdAmount()->getAmount())),
            'useLenderInviteCredit' => $this->getLenderInviteCredit()->isPositive(),
        ];
        
        return  $defaults + parent::getDefaultData();
    }

    public function getLenderInviteCredit()
    {
        if ($this->lenderInviteCredit === null) {
            if (!\Auth::check() || !\Auth::user()->isLender()) {
                $this->lenderInviteCredit = Money::create(0);
            } else {
                $this->lenderInviteCredit = InviteTransactionQuery::create()
                    ->getTotalInviteCreditAmount(\Auth::user()->getLender());
            }
        }

        return $this->lenderInviteCredit;
    }

    public function getCurrentBalance()
    {
        return $this->getLenderInviteCredit()->isPositive() ? $this->getLenderInviteCredit() : parent::getCurrentBalance(); 
    }
}