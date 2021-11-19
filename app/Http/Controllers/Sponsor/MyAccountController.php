<?php

namespace App\Http\Controllers\Sponsor;

use App\Http\Controllers\Controller;
use App\Models\OrderHd;
use App\Models\OrderProject;
use App\Models\Sponsor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class MyAccountController extends Controller
{
    //
    public function index()
    {
        return view('sponsor.dashboard');
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
}
