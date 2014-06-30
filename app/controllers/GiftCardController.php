<?php

use Zidisha\Balance\TransactionQuery;
use Zidisha\Currency\Money;
use Zidisha\Lender\GiftCardQuery;
use Zidisha\Lender\Form\GiftCard;
use Zidisha\Lender\GiftCardService;
use Zidisha\Payment\Form\GiftCardForm;
use Zidisha\Payment\Form\UploadFundForm;

class GiftCardController extends BaseController
{

    private $cardForm, $giftCardService;
    private $transactionQuery;
    private $giftCardForm;

    public function __construct(
        GiftCard $cardForm,
        GiftCardService $giftCardService,
        TransactionQuery $transactionQuery,
        GiftCardForm $giftCardForm
    ) {
        $this->cardForm = $cardForm;
        $this->giftCardService = $giftCardService;
        $this->transactionQuery = $transactionQuery;
        $this->giftCardForm = $giftCardForm;
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

            $currentBalance = $this->transactionQuery
                ->filterByUserId(Auth::getUser()->getId())
                ->getTotalAmount();
            $data = Session::get('giftCard');
            $amount = Money::create($data['amount'], 'USD');
            $enoughBalance = 1;
            if ($currentBalance < $amount) {
                $enoughBalance = 0;
            }

            return View::make('lender.gift-cards-terms', compact('enoughBalance'));
        }
        return Redirect::route('lender:gift-cards')->withForm($form);
    }

    public function postTermsAccept()
    {
        $form = $this->giftCardForm;
        $form->handleRequest(\Request::instance());
        $formData = $form->getData();

        $data = Session::get('giftCard');
        $lender = Auth::getUser()->getLender();

        if ($formData['amount']) {
           return $form->makePayment();
        } else {
            $giftCard = $this->giftCardService->addGiftCard($lender, $data);
        }

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
