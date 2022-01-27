<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Carbon\Carbon;
use App\Models\DataOrder;
use App\Models\ChildMaster;
use App\Models\OrderProject;
use App\Models\ProjectMaster;
use App\Models\DataDetailOrder;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\ProjectHistoryStatusPayment;

class CekStatusController extends Controller
{

    public function index($id)
    {
        DB::beginTransaction();
        try {

            $orderProject = OrderProject::where('order_project_id', $id)->first();

            if (empty($orderProject)) {
                DB::rollback();
                \Alert::add('error', 'Order proyek yang dimaksud tidak ditemukan.')->flash();
                return redirect(backpack_url('data-order-project'));
            }

            try {
                $orderId = $orderProject->order_project_id_midtrans;
                $decoderespon = \Midtrans\Transaction::status($orderId);

                $response = json_encode($decoderespon);
                $statuscode = $decoderespon->status_code;
            } catch (Exception $e) {
                DB::rollback();

                if ($e->getCode() == 404) {
                    $message = 'Order proyek belum terdaftar di Midtrans.';
                } else {
                    $message = 'Gagal mendapatkan status order proyek dari Midtrans. [' . $e->getCode() . ']';
                }

                \Alert::error($message)->flash();
                return redirect(backpack_url('data-order-project'));
            }

            $transaction = $decoderespon->transaction_status;
            $type = $decoderespon->payment_type;
            $order_id = $decoderespon->order_id;

            $paymentStatus = null;

            if ($transaction  == 'capture' || $transaction == 'settlement') {
                $paymentStatus = 2;
            }
            else if($transaction == 'pending' || $transaction == 'expire'){
                if($transaction == 'expire'){
                    $paymentStatus = $orderProject->payment_status;
                }
                else{
                    $paymentStatus = 1;
                }
            }
            else if($transaction == 'deny' || $transaction == 'cancel'){
                $paymentStatus = 3;
            } else {
                DB::rollback();
                \Alert::error('Status Midtrans : ' . $transaction . ' tidak dapat diproses sistem.')->flash();
                return redirect(backpack_url('data-order-project'));
            }

            $orderProject->status_midtrans = $transaction;
            $orderProject->payment_status = $paymentStatus;
            $orderProject->payment_type = $type;
            $orderProject->save();

            ProjectHistoryStatusPayment::create([
                'detail_history' => $response,
                'status' => $paymentStatus,
                'user_id' => backpack_user()->id,
                'status_midtrans' => $transaction,
            ]);


            $getProjectId = $orderProject->project_id;
            $totalPrice = OrderProject::where('project_id', $getProjectId)
                ->where('payment_status', 2)
                ->groupBy('project_id')
                ->sharedLock()
                ->sum('price');

            $project = ProjectMaster::where('project_id', $getProjectId)->first();

            $amount = $project->amount;
            $enddate = null;
            if ($project->end_date != null) {
                $enddate = Carbon::parse($project->end_date)->startOfDay();
            }
            $now = Carbon::now()->startOfDay();

            $project->last_amount = $totalPrice;
            $project->is_closed = ($enddate != null && $now > $enddate) || $totalPrice >= $amount;
            $project->save();

            DB::commit();

            \Alert::success('Status pembayaran berhasil diperbarui.')->flash();
            return redirect(backpack_url('data-order-project'));
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function childcekstatus($id)
    {
        DB::beginTransaction();

        try {

            $getStatus = DataOrder::where('order_id', $id)->first();
            if (empty($getStatus)) {
                \Alert::add('error', 'Order anak yang dimaksud tidak ditemukan.')->flash();
                return back()->withMessage(['message' => 'Order anak yang dimaksud tidak ditemukan']);
            }

            $getOrderIdMidtrans = $getStatus->order_id_midtrans;

            $decoderespon = \Midtrans\Transaction::status($getOrderIdMidtrans);

            $response = json_encode($decoderespon);
            $cekDetailOrder = DataDetailOrder::where('order_id', $id)->get('child_id');

            $transaction = $decoderespon->transaction_status;
            $type = $decoderespon->payment_type;
            $order_id = $decoderespon->order_id;

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
                    $child->is_paid = 0;
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
                    $child->is_paid = 0;
                    $child->current_order_id = null;
                    $child->save();
                }
            } else {
                \Alert::add('error', 'Status Midtrans : ' . $transaction . ' tidak dapat diproses sistem')->flash();
                return back()->withMessage(['message' => 'Status Midtrans : ' . $transaction . ' tidak dapat diproses sistem']);
            }

            DB::commit();

            // TODO set payment status in merchant's database to 'Denied'
            \Alert::add('success', 'Status pembayaran berhasil diperbarui')->flash();
            return back()->withMessage(['message' => 'Status pembayaran berhasil diperbarui']);

            return response()->json('');
        } catch (Exception $e) {
            if ($e->getCode() == '404') {

                DB::commit();

                \Alert::add('error', 'Data pembayaran tidak ditemukan')->flash();
                return back()->withMessage(['message' => 'Data pembayaran tidak ditemukan']);
            } else if ($e->getCode() != '404') {
                DB::commit();

                Alert::add('error', "Gagal mendapatkan status order anak dari Midtrans. ["  . $e->getCode() . "]");
                return back()->withMessage(['message' => "Gagal mendapatkan status order anak dari Midtrans. ["  . $e->getCode() . "]"]);
            } else {
                DB::rollBack();
                throw $e;
            }
        }
    }
}
