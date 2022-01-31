<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\ChildMasterImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Prologue\Alerts\Facades\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

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
            'file' => 'required|mimes:xlsx,xls',
         ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $messageErrors = [];
            foreach ($errors->all() as $message) {
                array_push($messageErrors, $message);
            }
            $messageErrors = implode('<br/>', $messageErrors);
            return response()->json([
                'status' => false,
                'validator' => true,
                'message' => $messageErrors
            ], 200);
        }

        $file = $request->file('file');

        // membuat nama file unik
		$nama_file = rand().$file->getClientOriginalName();

        // upload ke folder file_siswa di dalam folder public
        $file->storeAs('public/file_anak', $nama_file);
		//$file->move('file_anak',$nama_file);

        DB::beginTransaction();

        try{
            $import = new ChildMasterImport();
            $import->import(storage_path('/app/public/file_anak/'.$nama_file));

            if(file_exists( storage_path('/app/public/file_anak/'.$nama_file))) {
                unlink(storage_path('/app/public/file_anak/'.$nama_file));
            }
    
            if(count($import->errorsMessage) > 0){
                DB::rollback();
                return response()->json([
                    'data' => $import->errorsMessage,
                    'status' => false,
                    'message' => 'Ada data yang error ketika di import',
                    'notification' => 'Ada beberapa data tidak valid proses import',
                ], 200);
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Import Anak telah berhasil dilakukan',
                'notification' => 'File berhasil di import',
            ], 200);

        }catch(\Exception $e){
            DB::rollback();
            \Alert::add('error', $e->getMessage())->flash();
        }

    }
}
