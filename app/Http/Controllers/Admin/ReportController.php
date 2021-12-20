<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChildMaster;
use App\Models\DataOrder;
use App\Models\OrderHd;
use App\Models\Sponsor;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    //
    public function index(){

        $start= date("Y-n-j", strtotime("first day of this month"));
        $end=date("Y-n-j", strtotime("last day of this month"));

        $sponsoredchild = DataOrder::where('payment_status',2)
                          ->join('order_dt as odt','odt.order_id','=','order_hd.order_id')
                          ->join('child_master as cm','cm.child_id','=','odt.child_id')
                          ->where('cm.is_sponsored',1)
                         ->orWhere(function($query) {
                            $end=date("Y-n-j", strtotime("last day of this month"));

                            $query->where('odt.start_order_date', '<=',$end)
                                  ->where('odt.end_order_date', '<=', $end);
                        })
                          ->distinct()
                          ->get(); 


        $notsponsoredchild = ChildMaster::where('is_paid',0)->count();
        
        $data['sponsored'] = $sponsoredchild;
        $data['notsponsored']= $notsponsoredchild;
        return view('report');
    }
public function filterreport(Request $request){
    $startdate=$request->start;
    $endate=$request->end;
    $newstartDate = date("Y-m-d", strtotime($startdate));
    $newendDate = date("Y-m-d", strtotime($endate));   

    $total = DataOrder::where('payment_status',2)
                    ->join('order_dt as odt','odt.order_id','order_hd.order_id')
                    ->whereBetween('odt.start_order_date',[$newstartDate,$newendDate])
                    ->selectRaw('sum(odt.price) as sum_price')
                    ->pluck('sum_price')
                    ->first();
    $newsponsor = Sponsor::whereBetween('created_at',[$newstartDate,$newendDate])
                        ->count();
                    


    $totalAmount = "Rp " . number_format($total,2,',','.');
    return response()->json([
        'totalamount' => $totalAmount,
        'newsponsor'    => $newsponsor,
    ]);
}        
}
