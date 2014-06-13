<?php
/**
 * Created by PhpStorm.
 * User: Singularity Guy
 * Date: 6/13/14
 * Time: 11:08 AM
 */

class BorrowController extends BaseController {


    public function getPage()
    {
        return View::make('pages.borrow');
    }

} 