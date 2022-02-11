<?php

namespace App\Http\Controllers;

use App\Models\DonateGoods;
use Illuminate\Http\Request;

class DonationGoodsController extends Controller
{
    //
    public function index(){
        $donateGood = DonateGoods::first();
        return view('donategoods', ['donateGood' => $donateGood]);
    }
}
