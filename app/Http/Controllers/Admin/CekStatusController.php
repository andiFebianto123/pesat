<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChildMaster;
use App\Models\DataDetailOrder;
use App\Models\DataOrder;
use App\Models\OrderProject;
use App\Models\ProjectMaster;
use Exception;
use Illuminate\Support\Facades\DB;

class CekStatusController extends Controller
{

    public function index($id)
    {
        DB::beginTransaction();
        try {

            $decoderespon = \Midtrans\Transaction::status('proyek-' . $id);

            $response = json_encode($decoderespon);
            $statuscode = $decoderespon['status_code'];
            $getStatus = OrderProject::where('order_project_id', $id)->first();

            $getProjectId = $getStatus->project_id;

            if (empty($getStatus)) {
                \Alert::add('error', 'Order Proyek yang dimaksud tidak ditemukan')->flash();
                return back()->withMessage(['message' => 'Order Proyek yang dimaksud tidak ditemukan']);
            }

            $transaction = $decoderespon['transaction_status'];
            $type = $decoderespon['payment_type'];
            $order_id = $decoderespon['order_id'];
            $idproyek = substr($order_id, 0, -7);

            if ($transaction == 'capture') {
                // For credit card transaction, we need to check whether transaction is challenge by FDS or not                       

                DB::table('project_history_status_payment')->insert([
                    'detail_history' => $response,
                    'status' => 2,
                    'user_id' => backpack_user()->id,
                    'status_midtrans' => $transaction,

                ]);

                OrderProject::where('order_project_id', $id)
                    ->update([
                        'status_midtrans' => $transaction,
                        'payment_status' => 2,
                        'payment_type' => $type,
                    ]);

                //   echo "(capture) Transaction order_id: " . $order_id . " successfully captured using " . $type;
                DB::commit();

                \Alert::add('success', 'Status pembayaran berhasil diperbarui')->flash();
                return back()->withMessage(['message' => 'Status pembayaran berhasil diperbarui']);
            } else if ($transaction == 'settlement') {
                // TODO set payment status in merchant's database to 'Settlement'

                DB::table('project_history_status_payment')->insert([
                    'detail_history' => $response,
                    'status' => 2,
                    'status_midtrans' => $transaction,
                    'user_id' => backpack_user()->id,

                ]);

                OrderProject::where('order_project_id', $id)
                    ->update([
                        'status_midtrans' => $transaction,
                        'payment_status' => 2,
                        'payment_type' => $type,
                    ]);

                $totalPrice = OrderProject::where('project_id', $getProjectId)
                    ->where('payment_status', 2)
                    ->groupBy('project_id')
                    ->sum('price');

                $project = ProjectMaster::where('project_id', $getProjectId)->first();

                $amount = $project->amount;
                $enddate = Carbon::parse($project->end_date)->startOfDay();
                $now = Carbon::now();

                if (($enddate != null && $now > $enddate) || $totalPrice >= $amount) {
                    $project->is_closed = true;
                    $project->save();
                }
                DB::commit();

                \Alert::add('success', 'Status pembayaran berhasil diperbarui')->flash();
                return back()->withMessage(['message' => 'Status pembayaran berhasil diperbarui']);
            } else if ($transaction == 'pending') {

                DB::table('project_history_status_payment')->insert([
                    'detail_history' => $response,
                    'status' => 1,
                    'status_midtrans' => $transaction,
                    'user_id' => backpack_user()->id,

                ]);

                OrderProject::where('order_project_id', $id)
                    ->update([
                        'status_midtrans' => $transaction,
                        'payment_status' => 1,
                        'payment_type' => $type,
                    ]);

                // TODO set payment status in merchant's database to 'Pending'
                DB::commit();

                \Alert::add('error', 'Tidak ada status yang diperbarui')->flash();
                return back()->withMessage(['message' => 'Tidak ada status yang diperbarui']);
            } else if ($transaction == 'deny') {

                DB::table('project_history_status_payment')->insert([
                    'detail_history' => $response,
                    'status' => 3,
                    'status_midtrans' => $transaction,
                    'user_id' => backpack_user()->id,

                ]);

                //update last amount ////

                OrderProject::where('order_project_id', $id)
                    ->update([
                        'status_midtrans' => $transaction,
                        'payment_status' => 3,
                        'payment_type' => $type,
                    ]);

                // TODO set payment status in merchant's database to 'Denied'
                DB::commit();

                \Alert::add('success', 'Status pembayaran berhasil diperbarui')->flash();
                return back()->withMessage(['message' => 'Status pembayaran berhasil diperbarui']);
            } else if ($transaction == 'expire') {

                DB::table('project_history_status_payment')->insert([
                    'detail_history' => $response,
                    'status' => 3,
                    'status_midtrans' => $transaction,
                    'user_id' => backpack_user()->id,

                ]);
                OrderProject::where('order_project_id', $id)
                    ->update([
                        'status_midtrans' => $transaction,
                        'payment_status' => 3,
                        'payment_type' => $type,
                    ]);

                // TODO set payment status in merchant's database to 'expire'
                DB::commit();

                \Alert::add('success', 'Status pembayaran berhasil diperbarui')->flash();
                return back()->withMessage(['message' => 'Status pembayaran berhasil diperbarui']);
            } else if ($transaction == 'cancel') {

                DB::table('project_history_status_payment')->insert([
                    'detail_history' => $response,
                    'status' => 3,
                    'status_midtrans' => $transaction,
                    'user_id' => backpack_user()->id,

                ]);

                OrderProject::where('order_project_id', $id)
                    ->update([
                        'status_midtrans' => $transaction,
                        'payment_status' => 3,
                        'payment_type' => $type,
                    ]);

                // TODO set payment status in merchant's database to 'Denied'
                DB::commit();

                \Alert::add('success', 'Status pembayaran berhasil diperbarui')->flash();
                return back()->withMessage(['message' => 'Status pembayaran berhasil diperbarui']);
            }
            DB::commit();

            return response()->json(array('status' => 'success', 'msg' => 'Success!', 'message' => 'Status pembayaran berhasil diperbarui'));
        } catch (Exception $e) {
            DB::rollBack();
            if ($e->getCode() == 404) {
                \Alert::error(trans('Order proyek belum terdaftar di Midtrans' . $e->getCode()))->flash();
                return back()->withMessage(['message' => ('Order proyek belum terdaftar di Midtrans. ' . $e->getCode())]);
            } else {
                \Alert::error(trans('Gagal mendapatkan status order proyek dari Midtrans. '))->flash();
                return back()->withMessage(['message' => ('Gagal mendapatkan status order proyek dari Midtrans.' . $e->getCode())]);
            }
        }
    }
    public function childcekstatus($id)
    {
        DB::beginTransaction();

        try {

            $getStatus = DataOrder::where('order_id', $id)->first();
            $getOrderIdMidtrans = $getStatus->order_id_midtrans;

            $decoderespon = \Midtrans\Transaction::status($getOrderIdMidtrans);

            $response = json_encode($decoderespon);
            $cekDetailOrder = DataDetailOrder::where('order_id', $id)->get('child_id');

            if ($getStatus->payment_status == 2) {

                DB::commit();

                \Alert::add('success', 'Proses pembayaran sudah sukses')->flash();
                return back()->withMessage(['message' => 'Proses pembayaran sudah sukses']);
            } else {

                $transaction = $decoderespon['transaction_status'];
                $type = $decoderespon['payment_type'];
                $order_id = $decoderespon['order_id'];

                if ($transaction == 'capture') {
                    // For credit card transaction, we need to check whether transaction is challenge by FDS or not

                    DB::table('history_status_payment')->insert([
                        'detail_history' => $response,
                        'status' => 2,
                        'status_midtrans' => $transaction,
                        'user_id' => backpack_user()->id,
                    ]);

                    DataOrder::where('order_id', $id)
                        ->update([
                            'status_midtrans' => $transaction,
                            'payment_status' => 2,
                            'payment_type' => $type,
                        ]);
                    //                    echo "(capture) Transaction order_id: " . $order_id . " successfully captured using " . $type;
                    DB::commit();

                    \Alert::add('success', 'Status pembayaran berhasil diperbarui')->flash();
                    return back()->withMessage(['message' => 'Status pembayaran berhasil diperbarui']);
                } else if ($transaction == 'settlement') {
                    // TODO set payment status in merchant's database to 'Settlement'

                    DB::table('history_status_payment')->insert([
                        'detail_history' => $response,
                        'status' => 2,
                        'status_midtrans' => $transaction,
                        'user_id' => backpack_user()->id,
                    ]);

                    DataOrder::where('order_id', $id)
                        ->update([
                            'status_midtrans' => $transaction,
                            'payment_status' => 2,
                            'payment_type' => $type,
                        ]);
                    //             echo "(settlement)" . $cekType . " Transaction order_id: " . $order_id . " successfully transfered using " . $type;
                    DB::commit();

                    \Alert::add('success', 'Status pembayaran berhasil diperbarui')->flash();
                    return back()->withMessage(['message' => 'Status pembayaran berhasil diperbarui']);
                } else if ($transaction == 'pending') {

                    DB::table('history_status_payment')->insert([
                        'detail_history' => $response,
                        'status' => 1,
                        'status_midtrans' => $transaction,
                        'user_id' => backpack_user()->id,
                    ]);

                    DataOrder::where('order_id', $id)
                        ->update([
                            'status_midtrans' => $transaction,
                            'payment_status' => 1,
                            'payment_type' => $type,
                        ]);

                    // TODO set payment status in merchant's database to 'Pending'
                    DB::commit();

                    \Alert::add('error', 'Tidak ada status yang diperbarui')->flash();
                    return back()->withMessage(['message' => 'Tidak ada status yang diperbarui']);
                } else if ($transaction == 'deny') {

                    DB::table('history_status_payment')->insert([
                        'detail_history' => $response,
                        'status' => 3,
                        'status_midtrans' => $transaction,
                        'user_id' => backpack_user()->id,
                    ]);

                    DataOrder::where('order_id', $id)
                        ->update([
                            'status_midtrans' => $transaction,
                            'payment_status' => 3,
                            'payment_type' => $type,

                        ]);

                    foreach ($cekDetailOrder as $key => $detailOrder) {

                        $child = ChildMaster::find($detailOrder->child_id);
                        $child->is_sponsored = 0;
                        $child->current_order_id = null;
                        $child->save();
                    }

                    // TODO set payment status in merchant's database to 'Denied'
                    DB::commit();

                    \Alert::add('success', 'Status pembayaran berhasil diperbarui')->flash();
                    return back()->withMessage(['message' => 'Status pembayaran berhasil diperbarui']);
                } else if ($transaction == 'expire') {

                    DB::table('history_status_payment')->insert([
                        'detail_history' => $response,
                        //'status' => 3,
                        'status_midtrans' => $transaction,
                        'user_id' => backpack_user()->id,
                    ]);
                    DataOrder::where('order_id', $id)
                        ->update([
                            'status_midtrans' => $transaction,
                            //  'payment_status' => 3,
                            'payment_type' => $type,
                        ]);

                    foreach ($cekDetailOrder as $key => $detailOrder) {

                        $child = ChildMaster::find($detailOrder->child_id);
                        $child->is_sponsored = 0;
                        $child->current_order_id = null;
                        $child->save();
                    }

                    // TODO set payment status in merchant's database to 'expire'
                    DB::commit();

                    \Alert::add('success', 'Status pembayaran berhasil diperbarui')->flash();
                    return back()->withMessage(['message' => 'Status pembayaran berhasil diperbarui']);
                } else if ($transaction == 'cancel') {

                    DB::table('history_status_payment')->insert([
                        'detail_history' => $response,
                        'status' => 3,
                        'status_midtrans' => $transaction,
                        'user_id' => backpack_user()->id,
                    ]);

                    DataOrder::where('order_id', $id)
                        ->update([
                            'status_midtrans' => $transaction,
                            'payment_status' => 3,
                            'payment_type' => $type,
                        ]);

                    foreach ($cekDetailOrder as $key => $detailOrder) {

                        $child = ChildMaster::find($detailOrder->child_id);
                        $child->is_sponsored = 0;
                        $child->current_order_id = null;
                        $child->save();
                    }
                }

                DB::commit();

                // TODO set payment status in merchant's database to 'Denied'
                \Alert::add('success', 'Status pembayaran berhasil diperbarui')->flash();
                return back()->withMessage(['message' => 'Status pembayaran berhasil diperbarui']);
            }

            return response()->json('');
        } catch (Exception $e) {
            DB::rollBack();

            if ($e->getCode() == '404') {

                DB::commit();

                \Alert::add('error', 'Data pembayaran tidak ditemukan')->flash();
                return back()->withMessage(['message' => 'Data pembayaran tidak ditemukan']);
            } else {

                throw $e;
            }
        }
    }
}
