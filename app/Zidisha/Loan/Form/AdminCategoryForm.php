<?php

namespace Zidisha\Loan\Form;


use Zidisha\Form\AbstractForm;
use Zidisha\Loan\CategoryQuery;

class AdminCategoryForm extends AbstractForm
{

    public function getRules($data)
    {
        return [
            'category' => 'required',
            'secondaryCategory' => 'required',
        ];
    }

    public function getCategories()
    {
        $categories = CategoryQuery::create()
            ->orderBySortableRank()
            ->findByAdminOnly(false);

        return $categories->toKeyValue('id', 'name');
    }


    public function getSecondaryCategories()
    {
        $categories = CategoryQuery::create()
            ->orderBySortableRank()
            ->find();

        return $categories->toKeyValue('id', 'name');
    }


} 