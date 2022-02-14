<?php

namespace App\Console\Commands;

use App\Http\Models\CRM\CRMCount as CRMCRMCount;
use App\Http\Models\CRM\CrmRequest;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CRMCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate CRM Count Report';

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
     * @return mixed
     */
    public function handle()
    {
        
        if(Carbon::today()->format('D') != 'Sun'){
            if(Carbon::today()->format('D') == 'Mon'){
                $to = Carbon::today()->hour(17)->minute(29)->second(59);
                $from = Carbon::yesterday()->hour(17)->minute(31); // yesterday +1
                $new_launced = CrmRequest::join('crm_request_status_histories as crmsh','crmsh.crm_request_id','=','crm_requests.id')
                                ->where('crm_requests.case_nature_id','<>',4)
                                ->where('crmsh.status_id',1)
                                ->whereBetween('crmsh.created_at', [$from,$to])
                                ->count();
        
                $closed = CrmRequest::join('crm_request_status_histories as crmsh','crmsh.crm_request_id','=','crm_requests.id')
                                ->where('crm_requests.case_nature_id','<>',4)
                                ->where('crmsh.status_id',4)
                                ->whereBetween('crmsh.created_at', [$from,$to])
                                ->count();
        
                $pending = CRMCRMCount::whereDate('date',Carbon::yesterday()->toDateString()); // yesterday + 1
                
                if($pending->exists()){
                    $pending = $pending->first();
                    $pending_count = (($pending->pending + $pending->new_launched) - $pending->closed);
        
                }else{
                    $pending_count = 0;
        
                }
                CRMCRMCount::create([
                    'pending' => $pending_count,
                    'new_launched' => $new_launced,
                    'closed' => $closed,
                    'date' => Carbon::today(),
                    
                ]);
            }else{
                $to = Carbon::today()->hour(17)->minute(29)->second(59);
                $from = Carbon::yesterday()->hour(17)->minute(31);
                $new_launced = CrmRequest::join('crm_request_status_histories as crmsh','crmsh.crm_request_id','=','crm_requests.id')
                                ->where('crm_requests.case_nature_id','<>',4)
                                ->where('crmsh.status_id',1)
                                ->whereBetween('crmsh.created_at', [$from,$to])
                                ->count();
        
                $closed = CrmRequest::join('crm_request_status_histories as crmsh','crmsh.crm_request_id','=','crm_requests.id')
                                ->where('crm_requests.case_nature_id','<>',4)
                                ->where('crmsh.status_id',4)
                                ->whereBetween('crmsh.created_at', [$from,$to])
                                ->count();
        
                $pending = CRMCRMCount::whereDate('date',Carbon::yesterday()->toDateString());
                
                if($pending->exists()){
                    $pending = $pending->first();
                    $pending_count = (($pending->pending + $pending->new_launched) - $pending->closed);
        
                }else{
                    $pending_count = 0;
        
                }
                CRMCRMCount::create([
                    'pending' => $pending_count,
                    'new_launched' => $new_launced,
                    'closed' => $closed,
                    'date' => Carbon::today(),
                    
                ]);
            }

        }
    }
}
