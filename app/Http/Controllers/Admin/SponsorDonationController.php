<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SponsorDonationController extends Controller
{

    public function index()
    {
        $sponsors = Sponsor::with(
            [
                'data_order' => function ($query) {
                    $query->where('payment_status', 2)
                        ->where('deleted_at', null);
                },
                'project_order' => function ($query) {
                    $query->where('payment_status', 2)
                        ->where('deleted_at', null);
                }
            ]
        )->get();

        $data = [];

        foreach ($sponsors as $sponsor) {
            $totalPrice = 0;
            $totalOrder = 0;
            foreach ($sponsor->data_order as $dataOrder) {
                $totalPrice += $dataOrder->total_price;
                $totalOrder++;
            }
            foreach ($sponsor->project_order as $ProjectOrder) {
                $totalPrice += $ProjectOrder->total_price;
                $totalOrder++;
            }

            $data[] = [
                'sponsor_name' => $sponsor->name,
                'hometown' => $sponsor->hometown,
                'total_price' => $totalPrice,
                'total_order' => $totalOrder
            ];
        }

        return view('sponsor_donation', ['data' => $data]);
    }
}
