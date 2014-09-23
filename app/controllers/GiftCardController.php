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
        return View::make('lender.gift-cards.index', ['form' => $this->cardForm,]);
    }

    public function getTermsAccept(){
        $data = Session::get('giftCard');
        $amount = $data['amount'];
        $recipientName = $data['recipientName'];
        $paymentForm = new GiftCardForm($this->giftCardService);
        return View::make('lender.gift-cards.payment', compact('amount', 'recipientName'), ['paymentForm' => $paymentForm,]);
    }

    public function postGiftCards()
    {
        $form = $this->cardForm;
        $form->handleRequest(Request::instance());

        if ($form->isValid()) {
            $data = $form->getData();

            Session::put('giftCard', $data);

           return Redirect::route('lender:gift-cards:terms-accept');

        }
        return Redirect::route('lender:gift-cards')->withForm($form);
    }

    public function postTermsAccept()
    {
        $form = new GiftCardForm($this->giftCardService);
        $form->handleRequest(\Request::instance());

        if ($form->isValid()) {
            return $form->makePayment();
        }
        \Flash::error("Please enter the donation amount as a number.");
        $formCard = $this->cardForm;
        return Redirect::route('lender:gift-cards')->withForm($formCard);
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

        Flash::success(\Lang::get('common.comments.flash.redemption-success'));
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
            ->orderByDate('desc')
            ->find();

        return View::make('lender.gift-cards.track', compact('countCards', 'countRedeemed', 'cards'));
    }

}
