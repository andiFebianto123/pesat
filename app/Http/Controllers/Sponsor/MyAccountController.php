<?php

namespace App\Http\Controllers\Sponsor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MyAccountController extends Controller
{
    //
    public function index(){
        return view ('sponsor.dashboard');
    }

    public function childDonation(){
        return view('sponsor.childdonation');
    }

    public function projectDonation(){
        return view('sponsor.projectdonation');
    }
}
