<?php

namespace Zidisha\Upload;

use Propel\Runtime\Connection\ConnectionInterface;
use Symfony\Component\HttpFoundation\File\File;
use Zidisha\Upload\Base\Upload as BaseUpload;

class Upload extends BaseUpload
{
    protected static $image_types = ['jpeg', 'png', 'jpg'];

    protected $file = null;

    public function isImage()
    {
        return $this->getType() == 'image' ? true : false;
    }

    public function getUrl()
    {
        return asset('uploads/' . $this->getFilename());
    }

    public function getPath()
    {
        return public_path() . '/uploads/';
    }

    public function postDelete(ConnectionInterface $con = null)
    {
        $filename = $this->getFilename();
        $file = new \Illuminate\Filesystem\Filesystem();
        $file->delete($this->getPath() . $filename);
        return true;
    }

    public static function createFromFile(File $file)
    {
        $upload = new Upload();

        $filename = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();

        $fileType = in_array($extension, static::$image_types) ? 'image' : 'document';

        $upload->setFilename($filename);
        $upload->setExtension($extension);
        $upload->setType($fileType);
        $upload->setMimeType($mimeType);

        $upload->file = $file;

        return $upload;
    }

    public function preSave(ConnectionInterface $con = null)
    {
        return $this->file ? true : false;
    }

    public function postSave(ConnectionInterface $con = null)
    {
        $this->file = $this->file->move($this->getPath(), $this->getFilename());
    }

    public function getFile()
    {
        if ($this->file == null) {
            $this->file = new File($this->getPath());
        }

        return $this->file;
    }
}
