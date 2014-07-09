<?php

namespace Zidisha\Lender\Form;


use Illuminate\Http\Request;
use Zidisha\Form\AbstractForm;

class CreateGroupForm extends  AbstractForm
{

    public function getRules($data)
    {
        return [
            'name'                  => 'required|max:20|unique:lender_groups,name',
            'website'               => 'unique:lender_groups,website',
            'about'                 => 'required|min:100',
            'groupProfilePictureId' => 'image|max:2048'
        ];
    }

    public function getDataFromRequest(Request $request) {
        $data = parent::getDataFromRequest($request);
        $data['groupProfilePictureId'] = $request->file('groupProfilePictureId');

        return $data;
    }
}