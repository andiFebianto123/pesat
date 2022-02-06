<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Carbon\Carbon;
use App\Models\Dlp;
use App\Mail\SendEmailDlp;
use App\Models\ChildMaster;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class SendDlp extends Controller
{
    //
    public function sendEmail($child_id, $dlp_id)
    {

        $getchild = Dlp::where('dlp_id', $dlp_id)
            ->where('cm.child_id', $child_id)
            ->join('child_master as cm', 'cm.child_id', '=', 'dlp.child_id')
            ->first();

        if ($getchild !== null) {
            $now = Carbon::now()->startOfDay();
            $isSponsored = ChildMaster::getStatusSponsor($getchild->child_id, $now);
            if ($isSponsored) {

                $getEmail = ChildMaster::
                    join('order_dt as dt', 'dt.child_id', '=', 'child_master.child_id')
                    ->join('order_hd as ohd', 'ohd.order_id', '=', 'dt.order_id')
                    ->join('sponsor_master as sm', 'sm.sponsor_id', '=', 'ohd.sponsor_id')
                    ->join('dlp as dl', 'dl.child_id', '=', 'child_master.child_id')
                    ->whereDate('dt.start_order_date', '<=', $now)
                    ->whereDate('dt.end_order_date', '>=', $now)
                    ->where('ohd.payment_status', '<=', 2)
                    ->whereNull('dt.deleted_at')
                    ->where('dl.dlp_id', $dlp_id)
                    ->select('ohd.*', 'dl.*', 'child_master.full_name as child_name', 'sm.full_name as sponsor_name', 'sm.email')
                    ->first();

                $file = $getchild->file_dlp;
                $email = $getEmail->email;
                $childname = $getEmail->child_name;
                $sponsorname = $getEmail->sponsor_name;

                $emailData = [
                    'title' => 'Data Laporan Perkembangan',
                    'email' => $email,
                    'filedlp' => $file,
                    'child_name' => $childname,
                    'sponsor_name' => $sponsorname,
                ];

                try {
                    // Validate the value...
                    Mail::to($emailData['email'])->send(new SendEmailDlp($emailData));

                    $getchild->deliv_status = 2;
                    $getchild->save();

                    \Alert::add('success', 'Email was successfully sent.')->flash();
                    return redirect(backpack_url('dlp/' . $child_id . '/detail'));
                } catch (Exception $e) {

                    $getchild->deliv_status = 3;
                    $getchild->save();

                    \Alert::add('error', 'Email was failed to sent')->flash();

                    return redirect(backpack_url('dlp/' . $child_id . '/detail'));

                }
            } else {
                \Alert::add('error', "The child don't have a online sponsor.")->flash();

                return redirect(backpack_url('dlp/' . $child_id . '/detail'));
            }
        } else {
            \Alert::add('error', 'Data DLP tidak ditemukan.')->flash();

            return redirect(backpack_url('dlp/' . $child_id . '/detail'));
        }
    }
}
