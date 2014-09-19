<?php

use Propel\Runtime\ActiveQuery\Criteria;
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
    
    private $folders = ['borrower', 'common'];

    private $borrowerLanguages;

    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;

        $this->borrowerLanguages = LanguageQuery::create()
            ->filterBorrowerLanguages()
            ->filterByLanguageCode('EN', Criteria::NOT_EQUAL)
            ->find();

        $this->languageCodes = $this->borrowerLanguages->toKeyValue('LanguageCode', 'LanguageCode');
    }

    public function getTranslations($folder, $filename, $languageCode)
    {
        if (!in_array($languageCode, $this->languageCodes)) {
            \App::abort(404, 'Given language not found.');
        }

        if (!in_array($folder, $this->folders)) {
            \App::abort(404, 'Given folder not found.');
        }

        $fileLabels = $this->translationService->getFileLabels($folder, $filename);

        if (!$fileLabels) {
            \App::abort(404, 'The given file not found.');
        }

        $translationLabels = TranslationLabelQuery::create()
            ->filterByFolder($folder)
            ->filterByFilename($filename)
            ->filterByLanguageCode($languageCode)
            ->find();

        $nameToTranslationLabel = [];
        foreach ($translationLabels as $translationLabel) {
            $nameToTranslationLabel[$translationLabel->getName()] = $translationLabel;
        }

        $nameToValue = $translationLabels->toKeyValue('name', 'value');
        $defaultValues = Utility::toInputNames($nameToValue);
        $nameToTranslationLabel = Utility::toInputNames($nameToTranslationLabel);

        if (!$defaultValues) {
            \App::abort(404, 'The given file not found.');
        }

        return View::make('translation.form', compact(
            'folder', 'file', 'fileLabels', 'defaultValues', 'filename', 'nameToTranslationLabel', 'languageCode'
        ));
    }

    public function postTranslations($folder, $filename, $languageCode)
    {
        if (!in_array($languageCode, $this->languageCodes)) {
            \App::abort(404, 'Given language not found.');
        }

        if (!in_array($folder, $this->folders)) {
            \App::abort(404, 'Given folder not found.');
        }

        $fileExists = $this->translationService->fileExists($folder, $filename);

        if (!$fileExists) {
            \App::abort(404, 'Given file not found.');
        }

        $data = Utility::fromInputNames(Input::all());

        $this->translationService->updateTranslations($folder, $filename, $languageCode, $data);

        $thankyous = array('Thanks', 'Merci beaucoup', 'Terima kasih', 'Gracias', 'Obrigados', 'Jerejef', 'Asante sana', 'Danke schön', 'Shukran');
        $thanks = $thankyous[array_rand($thankyous)];

        \Flash::success('Your translation has been published. '.$thanks.'!');
        return Redirect::action('TranslationController@getTranslations', compact('filename', 'languageCode'));
    }

    public function getTranslation()
    {
        if (Input::has('languageCode') && !in_array(Input::get('languageCode'), $this->languageCodes)) {
            \App::abort(404, 'No Language given');
        }

        $languageCode = Input::get('languageCode', reset($this->languageCodes));
        
        $folders = [
            'borrower' => [],
            'common' => [],
        ];
        
        foreach ($folders as $folder => $_) {
            $folders[$folder] = TranslationLabelQuery::create()
                ->filterByLanguageCode(strtolower($languageCode))
                ->filterByFolder($folder)
                ->getTotals();
        }       

        return View::make('translation.index', ['languageCode' => $languageCode, 'borrowerLanguages' => $this->borrowerLanguages, 'folders' => $folders]);
    }
}
