<?php

namespace Zidisha\Form;

use Illuminate\Http\Request;
use Illuminate\Support\Contracts\MessageProviderInterface;

abstract class AbstractForm implements MessageProviderInterface
{

    /**
     * @var \Illuminate\Validation\Validator
     */
    protected $validator;
    
    /**
     * @var array
     */
    protected $data;
    
    public function handleRequest(Request $request)
    {
        $data = $this->getDataFromRequest($request);
        $data = $this->sanitize($data);
        $rules = $this->getRules($data);
        
        $this->validate($data, $rules);
        
        $this->data = $this->safeData($rules, $data);
    }

    public function getDataFromRequest(Request $request)
    {
        return $request->all();
    }

    public abstract function getRules($data);
        
    public function getData()
    {
        return $this->data ?: $this->getDefaultData();
    }
    
    public function getDefaultData()
    {
        return array();
    }

    public function getMessageBag()
    {
        return $this->validator->getMessageBag();
    }

    public function isValid ()
    {
        return $this->validator ? $this->validator->passes() : true;
    }
    
    protected function sanitize($data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->sanitize($value);
            } else {
                $data[$key] = trim($value);
            }
        }
        
        return $data;
    }
    
    protected function validate($data, $rules) {
        $this->validator = \Validator::make($data, $rules);
    }

    protected function safeData($rules, $data)
    {
        $safeData = array();
        foreach ($rules as $key => $rule) {
            $safeData[$key] = array_get($data, $key);
        }
        
        return $safeData;
    }
    
} 