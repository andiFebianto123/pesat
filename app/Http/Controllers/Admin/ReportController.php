<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChildMaster;
use App\Models\OrderHd;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    //
    public function index(){
        $start= date("Y-n-j", strtotime("first day of this month"));
        $end=date("Y-n-j", strtotime("last day of this month"));

        $sponsoredchild = OrderHd::where('payment_status',2)
                          ->join('order_dt as odt','odt.order_id','=','order_hd.order_id')
                          ->join('child_master as cm','cm.child_id','=','odt.child_id')
                          ->where('cm.is_sponsored',1)
                         // ->whereBetween('odt.start')
                         // whereBetween('reservation_from', [$from, $to])
                         ->orWhere(function($query) {
                            $end=date("Y-n-j", strtotime("last day of this month"));

                            $query->where('odt.start_order_date', '<=',$end)
                                  ->where('odt.end_order_date', '<=', $end);
                        })
                          ->distinct()
                          ->get();
                         // ->count(['cm.child_id']);  

//        dd($sponsoredchild);
        $notsponsoredchild = ChildMaster::where('is_paid',0)->count();
        
        $data['sponsored'] = $sponsoredchild;
        $data['notsponsored']= $notsponsoredchild;
        return view('report');
    }
}
