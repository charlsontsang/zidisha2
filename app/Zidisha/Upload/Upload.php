<?php

namespace Zidisha\Upload;

use Zidisha\Upload\Base\Upload as BaseUpload;

class Upload extends BaseUpload
{
    public function isImage()
    {
        return $this->getType() == 'image' ? true : false;
    }

}
