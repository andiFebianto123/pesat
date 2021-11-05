<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class TransactionController extends Controller
{
    //
    public function index(Request $request){
     
        if(session('key')==null){
            return redirect()->back()->with(['error' => 'Anda Harus Login Dulu !!']);
//            return redirect('/pesan')->with(['success' => 'Pesan Berhasil']);
        }else{
            return redirect()->back()->with(['success' => 'Sukses !!']);
        }
        //return $user->name; 

    }
}
