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
        $sponsoredchild = ChildMaster::where('is_sponsored', 1)
        ->orWhereHas('detailorders', function($innerQuery) use($now){
            $innerQuery->whereDate('start_order_date', '<=', $now)
            ->whereDate('end_order_date', '>=', $now)
            ->whereHas('order', function($deepestQuery){
                $deepestQuery->where('payment_status', '<=', 2);
            });
        })->count();


        $notsponsoredchild = ChildMaster::where('is_sponsored', 0)->whereDoesntHave('detailorders', function($innerQuery) use($now){
            $innerQuery->whereDate('start_order_date', '<=', $now)
            ->whereDate('end_order_date', '>=', $now)
            ->whereHas('order', function($deepestQuery){
                $deepestQuery->where('payment_status', '<=', 2);
            });
        })->count();


        $newSponsor = Sponsor::whereBetween('created_at', [$start, $end])->count();

        $notpaid = DataOrder::where('payment_status', 1)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $totalamount = DataOrder::where('payment_status', 2)
            ->whereBetween('created_at', [$start, $end])
            ->sum('total_price');

        $monthYear = $now->format('F Y');

        $data['sponsored'] = $sponsoredchild;
        $data['notsponsored'] = $notsponsoredchild;
        $data['notpaid']    = $notpaid;
        $data['totalamount'] = $totalamount;
        $data['newsponsor'] = $newSponsor;
        $data['monthYear'] = $monthYear;

        return view(backpack_view('dashboard'), $data);
    }
}
