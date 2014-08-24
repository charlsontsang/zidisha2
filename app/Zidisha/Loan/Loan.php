<?php

namespace Zidisha\Loan;

use Carbon\Carbon;
use Zidisha\Admin\Setting;
use Zidisha\Comment\CommentReceiverInterface;
use Zidisha\Currency\Currency;
use Zidisha\Currency\Money;
use Zidisha\Loan\Base\Loan as BaseLoan;
use Zidisha\Repayment\InstallmentQuery;

class Loan extends BaseLoan implements CommentReceiverInterface
{

    const OPEN = 0;
    const FUNDED = 1;
    const ACTIVE = 2;
    const REPAID = 3;
    const NO_LOAN = 4;
    const DEFAULTED = 5;
    const CANCELED = 6;
    const EXPIRED = 7;
    
    const WEEKLY_INSTALLMENT  = 'weekly';
    const MONTHLY_INSTALLMENT = 'monthly';

    /**
     * @return Money
     */
    public function getUsdAmount()
    {
        return Money::create(parent::getUsdAmount(), 'USD');
    }

    /**
     * @param Money $money
     * @return $this|Loan
     */
    public function setUsdAmount($money)
    {
        return parent::setUsdAmount($money->getAmount());
    }

    /**
     * @return Money
     */
    public function getAmount()
    {
        return Money::create(parent::getAmount(), $this->getCurrencyCode());
    }

    /**
     * @param Money $money
     * @return $this|Loan
     */
    public function setAmount($money)
    {
        return parent::setAmount($money->getAmount());
    }

    /**
     * @return Money
     */
    public function getRegistrationFee()
    {
        return Money::create(parent::getRegistrationFee(), $this->getCurrencyCode());
    }

    /**
     * @param Money $money
     * @return $this|Loan
     */
    public function setRegistrationFee($money)
    {
        return parent::setRegistrationFee($money->getAmount());
    }

    public function getCurrency()
    {
        return Currency::valueOf($this->getCurrencyCode());
    }

    public function calculateExtraDays($disbursedAt)
    {
        $date = Carbon::instance($disbursedAt);
        
        if ($this->isWeeklyInstallment()) {
            $extraDays = ($this->getInstallmentDay() - $date->dayOfWeek + 7) % 7;
        } else {
            if ($date->day > $this->getInstallmentDay()) {
                // TODO installmentDay = 15, date = jan 30 => march 2 => march 15 => extra days > month
                $date->addMonth();
            }
            $date->day($this->getInstallmentDay());
            $extraDays = $date->diffInDays(Carbon::instance($disbursedAt));
        }
        
        return $this->setExtraDays($extraDays);
    }

    /**
     * @param Money $raisedUsdAmount
     * @return $this|Loan
     */
    public function setRaisedUsdAmount($raisedUsdAmount)
    {
        if ($raisedUsdAmount->lessThan($this->getUsdAmount())) {
            $raisedPercentage = $raisedUsdAmount
                ->divide($this->getUsdAmount()->getAmount())
                ->multiply(100)->round(2)
                ->getAmount();
        } else {
            $raisedUsdAmount = $this->getUsdAmount();
            $raisedPercentage = 100;
        }
        
        $this->setRaisedPercentage($raisedPercentage);
        return parent::setRaisedUsdAmount($raisedUsdAmount->getAmount());
    }

    /**
     * @return Money
     */
    public function getRaisedUsdAmount()
    {
        return Money::create(parent::getRaisedUsdAmount(), 'USD');
    }
    
    public function getStillNeededUsdAmount()
    {
        return $this->getUsdAmount()->subtract($this->getRaisedUsdAmount())->max(Money::create(0));
    }

    /**
     * @return Money
     */
    public function getDisbursedAmount()
    {
        return Money::create(parent::getDisbursedAmount(), $this->getCurrencyCode());
    }

    /**
     * @param Money $money
     * @return $this|Loan
     */
    public function setDisbursedAmount($money)
    {
        return parent::setDisbursedAmount($money->getAmount());
    }

    /**
     * @return Money
     */
    public function getTotalAmount()
    {
        return Money::create(parent::getTotalAmount(), $this->getCurrencyCode());
    }

    /**
     * @param Money $money
     * @return $this|Loan
     */
    public function setTotalAmount($money)
    {
        return parent::setTotalAmount($money->getAmount());
    }

    /**
     * @return Money
     */
    public function getPaidAmount()
    {
        return Money::create(parent::getPaidAmount(), $this->getCurrencyCode());
    }

    /**
     * @param Money $money
     * @return $this|Loan
     */
    public function setPaidAmount($money)
    {
        $totalAmount = $this->getTotalAmount();

        if ($money->lessThan($totalAmount)) {
            $paidPercentage = $money
                ->divide($totalAmount->getAmount())
                ->multiply(100)->round(2)
                ->getAmount();
        } else {
            $paidPercentage = 100;
        }

        $this->setPaidPercentage($paidPercentage);
        $this->save();

        return parent::setPaidAmount($money->getAmount());
    }

    public function isWeeklyInstallment()
    {
        return $this->getInstallmentPeriod() == self::WEEKLY_INSTALLMENT;
    }

    public function isOpen()
    {
        return $this->getStatus() == static::OPEN;
    }
    
    public function isActive()
    {
        return $this->getStatus() == static::ACTIVE;
    }

    public function isFullyFunded()
    {
        return $this->getRaisedPercentage() == 100;
    }

    public function isAuthorized()
    {
        return (boolean) $this->getAuthorizedAt();
    }

    public function getDaysLeft()
    {
        // TODO
//        $time_left = '';
//        if ($this->isOpen()) {
//            $deadline = $loan['applydate'] + ($this->getAdminSetting('deadline') * 24 * 60 * 60);
//            $seconds_left = $deadline - time();
//            if ($seconds_left <= 0){
//                $time_left = 'Expired';
//            } elseif ($seconds_left < (60*60)){
//                $time_left = '<strong><font color = "red">'.ceil($seconds_left/60).' minutes</font></strong>';
//            } elseif ($seconds_left < (60*60*24)){
//                $time_left = '<strong><font color = "red">'.ceil($seconds_left/60/60).' hours</font></strong>';
//            } else {
//                $time_left = ceil($seconds_left/60/60/24).' days';
//            }
//        } else {
//            $time_left = 'Expired';
//        }
//        return $time_left;
        
        return 3;
    }


    public function isEnded()
    {
        return in_array($this->getStatus(), [Loan::REPAID, Loan::DEFAULTED]);
    }

    public function getEndedAt()
    {
        if ($this->getStatus() == Loan::REPAID) {
            return $this->getRepaidAt();
        } elseif ($this->getStatus() == Loan::DEFAULTED) {
            return $this->getExpiredAt();
        }
    }

    public function getCommentReceiverId()
    {
        return $this->getId();
    }

    public function getRepaidPercent()
    {
        $totals = InstallmentQuery::create()
            ->filterByLoanId($this->getId())
            ->select(array('totalAmount', 'totalPaidAmount'))
            ->withColumn('SUM(amount)', 'totalAmount')
            ->withColumn('SUM(paid_amount)', 'totalPaidAmount')
            ->findOne();
        if ($totals['totalAmount'] > 0) {
            return intval(($totals['totalPaidAmount']/$totals['totalAmount'])*100);
        } else {
            return 0;
        }
    }


    public function getFundRaisingTimeLeft()
    {
        if ($this->getStatus() == Loan::OPEN) {
            $deadLine = $this->getAppliedAt()->getTimestamp() + (Setting::get('loan.expireThreshold') * 24 * 60 * 60);
            $secondsLeft = $deadLine - time();
            if ($secondsLeft <= 0){
                $timeLeft = 'Expired';
            } elseif ($secondsLeft < (60*60)){
                $timeLeft = '<span class="label label-danger">'.ceil($secondsLeft/60).' minutes</span>';
            } elseif ($secondsLeft < (60*60*24)){
                $timeLeft = '<span class="label label-danger">'.ceil($secondsLeft/60/60).' hours</span>';
            } else {
                $timeLeft = ceil($secondsLeft/60/60/24).' days';
            }
        } else {
            $timeLeft = 'Expired';
        }
        return $timeLeft;
    }

    public function isDisbursed()
    {
        return $this->getStatus() >= Loan::ACTIVE;
    }
}
