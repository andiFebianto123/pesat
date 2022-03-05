<?php

namespace App\Http\Controllers\Sponsor;

use App\Models\Config;
use App\Models\Sponsor;
use App\Mail\NewSponsor;
use App\Models\DataOrder;
use App\Mail\ResetPassword;
use App\Models\ChildMaster;
use App\Models\OrderProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;

class MyAccountController extends Controller
{
    //
    public function index()
    {
        $user = auth()->user();
        $getUserId = $user->sponsor_id;
        $getuser = Sponsor::where('sponsor_id', $getUserId)->first();
        $data["user"] = $getuser;
        $data['title'] = "Dashboard";
        return view('sponsor.dashboard', $data);
    }

    public function childDonation()
    {
        $user = auth()->user();
        $getUserId = $user->sponsor_id;

        $getOrder = DataOrder::where('sponsor_id', $getUserId)
        ->with('oneorderdetail:order_id,child_id')
            ->orderBy('order_id', 'desc')->paginate(5);
        $data['orders'] = $getOrder;
        $data['title'] = "List Anak";

        return view('sponsor.childdonation', $data);
    }

    public function projectDonation()
    {
        $user = auth()->user();
        $getUserId = $user->sponsor_id;
        $getprojectorder = OrderProject::where('sponsor_id', $getUserId)
            ->orderBy('order_project_id', 'desc')->paginate(5);
        $data['projectorders'] = $getprojectorder;
        $data['title'] = "List Proyek";
        return view('sponsor.projectdonation', $data);
    }
    public function editaccount()
    {
        $user = auth()->user();
        $data['profile'] = $user;
        $data['title'] = "Edit Account";

        return view('sponsor.editaccount', $data);
    }
    public function updateaccount(Request $request)
    {

        $request->validate([

            'name' => 'required|max:255',
            'fullname' => 'required|max:255',
            'hometown' => 'max:255',
            'dateofbirth' => 'required|date|date_format:Y-m-d',
            'address' => 'max:255',
            'email' => 'required|email|max:255',
            'password' => 'nullable|confirmed|min:6',
            'nohp' => 'required|max:255',
            'churchmember' => 'max:255',
        ]);

        $id = $request->sponsorid;
        $panggilan = $request->name;
        $fullname = $request->fullname;
        $hometown = $request->hometown;
        $dateofbirth = date('Y-m-d', strtotime($request->dateofbirth));
        $email = $request->email;
        $password = $request->password;
        $nohp = $request->nohp;
        $churchmember = $request->churchmember;
        $address = $request->address;

        $sponsor = Sponsor::where('sponsor_id', $id)->first();

        $sponsor->name = $panggilan;
        $sponsor->full_name = $fullname;
        $sponsor->hometown = $hometown;
        $sponsor->date_of_birth = $dateofbirth;
        $sponsor->email = $email;
        if ($request->filled('password')) {
            $sponsor->password = bcrypt($password);
        }
        $sponsor->address = $address;
        $sponsor->no_hp = $nohp;
        $sponsor->church_member_of = $churchmember;

        $sponsor->save();

        return redirect(url('edit-account'))->with(['success' => 'Data Berhasil Di Update.']);
    }

    public function forgotpassword()
    {
        return view('sponsor.forgotpassword', ['title' => 'Lost Password']);
    }
    public function resetpassword(Request $request)
    {
        $getUser = Sponsor::where("email", "=", $request->email)->first();
        if ($getUser) {
            $length = 8;
            $generatepass = $this->generatepassword($length);

            Mail::to($getUser->email)->send(new ResetPassword("Reset Password", $generatepass));

            $getUser->password = bcrypt($generatepass);
            $getUser->save();
        }
        return redirect(url('forgot-password'))->with(['success' => 'Apabila email Anda terdaftar maka password baru akan dikirim ke email Anda.']);
    }

    public function generatepassword($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $newpass = '';
        for ($i = 0; $i < $length; $i++) {
            $newpass .= $characters[rand(0, $charactersLength - 1)];
        }
        return $newpass;
    }

    public function register(Request $request)
    {

        return view('sponsor.register', ['title' => "Register"]);
    }

    public function createaccount(Request $request)
    {

        $request->validate([
            'name' => 'required|max:255',
            'fullname' => 'required|max:255',
            'hometown' => 'required|max:255',
            'dateofbirth' => 'required|date|date_format:Y-m-d',
            'address' => 'required|max:255',
            'email' => 'unique:sponsor_master|required|email|max:255',
            'password' => 'required|confirmed|min:6',
            'nohp' => 'required|max:255',
            'memberofchurch' => 'max:255',
        ]);

        $cekEmail = Sponsor::where('email', $request->email)->first();

        DB::beginTransaction();
        try {
            $insertsponsor = new Sponsor();
            $insertsponsor->name = $request->name;
            $insertsponsor->full_name = $request->fullname;
            $insertsponsor->hometown = $request->hometown;
            $insertsponsor->date_of_birth = $request->dateofbirth;
            $insertsponsor->address = $request->address;
            $insertsponsor->email = $request->email;
            $insertsponsor->password = bcrypt($request->password);
            $insertsponsor->no_hp = $request->nohp;
            $insertsponsor->church_member_of = $request->memberofchurch;
            $insertsponsor->save();

            $config = Config::where('key', 'Administration Email Address')->first();
            if ($config != null) {
                $explodedEmail = collect(explode(',', $config->value));
                $email = $explodedEmail->shift();
                $cc = $explodedEmail->toArray();
                $mail =  Mail::to($email);
                if (count($cc) != 0) {
                    $mail->cc($cc);
                }

                $emailTitle = 'Ada yang mendaftar sponsor baru bernama ' . $insertsponsor->full_name;
                $mail->send(new NewSponsor($emailTitle, $insertsponsor->full_name));
            }

            DB::commit();
            return redirect(url('sponsor/login'))->with(['success' => 'Akun berhasil dibuat, silahkan login.']);
        } catch (Execption $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function childdetaildonation($id)
    {
        $orderhd = DataOrder::where('order_id', $id)->first();
        if ($orderhd == null) {
            return redirect(url('child-donation'))->with(['error' => 'Order anak yang dimaksud tidak ditemukan.']);
        }
        $orders = DataOrder::where('order_hd.order_id', $id)
            ->join('order_dt as odt', 'odt.order_id', '=', 'order_hd.order_id')
            ->whereNull('odt.deleted_at')
            ->join('child_master as cm', 'cm.child_id', '=', 'odt.child_id')
            ->join('sponsor_master as sm', 'sm.sponsor_id', '=', 'order_hd.sponsor_id')
            ->select(
                'order_hd.order_id',
                'order_hd.total_price',
                'order_hd.payment_status',
                'order_hd.snap_token',
                'order_hd.status_midtrans',
                'order_hd.created_at',
                'order_hd.payment_type',
                'odt.order_dt_id',
                'odt.price as price_dt',
                'odt.monthly_subscription',
                'cm.full_name as child_name',
                'cm.price as child_price',
                'sm.sponsor_id',
                'cm.child_id',
                'sm.full_name as sponsor_name',
                'sm.address as sponsor_address',
                'sm.no_hp',
                'sm.email'
            )
            ->get();
        $data['orders'] = $orders;
        $data['orderhd'] = $orderhd;
        $data['title'] = "Detail Donasi Anak";

        return view('sponsor.childdetaildonation', $data);
    }

    public function projectdetaildonation($id)
    {

        $orderproject = OrderProject::where('order_project_id', $id)
            ->join('sponsor_master as sm', 'sm.sponsor_id', '=', 'order_project.sponsor_id')
            ->join('project_master as pm', 'pm.project_id', '=', 'order_project.project_id')
            ->select('order_project.*', 'sm.*', 'pm.*', 'order_project.created_at as op_created_at')
            ->first();
        if ($orderproject == null) {
            return redirect(url('project-donation'))->with(['error' => 'Order proyek yang dimaksud tidak ditemukan.']);
        }
        $data['orders'] = $orderproject;
        $data['title'] = "Detail Donasi Proyek";
        return view('sponsor.projectdetaildonation', $data);
    }

    public function listdlp($id)
    {

        $user = auth()->user();
        $userId = $user->sponsor_id;

        // $downloaddlp = DataOrder::where('sponsor_id',$userId)
        //                     ->join('order_dt as odt','odt.order_id','=','order_hd.order_id')
        //                     ->join('child_master as cm','cm.child_id','=','odt.child_id')
        //                     ->join('dlp as dl','dl.child_id','=','cm.child_id')
        //                     ->select('cm.child_id','cm.full_name','dl.file_dlp','dl.created_at')
        //                     ->where('cm.child_id',$id)
        //                     ->orderBy('dl.dlp_id','desc')->paginate(5);
        $downloaddlp = ChildMaster::where('child_master.child_id', $id)
            ->join('dlp as dl', 'dl.child_id', '=', 'child_master.child_id')
            ->whereNull('dl.deleted_at')
            ->select('.child_master.child_id', 'full_name', 'dl.file_dlp', 'dl.created_at')
            ->paginate(5);
        $data['dlp'] = $downloaddlp;
        $data['title'] = "List DLP";

        return view('sponsor.listdlp', $data);
    }
}
