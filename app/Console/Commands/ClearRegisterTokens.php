<?php

namespace App\Console\Commands;

use App\Supports\ResponseSupport;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClearRegisterTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-register-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear expired registration tokens.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            DB::table('user_registration_tokens')->where('expired_at', '<',  now())->delete();
            $this->info('Expired tokens was cleared.');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $this->info('Error | ' . $e->getMessage());
        }
    }
}
