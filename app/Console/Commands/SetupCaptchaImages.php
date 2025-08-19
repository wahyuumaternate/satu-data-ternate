<?php

// ============================================================================
// SIMPLE SETUP COMMAND - app/Console/Commands/SetupCaptchaImages.php
// ============================================================================

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SetupCaptchaImages extends Command
{
    protected $signature = 'captcha:setup-images {--count=5 : Number of sample images to create}';
    protected $description = 'Create sample captcha background images using GD';
    
    public function handle(): int
    {
        $count = (int) $this->option('count');
        
        if ($count < 1 || $count > 20) {
            $this->error('Count must be between 1 and 20');
            return self::FAILURE;
        }
        
        // Check if GD is available
        if (!extension_loaded('gd')) {
            $this->error('GD extension is not available. Please install php-gd.');
            return self::FAILURE;
        }
        
        $this->info("Creating {$count} sample captcha images using GD...");
        
        // Ensure directory exists
        Storage::makeDirectory('captcha-images');
        
        // Color schemes for variety
        $colorSchemes = [
            [[70, 130, 180], [135, 206, 235], [255, 255, 255]], // Blue tones
            [[60, 179, 113], [144, 238, 144], [255, 255, 255]], // Green tones
            [[255, 165, 0], [255, 218, 185], [255, 255, 255]],  // Orange tones
            [[147, 112, 219], [221, 160, 221], [255, 255, 255]], // Purple tones
            [[220, 20, 60], [255, 182, 193], [255, 255, 255]],   // Red tones
            [[30, 144, 255], [173, 216, 230], [255, 255, 255]],  // Sky blue
            [[255, 20, 147], [255, 182, 193], [255, 255, 255]],  // Pink tones
            [[50, 205, 50], [152, 251, 152], [255, 255, 255]],   // Lime green
            [[255, 215, 0], [255, 250, 205], [255, 255, 255]],   // Gold tones
            [[138, 43, 226], [186, 85, 211], [255, 255, 255]],   // Violet tones
        ];
        
        for ($i = 0; $i < $count; $i++) {
            $colors = $colorSchemes[$i % count($colorSchemes)];
            $this->createSampleImage($i + 1, $colors);
        }
        
        $this->newLine();
        $this->info('✅ Sample captcha images created successfully!');
        $this->info('📁 Location: storage/app/captcha-images/');
        $this->info('💡 You can replace these with your own 400x200 images.');
        
        return self::SUCCESS;
    }
    
    private function createSampleImage(int $index, array $colors): void
    {
        $width = 400;
        $height = 200;
        
        // Create base image
        $image = imagecreatetruecolor($width, $height);
        
        // Enable alpha blending for transparency effects
        imagealphablending($image, true);
        imagesavealpha($image, true);
        
        // Create gradient background
        $this->createGradientBackground($image, $colors, $width, $height);
        
        // Add geometric patterns
        $this->addGeometricPatterns($image, $colors, $width, $height);
        
        // Add some texture
        $this->addTexture($image, $width, $height);
        
        // Save image
        ob_start();
        imagepng($image, null, 8); // Compression level 8 for smaller files
        $imageData = ob_get_clean();
        imagedestroy($image);
        
        $filename = "background_{$index}.png";
        Storage::put("captcha-images/{$filename}", $imageData);
        
        $this->line("✓ Created: {$filename}");
    }
    
    private function createGradientBackground($image, array $colors, int $width, int $height): void
    {
        $startColor = $colors[0];
        $endColor = $colors[1];
        
        for ($y = 0; $y < $height; $y++) {
            $ratio = $y / $height;
            
            $r = (int) ($startColor[0] + ($endColor[0] - $startColor[0]) * $ratio);
            $g = (int) ($startColor[1] + ($endColor[1] - $startColor[1]) * $ratio);
            $b = (int) ($startColor[2] + ($endColor[2] - $startColor[2]) * $ratio);
            
            $color = imagecolorallocate($image, $r, $g, $b);
            imageline($image, 0, $y, $width, $y, $color);
        }
    }
    
    private function addGeometricPatterns($image, array $colors, int $width, int $height): void
    {
        $accentColor = imagecolorallocate($image, $colors[2][0], $colors[2][1], $colors[2][2]);
        $semiTransparent = imagecolorallocatealpha($image, $colors[2][0], $colors[2][1], $colors[2][2], 90);
        
        // Add circles
        for ($i = 0; $i < 15; $i++) {
            $x = rand(0, $width);
            $y = rand(0, $height);
            $size = rand(10, 40);
            
            if ($i % 2 == 0) {
                imagefilledellipse($image, $x, $y, $size, $size, $semiTransparent);
            } else {
                imageellipse($image, $x, $y, $size, $size, $accentColor);
            }
        }
        
        // Add rectangles
        for ($i = 0; $i < 8; $i++) {
            $x1 = rand(0, $width - 50);
            $y1 = rand(0, $height - 30);
            $x2 = $x1 + rand(20, 50);
            $y2 = $y1 + rand(15, 30);
            
            if ($i % 2 == 0) {
                imagefilledrectangle($image, $x1, $y1, $x2, $y2, $semiTransparent);
            } else {
                imagerectangle($image, $x1, $y1, $x2, $y2, $accentColor);
            }
        }
        
        // Add lines
        for ($i = 0; $i < 12; $i++) {
            $thickness = rand(1, 3);
            imagesetthickness($image, $thickness);
            imageline(
                $image,
                rand(0, $width), rand(0, $height),
                rand(0, $width), rand(0, $height),
                $accentColor
            );
        }
        
        // Reset line thickness
        imagesetthickness($image, 1);
    }
    
    private function addTexture($image, int $width, int $height): void
    {
        // Add noise texture for visual interest
        for ($i = 0; $i < 200; $i++) {
            $x = rand(0, $width - 1);
            $y = rand(0, $height - 1);
            $alpha = rand(100, 127); // High alpha = more transparent
            
            $noiseColor = imagecolorallocatealpha($image, 255, 255, 255, $alpha);
            imagesetpixel($image, $x, $y, $noiseColor);
        }
    }
}

// ============================================================================
// SIMPLE CAPTCHA CONTROLLER - app/Http/Controllers/CaptchaPuzzleController.php
// ============================================================================

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CaptchaPuzzleController extends Controller
{
    private const CAPTCHA_PATH = 'captcha-images';
    private const TEMP_PATH = 'temp/captcha';
    private const IMAGE_WIDTH = 400;
    private const IMAGE_HEIGHT = 200;
    private const PUZZLE_SIZE = 60;
    private const NOTCH_SIZE = 15;
    private const TOLERANCE = 8;
    
    public function generate(): JsonResponse
    {
        try {
            Log::info('Starting CAPTCHA generation');
            
            // Check if GD extension is loaded
            if (!extension_loaded('gd')) {
                return $this->errorResponse('GD extension is required but not available', 500);
            }
            
            // Clear previous captcha data
            $this->clearPreviousCaptcha();
            
            // Ensure directories exist
            $this->ensureDirectoriesExist();
            
            // Get random background image
            $backgroundImage = $this->getRandomBackgroundImage();
            if (!$backgroundImage) {
                // Create a quick test image if none exist
                $this->createQuickTestImage();
                $backgroundImage = $this->getRandomBackgroundImage();
                
                if (!$backgroundImage) {
                    return $this->errorResponse('No captcha images available. Please run: php artisan captcha:setup-images', 500);
                }
            }
            
            // Generate puzzle
            $puzzleData = $this->createPuzzle($backgroundImage);
            
            // Store puzzle data in session
            Session::put('captcha_puzzle', [
                'correct_x' => $puzzleData['correct_x'],
                'puzzle_id' => $puzzleData['puzzle_id'],
                'created_at' => now()->timestamp,
                'attempts' => 0
            ]);
            
            Log::info('CAPTCHA generated successfully', ['puzzle_id' => $puzzleData['puzzle_id']]);
            
            return response()->json([
                'success' => true,
                'background_image' => $puzzleData['background_url'],
                'puzzle_piece' => $puzzleData['puzzle_url'],
                'puzzle_y' => $puzzleData['puzzle_y'],
                'puzzle_id' => $puzzleData['puzzle_id']
            ]);
            
        } catch (\Exception $e) {
            Log::error('CAPTCHA generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse('Failed to generate CAPTCHA: ' . $e->getMessage(), 500);
        }
    }
    
    public function verify(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'x_position' => ['required', 'numeric', 'min:0', 'max:400'],
                'puzzle_id' => ['required', 'string', 'max:20']
            ]);
            
            $sessionData = Session::get('captcha_puzzle');
            
            if (!$this->isValidSession($sessionData, $validated['puzzle_id'])) {
                return $this->failedVerification('Invalid or expired captcha');
            }
            
            // Check attempt limits
            if ($sessionData['attempts'] >= 3) {
                return $this->failedVerification('Too many attempts', true);
            }
            
            // Increment attempts
            $sessionData['attempts']++;
            Session::put('captcha_puzzle', $sessionData);
            
            // Verify position
            $userX = (int) $validated['x_position'];
            $correctX = $sessionData['correct_x'];
            
            if (abs($userX - $correctX) <= self::TOLERANCE) {
                $this->clearCaptchaSession();
                $this->cleanupTempFiles($sessionData['puzzle_id']);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Captcha verified successfully'
                ]);
            } else {
                if ($sessionData['attempts'] >= 2) {
                    return $this->failedVerification('Incorrect position', true);
                } else {
                    return $this->failedVerification('Incorrect position. Try again.');
                }
            }
            
        } catch (\Exception $e) {
            Log::error('CAPTCHA verification failed', ['error' => $e->getMessage()]);
            return $this->errorResponse('Verification failed', 500);
        }
    }
    
    private function ensureDirectoriesExist(): void
    {
        if (!Storage::exists(self::CAPTCHA_PATH)) {
            Storage::makeDirectory(self::CAPTCHA_PATH);
        }
        
        if (!Storage::exists(self::TEMP_PATH)) {
            Storage::makeDirectory(self::TEMP_PATH);
        }
    }
    
    private function createQuickTestImage(): void
    {
        try {
            $image = imagecreatetruecolor(self::IMAGE_WIDTH, self::IMAGE_HEIGHT);
            
            // Create a simple blue gradient
            $blue1 = imagecolorallocate($image, 70, 130, 180);
            $blue2 = imagecolorallocate($image, 135, 206, 235);
            $white = imagecolorallocate($image, 255, 255, 255);
            
            // Fill with gradient
            for ($y = 0; $y < self::IMAGE_HEIGHT; $y++) {
                $ratio = $y / self::IMAGE_HEIGHT;
                $r = 70 + (135 - 70) * $ratio;
                $g = 130 + (206 - 130) * $ratio;
                $b = 180 + (235 - 180) * $ratio;
                
                $color = imagecolorallocate($image, (int)$r, (int)$g, (int)$b);
                imageline($image, 0, $y, self::IMAGE_WIDTH, $y, $color);
            }
            
            // Add some simple shapes
            for ($i = 0; $i < 10; $i++) {
                $x = rand(0, self::IMAGE_WIDTH);
                $y = rand(0, self::IMAGE_HEIGHT);
                $size = rand(15, 35);
                imagefilledellipse($image, $x, $y, $size, $size, $white);
            }
            
            // Save image
            ob_start();
            imagepng($image);
            $imageData = ob_get_clean();
            imagedestroy($image);
            
            Storage::put(self::CAPTCHA_PATH . '/quick_test.png', $imageData);
            Log::info('Created quick test image');
            
        } catch (\Exception $e) {
            Log::error('Failed to create quick test image: ' . $e->getMessage());
        }
    }
    
    private function getRandomBackgroundImage(): ?string
    {
        try {
            $images = Storage::files(self::CAPTCHA_PATH);
            $validImages = array_filter($images, function($file) {
                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                return in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
            });
            
            return empty($validImages) ? null : $validImages[array_rand($validImages)];
            
        } catch (\Exception $e) {
            Log::error('Error getting background image: ' . $e->getMessage());
            return null;
        }
    }
    
    private function createPuzzle(string $backgroundImagePath): array
    {
        $puzzleId = Str::random(12);
        
        // Load background image
        $sourceImage = $this->loadImage($backgroundImagePath);
        
        // Generate puzzle position
        $minX = 30;
        $maxX = self::IMAGE_WIDTH - self::PUZZLE_SIZE - 30;
        $correctX = rand($minX, $maxX);
        $puzzleY = rand(20, self::IMAGE_HEIGHT - self::PUZZLE_SIZE - 20);
        
        // Create puzzle piece and background with hole
        $puzzlePiece = $this->createPuzzlePiece($sourceImage, $correctX, $puzzleY);
        $backgroundWithHole = $this->createBackgroundWithHole($sourceImage, $correctX, $puzzleY);
        
        // Save temporary files
        $paths = $this->saveTempFiles($puzzleId, $backgroundWithHole, $puzzlePiece);
        
        imagedestroy($sourceImage);
        
        return [
            'correct_x' => $correctX,
            'puzzle_y' => $puzzleY,
            'puzzle_id' => $puzzleId,
            'background_url' => $paths['background_url'],
            'puzzle_url' => $paths['puzzle_url']
        ];
    }
    
    private function loadImage(string $imagePath)
    {
        $fullPath = Storage::path($imagePath);
        $imageInfo = getimagesize($fullPath);
        
        if (!$imageInfo) {
            throw new \Exception("Invalid image file: {$imagePath}");
        }
        
        switch ($imageInfo['mime']) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($fullPath);
                break;
            case 'image/png':
                $source = imagecreatefrompng($fullPath);
                break;
            case 'image/gif':
                $source = imagecreatefromgif($fullPath);
                break;
            default:
                throw new \Exception("Unsupported image type: {$imageInfo['mime']}");
        }
        
        if (!$source) {
            throw new \Exception("Failed to load image: {$imagePath}");
        }
        
        // Resize to standard size
        $resized = imagecreatetruecolor(self::IMAGE_WIDTH, self::IMAGE_HEIGHT);
        imagecopyresampled(
            $resized, $source,
            0, 0, 0, 0,
            self::IMAGE_WIDTH, self::IMAGE_HEIGHT,
            imagesx($source), imagesy($source)
        );
        
        imagedestroy($source);
        return $resized;
    }
    
    private function createPuzzlePiece($sourceImage, int $x, int $y): string
    {
        // Create puzzle piece canvas
        $canvas = imagecreatetruecolor(self::PUZZLE_SIZE + self::NOTCH_SIZE, self::PUZZLE_SIZE);
        
        // Enable transparency
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefill($canvas, 0, 0, $transparent);
        imagealphablending($canvas, true);
        
        // Copy main piece
        imagecopy($canvas, $sourceImage, 0, 0, $x, $y, self::PUZZLE_SIZE, self::PUZZLE_SIZE);
        
        // Add notch
        $notchY = $y + (self::PUZZLE_SIZE / 2) - (self::NOTCH_SIZE / 2);
        if ($x + self::PUZZLE_SIZE + self::NOTCH_SIZE < self::IMAGE_WIDTH) {
            imagecopy(
                $canvas, $sourceImage,
                self::PUZZLE_SIZE, self::NOTCH_SIZE,
                $x + self::PUZZLE_SIZE, $notchY,
                self::NOTCH_SIZE, self::NOTCH_SIZE
            );
        }
        
        // Add border
        $border = imagecolorallocate($canvas, 255, 255, 255);
        imagerectangle($canvas, 0, 0, self::PUZZLE_SIZE + self::NOTCH_SIZE - 1, self::PUZZLE_SIZE - 1, $border);
        
        // Convert to string
        ob_start();
        imagepng($canvas);
        $data = ob_get_clean();
        imagedestroy($canvas);
        
        return $data;
    }
    
    private function createBackgroundWithHole($sourceImage, int $x, int $y): string
    {
        $result = imagecreatetruecolor(self::IMAGE_WIDTH, self::IMAGE_HEIGHT);
        imagecopy($result, $sourceImage, 0, 0, 0, 0, self::IMAGE_WIDTH, self::IMAGE_HEIGHT);
        
        // Create hole
        $holeColor = imagecolorallocatealpha($result, 0, 0, 0, 50);
        imagefilledrectangle($result, $x, $y, $x + self::PUZZLE_SIZE - 1, $y + self::PUZZLE_SIZE - 1, $holeColor);
        
        // Add notch hole
        $notchY = $y + (self::PUZZLE_SIZE / 2) - (self::NOTCH_SIZE / 2);
        if ($x + self::PUZZLE_SIZE + self::NOTCH_SIZE < self::IMAGE_WIDTH) {
            $centerX = $x + self::PUZZLE_SIZE + (self::NOTCH_SIZE/2);
            $centerY = $notchY + (self::NOTCH_SIZE/2);
            imagefilledellipse($result, $centerX, $centerY, self::NOTCH_SIZE, self::NOTCH_SIZE, $holeColor);
        }
        
        // Add border
        $border = imagecolorallocate($result, 150, 150, 150);
        imagerectangle($result, $x, $y, $x + self::PUZZLE_SIZE - 1, $y + self::PUZZLE_SIZE - 1, $border);
        
        ob_start();
        imagepng($result);
        $data = ob_get_clean();
        imagedestroy($result);
        
        return $data;
    }
    
    private function saveTempFiles(string $puzzleId, string $backgroundData, string $puzzleData): array
    {
        $backgroundPath = self::TEMP_PATH . "/{$puzzleId}_bg.png";
        $puzzlePath = self::TEMP_PATH . "/{$puzzleId}_piece.png";
        
        Storage::put($backgroundPath, $backgroundData);
        Storage::put($puzzlePath, $puzzleData);
        
        return [
            'background_url' => Storage::url($backgroundPath),
            'puzzle_url' => Storage::url($puzzlePath)
        ];
    }
    
    private function isValidSession(?array $sessionData, string $puzzleId): bool
    {
        if (!$sessionData || !isset($sessionData['puzzle_id'], $sessionData['created_at'])) {
            return false;
        }
        
        if ($sessionData['puzzle_id'] !== $puzzleId) {
            return false;
        }
        
        // Check expiration (5 minutes)
        if (now()->timestamp - $sessionData['created_at'] > 300) {
            return false;
        }
        
        return true;
    }
    
    private function failedVerification(string $message, bool $generateNew = false): JsonResponse
    {
        $this->clearCaptchaSession();
        
        $response = ['success' => false, 'message' => $message];
        
        if ($generateNew) {
            try {
                $newCaptcha = $this->generate();
                if ($newCaptcha->getStatusCode() === 200) {
                    $response['new_captcha'] = $newCaptcha->getData(true);
                }
            } catch (\Exception $e) {
                Log::error('Failed to generate new captcha: ' . $e->getMessage());
            }
        }
        
        return response()->json($response);
    }
    
    private function clearPreviousCaptcha(): void
    {
        $sessionData = Session::get('captcha_puzzle');
        if ($sessionData && isset($sessionData['puzzle_id'])) {
            $this->cleanupTempFiles($sessionData['puzzle_id']);
        }
    }
    
    private function clearCaptchaSession(): void
    {
        Session::forget('captcha_puzzle');
    }
    
    private function cleanupTempFiles(string $puzzleId): void
    {
        $files = [
            self::TEMP_PATH . "/{$puzzleId}_bg.png",
            self::TEMP_PATH . "/{$puzzleId}_piece.png"
        ];
        
        foreach ($files as $file) {
            if (Storage::exists($file)) {
                Storage::delete($file);
            }
        }
    }
    
    public function cleanup(): JsonResponse
    {
        try {
            $deleted = 0;
            $cutoff = now()->subHour()->timestamp;
            
            if (Storage::exists(self::TEMP_PATH)) {
                $tempFiles = Storage::files(self::TEMP_PATH);
                
                foreach ($tempFiles as $file) {
                    if (Storage::lastModified($file) < $cutoff) {
                        Storage::delete($file);
                        $deleted++;
                    }
                }
            }
            
            return response()->json([
                'message' => 'Cleanup completed successfully',
                'deleted_files' => $deleted
            ]);
            
        } catch (\Exception $e) {
            Log::error('Cleanup failed: ' . $e->getMessage());
            return $this->errorResponse('Cleanup failed', 500);
        }
    }
    
    private function errorResponse(string $message, int $status = 400): JsonResponse
    {
        return response()->json(['success' => false, 'error' => $message], $status);
    }
}

// ============================================================================
// SIMPLE COMMANDS TO RUN
// ============================================================================

/*
1. Copy the code above to replace your existing files

2. Run these commands:
   php artisan config:clear
   php artisan route:clear  
   php artisan storage:link

3. Create sample images:
   php artisan captcha:setup-images --count=5

4. Test the system:
   - Go to login page
   - Click login button
   - Try the CAPTCHA puzzle

5. Check Laravel logs if issues persist:
   tail -f storage/logs/laravel.log
*/