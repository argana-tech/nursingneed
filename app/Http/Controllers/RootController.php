<?php
/**
 * RootController class
 *
 * @package 看護必要度チェッカ―
 * @author     eBase Solutions, Inc <info@ebase-sl.jp>
 * @copyright  2018 eBase Solutions, Inc
**/

namespace App\Http\Controllers;

class RootController extends Controller
{
    public function index()
    {
        return redirect()
            ->route('results.index')
        ;
    }
}
