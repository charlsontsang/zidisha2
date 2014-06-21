<?php
class LoanServiceCest
{
    /**
     * @var Zidisha\Loan\LoanService
     */
    private $loanService;

    public function _before(UnitTester $I)
    {
        $this->loanService = $I->grabService('Zidisha\Loan\LoanService');
    }

    public function _after(UnitTester $I)
    {
    }

    public function testPlaceBid(UnitTester $I)
    {
        $loan = \Zidisha\Loan\LoanQuery::create()
            ->findOneById('1');

        $lender = \Zidisha\Lender\LenderQuery::create()
            ->findOneById('32');

        $data = [
            'amount' => '10',
            'interestRate' => '5'
        ];

        $this->loanService->placeBid($loan, $lender, $data);

    }
} 