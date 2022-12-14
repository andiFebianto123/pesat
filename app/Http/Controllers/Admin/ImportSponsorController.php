<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\SponsorMasterImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Prologue\Alerts\Facades\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Exports\SponsorExport;

class ImportSponsorController extends Controller
{
    //
    public function index()
    {

        return view('sponsorimport', ['title' => 'Import Sponsor']);
    }

    public function download()
    {
        return new SponsorExport();
    }


    public function importsponsor(Request $request)
    {

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
        $nama_file = rand() . $file->getClientOriginalName();

        // upload ke folder file_siswa di dalam folder public
        $file->storeAs('public/file_sponsor', $nama_file);
        //$file->move('file_sponsor',$nama_file);

        register_shutdown_function(function ($path) {
            if (file_exists($path)) {
                unlink($path);
            }
        }, storage_path('/app/public/file_sponsor/' . $nama_file));

        DB::beginTransaction();
        try {
            $import = new SponsorMasterImport;

            $import->import(storage_path('/app/public/file_sponsor/' . $nama_file));

            if (count($import->errorsMessage) > 0) {
                DB::rollback();
                return response()->json([
                    'data' => $import->errorsMessage,
                    'status' => false,
                    'message' => 'Ada data yang error ketika di import.',
                    'notification' => 'Ada beberapa data tidak valid proses import.',
                ], 200);
            }
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Import Sponsor telah berhasil dilakukan.',
                'notification' => 'File berhasil di import.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
