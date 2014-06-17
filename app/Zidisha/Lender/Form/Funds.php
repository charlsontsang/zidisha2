<?php
/**
 * Created by PhpStorm.
 * User: Singularity Guy
 * Date: 6/17/14
 * Time: 12:50 PM
 */

namespace Zidisha\Lender\Form;


use Zidisha\Form\AbstractForm;

class Funds extends AbstractForm{

    public function getRules($data)
    {
        return [
            'creditAmount'  => 'required|num',
            'feeAmount' => 'required|num',
            'donationAmount'  => 'num',
            'totalAmount' => 'required|num'
        ];
    }

    public function getDefaultData()
    {

        return [
            'creditAmount'  => 100.00,
            'feeAmount' => 3.5,
            'donationAmount'  => 15,
            'totalAmount'     => 118.50,
            ];
    }
}