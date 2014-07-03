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
        $array = range(1, 31);

        return array_combine($array, $array);
    }

    public function getDefaultData()
    {
        return \Session::get('loan_data');
    }
}