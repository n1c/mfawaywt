<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\CheckImgurCredits::class,
        \App\Console\Commands\CheckPosts::class,
        \App\Console\Commands\CrawlSubreddit::class,
        \App\Console\Commands\UpdateNewestPost::class,
        \App\Console\Commands\UpdateLastUpdatedPost::class,
        \App\Console\Commands\RepollPosts::class,
        \App\Console\Commands\ReparseComments::class,
        \App\Console\Commands\ManageCommentImages::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('app:updatenewestpost')->everyFiveMinutes();
        $schedule->command('app:updatelastupdatedpost')->everyFiveMinutes();
        $schedule->command('app:checkposts')->everyTenMinutes();
    }

    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
