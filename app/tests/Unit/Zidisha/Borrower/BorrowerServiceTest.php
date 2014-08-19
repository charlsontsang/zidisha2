<?php


class BorrowerServiceTest extends \TestCase
{
    /**
     * @var \Zidisha\Borrower\BorrowerService
     */
    private $borrowerService;

    public function setUp()
    {
        parent::setUp();
        $this->borrowerService = $this->app->make('Zidisha\Borrower\BorrowerService');
    }


}
