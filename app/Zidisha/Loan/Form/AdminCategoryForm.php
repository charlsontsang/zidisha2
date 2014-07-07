<?php

namespace Zidisha\Loan\Form;


use Zidisha\Form\AbstractForm;
use Zidisha\Loan\CategoryQuery;

class AdminCategoryForm extends AbstractForm
{

    public function getRules($data)
    {
        return [
            'category' => 'required|in:'. implode(',', array_keys($this->getCategories())),
            'secondaryCategory' => 'required|in:'. implode(',', array_keys($this->getSecondaryCategories())),
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