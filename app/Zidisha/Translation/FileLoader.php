<?php
namespace Zidisha\Translation;

use Illuminate\Filesystem\Filesystem;

class FileLoader {

    protected $files;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function loadAllFiles()
    {
        $this->files = $this->filesystem->allFiles(app_path().'/lang/en/borrower/');
    }

    public function showAllFiles()
    {
        return $this->files;
    }
}
