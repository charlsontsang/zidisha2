<?php

use Zidisha\Balance\TransactionQuery;
use Zidisha\Lender\Form\AccountPreferencesForm;
use Zidisha\Lender\Form\AutoLendingSettingForm;
use Zidisha\Lender\Lender;
use Zidisha\Lender\LenderQuery;
use Zidisha\Lender\LenderService;

class LenderPreferencesController extends BaseLenderController
{

    private $accountPreferencesForm;
    private $lenderService;

    /**
     * @var AutoLendingSettingForm
     */
    private $autoLendingSettingForm;
    
    public function __construct(
        LenderService $lenderService,
        AutoLendingSettingForm $autoLendingSettingForm
    )
    {
        $this->accountPreferencesForm = new AccountPreferencesForm($this->getLender());
        $this->lenderService = $lenderService;
        $this->autoLendingSettingForm = $autoLendingSettingForm;
    }
    
    public function getAccountPreference()
    {

        return View::make('lender.account-preference', ['form' => $this->accountPreferencesForm,]);
    }

    public function postAccountPreference()
    {
        $form = $this->accountPreferencesForm;
        $form->handleRequest(Request::instance());

        if ($form->isValid()) {
            $user = \Auth::user();
            $data = $form->getData();
            $preferences = $this->lenderService->updateAccountPreferences($user->getLender(), $data);
            if ($preferences) {
                Flash::success('Success! Your account settings are updated.');
                return Redirect::route('lender:public-profile', $user->getUsername());
            }
        }
        Flash::error('common.validation.error');
        return Redirect::route('lender:preference')->withForm($form);
    }
    
    public function getAutoLending()
    {
        $form = $this->autoLendingSettingForm;
        /** @var Lender $lender */
        $lender = \Auth::user()->getLender();
        
        $currentBalance = TransactionQuery::create()
            ->getCurrentBalance($lender->getId());

        return \View::make('lender.auto-lending-setting', compact('form', 'lender', 'currentBalance'));
    }

    public function postAutoLending($lenderId)
    {
        $lender = LenderQuery::create()
            ->findOneById($lenderId);

        if (!$lender) {
            \App::abort(404, 'Lender Not found.');
        }

        $form = $this->autoLendingSettingForm;
        $form->handleRequest(\Request::instance());

        if ($form->isValid()) {
            $data = $form->getData();
            $this->lenderService->autoLendingSetting($lender, $data);

            \Flash::success('Your settings are saved.');
            return \Redirect::route('lender:auto-lending');
        }

        \Flash::error('Please use proper options.');
        return \Redirect::route('lender:auto-lending');
    }
}
