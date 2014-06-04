<?php

class PageController extends BaseController {

	public function getOurStory() {
        return View::make('pages.our-story');
    }

    public function getHowItWorks() {
        return View::make('pages.how-it-works');
    }
    
    public function getWhyZidisha() {
        return View::make('pages.why-zidisha');
    }

}
