<?php


use Zidisha\Vendor\SiftScience\Tester\SiftScienceTester;

class SiftScienceTesterController extends BaseController
{
    private $siftScienceTester;

    public function __construct(SiftScienceTester $siftScienceTester)
    {
        $this->siftScienceTester = $siftScienceTester;
    }

    public function getAllSiftScienceEvents()
    {
        $siftScienceEvents = get_class_methods($this->siftScienceTester);

        return View::make('admin.test-sift-science.index', compact('siftScienceEvents'));
    }

    public function postSiftScienceEvent()
    {
        $method = Input::get('method');

            if (method_exists($this->siftScienceTester, $method)){
                $this->siftScienceTester->$method();

                \Flash::success('Sift Science for '.$method.' sent successfully.');
            }

        return Redirect::route('admin:test:sift-science');

    }
}
