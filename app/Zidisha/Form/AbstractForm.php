<?php

namespace Zidisha\Form;

use Illuminate\Http\Request;
use Illuminate\Support\Contracts\MessageProviderInterface;
use Zidisha\Utility\Utility;

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
    
    protected $validatorClass = 'Zidisha\Form\ZidishaValidator';

    public function handleData(array $data)
    {
        $rules = $this->getRules($data);

        $this->validate($data, $rules);

        $this->data = $this->safeData($rules, $data);
    }
    
    public function handleRequest(Request $request)
    {
        $data = $this->getDataFromRequest($request);
        $data = $this->sanitize($data);
        
        $this->handleData($data);
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
            } elseif (is_scalar($value)) {
                $data[$key] = trim($value);
            }
        }

        return $data;
    }
    
    protected function validate($data, $rules) {
        \Validator::resolver(
            function ($translator, $data, $rules, $messages, $parameters) {
                $class = $this->validatorClass;
                /** @var ZidishaValidator $validator */
                $validator = new $class($translator, $data, $rules, $messages, $parameters);
                $validator->setForm($this);
                
                return $validator;
            }
        );
        
        $this->validator = \Validator::make($data, $rules);
    }

    protected function safeData($rules, $data)
    {
        $safeData = array();
        foreach ($rules as $key => $rule) {
            $value = array_get($data, $key);
            if (!is_object($value)) {
                $safeData[$key] = $value;
            }
        }
        
        return $safeData;
    }

    public function getNestedData()
    {
        $data = $this->getData();

        return Utility::nestedArray($data);
    }
} 