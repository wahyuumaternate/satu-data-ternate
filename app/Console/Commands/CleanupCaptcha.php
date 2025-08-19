<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\CaptchaPuzzleController;

class CleanupCaptcha extends Command
{
    protected $signature = 'captcha:cleanup';
    protected $description = 'Clean up old captcha temporary files';
    
    public function handle(): int
    {
        $this->info('Starting captcha cleanup...');
        
        $controller = new CaptchaPuzzleController();
        $response = $controller->cleanup();
        
        $data = $response->getData(true);
        
        if (isset($data['message'])) {
            $this->info($data['message']);
            
            if (isset($data['deleted_files'])) {
                $this->info("Files deleted: {$data['deleted_files']}");
            }
            
            return self::SUCCESS;
        } else {
            $this->error('Cleanup failed: ' . ($data['error'] ?? 'Unknown error'));
            return self::FAILURE;
        }
    }
}