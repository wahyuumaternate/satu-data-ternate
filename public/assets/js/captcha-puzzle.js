/**
 * CAPTCHA Puzzle System
 * File: public/js/captcha-puzzle.js
 * 
 * Usage: new CaptchaPuzzle(containerId, options)
 */

class CaptchaPuzzle {
    constructor(containerId, options = {}) {
        this.container = document.getElementById(containerId);
        this.options = {
            generateUrl: '/captcha-puzzle/generate',
            verifyUrl: '/captcha-puzzle/verify',
            onVerified: null,
            onFailed: null,
            autoRefreshOnFail: true,
            tolerance: 5,
            ...options
        };
        
        // State management
        this.state = {
            loading: true,
            currentX: 0,
            sliderPosition: 3,
            isDragging: false,
            isSliderDragging: false,
            verified: false,
            maxSliderPosition: 0,
            puzzleWidth: 65,
            startMouseX: 0,
            startPuzzleX: 0,
            startSliderX: 0,
            puzzleId: '',
            puzzleY: 0,
            backgroundWidth: 400
        };
        
        // DOM elements cache
        this.elements = {};
        
        // Event handlers (bound to this context)
        this.boundHandlers = {
            onMouseMove: this.onMouseMove.bind(this),
            onMouseUp: this.onMouseUp.bind(this),
            onTouchMove: this.onTouchMove.bind(this),
            onTouchEnd: this.onTouchEnd.bind(this)
        };
        
        this.init();
    }
    
    /**
     * Initialize the captcha puzzle
     */
    init() {
        if (!this.container) {
            console.error('CAPTCHA container not found');
            return;
        }
        
        this.bindElements();
        this.bindEvents();
        this.generateCaptcha();
    }
    
    /**
     * Cache DOM elements for better performance
     */
    bindElements() {
        const elementIds = [
            'captchaContent', 'captchaLoading', 'backgroundContainer', 
            'backgroundImage', 'puzzlePiece', 'puzzleImage', 'successOverlay',
            'sliderTrack', 'sliderButton', 'statusMessage', 'statusIcon', 
            'statusText', 'refreshButton'
        ];
        
        elementIds.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                this.elements[id] = element;
            } else {
                console.warn(`Element with id '${id}' not found`);
            }
        });
    }
    
    /**
     * Bind all event listeners
     */
    bindEvents() {
        // Refresh button
        if (this.elements.refreshButton) {
            this.elements.refreshButton.addEventListener('click', () => this.refreshCaptcha());
        }
        
        // Puzzle piece drag events
        if (this.elements.puzzlePiece) {
            this.elements.puzzlePiece.addEventListener('mousedown', (e) => this.startDrag(e));
            this.elements.puzzlePiece.addEventListener('touchstart', (e) => this.startDrag(e), { passive: false });
        }
        
        // Slider button drag events
        if (this.elements.sliderButton) {
            this.elements.sliderButton.addEventListener('mousedown', (e) => this.startSliderDrag(e));
            this.elements.sliderButton.addEventListener('touchstart', (e) => this.startSliderDrag(e), { passive: false });
        }
        
        // Global mouse/touch events
        document.addEventListener('mousemove', this.boundHandlers.onMouseMove);
        document.addEventListener('mouseup', this.boundHandlers.onMouseUp);
        document.addEventListener('touchmove', this.boundHandlers.onTouchMove, { passive: false });
        document.addEventListener('touchend', this.boundHandlers.onTouchEnd);
        
        // Background image load event
        if (this.elements.backgroundImage) {
            this.elements.backgroundImage.addEventListener('load', () => this.onBackgroundLoad());
        }
        
        // Prevent context menu on images
        [this.elements.puzzleImage, this.elements.backgroundImage].forEach(img => {
            if (img) {
                img.addEventListener('contextmenu', (e) => e.preventDefault());
                img.addEventListener('dragstart', (e) => e.preventDefault());
            }
        });
    }
    
    /**
     * Generate new captcha puzzle
     */
    async generateCaptcha() {
        this.setLoading(true);
        this.resetState();
        
        try {
            const response = await this.fetchWithCSRF(this.options.generateUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                await this.loadCaptchaData(data);
            } else {
                this.showStatus('Gagal memuat captcha', 'error');
                console.error('Captcha generation failed:', data);
            }
        } catch (error) {
            console.error('Error generating captcha:', error);
            this.showStatus('Terjadi kesalahan saat memuat captcha', 'error');
        }
    }
    
    /**
     * Load captcha data and set up images
     */
    async loadCaptchaData(data) {
        this.state.puzzleY = data.puzzle_y;
        this.state.puzzleId = data.puzzle_id;
        
        // Load images
        if (this.elements.backgroundImage && this.elements.puzzleImage) {
            // Use Promise.all to load both images
            try {
                await Promise.all([
                    this.loadImage(this.elements.backgroundImage, data.background_image),
                    this.loadImage(this.elements.puzzleImage, data.puzzle_piece)
                ]);
                
                this.updatePuzzlePosition();
                this.updateSliderPosition();
            } catch (error) {
                console.error('Error loading images:', error);
                this.showStatus('Gagal memuat gambar captcha', 'error');
            }
        }
    }
    
    /**
     * Load image with promise
     */
    loadImage(imgElement, src) {
        return new Promise((resolve, reject) => {
            imgElement.onload = resolve;
            imgElement.onerror = reject;
            imgElement.src = src;
        });
    }
    
    /**
     * Handle background image load
     */
    onBackgroundLoad() {
        this.setLoading(false);
        this.calculateDimensions();
    }
    
    /**
     * Calculate container and slider dimensions
     */
    calculateDimensions() {
        if (this.elements.backgroundContainer && this.elements.sliderTrack && this.elements.sliderButton) {
            this.state.backgroundWidth = this.elements.backgroundContainer.offsetWidth;
            const trackWidth = this.elements.sliderTrack.offsetWidth;
            const buttonWidth = this.elements.sliderButton.offsetWidth;
            this.state.maxSliderPosition = trackWidth - buttonWidth - 6; // 6px for borders/padding
        }
    }
    
    /**
     * Start dragging puzzle piece
     */
    startDrag(event) {
        if (this.state.verified || this.state.loading) return;
        
        event.preventDefault();
        this.state.isDragging = true;
        this.elements.puzzlePiece?.classList.add('dragging');
        
        const clientX = this.getClientX(event);
        this.state.startMouseX = clientX;
        this.state.startPuzzleX = this.state.currentX;
        
        document.body.classList.add('no-select');
    }
    
    /**
     * Start dragging slider button
     */
    startSliderDrag(event) {
        if (this.state.verified || this.state.loading) return;
        
        event.preventDefault();
        this.state.isSliderDragging = true;
        this.elements.sliderButton?.classList.add('dragging');
        
        const clientX = this.getClientX(event);
        this.state.startMouseX = clientX;
        this.state.startSliderX = this.state.sliderPosition;
        
        document.body.classList.add('no-select');
    }
    
    /**
     * Handle mouse move events
     */
    onMouseMove(event) {
        if (!this.state.isDragging && !this.state.isSliderDragging) return;
        
        event.preventDefault();
        const clientX = this.getClientX(event);
        const deltaX = clientX - this.state.startMouseX;
        
        if (this.state.isDragging) {
            this.handlePuzzleDrag(deltaX);
        } else if (this.state.isSliderDragging) {
            this.handleSliderDrag(deltaX);
        }
    }
    
    /**
     * Handle touch move events
     */
    onTouchMove(event) {
        this.onMouseMove(event);
    }
    
    /**
     * Handle mouse up events
     */
    onMouseUp() {
        if (this.state.isDragging) {
            this.state.isDragging = false;
            this.elements.puzzlePiece?.classList.remove('dragging');
            this.checkPosition();
        }
        
        if (this.state.isSliderDragging) {
            this.state.isSliderDragging = false;
            this.elements.sliderButton?.classList.remove('dragging');
            this.checkPosition();
        }
        
        document.body.classList.remove('no-select');
    }
    
    /**
     * Handle touch end events
     */
    onTouchEnd() {
        this.onMouseUp();
    }
    
    /**
     * Handle puzzle piece drag movement
     */
    handlePuzzleDrag(deltaX) {
        const maxX = this.state.backgroundWidth - this.state.puzzleWidth;
        const newX = Math.max(0, Math.min(maxX, this.state.startPuzzleX + deltaX));
        this.state.currentX = newX;
        this.updatePuzzlePosition();
        
        // Sync slider position
        const ratio = maxX > 0 ? newX / maxX : 0;
        this.state.sliderPosition = 3 + (ratio * this.state.maxSliderPosition);
        this.updateSliderPosition();
    }
    
    /**
     * Handle slider drag movement
     */
    handleSliderDrag(deltaX) {
        const newSliderX = Math.max(3, Math.min(this.state.maxSliderPosition + 3, this.state.startSliderX + deltaX));
        this.state.sliderPosition = newSliderX;
        this.updateSliderPosition();
        
        // Sync puzzle position
        const ratio = this.state.maxSliderPosition > 0 ? (newSliderX - 3) / this.state.maxSliderPosition : 0;
        const maxX = this.state.backgroundWidth - this.state.puzzleWidth;
        this.state.currentX = ratio * maxX;
        this.updatePuzzlePosition();
    }
    
    /**
     * Update puzzle piece position
     */
    updatePuzzlePosition() {
        if (this.elements.puzzlePiece) {
            this.elements.puzzlePiece.style.left = this.state.currentX + 'px';
            this.elements.puzzlePiece.style.top = this.state.puzzleY + 'px';
        }
    }
    
    /**
     * Update slider button position
     */
    updateSliderPosition() {
        if (this.elements.sliderButton) {
            this.elements.sliderButton.style.left = this.state.sliderPosition + 'px';
        }
    }
    
    /**
     * Verify puzzle position with server
     */
    async checkPosition() {
        if (this.state.verified || this.state.loading) return;
        
        try {
            const response = await this.fetchWithCSRF(this.options.verifyUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    x_position: Math.round(this.state.currentX),
                    puzzle_id: this.state.puzzleId
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.handleVerificationSuccess();
            } else {
                this.handleVerificationFailure(data.message);
            }
        } catch (error) {
            console.error('Error verifying captcha:', error);
            this.showStatus('Terjadi kesalahan saat verifikasi', 'error');
            this.handleVerificationFailure();
        }
    }
    
    /**
     * Handle successful verification
     */
    handleVerificationSuccess() {
        this.state.verified = true;
        
        if (this.elements.successOverlay) {
            this.elements.successOverlay.style.display = 'flex';
        }
        
        this.showStatus('Verifikasi berhasil!', 'success');
        
        // Trigger callback
        if (typeof this.options.onVerified === 'function') {
            this.options.onVerified();
        }
    }
    
    /**
     * Handle failed verification
     */
    handleVerificationFailure(message = 'Posisi tidak tepat, coba lagi') {
        this.showStatus(message, 'error');
        
        // Trigger callback
        if (typeof this.options.onFailed === 'function') {
            this.options.onFailed();
        }
        
        // Auto-refresh on failure
        if (this.options.autoRefreshOnFail) {
            setTimeout(() => {
                this.generateCaptcha();
            }, 1500);
        }
    }
    
    /**
     * Show status message
     */
    showStatus(message, type) {
        if (!this.elements.statusMessage || !this.elements.statusText || !this.elements.statusIcon) {
            return;
        }
        
        this.elements.statusText.textContent = message;
        this.elements.statusMessage.className = `status-message status-${type}`;
        this.elements.statusIcon.className = type === 'success' ? 'bi bi-check-circle' : 'bi bi-exclamation-circle';
        this.elements.statusMessage.style.display = 'flex';
        
        // Auto-hide after 3 seconds for non-success messages
        if (type !== 'success') {
            setTimeout(() => {
                if (this.elements.statusMessage) {
                    this.elements.statusMessage.style.display = 'none';
                }
            }, 3000);
        }
    }
    
    /**
     * Reset all state to initial values
     */
    resetState() {
        this.state.currentX = 0;
        this.state.sliderPosition = 3;
        this.state.verified = false;
        this.state.isDragging = false;
        this.state.isSliderDragging = false;
        
        // Reset UI elements
        if (this.elements.successOverlay) {
            this.elements.successOverlay.style.display = 'none';
        }
        
        if (this.elements.statusMessage) {
            this.elements.statusMessage.style.display = 'none';
        }
        
        [this.elements.puzzlePiece, this.elements.sliderButton].forEach(element => {
            if (element) {
                element.classList.remove('dragging');
            }
        });
        
        document.body.classList.remove('no-select');
    }
    
    /**
     * Refresh captcha (public method)
     */
    refreshCaptcha() {
        if (!this.state.loading) {
            this.generateCaptcha();
        }
    }
    
    /**
     * Set loading state
     */
    setLoading(loading) {
        this.state.loading = loading;
        
        if (this.elements.captchaLoading) {
            this.elements.captchaLoading.style.display = loading ? 'block' : 'none';
        }
        
        if (this.elements.captchaContent) {
            this.elements.captchaContent.style.display = loading ? 'none' : 'block';
        }
        
        if (this.elements.refreshButton) {
            this.elements.refreshButton.disabled = loading;
        }
    }
    
    /**
     * Get client X coordinate from mouse or touch event
     */
    getClientX(event) {
        return event.type.includes('touch') ? event.touches[0].clientX : event.clientX;
    }
    
    /**
     * Fetch with CSRF token
     */
    async fetchWithCSRF(url, options = {}) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        
        if (!options.headers) {
            options.headers = {};
        }
        
        if (csrfToken) {
            options.headers['X-CSRF-TOKEN'] = csrfToken;
        }
        
        const response = await fetch(url, options);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return response;
    }
    
    /**
     * Public API methods
     */
    
    /**
     * Check if captcha is verified
     */
    isVerified() {
        return this.state.verified;
    }
    
    /**
     * Reset captcha (public method)
     */
    reset() {
        this.generateCaptcha();
    }
    
    /**
     * Get current state (for debugging)
     */
    getState() {
        return { ...this.state };
    }
    
    /**
     * Destroy captcha instance and cleanup
     */
    destroy() {
        // Remove global event listeners
        document.removeEventListener('mousemove', this.boundHandlers.onMouseMove);
        document.removeEventListener('mouseup', this.boundHandlers.onMouseUp);
        document.removeEventListener('touchmove', this.boundHandlers.onTouchMove);
        document.removeEventListener('touchend', this.boundHandlers.onTouchEnd);
        
        // Clear any timeouts
        this.resetState();
        
        // Clear references
        this.elements = {};
        this.container = null;
        
        console.log('CAPTCHA Puzzle destroyed');
    }
}

// Export for module systems (if needed)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CaptchaPuzzle;
}

// Auto-initialization helper (optional)
window.CaptchaPuzzle = CaptchaPuzzle;

// Utility function for easy initialization
window.initCaptchaPuzzle = function(containerId, options = {}) {
    return new CaptchaPuzzle(containerId, options);
};