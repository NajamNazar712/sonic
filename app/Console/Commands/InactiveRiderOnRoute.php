<?php

namespace App\Console\Commands;
use Carbon\Carbon;
use App\Http\Controllers\abcController;
use Illuminate\Console\Command;

class InactiveRiderOnRoute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:inactiverideronroutereport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rider Who Are In Active For Two Days or More Report';

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

        
        // echo $rdate->name;

        $test = abcController::index();
        $date = Carbon::now()->subDays(3)->format('Y-m-d');
        if(1==1){
            echo("<table>    
            <thead>
                <tr>
                    <td> <strong>Id</strong>   </td>
                    <td><strong>Employee</strong></td>
                </tr>
            </thead>
            <tbody>
            ");
             
                
            foreach ($test as $testvalue) {
                echo ("<tr>
                <td> ".
                 $testvalue->id .
                 "</td><td> ".
                  $testvalue->name .
                 "</td></tr>");
              }
              echo ("</tbody>
              </table>");
              
            
           

        }
        else{
            echo("<table>    
            <thead>
                <tr>
                    <td> <strong>Id</strong>   </td>
                    <td><strong>Employee</strong></td>
                </tr>
            </thead>
            <tbody>
            ");
             
                
            foreach ($test as $testvalue) {
                echo ("<tr>
                <td> ".
                 $testvalue->id .
                 "</td><td> ".
                  $testvalue->name .
                 "</td></tr>");
              }
              echo ("</tbody>
              </table>");
              
            
           
        }
        echo("<table>    
        <thead>
            <tr>
                <td> <strong>Id</strong>   </td>
                <td><strong>Employee</strong></td>
            </tr>
        </thead>
        <tbody>
        ");
         
            
        foreach ($test as $testvalue) {
            echo ("<tr>
            <td> ".
             $testvalue->id .
             "</td><td> ".
              $testvalue->name .
             "</td></tr>");
          }
          echo ("</tbody>
          </table>");
          
        
          echo (Carbon::now()->toDateTimeString());
          
        
       
      

    }
}
