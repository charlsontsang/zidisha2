<?php

use Illuminate\Console\Command;

class ImportTranslationsCommand extends Command {

	protected $name = 'import-translations';

	protected $description = 'Used to import language file to the databse.';


    public function __construct()
	{
		parent::__construct();
    }

	public function fire()
	{
        $translationService = \App::make('Zidisha\Translation\TranslationService');
        $translationService->loadLanguageFilesToDatabase();
        $this->info('All language files have been loaded into the database.');
	}
}
