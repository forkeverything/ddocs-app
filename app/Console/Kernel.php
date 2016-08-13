<?php

namespace App\Console;

use App\Jobs\ReplenishUserCredits;
use App\Jobs\SendLateFileReminders;
use App\Jobs\SendUpcomingDueFileReminders;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    use DispatchesJobs;

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Commands\Inspire::class,
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

        // Let recipients know on Monday night that they have late files. Because generally
        // people are more productive on Tues mornings.
        $schedule->call(function () {
            $this->dispatch(new SendLateFileReminders);
        })->weekly()->mondays()->at('21:00');

        // Every morning, send upcoming file reminders at 06:00
        $schedule->call(function () {
            $this->dispatch(new SendUpcomingDueFileReminders);
        })->dailyAt('06:00');

        // Replenish user credits to 5 / month
        $schedule->call(function () {
            $this->dispatch(new ReplenishUserCredits);
        })->monthlyOn(1, '00:00');

        /*
         * TODO ::: Maybe we can move dispatching the jobs into an Artisan command and have
         * the scheduler call the command. But maybe the extra abstraction is unecessary
         * unless we're going to be calling the job manually too.
         */
    }
}
