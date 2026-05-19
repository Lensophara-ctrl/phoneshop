/**
 * Netflix-Style 3D Carousel
 * A smooth, interactive slideshow with 3D perspective effects
 */

class NetflixCarousel {
    constructor(containerId, options = {}) {
        this.container = document.getElementById(containerId);
        if (!this.container) {
            console.error(`Netflix Carousel: Container with id "${containerId}" not found`);
            return;
        }

        // Configuration
        this.config = {
            autoPlay: options.autoPlay !== false,
            autoPlayInterval: options.autoPlayInterval || 5000,
            transitionDuration: options.transitionDuration || 800,
            pauseOnHover: options.pauseOnHover !== false,
            keyboardNav: options.keyboardNav !== false,
            touchNav: options.touchNav !== false,
            ...options
        };

        // State
        this.currentIndex = 0;
        this.slides = [];
        this.dots = [];
        this.autoPlayTimer = null;
        this.isTransitioning = false;
        this.touchStartX = 0;
        this.touchEndX = 0;

        this.init();
    }

    init() {
        // Get all slides
        this.slides = Array.from(this.container.querySelectorAll('.netflix-slide'));
        this.dots = Array.from(this.container.querySelectorAll('.netflix-dot'));
        this.totalSlides = this.slides.length;

        if (this.totalSlides === 0) {
            console.warn('Netflix Carousel: No slides found');
            return;
        }

        // Setup event listeners
        this.setupEventListeners();

        // Initial update
        this.updateSlides();

        // Start autoplay
        if (this.config.autoPlay) {
            this.startAutoPlay();
        }

        console.log(`Netflix Carousel initialized with ${this.totalSlides} slides`);
    }

    setupEventListeners() {
        // Navigation arrows
        const prevBtn = this.container.querySelector('.netflix-nav-arrow.prev');
        const nextBtn = this.container.querySelector('.netflix-nav-arrow.next');

        if (prevBtn) {
            prevBtn.addEventListener('click', () => this.prev());
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => this.next());
        }

        // Dot indicators
        this.dots.forEach((dot, index) => {
            dot.addEventListener('click', () => this.goTo(index));
        });

        // Keyboard navigation
        if (this.config.keyboardNav) {
            document.addEventListener('keydown', (e) => this.handleKeyboard(e));
        }

        // Touch navigation
        if (this.config.touchNav) {
            this.container.addEventListener('touchstart', (e) => this.handleTouchStart(e), { passive: true });
            this.container.addEventListener('touchend', (e) => this.handleTouchEnd(e), { passive: true });
        }

        // Pause on hover
        if (this.config.pauseOnHover) {
            this.container.addEventListener('mouseenter', () => this.pauseAutoPlay());
            this.container.addEventListener('mouseleave', () => this.resumeAutoPlay());
        }

        // Visibility change (pause when tab is hidden)
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.pauseAutoPlay();
            } else if (this.config.autoPlay) {
                this.resumeAutoPlay();
            }
        });
    }

    updateSlides() {
        if (this.isTransitioning) return;

        this.slides.forEach((slide, index) => {
            // Remove all position classes
            slide.classList.remove('active', 'prev', 'next', 'hidden');

            // Add appropriate class based on position
            if (index === this.currentIndex) {
                slide.classList.add('active');
            } else if (index === this.getPrevIndex()) {
                slide.classList.add('prev');
            } else if (index === this.getNextIndex()) {
                slide.classList.add('next');
            } else {
                slide.classList.add('hidden');
            }
        });

        // Update dots
        this.dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === this.currentIndex);
        });

        // Dispatch custom event
        this.container.dispatchEvent(new CustomEvent('slideChange', {
            detail: {
                currentIndex: this.currentIndex,
                totalSlides: this.totalSlides
            }
        }));
    }

    next() {
        if (this.isTransitioning) return;
        this.goTo(this.getNextIndex());
    }

    prev() {
        if (this.isTransitioning) return;
        this.goTo(this.getPrevIndex());
    }

    goTo(index) {
        if (this.isTransitioning || index === this.currentIndex) return;

        this.isTransitioning = true;
        this.currentIndex = this.normalizeIndex(index);
        this.updateSlides();

        // Reset transition lock after animation
        setTimeout(() => {
            this.isTransitioning = false;
        }, this.config.transitionDuration);

        // Reset autoplay timer
        if (this.config.autoPlay) {
            this.resetAutoPlay();
        }
    }

    getNextIndex() {
        return this.normalizeIndex(this.currentIndex + 1);
    }

    getPrevIndex() {
        return this.normalizeIndex(this.currentIndex - 1);
    }

    normalizeIndex(index) {
        return ((index % this.totalSlides) + this.totalSlides) % this.totalSlides;
    }

    startAutoPlay() {
        if (!this.config.autoPlay || this.autoPlayTimer) return;

        this.autoPlayTimer = setInterval(() => {
            this.next();
        }, this.config.autoPlayInterval);
    }

    pauseAutoPlay() {
        if (this.autoPlayTimer) {
            clearInterval(this.autoPlayTimer);
            this.autoPlayTimer = null;
        }
    }

    resumeAutoPlay() {
        if (this.config.autoPlay && !this.autoPlayTimer) {
            this.startAutoPlay();
        }
    }

    resetAutoPlay() {
        this.pauseAutoPlay();
        this.startAutoPlay();
    }

    handleKeyboard(e) {
        if (e.key === 'ArrowLeft') {
            e.preventDefault();
            this.prev();
        } else if (e.key === 'ArrowRight') {
            e.preventDefault();
            this.next();
        }
    }

    handleTouchStart(e) {
        this.touchStartX = e.changedTouches[0].screenX;
    }

    handleTouchEnd(e) {
        this.touchEndX = e.changedTouches[0].screenX;
        this.handleSwipe();
    }

    handleSwipe() {
        const swipeThreshold = 50;
        const diff = this.touchStartX - this.touchEndX;

        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0) {
                this.next();
            } else {
                this.prev();
            }
        }
    }

    destroy() {
        this.pauseAutoPlay();
        // Remove event listeners if needed
        console.log('Netflix Carousel destroyed');
    }
}

// Auto-initialize carousels with data attribute
document.addEventListener('DOMContentLoaded', () => {
    const carousels = document.querySelectorAll('[data-netflix-carousel]');
    carousels.forEach((container) => {
        const options = {
            autoPlay: container.dataset.autoplay !== 'false',
            autoPlayInterval: parseInt(container.dataset.interval) || 5000,
            pauseOnHover: container.dataset.pauseOnHover !== 'false',
            keyboardNav: container.dataset.keyboardNav !== 'false',
            touchNav: container.dataset.touchNav !== 'false'
        };
        new NetflixCarousel(container.id, options);
    });
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = NetflixCarousel;
}
