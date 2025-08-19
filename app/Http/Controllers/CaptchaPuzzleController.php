<?php

// ============================================================================
// FIXED CONTROLLER - Update method loadImage dan getRandomBackgroundImage
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
                return $this->errorResponse('No captcha images available. Please add images to storage/app/captcha-images/', 500);
            }
            
            Log::info('Selected background image', ['path' => $backgroundImage]);
            
            // Generate puzzle
            $puzzleData = $this->createPuzzle($backgroundImage);
            
            // Store puzzle data in session
            Session::put('captcha_puzzle', [
                'correct_x' => $puzzleData['correct_x'],
                'puzzle_id' => $puzzleData['puzzle_id'],
                'created_at' => now()->timestamp,
                'attempts' => 0
            ]);
            
            Log::info('CAPTCHA generated successfully', [
                'puzzle_id' => $puzzleData['puzzle_id'],
                'background_image' => $backgroundImage,
                'correct_x' => $puzzleData['correct_x']
            ]);
            
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
    
    // Method verify dan lainnya tetap sama...
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
    
    public function serveImage(Request $request, string $filename)
    {
        try {
            $filePath = self::TEMP_PATH . '/' . $filename;
            
            if (!Storage::exists($filePath)) {
                abort(404, 'Image not found');
            }
            
            $fileContents = Storage::get($filePath);
            $mimeType = 'image/png';
            
            return response($fileContents)
                ->header('Content-Type', $mimeType)
                ->header('Content-Length', strlen($fileContents))
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
                
        } catch (\Exception $e) {
            Log::error('Error serving image', ['filename' => $filename, 'error' => $e->getMessage()]);
            abort(404);
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
    
    /**
     * FIXED: Get random background image - prioritize existing images
     */
    private function getRandomBackgroundImage(): ?string
    {
        try {
            // Get all files from captcha-images directory
            $allFiles = Storage::files(self::CAPTCHA_PATH);
            
            Log::info('Found files in captcha-images', ['files' => $allFiles]);
            
            // Filter valid image files
            $validImages = array_filter($allFiles, function($file) {
                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                $isValid = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                
                Log::info('Checking file', [
                    'file' => $file, 
                    'extension' => $extension, 
                    'valid' => $isValid
                ]);
                
                return $isValid;
            });
            
            if (empty($validImages)) {
                Log::warning('No valid images found in captcha-images directory');
                return null;
            }
            
            // Select random image
            $selectedImage = $validImages[array_rand($validImages)];
            Log::info('Selected image', ['image' => $selectedImage]);
            
            return $selectedImage;
            
        } catch (\Exception $e) {
            Log::error('Error getting background image: ' . $e->getMessage());
            return null;
        }
    }
    
    private function createPuzzle(string $backgroundImagePath): array
    {
        $puzzleId = Str::random(12);
        
        Log::info('Creating puzzle', ['background_path' => $backgroundImagePath, 'puzzle_id' => $puzzleId]);
        
        // Load background image
        $sourceImage = $this->loadImage($backgroundImagePath);
        
        // Generate puzzle position
        $minX = 30;
        $maxX = self::IMAGE_WIDTH - self::PUZZLE_SIZE - 30;
        $correctX = rand($minX, $maxX);
        $puzzleY = rand(20, self::IMAGE_HEIGHT - self::PUZZLE_SIZE - 20);
        
        Log::info('Puzzle position', ['correct_x' => $correctX, 'puzzle_y' => $puzzleY]);
        
        // Create puzzle piece and background with hole
        $puzzlePiece = $this->createPuzzlePiece($sourceImage, $correctX, $puzzleY);
        $backgroundWithHole = $this->createBackgroundWithHole($sourceImage, $correctX, $puzzleY);
        
        // Save temporary files and get URLs
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
    
    /**
     * FIXED: Load image with better JPG support
     */
    private function loadImage(string $imagePath)
    {
        $fullPath = Storage::path($imagePath);
        
        Log::info('Loading image', ['full_path' => $fullPath, 'exists' => file_exists($fullPath)]);
        
        if (!file_exists($fullPath)) {
            throw new \Exception("Image file not found: {$fullPath}");
        }
        
        // Get image info
        $imageInfo = getimagesize($fullPath);
        
        if (!$imageInfo) {
            throw new \Exception("Invalid image file: {$imagePath}");
        }
        
        Log::info('Image info', [
            'width' => $imageInfo[0],
            'height' => $imageInfo[1], 
            'mime' => $imageInfo['mime']
        ]);
        
        // Create image resource based on type
        switch ($imageInfo['mime']) {
            case 'image/jpeg':
            case 'image/jpg':
                $source = imagecreatefromjpeg($fullPath);
                Log::info('Loaded JPEG image');
                break;
            case 'image/png':
                $source = imagecreatefrompng($fullPath);
                Log::info('Loaded PNG image');
                break;
            case 'image/gif':
                $source = imagecreatefromgif($fullPath);
                Log::info('Loaded GIF image');
                break;
            case 'image/webp':
                if (function_exists('imagecreatefromwebp')) {
                    $source = imagecreatefromwebp($fullPath);
                    Log::info('Loaded WebP image');
                } else {
                    throw new \Exception("WebP support not available");
                }
                break;
            default:
                throw new \Exception("Unsupported image type: {$imageInfo['mime']}");
        }
        
        if (!$source) {
            throw new \Exception("Failed to load image resource from: {$imagePath}");
        }
        
        // Get original dimensions
        $originalWidth = imagesx($source);
        $originalHeight = imagesy($source);
        
        Log::info('Original image dimensions', [
            'width' => $originalWidth, 
            'height' => $originalHeight
        ]);
        
        // Create resized image with proper aspect ratio handling
        $resized = imagecreatetruecolor(self::IMAGE_WIDTH, self::IMAGE_HEIGHT);
        
        // Fill with white background first
        $white = imagecolorallocate($resized, 255, 255, 255);
        imagefill($resized, 0, 0, $white);
        
        // Calculate aspect ratio
        $aspectRatio = $originalWidth / $originalHeight;
        $targetAspectRatio = self::IMAGE_WIDTH / self::IMAGE_HEIGHT;
        
        if ($aspectRatio > $targetAspectRatio) {
            // Image is wider - fit to width
            $newWidth = self::IMAGE_WIDTH;
            $newHeight = (int) (self::IMAGE_WIDTH / $aspectRatio);
            $offsetX = 0;
            $offsetY = (int) ((self::IMAGE_HEIGHT - $newHeight) / 2);
        } else {
            // Image is taller - fit to height
            $newHeight = self::IMAGE_HEIGHT;
            $newWidth = (int) (self::IMAGE_HEIGHT * $aspectRatio);
            $offsetX = (int) ((self::IMAGE_WIDTH - $newWidth) / 2);
            $offsetY = 0;
        }
        
        Log::info('Resize calculations', [
            'new_width' => $newWidth,
            'new_height' => $newHeight,
            'offset_x' => $offsetX,
            'offset_y' => $offsetY
        ]);
        
        // Resize and copy with proper positioning
        imagecopyresampled(
            $resized, $source,
            $offsetX, $offsetY, 0, 0,
            $newWidth, $newHeight,
            $originalWidth, $originalHeight
        );
        
        imagedestroy($source);
        
        Log::info('Image loaded and resized successfully');
        
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
        
        // Add border for better visibility
        $border = imagecolorallocate($canvas, 255, 255, 255);
        imagerectangle($canvas, 0, 0, self::PUZZLE_SIZE + self::NOTCH_SIZE - 1, self::PUZZLE_SIZE - 1, $border);
        
        // Convert to string
        ob_start();
        imagepng($canvas);
        $data = ob_get_clean();
        imagedestroy($canvas);
        
        Log::info('Puzzle piece created', ['size' => strlen($data) . ' bytes']);
        
        return $data;
    }
    
    private function createBackgroundWithHole($sourceImage, int $x, int $y): string
    {
        $result = imagecreatetruecolor(self::IMAGE_WIDTH, self::IMAGE_HEIGHT);
        imagecopy($result, $sourceImage, 0, 0, 0, 0, self::IMAGE_WIDTH, self::IMAGE_HEIGHT);
        
        // Create hole with shadow effect
        $holeColor = imagecolorallocatealpha($result, 0, 0, 0, 50);
        imagefilledrectangle($result, $x, $y, $x + self::PUZZLE_SIZE - 1, $y + self::PUZZLE_SIZE - 1, $holeColor);
        
        // Add notch hole
        $notchY = $y + (self::PUZZLE_SIZE / 2) - (self::NOTCH_SIZE / 2);
        if ($x + self::PUZZLE_SIZE + self::NOTCH_SIZE < self::IMAGE_WIDTH) {
            $centerX = $x + self::PUZZLE_SIZE + (self::NOTCH_SIZE/2);
            $centerY = $notchY + (self::NOTCH_SIZE/2);
            imagefilledellipse($result, $centerX, $centerY, self::NOTCH_SIZE, self::NOTCH_SIZE, $holeColor);
        }
        
        // Add border to hole
        $border = imagecolorallocate($result, 150, 150, 150);
        imagerectangle($result, $x, $y, $x + self::PUZZLE_SIZE - 1, $y + self::PUZZLE_SIZE - 1, $border);
        
        ob_start();
        imagepng($result);
        $data = ob_get_clean();
        imagedestroy($result);
        
        Log::info('Background with hole created', ['size' => strlen($data) . ' bytes']);
        
        return $data;
    }
    
    private function saveTempFiles(string $puzzleId, string $backgroundData, string $puzzleData): array
    {
        $backgroundPath = self::TEMP_PATH . "/{$puzzleId}_bg.png";
        $puzzlePath = self::TEMP_PATH . "/{$puzzleId}_piece.png";
        
        Storage::put($backgroundPath, $backgroundData);
        Storage::put($puzzlePath, $puzzleData);
        
        // Generate URLs
        $baseUrl = request()->getSchemeAndHttpHost();
        
        try {
            $bgUrl = Storage::url($backgroundPath);
            $puzzleUrl = Storage::url($puzzlePath);
            
            // If URLs don't start with http, prepend base URL
            if (!str_starts_with($bgUrl, 'http')) {
                $bgUrl = $baseUrl . $bgUrl;
            }
            if (!str_starts_with($puzzleUrl, 'http')) {
                $puzzleUrl = $baseUrl . $puzzleUrl;
            }
            
        } catch (\Exception $e) {
            // Fallback to direct route
            $bgUrl = $baseUrl . '/captcha-puzzle/image/' . basename($backgroundPath);
            $puzzleUrl = $baseUrl . '/captcha-puzzle/image/' . basename($puzzlePath);
        }
        
        Log::info('Temp files saved', [
            'background_url' => $bgUrl,
            'puzzle_url' => $puzzleUrl
        ]);
        
        return [
            'background_url' => $bgUrl,
            'puzzle_url' => $puzzleUrl
        ];
    }
    
    // Methods lainnya tetap sama
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
// TESTING STEPS
// ============================================================================

/*
1. Update controller dengan code di atas

2. Test image detection:
   http://localhost/debug-images

3. Clear cache:
   php artisan config:clear
   php artisan route:clear

4. Test CAPTCHA lagi dan check Laravel logs:
   tail -f storage/logs/laravel.log

5. Pastikan file JPG bisa dibaca:
   - Cek permission file
   - Cek format file (harus valid JPG)
   - Cek ukuran file (tidak corrupt)
*/