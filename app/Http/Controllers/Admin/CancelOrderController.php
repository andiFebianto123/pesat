<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChildMaster;
use App\Models\DataDetailOrder;
use App\Models\DataOrder;
use App\Models\OrderProject;
use Exception;
use Illuminate\Support\Facades\DB;

class CancelOrderController extends Controller
{
    //
    public function index($id)
    {

        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

        $cekDatas = DataOrder::where('order_id', $id)->first();
        
        $getOrderIdMidtrans = $cekDatas->order_id_midtrans;
        

        $cekDetailOrder = DataDetailOrder::where('order_id', $id)->get('child_id');
        DB::beginTransaction();

        if ($cekDatas !== null) {

            if ($cekDatas->payment_status == 2) {

                DB::commit();
                \Alert::add('error', 'Tidak bisa cancel order, karena sudah ada pembayaran')->flash();
                return back()->withMessage(['message' => 'Tidak bisa cancel order, karena sudah ada pembayaran']);

            } else {

                try {

                    //$orderId = $id . "-anak";
                    \Midtrans\Transaction::cancel($getOrderIdMidtrans);

                    $orderHd = DataOrder::find($id);
                    $orderHd->payment_status = 3;
                    $orderHd->status_midtrans = 'cancel';
                    $orderHd->save();

                    foreach ($cekDetailOrder as $key => $detailOrder) {

                        $child = ChildMaster::find($detailOrder->child_id);
                        $child->is_sponsored = 0;
                        $child->current_order_id = null;
                        $child->save();

                    }

                    DB::commit();

                    \Alert::add('success', 'Transaksi berhasil dibatalkan')->flash();
                    return back()->withMessage(['message' => 'Transaksi berhasil dibatalkan']);

                } catch (Exception $e) {

                    if ($e->getCode() == 404) {

                        $orderHd = DataOrder::find($id);
                        $orderHd->payment_status = 3;
                        $orderHd->save();

                        foreach ($cekDetailOrder as $key => $detailOrder) {

                            $child = ChildMaster::find($detailOrder->child_id);
                            $child->is_sponsored = 0;
                            $child->current_order_id = null;
                            $child->save();

                        }

                        DB::commit();

                        \Alert::add('success', 'Order berhasil dibatalkan')->flash();
                        return back()->withMessage(['message' => 'Order berhasil dibatalkan']);

                    } elseif ($e->getCode() == 412) {

                        DB::commit();
                        \Alert::add('error', 'Status transaksi sudah tidak bisa dirubah')->flash();
                        return back()->withMessage(['message' => 'Status transaksi sudah tidak bisa dirubah']);

                    } else {
                        throw $e;
                    }
                }

            }
        } else {
            DB::commit();

            \Alert::add('error', 'Data order tidak ada')->flash();
            return back()->withMessage(['message' => 'Data order tidak ada']);
        }

    }
    public function projectcancelorder($id)
    {
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

        $projectOrder = OrderProject::where('order_project_id', $id)->first();

        DB::beginTransaction();

        if ($projectOrder !== null) {

            if ($projectOrder->payment_status == 2) {

                DB::commit();

                \Alert::add('error', 'Tidak bisa cancel order, karena sudah ada pembayaran')->flash();
                return back()->withMessage(['message' => 'Tidak bisa cancel order, karena sudah ada pembayaran']);

            } else {

                try {
                    $orderId = $id . "-proyek";
                    \Midtrans\Transaction::cancel($orderId);

                    $projectOrder->payment_status = 3;
                    $projectOrder->status_midtrans = 'cancel';
                    $projectOrder->save();

                    DB::commit();

                    \Alert::add('success', 'Order berhasil dibatalkan')->flash();
                    return back()->withMessage(['message' => 'Order berhasil dibatalkan']);
                } catch (Exception $e) {

                    if ($e->getCode() == 404) {
                        $projectOrder->payment_status = 3;
                        $projectOrder->save();

                        DB::commit();
                        \Alert::add('success', 'Order berhasil dibatalkan')->flash();
                        return back()->withMessage(['message' => 'Order berhasil dibatalkan']);

                    } elseif ($e->getCode() == 412) {

                        DB::commit();
                        \Alert::add('error', 'Status transaksi sudah tidak bisa dirubah')->flash();
                        return back()->withMessage(['message' => 'Status transaksi sudah tidak bisa dirubah']);

                    } else {
                        throw $e;
                    }

                }

            }
        } else {

            DB::commit();

            \Alert::add('error', 'Data order tidak ada')->flash();
            return back()->withMessage(['message' => 'Data order tidak ada']);
        }

    }
}
