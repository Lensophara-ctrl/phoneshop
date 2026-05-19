@props(['slides'])

@if($slides->count() > 0)
<!-- Netflix-Style Carousel -->
<div class="netflix-carousel-wrapper">
    <div id="netflixCarousel" class="netflix-carousel-container" data-netflix-carousel data-autoplay="true" data-interval="5000">
        <div class="netflix-carousel-track">
            @foreach($slides as $index => $slide)
                <div class="netflix-slide {{ $index == 0 ? 'active' : '' }}" data-slide-index="{{ $index }}">
                    <div class="netflix-slide-inner">
                        <!-- Background Image or Gradient -->
                        @if($slide->image)
                            <img src="{{ asset('storage/'.$slide->image) }}" 
                                 alt="{{ $slide->title }}" 
                                 class="netflix-slide-image"
                                 loading="{{ $index == 0 ? 'eager' : 'lazy' }}">
                        @else
                            <div class="netflix-slide-gradient"></div>
                        @endif
                        
                        <!-- Content Overlay -->
                        <div class="netflix-slide-content">
                            <h2 class="netflix-slide-title">{{ $slide->title }}</h2>
                            
                            @if($slide->description)
                                <p class="netflix-slide-description">{{ $slide->description }}</p>
                            @endif
                            
                            @if($slide->button_text)
                                <a href="{{ $slide->button_link ?? '#' }}" class="netflix-slide-button">
                                    <span>{{ $slide->button_text }}</span>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Navigation Arrows -->
        @if($slides->count() > 1)
            <button class="netflix-nav-arrow prev" aria-label="Previous slide">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button class="netflix-nav-arrow next" aria-label="Next slide">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
            
            <!-- Dot Indicators -->
            <div class="netflix-dots-container">
                @foreach($slides as $index => $slide)
                    <button class="netflix-dot {{ $index == 0 ? 'active' : '' }}" 
                            aria-label="Go to slide {{ $index + 1 }}"
                            data-slide="{{ $index }}"></button>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endif

<style>
    /* Netflix-Style Carousel Container */
    .netflix-carousel-container {
        margin-bottom: 4rem;
    }
    
    .netflix-carousel-scene {
        position: relative;
        height: 600px;
        perspective: 1800px;
        overflow: hidden;
        background: linear-gradient(135deg, #0a1628 0%, #1e293b 100%);
        border-radius: 24px;
        padding: 60px 0;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
    }
    
    /* 3D Track */
    .netflix-carousel-track {
        position: relative;
        width: 100%;
        height: 100%;
        transform-style: preserve-3d;
    }
    
    /* 3D Cards - Netflix Style */
    .netflix-carousel-card {
        position: absolute;
        width: 850px;
        height: 480px;
        left: 50%;
        top: 50%;
        margin-left: -425px;
        margin-top: -240px;
        transition: all 0.7s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        transform-style: preserve-3d;
        backface-visibility: hidden;
    }
    
    .netflix-card-inner {
        width: 100%;
        height: 100%;
        background: #1e293b;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 30px 80px rgba(0, 0, 0, 0.7);
        position: relative;
        border: 2px solid rgba(255, 255, 255, 0.1);
    }
    
    /* Background Image */
    .netflix-card-bg {
        position: absolute;
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: brightness(0.65) contrast(1.1);
        transition: all 0.5s ease;
    }
    
    .netflix-carousel-card.active .netflix-card-bg {
        filter: brightness(0.7) contrast(1.1);
    }
    
    .netflix-card-gradient {
        position: absolute;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #ec4899 100%);
        background-size: 200% 200%;
        animation: gradientFlow 8s ease infinite;
    }
    
    @keyframes gradientFlow {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
    
    /* Content Overlay */
    .netflix-card-content {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 45px;
        background: linear-gradient(to top, 
            rgba(0, 0, 0, 0.98) 0%, 
            rgba(0, 0, 0, 0.85) 50%, 
            rgba(0, 0, 0, 0.4) 80%, 
            transparent 100%);
        color: white;
        z-index: 2;
        transform: translateY(0);
        transition: transform 0.4s ease;
    }
    
    .netflix-card-title {
        font-size: 2.8rem;
        font-weight: 900;
        margin-bottom: 15px;
        line-height: 1.1;
        text-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
        letter-spacing: -0.5px;
    }
    
    .netflix-card-description {
        font-size: 1.1rem;
        opacity: 0.95;
        margin-bottom: 25px;
        line-height: 1.6;
        max-width: 600px;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
    }
    
    .netflix-card-btn {
        display: inline-flex;
        align-items: center;
        background: white;
        color: #1e293b;
        padding: 14px 32px;
        border-radius: 50px;
        font-weight: 800;
        font-size: 1rem;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 6px 20px rgba(255, 255, 255, 0.4);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .netflix-card-btn:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 8px 30px rgba(255, 255, 255, 0.6);
        background: #f0f0f0;
        color: #4f46e5;
    }
    
    /* 3D Positioning States - Enhanced Netflix Effect */
    .netflix-carousel-card.active {
        transform: translateZ(0) rotateY(0deg) scale(1);
        opacity: 1;
        z-index: 10;
    }
    
    .netflix-carousel-card.prev {
        transform: translateZ(-300px) translateX(-500px) rotateY(45deg) scale(0.7);
        opacity: 0.4;
        z-index: 5;
        filter: blur(2px);
    }
    
    .netflix-carousel-card.next {
        transform: translateZ(-300px) translateX(500px) rotateY(-45deg) scale(0.7);
        opacity: 0.4;
        z-index: 5;
        filter: blur(2px);
    }
    
    .netflix-carousel-card.hidden {
        transform: translateZ(-600px) scale(0.3);
        opacity: 0;
        z-index: 1;
        pointer-events: none;
    }
    
    /* Navigation Arrows - Enhanced */
    .netflix-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(15px);
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        color: white;
        font-size: 1.4rem;
        cursor: pointer;
        z-index: 100;
        transition: all 0.3s ease;
    }
    
    .netflix-arrow:hover {
        background: rgba(255, 255, 255, 0.3);
        border-color: white;
        transform: translateY(-50%) scale(1.15);
        box-shadow: 0 8px 25px rgba(255, 255, 255, 0.3);
    }
    
    .netflix-arrow:active {
        transform: translateY(-50%) scale(1.05);
    }
    
    .netflix-arrow.prev {
        left: 40px;
    }
    
    .netflix-arrow.next {
        right: 40px;
    }
    
    /* Dot Indicators - Enhanced */
    .netflix-dots {
        position: absolute;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 12px;
        z-index: 100;
        padding: 12px 20px;
        background: rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(10px);
        border-radius: 50px;
    }
    
    .netflix-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.4);
        border: none;
        cursor: pointer;
        transition: all 0.4s ease;
        position: relative;
    }
    
    .netflix-dot::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 0;
        height: 0;
        background: white;
        border-radius: 50%;
        transition: all 0.4s ease;
    }
    
    .netflix-dot.active::after {
        width: 100%;
        height: 100%;
    }
    
    .netflix-dot.active {
        background: white;
        transform: scale(1.4);
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.9);
    }
    
    .netflix-dot:hover:not(.active) {
        background: rgba(255, 255, 255, 0.7);
        transform: scale(1.2);
    }
    
    /* Responsive Design */
    @media (max-width: 1200px) {
        .netflix-carousel-card {
            width: 700px;
            height: 400px;
            margin-left: -350px;
            margin-top: -200px;
        }
        
        .netflix-card-title {
            font-size: 2.2rem;
        }
    }
    
    @media (max-width: 991px) {
        .netflix-carousel-scene {
            height: 500px;
        }
        
        .netflix-carousel-card {
            width: 550px;
            height: 340px;
            margin-left: -275px;
            margin-top: -170px;
        }
        
        .netflix-card-title {
            font-size: 1.8rem;
        }
        
        .netflix-card-description {
            font-size: 1rem;
        }
        
        .netflix-arrow {
            width: 50px;
            height: 50px;
            font-size: 1.2rem;
        }
    }
    
    @media (max-width: 767px) {
        .netflix-carousel-scene {
            height: 450px;
            border-radius: 16px;
        }
        
        .netflix-carousel-card {
            width: 380px;
            height: 300px;
            margin-left: -190px;
            margin-top: -150px;
        }
        
        .netflix-carousel-card.prev,
        .netflix-carousel-card.next {
            display: none;
        }
        
        .netflix-card-content {
            padding: 30px;
        }
        
        .netflix-card-title {
            font-size: 1.5rem;
        }
        
        .netflix-card-description {
            font-size: 0.9rem;
            margin-bottom: 20px;
        }
        
        .netflix-card-btn {
            padding: 12px 24px;
            font-size: 0.9rem;
        }
        
        .netflix-arrow {
            width: 45px;
            height: 45px;
            font-size: 1rem;
        }
        
        .netflix-arrow.prev {
            left: 20px;
        }
        
        .netflix-arrow.next {
            right: 20px;
        }
    }
</style>

<script>
    const netflixCarousel = {
        currentIndex: 0,
        totalCards: {{ $slides->count() }},
        autoPlayTimer: null,
        
        update() {
            const cards = document.querySelectorAll('.netflix-carousel-card');
            const dots = document.querySelectorAll('.netflix-dot');
            
            cards.forEach((card, index) => {
                card.classList.remove('active', 'prev', 'next', 'hidden');
                if (dots[index]) dots[index].classList.remove('active');
                
                if (index === this.currentIndex) {
                    card.classList.add('active');
                    if (dots[index]) dots[index].classList.add('active');
                } else if (index === (this.currentIndex - 1 + this.totalCards) % this.totalCards) {
                    card.classList.add('prev');
                } else if (index === (this.currentIndex + 1) % this.totalCards) {
                    card.classList.add('next');
                } else {
                    card.classList.add('hidden');
                }
            });
        },
        
        move(direction) {
            this.currentIndex = (this.currentIndex + direction + this.totalCards) % this.totalCards;
            this.update();
            this.resetAutoPlay();
        },
        
        jumpTo(index) {
            this.currentIndex = index;
            this.update();
            this.resetAutoPlay();
        },
        
        startAutoPlay() {
            this.autoPlayTimer = setInterval(() => {
                this.move(1);
            }, 5000);
        },
        
        resetAutoPlay() {
            clearInterval(this.autoPlayTimer);
            this.startAutoPlay();
        },
        
        init() {
            this.update();
            this.startAutoPlay();
            
            // Pause on hover
            const scene = document.querySelector('.netflix-carousel-scene');
            if (scene) {
                scene.addEventListener('mouseenter', () => clearInterval(this.autoPlayTimer));
                scene.addEventListener('mouseleave', () => this.startAutoPlay());
            }
            
            // Keyboard navigation
            document.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') this.move(-1);
                if (e.key === 'ArrowRight') this.move(1);
            });
        }
    };
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => netflixCarousel.init());
    } else {
        netflixCarousel.init();
    }
</script>
@endif
