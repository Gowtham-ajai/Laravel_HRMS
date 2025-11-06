<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AttendanceController;

class AutoMarkAbsentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-mark-absent-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically mark absent employees at the end of the day';

    /**
     * Execute the console command.
     */
    public function handle()
    {
         $this->info('Starting auto-mark absent process...');
        
        try {
            $controller = new AttendanceController();
            $result = $controller->autoMarkAbsent();
            
            if ($result->getData()->success) {
                $this->info('Auto-mark absent completed successfully: ' . $result->getData()->message);
                Log::info('Auto-mark absent completed: ' . $result->getData()->message);
            } else {
                $this->error('Auto-mark absent failed: ' . $result->getData()->message);
                Log::error('Auto-mark absent failed: ' . $result->getData()->message);
            }
            
        } catch (\Exception $e) {
            $this->error('Error in auto-mark absent: ' . $e->getMessage());
            Log::error('Auto-mark absent error: ' . $e->getMessage());
        }
        
        return Command::SUCCESS;
    }
}
