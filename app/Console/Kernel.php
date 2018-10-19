<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use \App\Events\notice;
use \App\Task;
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $this->tasks=Task::all();        
        foreach($this->tasks as $key=>$val){
            $action=$val->type;
            $this->val=$val;
            $schedule->$action(function(){
                $val=$this->val;
                if($val->type=='call'){
                    //$json=json_decode($val->json);
                    //eval($json->script);
                    eval($val->json);
                }
            })->cron($val->plan)->runInBackground();
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
