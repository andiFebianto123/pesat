<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\ChildMasterImport;
use Exception;
use Illuminate\Http\Request;

class ImportChildController extends Controller
{
    //
    public function index()
    {

        return view('childimport');

    }

    public function import()
    {

        try {

            \Excel::import(new ChildMasterImport, request()->file('file'));
            return redirect()->back()->with(['success' => 'Data berhasil di import']);

        } catch (Exception $e) {

            $message = (explode("'",$e->errorInfo[2]));

            return redirect()->back()->with(['error' => 'Duplicate entry '.$message[1]]);

            throw $e;

        }

    }
}
