<?php

use Zidisha\Admin\Form\ExchangeRateForm;
use Zidisha\Admin\Form\FeatureFeedbackForm;
use Zidisha\Admin\Form\FilterBorrowers;
use Zidisha\Admin\Form\FilterLenders;
use Zidisha\Admin\Form\FilterLoans;
use Zidisha\Admin\Form\SettingsForm;
use Zidisha\Admin\Form\WithdrawalRequestsForm;
use Zidisha\Admin\Setting;
use Zidisha\Admin\Form\TranslateForm;
use Zidisha\Admin\Form\TranslationFeedForm;
use Zidisha\Balance\TransactionQuery;
use Zidisha\Balance\WithdrawalRequestQuery;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Borrower\BorrowerService;
use Zidisha\Borrower\FeedbackMessageQuery;
use Zidisha\Comment\BorrowerCommentQuery;
use Zidisha\Comment\CommentQuery;
use Zidisha\Borrower\Form\AdminEditForm;
use Zidisha\Country\CountryQuery;
use Zidisha\Currency\CurrencyService;
use Zidisha\Lender\GiftCardQuery;
use Zidisha\Lender\LenderQuery;
use Zidisha\Loan\Form\AdminCategoryForm;
use Zidisha\Loan\LoanQuery;
use Zidisha\Loan\LoanService;
use Zidisha\Loan\Loan;
use Zidisha\Mail\LenderMailer;
use Zidisha\Payment\Paypal\PaypalMassPaymentException;
use Zidisha\Payment\Paypal\PayPalService;

class AdminController extends BaseController
{

    protected $lenderQuery, $borrowerQuery, $countryQuery;
    protected $borrowersForm, $lendersForm, $loansForm, $translationFeedForm;
    private $loanService;
    private $exchangeRateForm;
    private $currencyService;
    private $featureFeedbackForm;
    private $borrowerService;
    private $adminCategoryForm;
    protected $translateForm;
    private $lenderMailer;
    private $withdrawalRequestsForm;
    private $payPalService;

    public function  __construct(
        LenderQuery $lenderQuery,
        BorrowerQuery $borrowerQuery,
        CountryQuery $countryQuery,
        FilterBorrowers $borrowersForm,
        FilterLenders $lendersForm,
        FilterLoans $loansForm,
        LoanService $loanService,
        CurrencyService $currencyService,
        ExchangeRateForm $exchangeRateForm,
        FeatureFeedbackForm $featureFeedbackForm,
        BorrowerService $borrowerService,
        AdminCategoryForm $adminCategoryForm,
        TranslateForm $translateForm,
        TranslationFeedForm $translationFeedForm,
        LenderMailer $lenderMailer,
        WithdrawalRequestsForm $withdrawalRequestsForm,
        PayPalService $payPalService
    ) {
        $this->lenderQuery = $lenderQuery;
        $this->$borrowerQuery = $borrowerQuery;
        $this->countryQuery = $countryQuery;
        $this->borrowersForm = $borrowersForm;
        $this->lendersForm = $lendersForm;
        $this->loansForm = $loansForm;
        $this->loanService = $loanService;
        $this->exchangeRateForm = $exchangeRateForm;
        $this->currencyService = $currencyService;
        $this->featureFeedbackForm = $featureFeedbackForm;
        $this->borrowerService = $borrowerService;
        $this->adminCategoryForm = $adminCategoryForm;
        $this->translateForm = $translateForm;
        $this->translationFeedForm = $translationFeedForm;
        $this->lenderMailer = $lenderMailer;
        $this->withdrawalRequestsForm = $withdrawalRequestsForm;
        $this->payPalService = $payPalService;
    }

    public
    function getDashboard()
    {
        return View::make('admin.dashboard');
    }

    public function getBorrowers()
    {
        $page = Request::query('page') ? : 1;
        $countryId = Request::query('country') ? : null;
        $email = Request::query('email') ? : null;

        $query = BorrowerQuery::create();

        if ($countryId) {
            $query->filterByCountryId($countryId);
        }
        if ($email) {
            $query
                ->useUserQuery()
                ->filterByEmail($email)
                ->endUse();
        }

        $paginator = $query
            ->orderById()
            ->paginate($page, 3);

        return View::make('admin.borrowers', compact('paginator'), ['form' => $this->borrowersForm,]);
    }

    public function getBorrower($borrowerId)
    {
        $borrower = BorrowerQuery::create()
            ->filterById($borrowerId)
            ->findOne();

        if (!$borrower) {
            App::abort(404);
        }

        $personalInformation = $borrower->getPersonalInformation();
        $loans = LoanQuery::create()
            ->filterByBorrowerId($borrowerId);

        return View::make('admin.borrower', compact('borrower', 'personalInformation', 'loans'));
    }

    public function getBorrowerEdit($borrowerId)
    {
        $borrower = BorrowerQuery::create()
            ->filterById($borrowerId)
            ->findOne();

        if (!$borrower) {
            App::abort(404);
        }

        $form = new AdminEditForm($borrower);

        return \View::make(
            'admin.borrower-information',
            compact('form', 'borrower', 'borrowerId')
        );
    }

    public function postBorrowerEdit($borrowerId)
    {
        $borrower = BorrowerQuery::create()
            ->filterById($borrowerId)
            ->findOne();

        if (!$borrower) {
            App::abort(404);
        }

        $form = new AdminEditForm($borrower);
        $form->handleRequest(Request::instance());

        if ($form->isValid()) {
            $this->borrowerService->updatePersonalInformation($borrower, $form->getNestedData());

            $data = $form->getData();
            $this->borrowerService->updateProfileInformation($borrower, $data);

            Flash::success('Changes updated.');
            return Redirect::route('admin:borrower:edit', $borrowerId);
        }

        Flash::error('Please submit correct data.');
        return Redirect::route('admin:borrower:edit', $borrowerId)->withForm($form);
    }

    public function getLenders()
    {
        $page = Request::query('page') ? : 1;
        $countryId = Request::query('country') ? : null;
        $email = Request::query('email') ? : null;

        $query = LenderQuery::create();

        if ($countryId) {
            $query->filterByCountryId($countryId);
        }
        if ($email) {
            $query
                ->useUserQuery()
                ->filterByEmail($email)
                ->endUse();
        }

        $paginator = $query
            ->orderById()
            ->paginate($page, 3);

        return View::make('admin.lenders', compact('paginator'), ['form' => $this->borrowersForm,]);
    }

    public function getLoans()
    {
        $page = Request::query('page') ? : 1;
        $countryName = Request::query('country') ? : null;
        $status = Request::query('status') ? : null;

        $selectedCountry = $this->countryQuery->findOneBySlug($countryName);

        $conditions = [];

        $routeParams = [
            'stage' => 'fund-raising',
            'country' => 'everywhere'
        ];

        if ($selectedCountry) {
            $conditions['countryId'] = $selectedCountry->getId();
            $routeParams['country'] = $selectedCountry->getSlug();
        }

        if ($status) {
            if ($status == 'completed') {
                $routeParams['stage'] = 'completed';
                $conditions['status'] = [Loan::DEFAULTED, Loan::REPAID];
            } elseif ($status == 'active') {
                $routeParams['stage'] = 'active';
                $conditions['status'] = [Loan::ACTIVE, Loan::FUNDED];
            } else {
                $routeParams['stage'] = 'fund-raising';
                $conditions['status'] = Loan::OPEN;
            }
        }

        $paginator = $this->loanService->searchLoans($conditions, $page);

        return View::make('admin.loans', compact('paginator'), ['form' => $this->loansForm,]);
    }

    public function getExchangeRates($countrySlug = null)
    {
        $page = Request::query('page') ? : 1;
        $rates = $this->currencyService->getExchangeRatesForCountry($countrySlug);

        $paginator = $rates
            ->paginate($page, 50);
        $offset = ($page - 1) * 50;

        return View::make('admin.exchange-rates', compact('paginator', 'countrySlug', 'offset'),
            ['form' => $this->exchangeRateForm,]);
    }

    public function postExchangeRates()
    {
        $form = $this->exchangeRateForm;
        $form->handleRequest(Request::instance());
        $data = $form->getData();
        $countrySlug = array_get($data, 'countrySlug');

        if ($form->isValid()) {

            $this->currencyService->updateExchangeRateForCountry($data);

            \Flash::success("Exchange rate Successfully updated!");
            return Redirect::route('admin:exchange-rates', $countrySlug);
        }

        return Redirect::route('admin:exchange-rates', $countrySlug)->withForm($form);
    }

    public function getLoanFeedback($loanId)
    {
        $loan = LoanQuery::create()
            ->filterById($loanId)
            ->findOne();

        $borrower = $loan->getBorrower();
        Session::put('loanId', $loanId);

        $feedbackMessages = $this->borrowerService->getFeedbackMessages($loan);

        return View::make('admin.borrower-feedback', compact('borrower', 'feedbackMessages', 'loanId'),
            ['form' => $this->featureFeedbackForm,]);
    }

    public function postLoanFeedback()
    {
        $form = $this->featureFeedbackForm;
        $form->handleRequest(Request::instance());

        $loanId = Session::get('loanId');

        if ($form->isValid()) {
            $data = $form->getData();

            $this->borrowerService->addLoanFeedback($loanId, $data);
            Session::forget('loanId');

            \Flash::success("Suggestion successfully sent!");
            return Redirect::route('loan:index', $loanId);
        }

        return Redirect::route('admin:loan-feedback', $loanId)->withForm($form);
    }

    public function postAdminCategory($loanId)
    {
        $loan = LoanQuery::create()
            ->findOneById($loanId);

        if (!$loan) {
            App::abort(404);
        }

        $form = $this->adminCategoryForm;
        $form->handleRequest(Request::instance());

        if ($form->isValid()) {
            $data = $form->getData();
            $this->loanService->updateLoanCategories($loan, $data);

            \Flash::success("Categories successfully set!");
            return Redirect::route('loan:index', $loanId);
        }

        \Flash::error("Couldn't set categories!");
        return Redirect::route('loan:index', $loanId)->withForm($form);
    }
    
    public function getSettings()
    {
        $settingsForm = new SettingsForm();
        $groups = Setting::getGroups();

        return View::make('admin.settings', compact('settingsForm', 'groups'));
    }

    public function postSettings()
    {
        $settingsForm = new SettingsForm();
        $settingsForm->handleRequest(\Request::instance());
        
        if ($settingsForm->isValid()) {
            $data = $settingsForm->getSettingsData();
            
            Setting::updateSettings($data);
            
            \Flash::success("Successfully updated the setting.");
            return Redirect::route('admin:settings');
        }

        \Flash::error('Please correct the errors.');
        return Redirect::route('admin:settings')->withForm($settingsForm);
    }

    public function getTranslate($loanId)
    {
        $loan = LoanQuery::create()
            ->findOneById($loanId);

        if (!$loan) {
            App::abort(404);
        }

        $borrower = $loan->getBorrower();

        return View::make('admin.translate', compact('borrower', 'loan'),
            ['form' => $this->translateForm,]);
    }

    public function postTranslate($loanId)
    {
        $loan = LoanQuery::create()
            ->findOneById($loanId);

        if (!$loan) {
            App::abort(404);
        }

        $form = $this->translateForm;
        $form->handleRequest(Request::instance());

        if ($form->isValid()) {
            $data = $form->getData();

            $this->loanService->addTranslations($loan, $data);

            \Flash::success("Translations successfully saved!");
            return Redirect::route('loan:index', $loanId);
        }

        \Flash::error("Couldn't save Translations!");
        return Redirect::route('loan:index', $loanId)->withForm($form);
    }

    public function getTranslationFeed($type = null)
    {
        $languageCode = Request::query('language') ? : null;

        if($type == 'loans')
        {
            $loans = LoanQuery::create()
            ->condition('summery', 'Loan.SummaryTranslation IS NULL')
            ->condition('proposal', 'Loan.ProposalTranslation IS NULL')
            ->where(array('summery', 'proposal'), 'or')
                ->_or()
                ->useBorrowerQuery()
                    ->useProfileQuery()
                        ->condition('aboutMe', 'Profile.AboutMeTranslation IS NULL')
                        ->condition('aboutBusiness', 'Profile.AboutBusinessTranslation IS NULL')
                        ->where(array('aboutMe', 'aboutBusiness'), 'or')
                    ->endUse()
                ->endUse();

            if($languageCode){
                $loans->filterByLanguageCode($languageCode);
            }

            $page = Request::query('page') ? : 1;
            $paginator = $loans->paginate($page, 10);

        }else{
            $type = 'comments';
            $comments = CommentQuery::create()
                ->where('Comment.BorrowerId = Comment.UserId')
                ->filterByMessageTranslation(null);


            if($languageCode){
                $comments->useBorrowerQuery()
                    ->useCountryQuery()
                        ->useLanguageQuery()
                            ->filterByLanguageCode($languageCode)
                            ->endUse()
                        ->endUse()
                    ->endUse();
            }

            $page = Request::query('page') ? : 1;
            $paginator = $comments->paginate($page, 10);

        }

        return View::make(
            'admin.translation-feed',
            compact(
                'type', 'loans', 'comments', 'paginator'
            ), ['form' => $this->translationFeedForm,]
        );
    }

    public function getGiftCards()
    {
        $page = Request::query('page') ? : 1;

        $paginator = GiftCardQuery::create()
            ->orderByDate('desc')
            ->paginate($page, 10);

        return View::make('admin.gift-cards', compact('paginator'));
    }

    public function resendEmailToRecipient($id)
    {
        $giftCard = GiftCardQuery::create()
            ->findOneById($id);

        $this->lenderMailer->sendGiftCardMailToRecipient($giftCard);

        \Flash::success("Email successfully sent!");
        return Redirect::route('admin:get:gift-cards');
    }

    public function getWithdrawalRequests()
    {
        $page = Request::query('page') ? : 1;

        $paginator = WithdrawalRequestQuery::create()
            ->joinWith('Lender')
            ->joinWith('Lender.User')
            ->orderByUpdatedAt('desc')
            ->paginate($page, 10);

        $userIds = $paginator->toKeyValue('lenderId', 'lenderId');

        $uploaded = TransactionQuery::create()
            ->filterFundUpload()
            ->getTotalAmounts($userIds);

        $repaid = TransactionQuery::create()
            ->filterRepaidToLender()
            ->getTotalAmounts($userIds);

        $withdrawn = TransactionQuery::create()
            ->filterFundWithdraw()
            ->getTotalAmounts($userIds);

        return View::make('admin.withdrawal-requests', compact('paginator', 'uploaded', 'repaid', 'withdrawn'),
            ['form' => $this->withdrawalRequestsForm,]);
    }

    public function postWithdrawalRequests($withdrawalRequestId)
    {
        $withdrawalRequest = WithdrawalRequestQuery::create()
            ->findOneById($withdrawalRequestId);

        if (!$withdrawalRequest) {
            App::abort(404);
        }

        $form = $this->withdrawalRequestsForm;
        $form->handleRequest(Request::instance());

        if ($form->isValid()) {
            $withdrawalRequest->setPaid(true);
            $withdrawalRequest->save();
            \Flash::success("Successfully paid!");
            return Redirect::route('admin:get:withdrawal-requests');
        }

        \Flash::error('Some error occured!');
        return Redirect::route('admin:get:withdrawal-requests')->withForm($form);
    }

    public function postPaypalWithdrawalRequests()
    {
        $ids = Input::get('ids');

        if (!is_array($ids)) {
            App::abort(404);
        }
        try{
            $this->payPalService->processMassPayment($ids);
        }catch (PaypalMassPaymentException $e) {
            \Flash::error('Some error occured!' . $e->getMessage());
            return Redirect::route('admin:get:withdrawal-requests');
        }
        \Flash::success("Successfully processed!");
        return Redirect::route('admin:get:withdrawal-requests');
    }

    public function getPublishComments()
    {
        $page = Request::query('page') ? : 1;

        $comments = BorrowerCommentQuery::create()
            ->filterByPublished(false)
            ->filterByCreatedAt(array('min' => time() - 2 * 30 * 24 * 60 * 60))
            ->joinWith('User')
            ->paginate($page, 10);

        return View::make('admin.publish-comments', compact('comments'));
    }
}
