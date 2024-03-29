<?php

use Carbon\Carbon;
use Propel\Runtime\ActiveQuery\Criteria;
use Zidisha\Admin\AdminNote;
use Zidisha\Admin\AdminNoteQuery;
use Zidisha\Admin\Form\EnterRepaymentForm;
use Zidisha\Admin\Form\UploadRepaymentsForm;
use Zidisha\Admin\Form\ExchangeRateForm;
use Zidisha\Admin\Form\FeatureFeedbackForm;
use Zidisha\Admin\Form\FilterBorrowers;
use Zidisha\Admin\Form\FilterLenders;
use Zidisha\Admin\Form\FilterLoans;
use Zidisha\Admin\Form\AllowLoanForgivenessForm;
use Zidisha\Admin\Form\SettingsForm;
use Zidisha\Admin\Form\WithdrawalRequestsForm;
use Zidisha\Admin\Setting;
use Zidisha\Admin\Form\TranslateForm;
use Zidisha\Admin\Form\TranslationFeedForm;
use Zidisha\Balance\InviteTransactionQuery;
use Zidisha\Balance\TransactionQuery;
use Zidisha\Balance\WithdrawalRequestQuery;
use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Borrower\BorrowerService;
use Zidisha\Borrower\VolunteerMentorQuery;
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
use Zidisha\Payment\BalanceService;
use Zidisha\Payment\Paypal\PaypalMassPaymentException;
use Zidisha\Payment\Paypal\PayPalService;
use Zidisha\Repayment\BorrowerPaymentQuery;
use Zidisha\Repayment\BorrowerRefundQuery;
use Zidisha\Repayment\ImportService;
use Zidisha\Repayment\RepaymentService;
use Zidisha\User\User;
use Zidisha\User\UserQuery;

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
    private $uploadRepaymentsForm;
    private $importService;
    private $repaymentService;
    private $forgiveLoanForm;
    private $balanceService;

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
        PayPalService $payPalService,
        UploadRepaymentsForm $uploadRepaymentsForm,
        ImportService$importService,
        RepaymentService $repaymentService,
        AllowLoanForgivenessForm $forgiveLoanForm,
        BalanceService $balanceService
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
        $this->uploadRepaymentsForm = $uploadRepaymentsForm;
        $this->importService = $importService;
        $this->repaymentService = $repaymentService;
        $this->forgiveLoanForm = $forgiveLoanForm;
        $this->balanceService = $balanceService;
    }

    public
    function getDashboard()
    {
        $totalLenders = LenderQuery::create()
            ->count();
        
        $activeLenders = LenderQuery::create()
            ->useUserQuery()
                ->filterByActive(true)
            ->endUse()
            ->count();
        
        $activeLendersInPastTwoMonths = LenderQuery::create()
            ->useUserQuery()
                ->filterByLastLoginAt(Carbon::now()->subMonths(2), Criteria::GREATER_THAN)
            ->endUse()
            ->count();
        
        
        $lenderUsingAutomatedLending = LenderQuery::create()
            ->useAutoLendingSettingQuery()
                ->filterByActive(true)
            ->endUse()
            ->count();
        
        $totalLenderCredit  = DB::select(
            'SELECT SUM(amount) AS total
             FROM transactions
             WHERE user_id IN (SELECT id FROM users WHERE users.sub_role = 0)'
        );
        $totalLenderCredit = $totalLenderCredit[0]->total;

        return View::make('admin.dashboard', compact(
            'totalLenders',
            'activeLenders',
            'activeLendersInPastTwoMonths',
            'lenderUsingAutomatedLending',
            'totalLenderCredit'));
    }

    public function getBorrowers()
    {
        $form = $this->borrowersForm;
        $page = Request::query('page') ? : 1;

        $paginator = $form->getQuery()
            ->orderById()
            ->paginate($page, 100);

        return View::make('admin.borrowers.index', compact('paginator', 'form'));
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

        return View::make('admin.borrowers.profile', compact('borrower', 'personalInformation', 'loans'));
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
            'admin.borrowers.edit',
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

        Flash::error('The data format is invalid.');
        return Redirect::route('admin:borrower:edit', $borrowerId)->withForm($form);
    }

    public function getLenders()
    {
        $form = $this->lendersForm;
        $page = Request::query('page') ?: 1;

        $paginator = $form->getQuery()
            ->orderById()
            ->paginate($page, 100);
        
        return View::make(
            'admin.lenders',
            compact(
                'paginator',
                'form'
            )
        );
    }

    public function postLastCheckInEmail($lenderId)
    {
        $lender = LenderQuery::create()
            ->findOneById($lenderId);

        if(!$lender) {
            App::abort(404, 'Lender with this ID not found.');
        }
        
        if (!Input::has('lastCheckInEmail')){
            App::abort(404, 'Please select a proper date');            
        }        
        
        $lastCheckInEmailDate = Input::get('lastCheckInEmail');
        $lender->setLastCheckInEmail($lastCheckInEmailDate);
        $lender->save();
        
        \Flash::success('Last check-in email date added.');
        return Redirect::back();
    }
    
    public function postDeleteLender($lenderId)
    {
        $lender = LenderQuery::create()
            ->findOneById($lenderId);
        
        if(!$lender) {
            App::abort(404, 'Lender not found.');
        }
        
        $user = $lender->getUser();
        
        $userTransactionCount = TransactionQuery::create()
            ->filterByUser($user)
            ->count();
        
        $lenderInviteTransactionCount = InviteTransactionQuery::create()
            ->filterByLender($lender)
            ->count();

        if ($userTransactionCount > 0 || $lenderInviteTransactionCount > 0) {
            \Flash::error('This account cannot be deleted, because it is linked to invites or transactions.');
        }else {
            $user->delete();
            \Flash::success('Lender deleted.');
        }
        
        return Redirect::back();
    }


    public function postDeactivateLender($lenderId)
    {
        $lender = LenderQuery::create()
            ->findOneById($lenderId);

        if(!$lender) {
            App::abort(404, 'Lender not found.');
        }
        
        $user = $lender->getUser();
        $user->setActive(false);
        $user->save();
        
        $lender->setActive(false);
        $lender->save();
        
        \Flash::success('Lender deactivated');
        return Redirect::back();
    }

    public function postActivateLender($lenderId)
    {
        $lender = LenderQuery::create()
            ->findOneById($lenderId);

        if(!$lender) {
            App::abort(404, 'Lender not found.');
        }

        $user = $lender->getUser();
        $user->setActive(true);
        $user->save();

        $lender->setActive(true);
        $lender->save();

        \Flash::success('Lender activated.');
        return Redirect::back();
    }

    public function getVolunteers()
    {
        $form = $this->lendersForm;
        $page = Request::query('page') ? : 1;
        $countryId = Request::query('country') ? : null;
        $search = Request::query('search') ? : null;

        $query = LenderQuery::create();

        if (($countryId == 'all_countries' || !$countryId) && !$search) {
            $query->useUserQuery()
                ->filterBySubRole(User::SUB_ROLE_VOLUNTEER)
                ->endUse();
        }

        $paginator = $form->getQuery($query)
            ->orderById()
            ->paginate($page, 100);

        $count = count($paginator);

        return View::make('admin.volunteers', compact('paginator', 'form', 'count'));
    }

    public function getVolunteerMentors()
    {
        $form = $this->borrowersForm;
        $page = Request::query('page') ? : 1;
        $borrowerService = $this->borrowerService;

        $query = BorrowerQuery::create()
            ->useUserQuery()
            ->filterBySubRole(User::SUB_ROLE_VOLUNTEER_MENTOR)
            ->endUse();

        $paginator = $form->getQuery($query)
            ->orderById()
            ->paginate($page, 100);

        $paginator->populateRelation('AdminNote');

        $assignedMembers = BorrowerQuery::create()
            ->filterByVolunteerMentorId($paginator->toKeyValue('id', 'id'))
            ->find();

        $menteeCounts = VolunteerMentorQuery::create()
            ->filterByBorrowerId($paginator->toKeyValue('id', 'id'))
            ->find()
            ->toKeyValue('borrowerId', 'menteeCount');

        $_adminNotes = AdminNoteQuery::create()
            ->filterByBorrowerId($paginator->toKeyValue('id', 'id'))
            ->joinWith('User')
            ->find();

        $adminNotes = [];
        foreach ($_adminNotes as $VmNote) {
            if (!isset($adminNotes[$VmNote->getBorrowerId()])) {
                $adminNotes[$VmNote->getBorrowerId()] = [];
            }
            $adminNotes[$VmNote->getBorrowerId()][] = $VmNote;
        }

        return View::make('admin.volunteer-mentors.index', compact('paginator', 'form', 'menteeCounts', 'assignedMembers', 'adminNotes', 'borrowerService'));
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

            \Flash::success("Exchange rate updated.");
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

            \Flash::success("Message sent. Thanks!");
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

            \Flash::success("Categories successfully set.");
            return Redirect::route('loan:index', $loanId);
        }

        \Flash::error('common.validation.error');
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
            
            \Flash::success("Setting updated.");
            return Redirect::route('admin:settings');
        }

        \Flash::error('common.validation.error');
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

            $thankyous = array('Thanks', 'Merci beaucoup', 'Terima kasih', 'Gracias', 'Obrigados', 'Jerejef', 'Asante sana', 'Danke schön', 'Shukran');
            $thanks = $thankyous[array_rand($thankyous)];

            \Flash::success('Your translation has been published. '.$thanks.'!');

            return Redirect::route('loan:index', $loanId);
        }

        \Flash::error('common.validation.error');
        return Redirect::route('loan:index', $loanId)->withForm($form);
    }

    public function getTranslationFeed($type = null)
    {
        $languageCode = Request::query('language') ? : null;

        if ($type == 'loans') {
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

        } else {
            $type = 'comments';
            $comments = BorrowerCommentQuery::create()
                ->where('BorrowerComment.BorrowerId = BorrowerComment.UserId')
                ->filterByMessageTranslation(null);


            if ($languageCode){
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
            ->paginate($page, 100);

        return View::make('admin.gift-cards', compact('paginator'));
    }

    public function resendEmailToRecipient($id)
    {
        $giftCard = GiftCardQuery::create()
            ->findOneById($id);

        $this->lenderMailer->sendGiftCardMailToRecipient($giftCard);

        \Flash::success("Email sent.");
        return Redirect::route('admin:get:gift-cards');
    }

    public function addVolunteer($id)
    {
        $user = UserQuery::create()
            ->findOneById($id);
        $user->setSubRole(User::SUB_ROLE_VOLUNTEER);
        $user->save();

        \Flash::success("Volunteer added.");
        return Redirect::back();
    }

    public function removeVolunteer($id)
    {
        $user = UserQuery::create()
            ->findOneById($id);
        if (!$user) {
            App::abort(404);
        }
        if ($this->borrowerService->removeVolunteerMentor($user)) {
            \Flash::success("Volunteer removed.");
            return Redirect::back();
        }
        \Flash::success('common.validation.error');
        return Redirect::back();
    }

    public function addVolunteerMentor($id)
    {
        $user = UserQuery::create()
            ->findOneById($id);
        if (!$user) {
            App::abort(404);
        }

        $this->borrowerService->addVolunteerMentor($user);

        \Flash::success("Volunteer added.");
        return Redirect::back();
    }

    public function removeVolunteerMentor($id)
    {
        $user = UserQuery::create()
            ->findOneById($id);
        $user->setSubRole(null);
        $user->save();

        \Flash::success("Volunteer removed.");
        return Redirect::back();
    }

    public function getWithdrawalRequests()
    {
        $page = Request::query('page') ? : 1;

        $paginator = WithdrawalRequestQuery::create()
            ->joinWith('Lender')
            ->joinWith('Lender.User')
            ->orderByUpdatedAt('desc')
            ->paginate($page, 100);

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
            $this->balanceService->payWithdrawRequest($withdrawalRequest);
            \Flash::success('Payments sent.');
            return Redirect::route('admin:get:withdrawal-requests');
        }

        \Flash::error('common.validation.error');
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
            \Flash::error('Error:' . $e->getMessage());
            return Redirect::route('admin:get:withdrawal-requests');
        }
        \Flash::success('Successfully processed.');
        return Redirect::route('admin:get:withdrawal-requests');
    }

    public function getPublishComments()
    {
        $page = Input::get('page', 1);

        $comments = BorrowerCommentQuery::create()
            ->filterByPublished(false)
            ->filterByCreatedAt(array('min' => Carbon::create()->subMonth()))
            ->orderByCreatedAt('desc')
            ->joinWith('User')
            ->paginateWithUploads($page, 10);

        return View::make('admin.publish-comments', compact('comments'));
    }

    public function postPublishComments()
    {
        $borrowerCommentId = Input::get('borrowerCommentId');

        $comment = BorrowerCommentQuery::create()
            ->findOneById($borrowerCommentId);

        if (!$comment) {
            App::abort(404, 'Comment not found.');
        }

        $comment->setPublished(true);
        $comment->save();

        \Flash::success('common.comments.flash.post');

        return Redirect::back();
    }

    public function getRepayments()
    {
        $form = $this->uploadRepaymentsForm;
        $filterForm = $this->borrowersForm;
        $paymentCounts = $this->repaymentService->getNumberOfPayments();

        $page = Request::query('page') ? : 1;

        if ($filterForm->isFiltering()) {
            $borrowers = $filterForm->getQuery()
                ->joinWith('User')
                ->joinWith('Profile')
                ->orderById()
                ->paginate($page, 10);  
        } else {
            $borrowers = null;
        }
        
        return View::make(
            'admin.repayments.repayments',
            compact('paymentCounts', 'form', 'filterForm', 'borrowers')
        );
    }

    public function postUploadRepayments()
    {
        $form = $this->uploadRepaymentsForm;
        $form->handleRequest(Request::instance());

        if ($form->isValid()) {
            $data = $form->getData();

            if (Input::hasFile('inputFile')) {
                $file = Input::file('inputFile');
                $importPayments =  $this->importService->importBorrowerPayments($data['countryCode'], $file);
                if ( !$importPayments) {
                    Flash::error('common.validation.error');
                    return Redirect::route('admin:repayments')->withForm($form);
                }
                Flash::success('Repayments successfully posted. Thanks!');
                return Redirect::route('admin:repayments');
            }
        }

        Flash::error('common.validation.error');
        return Redirect::route('admin:repayments')->withForm($form);
    }

    public function getRepaymentProcess($status = null)
    {
        switch ($status) {
            case Borrower::PAYMENT_COMPLETE:
                $name = "Ready to process";
                break;
            case Borrower::PAYMENT_INCOMPLETE:
                $name = "Incomplete";
                break;
            default:
                $name = "Failed";
        }

        $payments = $this->repaymentService->getBorrowerRepayments($status);
        if ( $status == Borrower::PAYMENT_INCOMPLETE || $status == Borrower::PAYMENT_FAILED) {
            $deletable = true;
        } else {
            $deletable = false;
        }
        return View::make('admin.repayments.repayment-process', compact('name', 'payments', 'status', 'deletable'));
    }

    public function postRepaymentProcess()
    {
        $paymentIds = Input::get('paymentIds');
        $paymentIds = is_array($paymentIds) ? $paymentIds : [];
        $status = Input::get('status');

        $payments = BorrowerPaymentQuery::create()
            ->updateStatusToDeleted($paymentIds);

        if ($payments) {
            Flash::success('Successfully deleted.');
            return Redirect::route('admin:repayment-process', compact('status'));
        }
        Flash::error('common.validation.error');
        return Redirect::route('admin:repayment-process', compact('status'));
    }

    public function getRepaymentRefund()
    {
        $refunds = $this->repaymentService->getBorrowerRefunds(false);
        return View::make('admin.repayments.repayment-refunds', compact('refunds'));
    }

    public function postRepaymentRefund()
    {
        $refundsIds = Input::get('refundsIds');
        $refundsIds = is_array($refundsIds) ? $refundsIds : [];

        $refunds = BorrowerRefundQuery::create()
            ->updateRefundToTrue($refundsIds);

        if ($refunds) {
            Flash::success('Transaction processed.');
            return Redirect::route('admin:repayments-refunds');
        }
        Flash::error('common.validation.error');
        return Redirect::route('admin:repayments-refunds');
    }

    public function getRepaymentSchedule($borrowerId, $loanId = null)
    {
        $borrower = BorrowerQuery::create()
            ->filterById($borrowerId)
            ->findOne();

        if (!$borrower) {
            App::abort(404);
        }

        if ($loanId) {
            $loan = LoanQuery::create()
                ->filterById($loanId)
                ->filterByBorrower($borrower)
                ->findOne();

            if (!$borrower) {
                App::abort(404);
            }
        } else {
            $loan = $borrower->getActiveLoan();
        }
        
        if ($loan->isDisbursed()) {
            $repaymentSchedule = $this->repaymentService->getRepaymentSchedule($loan);
        } else {
            $repaymentSchedule = null;
        }
        
        $allowPayment = $loan && $loan->isActive();
        $form = new EnterRepaymentForm();

        return View::make(
            'admin.repayments.repayment-schedule',
            compact('borrower', 'loan', 'repaymentSchedule', 'allowPayment', 'form')
        );
    }

    public function postEnterRepayment($loanId)
    {
        $loan = LoanQuery::create()
            ->filterById($loanId)
            ->findOne();

        if (!$loan || !$loan->isActive()) {
            App::abort(404);
        }

        $borrower = $loan->getBorrower();

        $redirect = Redirect::route(
            'admin:repayment-schedule',
            ['borrowerId' => $borrower->getId(), 'loanId' => $loanId]
        );

        $form = new EnterRepaymentForm();
        $form->handleRequest(Request::instance());

        if ($form->isValid()) {
            $data = $form->getData();
            $this->repaymentService->addRepayment($loan, [
                'date'   => Carbon::createFromFormat('m/d/Y', $data['date']),
                'amount' => $data['amount'],
            ]);
            
            \Flash::success('Repayment successfully entered. Thanks!');
        } else {
            \Flash::error('common.validation.error');
            $redirect->withForm($form);
        }

        return $redirect;
    }

    public function postVmNote()
    {
        $borrowerId = Input::get('borrowerId');
        $note = Input::get('note');

        $user = \Auth::user();

        $borrower = BorrowerQuery::create()
            ->findOneById($borrowerId);

        if (!$borrower) {
            App::abort(404, 'VM not found');
        }

        $loanNote = new AdminNote();
        $loanNote
            ->setUser($user)
            ->setBorrower($borrower)
            ->setType('VMNote')
            ->setNote($note);
        $loanNote->save();

        return \Redirect::back();
    }

}
