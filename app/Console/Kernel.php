<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        'App\Console\Commands\SendNotificationsPaymentsToExpirate',
        'App\Console\Commands\AddNewPolices',
        'App\Console\Commands\AddNewUsers',
        'App\Console\Commands\HappyBirthday',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        //DEBO DE CREAR OTRO DONDE ANTES DE ENVIAR LOS RECIBOS NO PAGADOS, HAGA UN BARRIDO DE CUALES YA ESTAN PAGADOS
        $schedule->command('zeus:AddNewUsers')->dailyAt("8");
        $schedule->command('zeus:AddNewUsers')->dailyAt("8:20");
        
        $schedule->command('zeus:AddNewPolices')->dailyAt("8:30");
        $schedule->command('zeus:AddNewPolices')->dailyAt("8:50");
        
        $schedule->command('zeus:paymentsToExpirate')->dailyAt("9");
        $schedule->command('zeus:paymentsToExpirate')->dailyAt("9:20");
        
        $schedule->command('zeus:SendMailHappyBirthday')->dailyAt("9");
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
