<?php

namespace Zidisha\Auth\Form;


use Zidisha\Form\AbstractForm;

class Join extends AbstractForm
{
    
    protected $facebookJoin = false;

    /**
     * @param boolean $facebookJoin
     */
    public function setFacebookJoin($facebookJoin)
    {
        $this->facebookJoin = $facebookJoin;
    }

    /**
     * @return boolean
     */
    public function isFacebookJoin()
    {
        return $this->facebookJoin;
    }

    public function getRules($data)
    {
        $rules = [
            'username' => 'required|max:20',
        ];
        
        if (!$this->isFacebookJoin()) {
            $rules += [
                'email'    => 'required|email',
                'password' => 'required|confirmed'
            ];
        } else {
            $rules['aboutMe'] = '';
        }
        
        return $rules;
    }

}
