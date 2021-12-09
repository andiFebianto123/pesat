<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\ChildMasterImport;
use Illuminate\Http\Request;

class ImportChildController extends Controller
{
    //
    Public function index()
    {

        return view('childimport');

    }

    Public function import()
    {
        \Excel::import(new ChildMasterImport,request()->file('file'));
             
        return back();
    }
}
