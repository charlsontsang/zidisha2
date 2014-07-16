<?php

namespace Zidisha\Vendor\SiftScience;

class DummySiftScienceResponse
{
    public  $body;

    public function isOk()
    {
        return true;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }
}
