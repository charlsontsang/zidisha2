<?php

namespace Zidisha\Http;


use Zidisha\Form\AbstractForm;

class RedirectResponse extends \Illuminate\Http\RedirectResponse {

    public function withForm(AbstractForm $form) {
        $this->withInput($form->getData())->withErrors($form);
        return $this;
    }
    
} 