<?php

class LenderController extends BaseController
{
    public function getPublicProfile()
    {
        $lender = LenderQuery::create()
            ->useUserQuery()
                ->filterById(Auth::user()->getId())
            ->endUse()
            ->findOne();

        return View::make(
            'lender.public-profile',
            compact('lender')
        );
    }

    public function getEditProfile()
    {
        $lender = LenderQuery::create()
            ->useUserQuery()
                ->filterById(Auth::user()->getId())
            ->endUse()
            ->findOne();

        return View::make(
            'lender.edit-profile',
            compact('lender')
        );
    }

    public function postEditProfile()
    {
        $data = Input::all();

        $rules = array(
            'username' => 'required|alpha_num',
            'firstName' => 'required|alpha_num',
            'lastName' => 'required|alpha_num',
            'email' => 'required|email',
            'password' => 'confirmed'
        );

        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $lender = LenderQuery::create()
                ->useUserQuery()
                    ->filterById(Auth::user()->getId())
                ->endUse()
                ->findOne();

            $lender->setFirstName($data['firstName']);
            $lender->setLastName($data['lastName']);
            $lender->getUser()->setEmail($data['email']);
            $lender->getUser()->setUsername($data['username']);
            $lender->setAboutMe($data['aboutMe']);

            if (!empty($data['password'])) {
                $lender->getUser()->setPassword($data['password']);
            }

            $lender->save();

            return Redirect::route('lender:public-profile');
        }

        return Redirect::route('lender:edit-profile')->withInput($data)->withErrors($validator);
    }
}
