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
        // TODO remove
        $this->translationService->loadLanguageFilesToDatabase();

        //TODO : get locale array from admin
        $languageCodes = ['fr', 'in'];

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
        //TODO : get locale array from admin
        $languageCodes = ['fr', 'in'];

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
        //Todo: get language files from admin
        $languageCodes = ['fr', 'in'];

        if (Input::has('languageCode') && !in_array(Input::get('languageCode'), $languageCodes)) {
            \App::abort(404, 'No Language given');
        }

        $languageCode = Input::get('languageCode', $languageCodes[0]);

        $files = TranslationLabelQuery::create()
            ->filterByLanguageCode($languageCode)
            ->getTotals();

        return View::make('translation.index', compact('languageCodes', 'languageCode', 'files'));
    }
}
