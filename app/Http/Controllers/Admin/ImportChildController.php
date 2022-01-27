<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\ChildMasterImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Prologue\Alerts\Facades\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

        $validator = Validator::make($request->all(), [
            'file' => 'required',
         ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'validator' => true,
            ], 200);
        }

        $file = $request->file('file');

        // membuat nama file unik
		$nama_file = rand().$file->getClientOriginalName();

        // upload ke folder file_siswa di dalam folder public
		$file->move('file_anak',$nama_file);

        $import = new ChildMasterImport();
        $import->import(public_path('/file_anak/'.$nama_file));

        if(file_exists( public_path('/file_anak/'.$nama_file))) {
            unlink(public_path('/file_anak/'.$nama_file));
        }


        if(count($import->failures()) > 0){
            // jika ada error
            $dataErrors = [];

            $errorPerRow = [];
            foreach ($import->failures() as $failure) {
                if(!isset($errorPerRow['row'])){
                    $errorPerRow['row'] = $failure->row();
                    $errorPerRow['message'] = [];
                    $errorPerRow['message'][] = implode('\n', $failure->errors());
                }else{
                    if($errorPerRow['row'] == $failure->row()){
                        $errorPerRow['message'][] = implode('\n', $failure->errors());
                    }else{
                        $message = implode("<br/>", $errorPerRow['message']);
                        $errorPerRow['message'] = $message;
                        $dataErrors[] = $errorPerRow;
                        $errorPerRow = [];

                        $errorPerRow['row'] = $failure->row();
                        $errorPerRow['message'] = [];
                        $errorPerRow['message'][] = implode('\n', $failure->errors());
                    }
                }
                // $errors = [
                //     'row' => $failure->row(),
                //     //'attribute' => $failure->attribute(),
                //     'message' => implode('\n', $failure->errors()),
                //     // 'values' => $failure->values()
                // ];
            }

            return response()->json([
                'data' => $dataErrors,
                'status' => false,
                'message' => 'Ada data yang error ketika di import',
                'notification' => 'Ada beberapa kesalahan saat import data',
            ], 200);

        }

        return response()->json([
            'status' => true,
            'message' => 'Import Anak telah berhasil dilakukan',
            'notification' => 'File berhasil di import',
        ], 200);


    }
}
