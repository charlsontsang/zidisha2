<?php

class LenderController extends BaseController
{
    public function getPublicProfile(){
        $username = Auth::user()->getUserName();
        $lender = LenderQuery::create()
            ->useUserQuery()
            ->filterByUsername($username)
            ->endUse()
            ->findOne();

        return View::make(
            'lender.public-profile',
            compact('lender')
        );
    }
}
