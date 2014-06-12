<?php
/**
 * Created by PhpStorm.
 * User: Singularity Guy
 * Date: 6/12/14
 * Time: 11:13 AM
 */

namespace Zidisha\Borrower\Form\Loan;

use Zidisha\Form\AbstractForm;
use Zidisha\Loan\CategoryQuery;

class Application extends AbstractForm
{

    public function getRules($data)
    {
        return [
            'title' => 'required|min:10',
            'proposal' => 'required|min:200',
            'categoryId' => 'required',
            'amount' => 'required|numeric',
            'installmentAmount' => 'required|numeric',
            'installmentDay' => 'required',
        ];
    }

    public function getCategories()
    {
        $loanCategories = CategoryQuery::create()
            ->orderBySortableRank()
            ->findByAdminOnly(false);


        $array = [];
        foreach ($loanCategories as $loanCategory) {
            $array[] = $loanCategory->getName();
        }

        return $array;
    }

    public function getDays()
    {
        $array = range(1, 30);

        return array_combine($array, $array);
    }

    public function getDefaultData()
    {
        return \Session::get('loan_data');
    }
}