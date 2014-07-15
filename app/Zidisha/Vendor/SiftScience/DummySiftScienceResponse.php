<?php

namespace Zidisha\Vendor\SiftScience;

class DummySiftScienceResponse
{
    protected $body;

    public function isOk()
    {
        return true;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }
}
