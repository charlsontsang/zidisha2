<?php

namespace Zidisha\Translation;


use Illuminate\Filesystem\Filesystem;
use Propel\Runtime\ActiveQuery\Criteria;
use Zidisha\Country\LanguageQuery;
use Zidisha\Translation\TranslationLabelQuery;
use Zidisha\Vendor\PropelDB;

class TranslationService
{

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function loadLanguageFilesToDatabase()
    {
        $languageCodes = LanguageQuery::create()
            ->filterBorrowerLanguages()
            ->filterByLanguageCode('EN', Criteria::NOT_EQUAL)
            ->find()
            ->toKeyValue('LanguageCode', 'LanguageCode');

        array_unshift($languageCodes, "en");

        foreach (['borrower', 'common'] as $folder) {
            $files = $this->filesystem->allFiles(app_path() . "/lang/en/$folder/");

            foreach ($files as $file) {
                $filename = str_replace(
                    '.php',
                    '',
                    $file->getRelativePathname()
                );
                $fileLabels = $this->getFlattenedFileLabels($folder, $filename);

                PropelDB::transaction(
                    function ($con) use ($folder, $filename, $fileLabels, $languageCodes) {
                        $updatedNames = [];

                        foreach ($languageCodes as $languageCode) {
                            $_labels = TranslationLabelQuery::create()
                                ->filterByFolder($folder)
                                ->filterByFilename($filename)
                                ->filterByLanguageCode($languageCode)
                                ->find();

                            $labels = [];
                            foreach ($_labels as $label) {
                                $labels[$label->getName()] = $label;
                            }

                            foreach ($fileLabels as $name => $value) {
                                if (!isset($labels[$name])) {
                                    $translationLabel = new TranslationLabel();
                                    $translationLabel
                                        ->setFolder($folder)
                                        ->setFileName($filename)
                                        ->setName($name)
                                        ->setLanguageCode($languageCode);

                                    if ($languageCode == 'en') {
                                        $translationLabel->setValue($value);
                                    }

                                    $translationLabel->save($con);
                                    continue;
                                }

                                /** @var TranslationLabel $translationLabel */
                                $translationLabel = $labels[$name];

                                if ($languageCode == 'en') {
                                    if ($translationLabel->getValue() != $fileLabels[$name]) {
                                        $updatedNames[$name] = $name;
                                    }
                                    $translationLabel->setValue($value);
                                } elseif (isset($updatedNames[$translationLabel->getName()])) {
                                    $translationLabel->setUpdated(true);
                                }
                                $translationLabel->save($con);
                            }

                            foreach ($labels as $label) {
                                if (!isset($fileLabels[$label->getName()])) {
                                    $deprecatedLabel = TranslationLabelQuery::create()
                                        ->filterByName($label->getName())
                                        ->filterByFolder($folder)
                                        ->filterByFilename($filename)
                                        ->filterByLanguageCode($languageCode)
                                        ->findOne();

                                    $deprecatedLabel->delete();
                                }
                            }
                        }
                    }
                );
            }
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

    public function getFileLabels($folder, $filename)
    {
        return $this->getRequire(app_path() . "/lang/en/$folder/$filename.php");
    }

    public function getFlattenedFileLabels($folder, $filename)
    {
        return array_dot($this->getFileLabels($folder, $filename));
    }

    protected function getAssociativeLabels($labels)
    {
        $defaultValues = [];

        foreach ($labels as $label) {
            $defaultValues[str_replace('.', '_', $label->getName())] = $label->getValue();
        }

        return $defaultValues;
    }

    public function updateTranslations($folder, $filename, $languageCode, $data)
    {
        $translationLabels = TranslationLabelQuery::create()
            ->filterByFolder($folder)
            ->filterByFilename($filename)
            ->filterByLanguageCode($languageCode)
            ->find();

        PropelDB::transaction(
            function ($con) use ($translationLabels, $data) {
                foreach ($translationLabels as $translationLabel) {
                    if (!isset($data[$translationLabel->getName()])) {
                        continue;
                    }

                    $value = $data[$translationLabel->getName()];

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

    public function fileExists($folder, $filename)
    {
        return $this->filesystem->exists(app_path() . "/lang/en/$folder/$filename.php");
    }
}
