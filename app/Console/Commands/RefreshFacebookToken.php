<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Admin\DashboardController;

class RefreshFacebookToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facebook:refresh-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh Facebook long-lived access token';

    /**
     * Execute the console command.
     */
    public function handle()
    {
         $controller = new DashboardController();
        $result = $controller->refreshFacebookToken();

        if ($result) {
            $this->info("Token refreshed successfully");
        } else {
            $this->error("Failed to refresh token");
        }
    }
}
