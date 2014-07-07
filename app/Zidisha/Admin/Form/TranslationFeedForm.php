<?php

namespace Zidisha\Admin\Form;


use Zidisha\Country\LanguageQuery;
use Zidisha\Form\AbstractForm;

class TranslationFeedForm extends AbstractForm
{

    public function getRules($data)
    {
        return [
            'language' => '',

        ];
    }

    public function getLanguages()
    {

        $list = [];
        $list[0] = null;

        $languages =  LanguageQuery::create()
            ->filterByActive(true)
            ->find();

        foreach ($languages as $language) {
            $list[$language->getLanguageCode()] = $language->getName();
        }

        return $list;
    }

}