<?php

namespace Zidisha\Upload;

use Config;
use Illuminate\Filesystem\Filesystem;
use Propel\Runtime\Connection\ConnectionInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zidisha\Upload\Base\Upload as BaseUpload;
use Zidisha\Upload\Exceptions\ConfigurationNotFoundException;
use Zidisha\Upload\Exceptions\FileTypeMisMatchException;

class Upload extends BaseUpload
{
    protected static $image_types = ['jpeg', 'png', 'jpg'];

    protected $file = null;

    public function isImage()
    {
        return $this->getType() == 'image' ? true : false;
    }

    public function getImageUrl($format)
    {
        if (!Config::get('image.formats.' . $format)) {
            throw new ConfigurationNotFoundException();
        }

        if (!$this->isImage()) {
            throw new FileTypeMisMatchException();
        }

        $file = new Filesystem();

        if ($file->exists($this->getCachePath($format))) {
            return asset('uploads/cache/' . $format . '/' . $this->getUserId() . '/' . $this->getFilename());
        } else {
            return route('image:resize', ['upload_id' => $this->getId(), 'format' => $format]);
        }
    }

    public function getFileUrl()
    {
        return asset('uploads/' .  $this->getUserId() . '/' . $this->getFilename());
    }

    public function getPath()
    {
        return $this->getBasePath() . $this->getFilename();
    }

    protected function getBasePath()
    {
        return public_path() . '/uploads/' . $this->getUserId() . '/';
    }

    public function postDelete(ConnectionInterface $con = null)
    {
        $file = new Filesystem();
        $file->delete($this->getPath());
        return true;
    }

    public static function createFromFile(UploadedFile $file)
    {
        $upload = new Upload();

        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();

        $fileType = in_array($extension, static::$image_types) ? 'image' : 'document';

        //TODO convert to jpg for profile pics
//        $picture = new File(imagejpeg(imagecreatefromstring(file_get_contents($file))));

        $upload->setExtension($extension)
            ->setType($fileType)
            ->setMimeType($mimeType);

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
                    $constraint->upsize();
                }
            );

            if(!$file->exists($this->getCacheBasePath($format))){
                $file->makeDirectory($this->getCacheBasePath($format), 0755, true);
            }
            $img->save($cachePath);
        }

        return new File($cachePath);
    }

    protected function getCachePath($format)
    {
        if (!Config::get('image.formats.' . $format)) {
            throw new ConfigurationNotFoundException();
        }

        return public_path() . '/uploads/cache/' . $format . '/' . $this->getUserId() . '/' . $this->getFilename();
    }

    protected function getCacheBasePath($format)
    {
        if (!Config::get('image.formats.' . $format)) {
            throw new ConfigurationNotFoundException();
        }
        return public_path() . '/uploads/cache/' . $format . '/' . $this->getUserId() . '/';
    }
}
