<?php

use Propel\Runtime\Propel;
use Zidisha\Balance\Map\TransactionTableMap;
use Zidisha\Currency\Money;
use Zidisha\Lender\Card;
use Zidisha\Lender\CardQuery;
use Zidisha\Lender\Form\GiftCard;
use Faker\Factory as Faker;
use Zidisha\Lender\GiftCardService;

class GiftCardController extends BaseController
{

    private $cardForm, $giftCardService;

    public function __construct(GiftCard $cardForm, GiftCardService $giftCardService)
    {
        $this->cardForm = $cardForm;
        $this->giftCardService = $giftCardService;
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

    public function postTermsAccept()
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
                return Redirect::route('lender:gift-cards:track');
            } else {
                $con->rollback();
            }
        }
        // TODO flash message
        Log::error("Some error came");
        // TODO send mail

    }

    public function PostRedeemCard()
    {
        $data = Input::all();
        $redemptionCode = $data['redemptionCode'];

        $validateCode = $this->giftCardService->validateCode($redemptionCode);

        if ($validateCode == 3) {
            Flash::error('comments.flash.duplicate-code');
            return Redirect::route('lender:funds');
        } elseif ($validateCode == 2) {
            Flash::error(\Lang::get('comments.flash.invalid-code'));
            return Redirect::route('lender:funds');
        } elseif ($validateCode == 0) {
            Flash::error(\Lang::get('comments.flash.invalid-code'));
            return Redirect::route('lender:funds');
        } elseif ($validateCode == 1) {
            $card = CardQuery::create()
                ->filterByCardCode($redemptionCode)
                ->findOne();
            if($card->getClaimed() == 1){
                Flash::error(\Lang::get('comments.flash.redeemed-code'));
                return Redirect::route('lender:funds');
            }else{
                $currentDate = new \DateTime();
                if($card->getExpireDate() < $currentDate){
                    Flash::error(\Lang::get('comments.flash.expired-code'));
                    return Redirect::route('lender:funds');
                }else{
                    //TODO make GIFT_REDEEM Transaction
                    $card->setClaimed(1);
                    $card->setRecipient(Auth::getUser());
                    $card->save();
                    //TODO if Transaction is successful then return success
                    Flash::success(\Lang::get('comments.flash.redemption-success'));
                    return Redirect::route('lender:gift-cards:track');
                }
            }
        }
    }

    public function getTrackCards(){
        $countQuery = CardQuery::create()
            ->filterByUser(Auth::getUser());

        $countCards =$countQuery->count();
        $countRedeemed = $countQuery->filterByClaimed(1)->count();
        $cards = CardQuery::create()
            ->filterByUser(Auth::getUser())
            ->find();

        return View::make('lender.gift-cards-track', compact('countCards', 'countRedeemed', 'cards'));
    }

}
