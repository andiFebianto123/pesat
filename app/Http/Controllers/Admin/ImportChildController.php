<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\ChildMasterImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Prologue\Alerts\Facades\Alert;
use Illuminate\Http\Request;

class ImportChildController extends Controller
{
    //
    public function index()
    {

        return view('childimport');

    }

    public function import1()
    { 
        try {

            \Excel::import(new ChildMasterImport, request()->file('file'));
            // return redirect()->back()->with(['success' => 'Data berhasil di import']);

        } catch (Exception $e) {

            if ($e->getCode() == 0) {

                return redirect()->back()->with(['error' => 'Wrong format, please check again']);

            } else {
                $message = (explode("'", $e->errorInfo[2]));

                return redirect()->back()->with(['error' => $message[0] . $message[1] . $message[2]]);
            }
            throw $e;

        }

    }

    public function import(Request $request){
        // menangkap file excel
		$file = $request->file('file');

        // membuat nama file unik
		$nama_file = rand().$file->getClientOriginalName();

        // upload ke folder file_siswa di dalam folder public
		$file->move('file_anak',$nama_file);

        $import = new ChildMasterImport();
        $import->import(public_path('/file_anak/'.$nama_file));

        $dataErrors = [];

        foreach ($import->failures() as $failure) {
            $errors = [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => implode('\n', $failure->errors()),
                // 'values' => $failure->values()
            ];
            $dataErrors[] = $errors;
       }

       if (file_exists( public_path('/file_anak/'.$nama_file))) {
         unlink(public_path('/file_anak/'.$nama_file));
       }

       if(count($dataErrors) > 0){
        // jika ada data error
            return response()->json([
                'data' => $dataErrors,
                'status' => false,
                'message' => 'Ada data yang error ketika di import',
            ], 200);
       }

        return response()->json([
            'status' => true,
            'message' => 'Import Anak telah berhasil dilakukan',
        ], 200);

    }
}
