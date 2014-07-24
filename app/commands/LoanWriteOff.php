<?php

use Illuminate\Console\Command;
use Zidisha\Loan\LoanService;

class LoanWriteOff extends Command
{
    protected $name = 'writeOffLoan';

    protected $description = 'Used to write of unpaid loans.';

    /**
     * @var LoanService
     */
    private $loanService;

    public function __construct(LoanService $loanService)
    {
        parent::__construct();
        $this->loanService = $loanService;
    }

    public function fire()
    {
        $this->loanService->writeOffLoans();
    }
} 
