<?php
/**
 * Created by PhpStorm.
 * User: Singularity Guy
 * Date: 6/12/14
 * Time: 11:13 AM
 */

namespace Zidisha\Borrower\Form\Loan;

use Zidisha\Borrower\Base\Borrower;
use Zidisha\Form\AbstractForm;
use Zidisha\Loan\CategoryQuery;
use Zidisha\Loan\Loan;

class ApplicationForm extends AbstractForm
{

    /**
     * @var \Zidisha\Borrower\Base\Borrower
     */
    private $borrower;

    public function __construct(Borrower $borrower)
    {

        $this->borrower = $borrower;
    }

    public function getRules($data)
    {
        return [
            'title' => 'required|min:10',
            'proposal' => 'required|min:200',
            'categoryId' => 'required|exists:loan_categories,id,admin_only,false',
            'amount' => 'required|numeric',
            'installmentAmount' => 'required|numeric',
            'installmentDay' => 'required',
        ];
    }

    public function getCategories()
    {
        $categories = CategoryQuery::create()
            ->orderBySortableRank()
            ->findByAdminOnly(false);

        return $categories->toKeyValue('id', 'name');
    }

    public function getDays()
    {
        $installmentPeriod = $this->borrower->getCountry()->getInstallmentPeriod();
        $dayCount = ($installmentPeriod == Loan::WEEKLY_INSTALLMENT) ? 7 : 31;
        
        $array = range(1, $dayCount);

        return array_combine($array, $array);
    }

    public function getDefaultData()
    {
        return \Session::get('loan_data');
    }
}