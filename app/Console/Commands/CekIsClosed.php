<?php

namespace App\Console\Commands;

use Exception;
use Carbon\Carbon;
use App\Models\ProjectMaster;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CekIsClosed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:isclosed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        DB::beginTransaction();
        try {
            $now = Carbon::now()->startOfDay();
            $datasOrder = ProjectMaster::where('is_closed', 0)->get();
            foreach ($datasOrder as $key => $data) {
                $amount = $data->amount;
                $lastAmount = $data->last_amount;
                $enddate = $data->end_date;
                if ($enddate != null) {
                    $enddate = Carbon::parse($enddate)->startOfDay();
                }
                if (($enddate != null & $now > $enddate) || $lastAmount >= $amount) {
                    $data->is_closed = 1;
                    $data->save();
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            Log::channel('cron')->info('ERROR CRON JOB CekIsClosed');
            Log::channel('cron')->error($e);
        }
    }
}
