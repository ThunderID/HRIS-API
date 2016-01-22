<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //do something
        'App\Console\Commands\ScheduleCommand',

        //cron
        'App\Console\Commands\HRQueueCommand',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //running queue table jobs (every five minutes)
        $schedule->command('hr:queue HRQueueCommand')
                 ->everyFiveMinutes();
    }
}
