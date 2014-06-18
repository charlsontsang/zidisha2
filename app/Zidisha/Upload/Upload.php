<?php

namespace Zidisha\Upload;

use Config;
use Illuminate\Filesystem\Filesystem;
use Propel\Runtime\Connection\ConnectionInterface;
use Symfony\Component\HttpFoundation\File\File;
use Zidisha\Upload\Base\Upload as BaseUpload;
use Zidisha\Upload\Exceptions\ConfigurationNotFoundException;

class Upload extends BaseUpload
{
    protected static $image_types = ['jpeg', 'png', 'jpg'];

    protected $file = null;

    public function isImage()
    {
        return $this->getType() == 'image' ? true : false;
    }

    public function getImageUrl()
    {
        $file = new Filesystem();
        if ($file->exists(public_path() . '/uploads/cache/' . $this->getFilename())) {
            return asset('uploads/cache/' . $this->getFilename());
        } else {
            return route('image:resize', ['upload_id' => $this->getId(), 'format' => 'small_profile_pic']);
        }
    }

    public function getFileUrl()
    {
        return asset('uploads/' . $this->getFilename());
    }

    public function getPath()
    {
        return $this->getBasePath() . $this->getFilename();
    }

    protected function getBasePath()
    {
        return public_path() . '/uploads/';
    }

    public function postDelete(ConnectionInterface $con = null)
    {
        $file = new Filesystem();
        $file->delete($this->getPath());
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
        $this->file = $this->file->move($this->getBasePath(), $this->getFilename());
    }

    public function getFile()
    {
        if ($this->file == null) {
            $this->file = new File($this->getPath());
        }

        return $this->file;
    }

    public function resize($format)
    {
        $file = new Filesystem();
        $cachePath = $this->getCachePath($format);
        if (!$file->exists($cachePath)) {
            $img = \Image::make($this->getPath());

            if (!Config::get('image.formats.' . $format)) {
                throw new ConfigurationNotFoundException();
            }

            $img->resize(
                Config::get("image.formats.$format.width"),
                Config::get("image.formats.$format.height"),
                function ($constraint) {
                    $constraint->aspectRatio();
                }
            );

            $file->makeDirectory($this->getCacheBasePath($format), 0755, true);
            $img->save($cachePath);
        }

        return new File($cachePath);
    }

    protected function getCachePath($format)
    {
        if (!Config::get('image.formats.' . $format)) {
            throw new ConfigurationNotFoundException();
        }

        return public_path() . '/uploads/cache/' . $format . '/' . $this->getFilename();
    }

    protected function getCacheBasePath($format)
    {
        if (!Config::get('image.formats.' . $format)) {
            throw new ConfigurationNotFoundException();
        }
        return public_path() . '/uploads/cache/' . $format . '/';
    }
}
