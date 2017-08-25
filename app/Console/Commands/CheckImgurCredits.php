<?php

namespace App\Console\Commands;

use App\Libs\Imgur\API as ImgurAPI;
use Illuminate\Console\Command;

class CheckImgurCredits extends Command
{
    protected $signature = 'app:checkimgurcredits';
    protected $description = 'Checks Imgur credits';

    public function handle()
    {
        $credits = ImgurAPI::getCredits();
        if (!$credits) {
            $this->error('Failed to get credits!');
        }

        $this->info(sprintf(
            'User: %u/%u, Resets: %s, Client: %u/%u',
            $credits->UserRemaining,
            $credits->UserLimit,
            number_format(($credits->UserReset - time()) / 60, 0) . ' mins',
            $credits->ClientRemaining,
            $credits->ClientLimit
        ));
    }
}
