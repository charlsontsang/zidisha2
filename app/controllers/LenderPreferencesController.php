<?php

use Zidisha\Lender\Form\AccountPreferencesForm;
use Zidisha\Lender\Form\AutoLendingSettingForm;
use Zidisha\Lender\LenderService;

class LenderPreferencesController extends BaseController
{

    private $accountPreferencesForm;
    private $lenderService;

    /**
     * @var AutoLendingSettingForm
     */
    private $autoLendingSettingForm;
    
    public function __construct(
        AccountPreferencesForm $accountPreferencesForm,
        LenderService $lenderService,
        AutoLendingSettingForm $autoLendingSettingForm
    )
    {
        $this->accountPreferencesForm = $accountPreferencesForm;
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
                Flash::success('lender.flash.preferences.success');
                return Redirect::route('lender:public-profile', $user->getUsername());
            }
        }
        return Redirect::route('lender.account-preference')->withForm($form);
    }
    
    public function getAutoLending()
    {
        $form = $this->autoLendingSettingForm;
        return \View::make('lender.auto-lending-setting', compact('form'));
    }

}
