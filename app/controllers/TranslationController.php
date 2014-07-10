<?php

use Zidisha\Country\LanguageQuery;
use Zidisha\Translation\TranslationLabelQuery;
use Zidisha\Translation\TranslationService;
use Zidisha\Utility\Utility;

class TranslationController extends BaseController
{
    /**
     * @var Zidisha\Translation\TranslationService
     */
    private $translationService;

    private $languageCodes;

    private $borrowerLanguages;

    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;

        $this->borrowerLanguages = LanguageQuery::create()
            ->filterBorrowerLanguages()
            ->find();

        $this->languageCodes = $this->borrowerLanguages->toKeyValue('LanguageCode', 'LanguageCode');
    }

    public function getTranslations($filename, $languageCode)
    {
        if (!in_array($languageCode, $this->languageCodes)) {
            \App::abort(404, 'Given language not found.');
        }

        $fileLabels = $this->translationService->getFileLabels($filename);

        if (!$fileLabels) {
            \App::abort(404, 'The given file not found.');
        }

        $translationLabels = TranslationLabelQuery::create()
            ->filterByFilename($filename)
            ->filterByLanguageCode($languageCode)
            ->find();

        $keyToTranslationLabel = [];
        foreach ($translationLabels as $translationLabel) {
            $keyToTranslationLabel[$translationLabel->getKey()] = $translationLabel;
        }

        $keyToValue = $translationLabels->toKeyValue('key', 'value');
        $defaultValues = Utility::toInputNames($keyToValue);
        $keyToTranslationLabel = Utility::toInputNames($keyToTranslationLabel);

        if (!$defaultValues) {
            \App::abort(404, 'The given file not found.');
        }

        return View::make('translation.form', compact('file', 'fileLabels', 'defaultValues', 'filename', 'keyToTranslationLabel'));
    }

    public function postTranslations($filename, $languageCode)
    {
        if (!in_array($languageCode, $this->languageCodes)) {
            \App::abort(404, 'Given language not found.');
        }

        $fileExists = $this->translationService->fileExists($filename);

        if (!$fileExists) {
            \App::abort(404, 'Given file not found.');
        }

        $data = Utility::fromInputNames(Input::all());

        $this->translationService->updateTranslations($filename, $languageCode, $data);

        \Flash::success('Your updates have been saved.');
        return Redirect::action('TranslationController@getTranslations', compact('filename', 'languageCode'));
    }

    public function getTranslation()
    {
        if (Input::has('languageCode') && !in_array(Input::get('languageCode'), $this->languageCodes)) {
            \App::abort(404, 'No Language given');
        }

        $languageCode = Input::get('languageCode', reset($this->languageCodes));

        $files = TranslationLabelQuery::create()
            ->filterByLanguageCode(strtolower($languageCode))
            ->getTotals();

        return View::make('translation.index', ['languageCode' => $languageCode, 'borrowerLanguages' => $this->borrowerLanguages, 'files' => $files]);
    }
}
