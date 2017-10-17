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
        \App\Console\Commands\KeyGenerateCommand::class,
        \App\Console\Commands\ControllerMakeCommand::class,
        \App\Console\Commands\MiddlewareMakeCommand::class,
        \App\Console\Commands\ModelMakeCommand::class,
        \App\Console\Commands\StorageLinkCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}
