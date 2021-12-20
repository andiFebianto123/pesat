<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChildMaster;
use App\Models\DataOrder;
use App\Models\OrderHd;
use App\Models\Sponsor;

class DashboardController extends Controller
{
    //
    public function index()
    {
        $start= date("Y-n-j", strtotime("first day of this month"));
        $end=date("Y-n-j", strtotime("last day of this month"));

        $sponsoredchild = DataOrder::where('payment_status', 2)
            ->join('order_dt as odt', 'odt.order_id', '=', 'order_hd.order_id')
            ->join('child_master as cm', 'cm.child_id', '=', 'odt.child_id')
//            ->where('cm.is_sponsored', 1)

            ->whereBetween('odt.start_order_date', [$start, $end])
            ->distinct()
            ->count();

        $totalchild = ChildMaster::count(); 
        $notsponsoredchild = $totalchild - $sponsoredchild;

        $newSponsor = Sponsor::whereBetween('created_at',[$start,$end])->count();

        $notpaid = DataOrder::where('payment_status',1)
                            ->whereBetween('created_at',[$start,$end])
                            ->count();

        $totalamount = DataOrder::where('payment_status',2)
                        ->join('order_dt as odt','odt.order_id','=','order_hd.order_id')
                        ->join('child_master as cm','cm.child_id','=','odt.child_id')
                        ->whereBetween('odt.start_order_date',[$start,$end])
                        ->selectRaw('sum(odt.price) as sum_price')
                        ->pluck('sum_price')
                        ->first();


        $data['sponsored'] = $sponsoredchild;
        $data['notsponsored'] = $notsponsoredchild;
        $data['notpaid']    = $notpaid;
        $data['totalamount']= $totalamount;
        $data['newsponsor'] = $newSponsor;

        return view(backpack_view('dashboard'), $data);
    }
}
