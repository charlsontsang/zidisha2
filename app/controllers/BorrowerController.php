<?php


use Illuminate\Support\ViewErrorBag;
use Zidisha\Admin\Setting;
use Zidisha\Borrower\Borrower;
use Zidisha\Borrower\BorrowerActivationService;
use Zidisha\Borrower\BorrowerQuery;
use Zidisha\Borrower\BorrowerService;
use Zidisha\Borrower\Form\EditProfile;
use Zidisha\Borrower\Form\PersonalInformationForm;
use Zidisha\Credit\CreditsEarnedQuery;
use Zidisha\Currency\Converter;
use Zidisha\Currency\ExchangeRateQuery;
use Zidisha\Currency\Money;
use Zidisha\Loan\Loan;
use Zidisha\Loan\LoanQuery;
use Zidisha\Loan\LoanService;
use Zidisha\Loan\StageQuery;
use Zidisha\Mail\BorrowerMailer;
use Zidisha\Repayment\RepaymentService;
use Zidisha\Upload\UploadQuery;
use Zidisha\Utility\Utility;
use Zidisha\Vendor\Facebook\FacebookService;

class BorrowerController extends BaseController
{
    private $borrowerService;
    private $borrowerMailer;
    private $facebookService;
    private $borrowerActivationService;
    private $repaymentService;
    private $loanService;


    public function __construct(
        BorrowerService $borrowerService,
        BorrowerMailer $borrowerMailer,
        FacebookService $facebookService,
        BorrowerActivationService $borrowerActivationService,
        RepaymentService $repaymentService,
        LoanService $loanService
    ) {
        $this->borrowerService = $borrowerService;
        $this->borrowerMailer = $borrowerMailer;
        $this->facebookService = $facebookService;
        $this->borrowerActivationService = $borrowerActivationService;
        $this->repaymentService = $repaymentService;
        $this->loanService = $loanService;
    }

    public function getPublicProfile($username)
    {
        $borrower = BorrowerQuery::create()
            ->useUserQuery()
            ->filterByUsername($username)
            ->endUse()
            ->findOne();

        if (!$borrower) {
            App::abort(404);
        }

        return View::make(
            'borrower.public-profile',
            compact('borrower')
        );
    }

    public function getEditProfile()
    {
        $borrower = \Auth::user()->getBorrower();

        $form = new EditProfile($borrower);

        return View::make(
            'borrower.edit-profile',
            compact('form', 'borrower')
        );
    }

    public function postEditProfile()
    {
        $user = \Auth::user();
        $borrower = $user->getBorrower();
        $username = $user->getUsername();

        $form = new EditProfile($borrower);
        $form->handleRequest(Request::instance());

        if ($form->isValid()) {
            $data = $form->getData();

            $files = $this->getInputFiles();

            $this->borrowerService->editBorrower($borrower, $data, $files);

            return Redirect::route('borrower:public-profile', $username);
        }

        return Redirect::route('borrower:edit-profile')->withForm($form);
    }

    protected function getInputFiles()
    {
        $files = [];
        if (\Input::hasFile('images')) {
            foreach (\Input::file('images') as $file) {
                if (!empty($file)) {
                    if ($file->isValid() && $file->getSize() < Config::get('image.allowed-file-size')) {
                        $files[] = $file;
                    } else {
                        Flash::error(\Lang::get('borrower.flash.file-not-valid'));
                    }
                }
            }
            return $files;
        }
        return $files;
    }

    public function getDashboard()
    {
        /** @var Borrower $borrower */
        $borrower = \Auth::User()->getBorrower();

        $volunteerMentor = $borrower->getVolunteerMentor() ? $borrower->getVolunteerMentor()->getBorrowerVolunteer() : null;
        $feedbackMessages = [];

        $loan = $borrower->getActiveLoan();

        if ($loan){
            $feedbackMessages = $this->borrowerService->getFeedbackMessages($loan);
        }
        if ($borrower->isActivationPending()) {
            $feedbackMessages = $this->borrowerActivationService->getFeedbackMessages($borrower);
        }

        return View::make('borrower.dashboard', compact('borrower', 'volunteerMentor', 'feedbackMessages'));
    }

    public function getTransactionHistory()
    {
        return View::make('borrower.history');
    }

    public function postDeleteUpload()
    {
        $borrower = BorrowerQuery::create()->filterById(\Input::get('borrower_id'))->findOne();
        $upload = UploadQuery::create()->filterById(\Input::get('upload_id'))->findOne();

        $user = \Auth::user();

        if (!$borrower || !$upload) {
            App::abort(404, 'Bad Request');
        }

        $this->borrowerService->deleteUpload($borrower, $upload);

        Flash::success(\Lang::get('borrower.flash.file-deleted'));
        return Redirect::back();
    }

    public function resendVerificationMail()
    {
        $borrower = \Auth::user()->getBorrower();

        $this->borrowerService->sendVerificationCode($borrower);

        \Flash::info('A verification code has been sent to your email. Please check your email.');
        return \Redirect::action('BorrowerController@getDashboard');
    }

    public function getPersonalInformation()
    {
        $borrower = \Auth::user()->getBorrower();

        $personalInformation = $borrower->getPersonalInformation();

        $form = new PersonalInformationForm($borrower);
        $form->handleData($form->getDefaultData());

        $errors = new ViewErrorBag();
        $errors->put('default', $form->getMessageBag());
        Session::flash('errors', $errors);

        $isFacebookRequired = $this->borrowerService->isFacebookRequired($borrower);

        $facebookJoinUrl = $this->facebookService->getLoginUrl(
            'borrower:facebook-verification',
            ['scope' => 'email,user_location,publish_stream,read_stream']
        );

        if ($isFacebookRequired) {
            \Flash::error('Facebook verification required.');
        }

        return \View::make(
            'borrower.personal-information',
            ['personalInformation' => $personalInformation, 'form' => $form, 'facebookJoinUrl' => $facebookJoinUrl, 'borrower' => $borrower, 'isFacebookRequired' => $isFacebookRequired]
        );
    }

    public function postPersonalInformation()
    {
        $borrower = \Auth::user()->getBorrower();

        $form = new PersonalInformationForm($borrower);

        $form->handleRequest(\Request::instance());

        if ($form->isValid()) {
            $data = $form->getNestedData();

            $this->borrowerService->updatePersonalInformation($borrower, $data);

            \Flash::success('Your profile has been updated.');
            return Redirect::route('borrower:personal-information');
        }

        return Redirect::route('borrower:personal-information')->withForm($form);
    }

    public function getFacebookRedirect()
    {
        $facebookUser = $this->facebookService->getUserProfile();

        if ($facebookUser) {
            $errors = $this->borrowerService->validateConnectingFacebookUser($facebookUser);

            if ($errors) {
                foreach ($errors as $error) {
                    Flash::error($error);
                }
                return Redirect::route('borrower:personal-information');
            }
        }

        $facebookId = $facebookUser['id'];

        $user = \Auth::user();

        $user->setFacebookId($facebookId);
        $user->save();

        \Flash::success('Your facebook account is linked successfully.');
        return Redirect::route('borrower:personal-information');
    }

    public function getCurrentCredit()
    {
        /** @var $borrower Borrower */
        $borrower = \Auth::user()->getBorrower();
        if (!$borrower) {
            App::abort(404, 'Bad Request');
        }

        $lastRepaidLoan = LoanQuery::create()
            ->getLastRepaidLoan($borrower);
        $activeLoan = $borrower->getActiveLoan();
        $exchangeRate = ExchangeRateQuery::create()
            ->findCurrent($borrower->getCountry()->getCurrency());
        if(empty($activeLoan) && $lastRepaidLoan) {
            $activeLoan = $lastRepaidLoan;
        }
        //$lastRepaidStatus= $lastRepaidLoan->getStatus();
        $loanCounts = LoanQuery::create()
            ->filterByBorrower($borrower)
            ->filterByStatus(Loan::REPAID)
            ->_or()
            ->filterByStatus(Loan::DEFAULTED)
            ->count();

        $secondLoanPercentage = Setting::get('loan.secondLoanPercentage');
        $nextLoanPercentage = Setting::get('loan.nextLoanPercentage');
        $firstLoanValue = Setting::get('loan.firstLoanValue');
        $secondLoanValue = Setting::get('loan.secondLoanValue');
        $thirdLoanValue = Setting::get('loan.thirdLoanValue');
        $nextLoanValue = Setting::get('loan.nextLoanValue');
        $repaymentSchedule = $this->repaymentService->getRepaymentSchedule($activeLoan);
        $currency = $borrower->getCountry()->getCurrency();
        $currencyCode = $borrower->getCountry()->getCurrencyCode();
        $maximumAmount = Converter::fromUSD(Money::create($nextLoanValue, 'USD'), $currency, $exchangeRate)->getAmount();
        $loanStatus = $borrower->getLoanStatus();
        $inviteCredit = $this->borrowerService->getInviteCredit($borrower);
        $vmCredit = $this->borrowerService->getVMCredit($borrower);
        $creditEarned = $inviteCredit + $vmCredit;
        $repaymentRate = $this->loanService->getOnTimeRepaymentScore($borrower);
        $borrowerAmountExceptCredit = $this->borrowerService->getCurrentCreditLimit($borrower, $creditEarned, false);
        $maximumBorrowerAmountNextLoan = $this->borrowerService->getCurrentCreditLimit($borrower, $creditEarned, true);
        $raisedUsdAmount = $activeLoan->getRaisedUsdAmount();
        $raisedAmount = Converter::fromUSD($raisedUsdAmount, $activeLoan->getCurrency(), $exchangeRate);

        if ($loanStatus == Loan::OPEN || $loanStatus == Loan::ACTIVE || $lastRepaidLoan || empty($activeLoan)) {
            $borrowerAllRepaidLoans = LoanQuery::create()
                ->getAllRepaidLoansForBorrower($borrower);
            $borrowerAllRepaidLoansCount = $borrowerAllRepaidLoans->count();
            $k = 2;
            $params['nxtLoanvalue'] = '';
            if (!empty($lastRepaidLoan) && $lastRepaidLoan->getStatus() == Loan::DEFAULTED) {
                $params['nxtLoanvalue'] = '';
                $params['firstLoanVal'] = '';
            } else {
                if (!empty($borrowerAllRepaidLoans) && $borrowerAllRepaidLoansCount > 0) {
                    $k = 1;
                    /** @var Loan $borrowerAllRepaidLoan */
                    foreach ($borrowerAllRepaidLoans as $borrowerAllRepaidLoan) {
                        $loanRepaidDate = StageQuery::create()
                            ->filterByLoan($borrowerAllRepaidLoan)
                            ->filterByStatus(Loan::REPAID)
                            ->findOne();
                        $loanProfileUrl = route('loan:index', $borrowerAllRepaidLoan->getId());
                        if ($k = 1) {
                            $params['firstLoanVal'] = '1.'." ".$currencyCode." ".number_format($raisedAmount->getAmount(), 0, ".", ",")." (<a href='$loanProfileUrl' >Repaid ".$loanRepaidDate->format('M j, Y')."</a>)";
                        } else {
                            $params['nxtLoanvalue'] .= "<br/>".$k.". ".$currencyCode." ".number_format($raisedAmount->getAmount(), 0, ".", ",")." (<a href='$loanProfileUrl' >Repaid ".$loanRepaidDate->format('M j, Y')."</a>)";
                        }
                        $k++;
                    }
                } elseif (empty($activeLoan)) {
                    $value = $firstLoanValue;
                    $params['firstLoanVal'] = '1.'." ".$currencyCode.' '.number_format(Converter::fromUSD($firstLoanValue, $borrower->getCountry()->getCurrency(), $exchangeRate)->getAmount(), 0, ".", ",");
                }

                if (!empty($activeLoan) && $activeLoan != $lastRepaidLoan) {
                    $repaidPercent = $activeLoan->getPaidPercentage();
                    $loanProfileUrl = route('loan:index', $activeLoan->getId());

                    if ($loanCounts == 0) {
                        $k = 1;
                        if ($loanStatus == Loan::FUNDED || $loanStatus == Loan::OPEN) {
                            $params['firstLoanVal'] = '1.'." ".$currencyCode." ".number_format($raisedAmount->getAmount(), 0, ".", ",")." (<a href='$loanProfileUrl' >Fundraising Loan</a>)";
                        } else {
                            $params['firstLoanVal'] = '1.'." ".$currencyCode." ".number_format($raisedAmount->getAmount(), 0, ".", ",")." (<a href='$loanProfileUrl' >Disbursed ".$activeLoan->getDisbursedAt()->format('M j, Y').", ".$repaidPercent."% repaid</a>)";
                        }
                    } else {
                        if ($loanStatus == Loan::FUNDED || $loanStatus == Loan::OPEN) {
                            $params['nxtLoanvalue'] .= "<br/>".$k.". ".$currencyCode." ".number_format($raisedAmount->getAmount(), 0, ".", ",")." (<a href='$loanProfileUrl' >Fundraising Loan</a>)";
                        } else {
                            $params['nxtLoanvalue'] .= "<br/>".$k.". ".$currencyCode." ".number_format($raisedAmount->getAmount(), 0, ".", ",")." (<a href='$loanProfileUrl' >Disbursed ".$activeLoan->getDisbursedAt()->format('M j, Y').", ".$repaidPercent."% repaid</a>)";
                        }
                    }
                    $k++;
                }
                if (!empty($activeLoan)) {
                    $valueNative = $borrowerAmountExceptCredit;
                    $params['nxtLoanvalue'] .= "<br/>".$k.". ".$currencyCode.' '.number_format($maximumBorrowerAmountNextLoan, 0, ".", ",");
                    $k++;
                    $valueObj = Converter::toUSD(Money::create($valueNative, $borrower->getCountry()->getCurrencyCode()), $exchangeRate);
                    $value = $valueObj->getAmount();
                }
                for ($i = $k; $i < 12; $i++) {
                    if ($value <= 200) {
                        $value = ($value * $secondLoanPercentage)/100;
                        if ($i == 2) {
                            $loanUsdValue = $secondLoanValue;
                        } else {
                            $loanUsdValue = $thirdLoanValue;
                        }
                        if ($loanUsdValue < $value) {
                            $value = $loanUsdValue;
                        }
                        $val= number_format(Converter::fromUSD(Money::create($value, 'USD'), $borrower->getCountry()->getCurrency(), $exchangeRate)->getAmount(), 0, ".", ",");

                        $params['nxtLoanvalue'] .= "<br/>".$i.". ".$currencyCode.' '.$val;
                    } else {
                        $value = ($value * $nextLoanPercentage) / 100;
                        $localValue = Converter::fromUSD(Money::create($value, 'USD'), $borrower->getCountry()->getCurrency(), $exchangeRate)->getAmount();

                        if ($localValue > $maximumAmount) {
                            $val= number_format($maximumAmount, 0, ".", ",");
                            $params['nxtLoanvalue'] .= "<br/>".$i.". and thereafter ".$currencyCode.' '.$val;
                            break;
                        } else {
                            $val= number_format($localValue, 0, ".", ",");
                            $params['nxtLoanvalue'] .="<br/>".$i.". ".$currencyCode.' '.$val;
                        }
                    }
                }
            }
            if ($repaymentSchedule->getMissedInstallmentCount() == 0) {
                $currentCreditLimit = $maximumBorrowerAmountNextLoan;

                if ($loanStatus == Loan::FUNDED || $loanStatus == Loan::OPEN) {
                    $params['currentCreditLimit'] = $currencyCode .' '. number_format($raisedAmount->getAmount(), 0, ".", ",");
                } else {
                    $params['currentCreditLimit'] = $currencyCode .' '. number_format($currentCreditLimit, 0, ".", ",");
                }
                $params['baseCreditLimit'] = $currencyCode .' '. number_format($borrowerAmountExceptCredit, 0, ".", ",");
                $params['inviteCredit'] = $currencyCode .' '.number_format($inviteCredit);
                $params['myInvites'] = route('borrower:invites');
                $params['volunteerMentorCredit'] = $currencyCode .' '.number_format($vmCredit);
            } else {
                $currentCreditLimit = $maximumBorrowerAmountNextLoan;

                if ($loanStatus == Loan::FUNDED || $loanStatus == Loan::OPEN) {
                    $params['currentCreditLimit'] = $currencyCode .' '. number_format($raisedAmount->getAmount(), 0, ".", ",");
                } else {
                    $params['currentCreditLimit'] = $currencyCode .' '. number_format($currentCreditLimit, 0, ".", ",");
                }
                $params['baseCreditLimit'] = $currencyCode .' '. number_format($borrowerAmountExceptCredit, 0, ".", ",");
                $params['inviteCredit'] = $currencyCode .' '.number_format($inviteCredit);
                $params['myInvites'] = route('borrower:invites');
                $params['volunteerMentorCredit'] = $currencyCode .' '.number_format($vmCredit);
            }

            $isEverFunded = $this->borrowerService->isEverFundedLoan($borrower);
            $disbursedDate = $activeLoan->getDisbursedAt();
            $currentTime = new \DateTime();
            $disbursedAt = $disbursedDate ? $disbursedDate->getTimestamp() : null;
            $months = Utility::getIntervalInMonths($disbursedAt, $currentTime->getTimestamp());
            $minimumRepaymentRate = $minRepaymentRate = \Setting::get('invite.minRepaymentRate');

            if ($raisedUsdAmount->lessThanOrEqual(Money::create(200, 'USD'))) {
                $timeThreshold = Setting::get('loan.loanIncreaseThresholdLow');
            } elseif ($raisedUsdAmount->lessThanOrEqual(Money::create(1000, 'USD'))) {
                $timeThreshold = Setting::get('loan.loanIncreaseThresholdMid');
            } elseif ($raisedUsdAmount->lessThanOrEqual(Money::create(3000, 'USD'))) {
                $timeThreshold = Setting::get('loan.loanIncreaseThresholdHigh');
            } else {
                $timeThreshold = Setting::get('loan.loanIncreaseThresholdTop');
            }
            $params['TimeThrshld'] = $timeThreshold;

            if (empty($isEverFunded)) {
                $note = \Lang::get('borrower.loan-application.current-credit.first-loan');
            } elseif (empty($activeLoan) && $lastRepaidLoan) {
                $ontime = $this->loanService->isRepaidOnTime($borrower, $lastRepaidLoan);
                if ($ontime != 1) {
                    $note = \Lang::get('borrower.loan-application.current-credit.repaid-late', array('minimumRepaymentRate' => $minimumRepaymentRate) );
                }
            } elseif ($months < $timeThreshold) {
                $note = \Lang::get('borrower.loan-application.current-credit.time-insufficient', array('timeThreshold' => $timeThreshold, 'minimumRepaymentRate' => $minimumRepaymentRate));
            } elseif ($repaymentRate < $minRepaymentRate) {
                $note = \Lang::get('borrower.loan-application.current-credit.repayment-rate-insufficient', array('borrowerRepaymentRate' => $repaymentRate, 'minimumRepaymentRate' => $minimumRepaymentRate));
            } else {
                $note = \Lang::get('borrower.loan-application.current-credit.repayment-rate-sufficient', array('borrowerRepaymentRate' => $repaymentRate, 'minimumRepaymentRate' => $minimumRepaymentRate));
            }

            $beginning = \Lang::get('borrower.loan-application.current-credit.beginning', array('currentCreditLimit' => $params['currentCreditLimit'], 'baseCreditLimit' => $params['baseCreditLimit']));
            $inviteCredit = \Lang::get('borrower.loan-application.current-credit.invite-credit', array('myInvites' => $params['myInvites'], 'inviteCredit' => $params['inviteCredit']));
            $volunteerMentorCredit = \Lang::get('borrower.loan-application.current-credit.volunteer-mentor-credit', array('volunteerMentorCredit' => $params['volunteerMentorCredit']));
            $end = \Lang::get('borrower.loan-application.current-credit.end', array('currentCreditLimit' => $params['currentCreditLimit'], 'minimumRepaymentRate' => $minimumRepaymentRate, 'timeThreshold' => $timeThreshold, 'firstLoanVal' => $params['firstLoanVal'], 'nxtLoanvalue' => $params['nxtLoanvalue']));
        }

        return View::make('borrower.current-credit',
            compact('beginning', 'note', 'inviteCredit', 'volunteerMentorCredit', 'end')
            );
    }
}
