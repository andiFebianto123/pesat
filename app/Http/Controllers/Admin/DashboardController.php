<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\OrderHd;
use App\Models\Sponsor;
use App\Models\DataOrder;
use App\Models\ChildMaster;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    //
    public function index()
    {
        $now = Carbon::now();
        $start = $now->copy()->startOfMonth();
        $end = $now->copy()->endOfMonth();

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


        $newSponsor = Sponsor::whereBetween('created_at', [$start, $end])->count();

        $notpaid = DataOrder::where('payment_status', 1)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $totalamount = DataOrder::where('payment_status', 2)
            ->join('order_dt as odt', 'odt.order_id', '=', 'order_hd.order_id')
            ->join('child_master as cm', 'cm.child_id', '=', 'odt.child_id')
            ->whereBetween('odt.start_order_date', [$start, $end])
            ->selectRaw('sum(odt.price) as sum_price')
            ->pluck('sum_price')
            ->first();

        $monthYear = $now->format('F Y');

        $data['sponsored'] = $sponsoredchild->count();
        $data['notsponsored'] = $notsponsoredchild;
        $data['notpaid']    = $notpaid;
        $data['totalamount'] = $totalamount;
        $data['newsponsor'] = $newSponsor;
        $data['monthYear'] = $monthYear;

        return view(backpack_view('dashboard'), $data);
    }
}
