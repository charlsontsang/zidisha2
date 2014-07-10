<?php

use Zidisha\Translation\TranslationLabelQuery;
use Zidisha\Translation\TranslationService;
use Zidisha\Utility\Utility;

class TranslationController extends BaseController
{
    /**
     * @var Zidisha\Translation\TranslationService
     */
    private $translationService;

    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }


    public function getTranslations($filename, $languageCode)
    {
        $languageCodes = [];
        $languages = \Zidisha\Country\CountryQuery::create()
            ->filterByBorrowerCountry(true)
            ->find();

        foreach ($languages as $language) {
            if (!in_array($language->getLanguageCode(), $languageCodes)) {
                $languageCodes[] = $language->getLanguageCode();
            }
        }

        if (!in_array($languageCode, $languageCodes)) {
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

        $translatedState = [];
        foreach ($translationLabels as $translationLabel) {
            $translatedState[str_replace('.', '_', $translationLabel->getKey())] = $translationLabel;
        }

        $keyToValue = $translationLabels->toKeyValue('key', 'value');
        $defaultValues = Utility::toInputNames($keyToValue);

        if (!$defaultValues) {
            \App::abort(404, 'The given file not found.');
        }

        return View::make('translation.form', compact('file', 'fileLabels', 'defaultValues', 'filename', 'translatedState'));
    }

    public function postTranslations($filename, $languageCode)
    {
        $languageCodes = [];
        $languages = \Zidisha\Country\CountryQuery::create()
            ->filterByBorrowerCountry(true)
            ->find();

        foreach ($languages as $language) {
            if (!in_array($language->getLanguageCode(), $languageCodes)) {
                $languageCodes[] = $language->getLanguageCode();
            }
        }

        if (!in_array($languageCode, $languageCodes)) {
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
        $borrowerLanguages = [];
        $languageCodes = [];
        $languages = \Zidisha\Country\CountryQuery::create()
            ->filterByBorrowerCountry(true)
            ->find();

        foreach ($languages as $language) {
            $borrowerLanguages[$language->getLanguageCode()] = $language->getLanguage()->getName();

            if (!in_array($language->getLanguageCode(), $languageCodes)) {
                $languageCodes[] = $language->getLanguageCode();
            }
        }

        if (Input::has('languageCode') && !in_array(Input::get('languageCode'), $languageCodes)) {
            \App::abort(404, 'No Language given');
        }

        $languageCode = Input::get('languageCode', $languageCodes[0]);

        $files = TranslationLabelQuery::create()
            ->filterByLanguageCode($languageCode)
            ->getTotals();

        return View::make('translation.index', compact('languageCodes', 'languageCode', 'borrowerLanguages', 'files'));
    }
}
