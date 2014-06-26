<?php

use Propel\Runtime\Propel;
use Zidisha\Balance\Map\TransactionTableMap;
use Zidisha\Balance\Transaction;
use Zidisha\Currency\Money;
use Zidisha\Lender\Card;
use Zidisha\Lender\Form\GiftCard;
use Faker\Factory as Faker;

class GiftCardController extends BaseController
{

    private $cardForm;

    public function __construct(GiftCard $cardForm)
    {
        $this->cardForm = $cardForm;
    }

    public function getGiftCards()
    {

        return View::make('lender.gift-cards', ['form' => $this->cardForm,]);
    }

    public function postGiftCards()
    {

        $form = $this->cardForm;
        $form->handleRequest(Request::instance());

        if ($form->isValid()) {
            $data = $form->getData();

            Session::put('giftCard', $data);

            return View::make('lender.gift-cards-terms');
        }

        return Redirect::route('lender:gift-cards')->withForm($form);
    }

    public function getTermsAccept()
    {

        $data = Session::get('giftCard');
        $amount = Money::create(str_replace(array('$', ','), '', $data['amount']), 'USD');

        $con = Propel::getWriteConnection(TransactionTableMap::DATABASE_NAME);
        $faker = Faker::create();

        for ($retry = 0; $retry < 3; $retry++) {
            $con->beginTransaction();


            $giftCard = new Card();
            $giftCard->setUser(Auth::getUser());
            //TODO set template in Giftcard table
            $giftCard->setOrderType($data['deliveryMethod']);
            $giftCard->setCardAmount($amount);
            //TODO set redeipent email
            $giftCard->setRecipientName($data['toName'] ? $data['toName'] : null);
            $giftCard->setFromName($data['fromName'] ? $data['fromName'] : null);
            $giftCard->setMessage($data['message'] ? $data['message'] : null);
            $giftCard->setDate(new \DateTime());
            $giftCard->setExpireDate(strtotime('+1 year'));
            $giftCard->setCardCode($faker->creditCardNumber);
            $giftCard->setConfirmationEmail($data['yourEmail'] ? $data['yourEmail'] : null);
            $transaction1 = $giftCard->save($con);

            if ($transaction1 == 1) {

                $con->commit();
                Session::forget('giftCard');
                Flash::success("GiftCard Successfully Made.");
                return Redirect::route('lender:history');
            } else {
                $con->rollback();
            }
        }
        // TODO flash message
        Log::error("Some error came");
        // TODO send mail

    }

} 