<?php
/**
 * Created by PhpStorm.
 * User: boomerang
 * Date: 6/12/14
 * Time: 12:49 PM
 */

namespace Zidisha\Borrower\Form;


use Zidisha\Form\AbstractForm;

class Join extends AbstractForm{

    public function getRules($data)
    {
        return [
          'first_name' => 'required',
          'last_name' => 'required',
          'email' => 'required|email'
        ];
    }
}