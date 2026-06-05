<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Sauvegarde quotidienne à 2h du matin
        $schedule->command('db:backup')->daily()->at('02:00');
        
        // Sauvegarde supplémentaire à midi (optionnel)
        // $schedule->command('db:backup')->dailyAt('12:00');
        
        // Nettoyage des vieilles sauvegardes (optionnel)
        // $schedule->command('db:backup:clean')->weekly();
    }
    
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}