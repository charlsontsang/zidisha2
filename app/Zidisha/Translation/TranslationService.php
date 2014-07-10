<?php

namespace Zidisha\Translation;


use Illuminate\Filesystem\Filesystem;
use Zidisha\Translation\TranslationLabelQuery;
use Zidisha\Vendor\PropelDB;

class TranslationService
{

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $filesystem;

    //Todo: get languages from admin
    protected $languageCodes = ['en', 'fr', 'in'];

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function loadLanguageFilesToDatabase()
    {
        $files = $this->filesystem->allFiles(app_path() . '/lang/en/borrower/');

        foreach ($files as $file) {
            $filename = str_replace('.php', '', $file->getRelativePathname());
            $fileLabels = $this->getFlattenedFileLabels($filename);

            PropelDB::transaction(function($con) use($filename, $fileLabels) {
                $updatedKeys = [];

                foreach ($this->languageCodes as $languageCode) {
                    $_labels = TranslationLabelQuery::create()
                        ->filterByFilename($filename)
                        ->filterByLanguageCode($languageCode)
                        ->find();

                    $labels = [];
                    foreach ($_labels as $label) {
                        $labels[$label->getKey()] = $label;
                    }

                    foreach ($fileLabels as $key => $value) {
                        if (!isset($labels[$key])) {
                            $translationLabel = new TranslationLabel();
                            $translationLabel
                                ->setFileName($filename)
                                ->setKey($key)
                                ->setLanguageCode($languageCode);

                            if ($languageCode == 'en') {
                                $translationLabel->setValue($value);
                            }

                            $translationLabel->save($con);
                            continue;
                        }

                        /** @var TranslationLabel $translationLabel */
                        $translationLabel = $labels[$key];

                        if ($languageCode == 'en') {
                            if ($translationLabel->getValue() != $fileLabels[$key]) {
                                $updatedKeys[$key] = $key;
                            }
                            $translationLabel->setValue($value);
                        } elseif(isset($updatedKeys[$translationLabel->getKey()])) {
                            $translationLabel->setUpdated(true);
                        }
                        $translationLabel->save($con);
                    }

                    foreach ($labels as $label) {
                        if (!isset($fileLabels[$label->getKey()])){
                            $deprecatedLabel = TranslationLabelQuery::create()
                                ->filterByKey($label->getKey())
                                ->filterByFilename($filename)
                                ->filterByLanguageCode($languageCode)
                                ->findOne();

                            $deprecatedLabel->delete();
                        }
                    }
                }
            });
        }
    }

    public function getAllFiles($languageCode)
    {
        $files = $this->filesystem->allFiles(app_path() . '/lang/' . $languageCode . '/borrower/');

        $filePaths = [];

        foreach ($files as $file) {
            $filePaths[] = $file->getRelativePathname();
        }

        return $filePaths;
    }

    public function getRequire($fullPath)
    {
        if (!$this->filesystem->exists($fullPath)) {
            return null;
        }

        return $this->filesystem->getRequire($fullPath);
    }

    public function getFileLabels($filename)
    {
        return $this->getRequire(app_path() . '/lang/en/borrower/' . $filename . '.php');
    }

    public function getFlattenedFileLabels($filename)
    {
        $fileLabels = array_dot($this->getFileLabels($filename));

        $result = [];
        foreach ($fileLabels as $key => $value) {
            $result[$filename . '.' . $key] = $value;
        }

        return $result;
    }

    protected function getAssociativeLabels($labels)
    {
        $defaultValues = [];

        foreach ($labels as $label) {
            $defaultValues[str_replace('.', '_', $label->getKey())] = $label->getValue();
        }

        return $defaultValues;
    }

    public function updateTranslations($filename, $languageCode, $data)
    {
        $translationLabels = TranslationLabelQuery::create()
            ->filterByFilename($filename)
            ->filterByLanguageCode($languageCode)
            ->find();

        PropelDB::transaction(
            function ($con) use ($translationLabels, $data) {
                foreach ($translationLabels as $translationLabel) {
                    if (!isset($data[$translationLabel->getKey()])) {
                        continue;
                    }

                    $value = $data[$translationLabel->getKey()];

                    if ($value) {
                        $translationLabel->setTranslated(true);
                    }

                    $translationLabel
                        ->setValue($value)
                        ->setUpdated(false);
                    $translationLabel->save($con);
                }
            }
        );
    }

    public function fileExists($filename)
    {
        return $this->filesystem->exists(app_path() . '/lang/en/borrower/' . $filename . '.php');
    }
}
