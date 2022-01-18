<?php

namespace App\Http\Controllers;

use App\Models\OrderProject;
use App\Models\ProjectMaster;
use App\Models\Sponsor;
use App\Services\Midtrans\CreateSnapTokenForProjectService;
use App\Services\Midtrans\CreateSnapTokenService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class ProjectOrderController extends Controller
{
    //
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user == null) {

            return redirect()->back()->with(['error' => 'Silahkan login sebelum melakukan donasi.']);
        } else {
            DB::beginTransaction();
            
            try {

                $idsponsor = $user->sponsor_id;

                // save table order_project
                $orderProject =  OrderProject::create([
                    'sponsor_id'      => $idsponsor,
                    'project_id'      => $request->projectid,
                    'price'           => $request->total,
                    'payment_status' => 1,
                    'created_at'    => Carbon::now(),
                ]);

                $project = ProjectMaster::where('project_id', $request->projectid)->first();

                $OrderId = $orderProject->order_project_id;

                if (!empty($project) && $project->is_closed) {
                    return redirect()->back()->with(['error' => 'Maaf, Anda sudah tidak dapat melakukan donasi karena status proyek telah ditutup']);
                }


                $Snaptokenorder = DB::table('order_project')->where('order_project.order_project_id', $OrderId)
                    ->join('sponsor_master as sm', 'sm.sponsor_id', '=', 'order_project.sponsor_id')
                    ->join('project_master as pm', 'pm.project_id', '=', 'order_project.project_id')
                    ->get();

                $order = OrderProject::where('order_project_id', $OrderId)->first();
                $midtrans = new CreateSnapTokenForProjectService($Snaptokenorder, $OrderId);
                $snapToken = $midtrans->getSnapToken();
                $order->snap_token = $snapToken;
                $order->order_project_id_midtrans = 'proyek-' . $OrderId;;
                $order->save();

                DB::commit();

                return  Redirect::route('orderprojectcheckout', array('snap_token' => $snapToken, 'code' => $OrderId));
            } catch (Execption $e) {
                DB::rollBack();
                throw $e;
            }
        }
    }
    public function orderproject($snapToken, $code)
    {
        $data['order'] = OrderProject::where('order_project_id', $code)->first();
        $data['snapToken'] = $snapToken;

        return view('projectshowpayment', $data);
    }
}
