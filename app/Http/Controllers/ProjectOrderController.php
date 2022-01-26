<?php

namespace App\Http\Controllers;

use App\Models\OrderProject;
use App\Models\ProjectMaster;
use App\Services\Midtrans\CreateSnapTokenForProjectService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class ProjectOrderController extends Controller
{
    //
    public function index($id, Request $request)
    {
        $request->validate(['donation' => 'required|integer|min:1']);

        DB::beginTransaction();
        try {

            $project = ProjectMaster::where('project_id', $id)->first();

            if($project == null){
                DB::rollback();
                return redirect(url('list-proyek'))->with(['error' => 'Order proyek yang dimaksud tidak ditemukan.']);
            }

            if ($project->is_closed) {
                DB::rollback();
                return redirect()->back()->with(['error' => 'Maaf, Anda sudah tidak dapat melakukan donasi karena status proyek telah ditutup.']);
            }

            $user = auth()->user();
            $idsponsor = $user->sponsor_id;

            // save table order_project
            $orderProject = OrderProject::create([
                'sponsor_id' => $idsponsor,
                'project_id' => $id,
                'price' => $request->donation,
                'payment_status' => 1,
            ]);

            
            $orderId = $orderProject->order_project_id;

         
            $Snaptokenorder = DB::table('order_project')->where('order_project.order_project_id', $orderId)
                ->join('sponsor_master as sm', 'sm.sponsor_id', '=', 'order_project.sponsor_id')
                ->join('project_master as pm', 'pm.project_id', '=', 'order_project.project_id')
                ->select(
                    'order_project.*',
                    'pm.title',
                    'sm.full_name',
                    'sm.email',
                    'sm.no_hp'
                )
                ->get();

            $midtrans = new CreateSnapTokenForProjectService($Snaptokenorder, $orderId);
            $snapToken = $midtrans->getSnapToken();
            $orderProject->snap_token = $snapToken;
            $orderProject->order_project_id_midtrans = 'proyek-' . $orderId;
            $orderProject->save();

            DB::commit();
            return Redirect::route('orderprojectcheckout', array('code' => $orderId));
        } catch (Execption $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function orderproject($code)
    {
        DB::beginTransaction();
        try {
            $orderProject = OrderProject::where('order_project_id', $code)->first();
            if ($orderProject == null) {
                DB::rollBack();
                return redirect()->route('projectdonation')->with(['error' => 'Order proyek yang dimaksud tidak ditemukan.']);
            }

            $data['error'] = '';

            $getStatusMidtrans = $orderProject->order_project_id_midtrans;
            $transaction = null;
            try{
                $decoderespon = \Midtrans\Transaction::status($getStatusMidtrans);
                $transaction = $decoderespon->transaction_status;
            }
            catch(Exception $e){
                if ($e->getCode() != 404){
                    $data['error'] = "Gagal mendapatkan status order proyek dari Midtrans. [" . $e->getCode() . "]";
                    $data['error_status'] = $e->getCode();
                }
            }

            $now = Carbon::now()->startOfDay();
            $nowSub2Days = $now->copy()->addDay(-2);

            $createdAt = Carbon::parse($orderProject->created_at)->startOfDay();
            if ($transaction == 'expire' && $nowSub2Days <= $createdAt) {
                $Snaptokenorder = DB::table('order_project')->where('order_project.order_project_id', $orderProject->order_project_id)
                    ->join('sponsor_master as sm', 'sm.sponsor_id', '=', 'order_project.sponsor_id')
                    ->join('project_master as pm', 'pm.project_id', '=', 'order_project.project_id')
                    ->select(
                        'order_project.*',
                        'pm.title',
                        'sm.full_name',
                        'sm.email',
                        'sm.no_hp'
                    )
                    ->get();
                $code = $orderProject->order_project_id . "-" . Carbon::now()->timestamp;
                $orderIdMidtrans = "proyek-" . $code;
                $midtrans = new CreateSnapTokenForProjectService($Snaptokenorder, $code);
                $snapToken = $midtrans->getSnapToken();
                $orderProject->snap_token = $snapToken;
                $orderProject->order_project_id_midtrans = $orderIdMidtrans;
                $orderProject->save();
            }

            DB::commit();

            $data['order'] = $orderProject;
            $data['snapToken'] = $orderProject->snap_token;

            return view('projectshowpayment', $data);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
