<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChildMaster;
use App\Models\DataOrder;
use App\Models\OrderHd;
use App\Models\Sponsor;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    //
    public function index()
    {
        $now = Carbon::now()->startOfDay();
        $sponsoredchild = DataOrder::join('order_dt as odt', 'odt.order_id', '=', 'order_hd.order_id')
            ->join('child_master as cm', 'cm.child_id', '=', 'odt.child_id')
            ->where('is_sponsored', 1)
            ->orWhere(function ($query) use ($now) {
                $query->where('payment_status', 2)
                    ->whereDate('odt.start_order_date', '<=', $now)
                    ->whereDate('odt.end_order_date', '>=', $now)
                    ->where('odt.deleted_at', null);
            })
            ->distinct()
            ->get();

        $childIds = $sponsoredchild->pluck('child_id');

        $notsponsoredchild = ChildMaster::whereNotIn('child_id', $childIds)->count();

        $data['sponsored'] = $sponsoredchild;
        $data['notsponsored'] = $notsponsoredchild;
        return view('report');
    }
    public function filterreport(Request $request)
    {
        $startdate = $request->start;
        $endate = $request->end;
        $newstartDate = date("Y-m-d", strtotime($startdate));
        $newendDate = date("Y-m-d", strtotime($endate));

        $total = DataOrder::where('payment_status', 2)
            ->join('order_dt as odt', 'odt.order_id', 'order_hd.order_id')
            ->whereBetween('odt.start_order_date', [$newstartDate, $newendDate])
            ->selectRaw('sum(odt.price) as sum_price')
            ->pluck('sum_price')
            ->first();
        $newsponsor = Sponsor::whereBetween('created_at', [$newstartDate, $newendDate])
            ->count();



        $totalAmount = "Rp " . number_format($total, 2, ',', '.');
        return response()->json([
            'totalamount' => $totalAmount,
            'newsponsor'    => $newsponsor,
        ]);
    }
}
