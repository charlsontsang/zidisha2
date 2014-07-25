<?php

use Illuminate\Console\Command;
use Zidisha\Loan\LoanService;

class ExpireLoans extends Command
{
    protected $name = 'expireLoans';

    protected $description = 'Used to Expire loans.';

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
        $this->loanService->expireLoans();
    }
} 
