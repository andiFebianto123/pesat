<?php

namespace App\Http\Controllers\Sponsor;

use App\Http\Controllers\Controller;
use App\Models\OrderHd;
use App\Models\OrderProject;
use App\Models\Sponsor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class MyAccountController extends Controller
{
    //
    public function index()
    {
        $getemail = Session::get('key');
        $getuser = Sponsor::where('email', $getemail)->first();
           
        $data["user"] =  $getuser;
        return view('sponsor.dashboard',$data);
    }

    public function childDonation()
    {

        $getemail = Session::get('key');
        $getuser = Sponsor::where('email', $getemail)
            ->first();

        $getOrder = OrderHd::where('sponsor_id', $getuser->sponsor_id)
            ->get();
        $data['orders'] = $getOrder;

        return view('sponsor.childdonation', $data);
    }

    public function projectDonation()
    {
        $getemail   = Session::get('key');
        $getuser    = Sponsor::where('email',$getemail)->first();

        $getprojectorder    = OrderProject::where('sponsor_id',$getuser->sponsor_id)
                                ->get();
        $data['projectorders']   = $getprojectorder;
        return view('sponsor.projectdonation',$data);
    }
    public function editaccount()
    {
        $getemail = Session::get('key');
        $getuser = Sponsor::where('email', $getemail)
            ->first();

        $data['profile'] = $getuser;

        return view('sponsor.editaccount',$data);
    }
    public function updateaccount(Request $request){


        $request->validate([
            'password' => 'nullable|confirmed|min:6'
        ]);

        $id             = $request->sponsorid;
        $fullname       = $request->fullname;
        $hometown       = $request->hometown;
        $dateofbirth    = date('Y-m-d',strtotime($request->dateofbirth));
        $email          = $request->email;
        $password       = $request->password;
        $nohp           = $request->nohp;
        $churchmember   = $request->churchmember;
        $address        = $request->address;
       

        $sponsor = Sponsor::where('sponsor_id',$id)->first();

        $sponsor->full_name          = $fullname;
        $sponsor->hometown      = $hometown;
        $sponsor->date_of_birth = $dateofbirth;
        $sponsor->email         = $email;
        if($request->filled('password')) {
        $sponsor->password      = bcrypt($password);
        } 
        $sponsor->address       = $address;
        $sponsor->no_hp         = $nohp;
        $sponsor->church_member_of = $churchmember;

        $sponsor->save();

        return   redirect()->back()->with(['success' => 'Data Berhasil Di Update !!']);

    }

    public function forgotpassword(){
        return view('sponsor.forgotpassword');
    }
    public function resetpassword(Request $request){
     $getUser =  Sponsor::where("email", "=", $request->email)->first();
        if($getUser){
            $length=8;
            $newpass='';
            $generatepass =$this->generatepassword($length,$newpass);
            

            $data["email"] = $request->email;
            $data["title"] = "Reset Password";
            $data["body"] = "This is Demo";
            $data["generatepass"]=$generatepass;

 
            Mail::send('Email.ResetPassword', $data, function($message)use($data) {
                $message->to($data["email"], $data["email"])
                                ->subject($data["title"]);
                });            

            
            $getUser->password= bcrypt($generatepass);
            $getUser->save();
            
            return   redirect()->back()->with(['success' => 'Password berhasil direset, silahkan cek email anda !!']);            

        }else{
            return   redirect()->back()->with(['error' => 'Email yang anda masukan tidak terdaftar !!']);            
        }

    }

    function generatepassword($length,$newpass) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $newpass = '';
        for ($i = 0; $i < $length; $i++) {
            $newpass .= $characters[rand(0, $charactersLength - 1)];
        }
        return $newpass;
    }

    public function register(){
        return view('sponsor.register');
    }

    public function createaccount(Request $request){

        $request->validate([
            'password' => 'nullable|confirmed|min:6'
        ]);
        
        $cekEmail = Sponsor::where('email',$request->email)->first();

       
        if($cekEmail){
        return   redirect()->back()->with(['error' => 'Email sudah terdaftar , silahkan gunakan email lain !!']);       
        }else{
        $insertsponsor = new Sponsor();
        $insertsponsor->name             = $request->name;
        $insertsponsor->full_name        = $request->fullname;
        $insertsponsor->hometown         = $request->hometown;
        $insertsponsor->date_of_birth    = $request->dateofbirth;
        $insertsponsor->address          = $request->address;
        $insertsponsor->email            = $request->email;
        $insertsponsor->password         = bcrypt($request->password);
        $insertsponsor->no_hp            = $request->nohp;
        $insertsponsor->church_member_of = $request->memberofchurch;
        $insertsponsor->save();

        return   redirect()->back()->with(['success' => 'Akun berhasil dibuat , silahkan login !!']);       
        }
    }

    public function childdetaildonation($id){
        
        $orders = OrderHd::where('order_hd.order_id',$id)
                    ->join('order_dt as odt','odt.order_id','=','order_hd.order_id')
                    ->join('child_master as cm','cm.child_id','=','odt.child_id')
                    ->join('sponsor_master as sm','sm.sponsor_id','=','order_hd.sponsor_id')
                    ->select('order_hd.order_id','order_hd.total_price','order_hd.payment_status','order_hd.snap_token','order_hd.status_midtrans','order_hd.created_at','order_hd.payment_type',
                            'odt.order_dt_id','odt.price as price_dt','odt.monthly_subscription','cm.full_name as child_name','cm.price as child_price',
                            'sm.sponsor_id','sm.full_name as sponsor_name','sm.address as sponsor_address','sm.no_hp','sm.email'
                            )
                    ->get();
        $orderhd = OrderHd::where('order_id',$id)->first();
       
        $data['orders'] = $orders;
        $data['orderhd']= $orderhd;


        return view('sponsor.childdetaildonation',$data);
    }

    public function projectdetaildonation($id){
        
        $orderproject = OrderProject::where('order_project_id',$id)
                        ->join('sponsor_master as sm','sm.sponsor_id','=','order_project.sponsor_id')
                        ->join('project_master as pm','pm.project_id','=','order_project.project_id')
                        ->first();

        $data['orders'] = $orderproject;
        return view('sponsor.projectdetaildonation',$data); 
    }

}
