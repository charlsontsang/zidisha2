<?php

/*
|--------------------------------------------------------------------------
| Register The Artisan Commands
|--------------------------------------------------------------------------
|
| Each available Artisan command must be registered with the console so
| that it is available to be called. We'll register every command so
| the console gets access to each of the command object instances.
|
*/

Artisan::add(new ApplicationSetup);
Artisan::add(new GenerateModelData);
Artisan::add(new Settings);
Artisan::add(new ImportTranslationsCommand);
Artisan::add(new LoanWriteOff(App::make('Zidisha\Loan\LoanService')));
Artisan::add(new ExpireLoans(App::make('Zidisha\Loan\LoanService')));
Artisan::add(new ScheduledJobs);
Artisan::add(new EnqueueScheduledJobs);
