<?php

namespace Zidisha\Upload;

use Propel\Runtime\Connection\ConnectionInterface;
use Zidisha\Upload\Base\Upload as BaseUpload;

class Upload extends BaseUpload
{
    public function isImage()
    {
        return $this->getType() == 'image' ? true : false;
    }

    public function getUrl()
    {
        return asset('uploads/'.$this->getFilename());
    }

    public function getPath()
    {
        return public_path().'/uploads/';
    }

    public function postDelete(ConnectionInterface $con = null)
    {
        $filename = $this->getFilename();
        $file = new \Illuminate\Filesystem\Filesystem();
        $file->delete($this->getPath().$filename);
        return true;
    }
}
