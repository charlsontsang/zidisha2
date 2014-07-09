<?php

namespace Zidisha\Lender\Form;


use Illuminate\Http\Request;
use Zidisha\Form\AbstractForm;

class CreateGroupForm extends  AbstractForm
{

    public function getRules($data)
    {
        return [
            'name'                  => 'required|unique:lending_groups,name',
            'website'               => 'unique:lending_groups,website',
            'about'                 => 'required',
            'groupProfilePictureId' => 'image|max:2048'
        ];
    }

    public function getDataFromRequest(Request $request) {
        $data = parent::getDataFromRequest($request);
        $data['groupProfilePictureId'] = $request->file('groupProfilePictureId');

        return $data;
    }
}