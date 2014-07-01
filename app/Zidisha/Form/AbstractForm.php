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
            } elseif (is_scalar($value)) {
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
        $nestedData = [];
        
        foreach ($data as $k => $v) {
            $keys = explode('_', $k);
            $count = count($keys);
            $parent = &$nestedData;
            foreach ($keys as $key) {
                if ($count == 1) {
                    $parent[$key] = $v;
                } else {
                    if (!isset($parent[$key])) {
                        $parent[$key] = [];
                    }
                    $parent = &$parent[$key];
                }
                $count -= 1;
            }
        }
        
        return $nestedData;
    }
    
} 