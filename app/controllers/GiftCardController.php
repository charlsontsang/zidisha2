<?php

use Zidisha\Lender\GiftCardQuery;
use Zidisha\Lender\Form\GiftCard;
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
        $lender = Auth::getUser()->getLender();
        $giftCard = $this->giftCardService->addGiftCard($lender, $data);

        Session::forget('giftCard');
        Flash::success("GiftCard Successfully Made.");

        return Redirect::route('lender:gift-cards:track');
    }

    public function postRedeemCard()
    {
        $redemptionCode = Input::get('redemptionCode');
        $lender = Auth::getUser()->getLender();

        $errorMessage = $this->giftCardService->validateCode($redemptionCode);

        if ($errorMessage) {
            Flash::error(\Lang::get($errorMessage));
            return Redirect::route('lender:funds');
        }

        $this->giftCardService->redeemGiftCard($lender, $redemptionCode);

        Flash::success(\Lang::get('comments.flash.redemption-success'));
        return Redirect::route('lender:gift-cards:track');
    }

    public function getTrackCards()
    {
        $countQuery = GiftCardQuery::create()
            ->filterByLender(Auth::getUser()->getLender());

        $countCards = $countQuery->count();
        $countRedeemed = $countQuery->filterByClaimed(1)->count();
        $cards = GiftCardQuery::create()
            ->filterByLender(Auth::getUser()->getLender())
            ->find();

        return View::make('lender.gift-cards-track', compact('countCards', 'countRedeemed', 'cards'));
    }

}
