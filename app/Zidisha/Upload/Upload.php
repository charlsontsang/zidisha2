<?php

namespace Zidisha\Upload;

use Config;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
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
    protected $isProfileUpload = false;

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
            $width = Config::get("image.formats.$format.width");
            $height = Config::get("image.formats.$format.height");
            return asset('uploads/cache/' . $width . 'X' . $height . '/' . $this->getUserId() . '/' . $this->getFilename());
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
        $formats = array_keys(Config::get('image.formats'));
        foreach ($formats as $format) {
            $cachePath = $this->getCachePath($format);
            if ($file->exists($cachePath)) {
                $file->delete($cachePath);
            }
        }
        return true;
    }

    public static function createFromFile(UploadedFile $file, $isProfileUpload = false)
    {
        $upload = new Upload();
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();

        $fileType = in_array($extension, static::$image_types) ? 'image' : 'document';

        if ($isProfileUpload) {
            $upload->isProfileUpload = $isProfileUpload;
            $file = \Image::make($file);
            $upload->setFileName('profile.jpg');
            $extension = 'jpg';
            $mimeType = 'image/jpeg';
        } else {
            $fileName = substr(Str::slug($file->getClientOriginalName()), 0, -(strlen($extension)));
            $upload->setFileName('-' . substr($fileName, 0, 32). '.' . $extension);
        }

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

    public function postInsert(ConnectionInterface $con = null)
    {
        if ($this->isProfileUpload) {
            $file = new Filesystem();
            if ($file->exists($this->getPath())) {
                $this->postDelete();
            }
            if (!$file->exists($this->getBasePath())) {
                $file->makeDirectory($this->getBasePath(), 0755 , true);
            }
            $this->file = $this->file->save($this->getBasePath() . $this->getFilename());
        } else {
            $this->setFileName( $this->getId() . $this->getFileName());
            $this->save();
            $this->file = $this->file->move($this->getBasePath(), $this->getFilename());
        }
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
        return $this->getCacheBasePath($format) . $this->getFilename();
    }

    protected function getCacheBasePath($format)
    {
        if (!Config::get('image.formats.' . $format)) {
            throw new ConfigurationNotFoundException();
        }
        $width = Config::get("image.formats.$format.width");
        $height = Config::get("image.formats.$format.height");
        return public_path() . '/uploads/cache/' . $width . 'X' . $height . '/' . $this->getUserId() . '/';
    }
}
