<!-- ArtistSlideShow.vue -->
<template>
    <div class="artist-slideshow-container overflow-hidden relative">
        <div 
            class="slideshow-track flex" 
            :style="{ transform: `translateX(${translateX}px)` }"
        >
            <div 
                v-for="(artist, index) in duplicatedArtists" 
                :key="index" 
                class="artist-slide shrink-0 px-2"
            >
                <img 
                    :src="artist.artist_img" 
                    :alt="artist.artist_name"
                    class="w-32 h-32 object-cover rounded-lg transform rotate-3 transition-transform duration-300"
                >
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "ArtistSlideShow",
    
    props: ['artists', 'speed', 'direction'],
    
    data() {
        return {
            translateX: 0,
            animationId: null,
            lastTimestamp: 0,
            slideWidth: 144, // 128px image width + 16px padding (8px on each side)
        };
    },
    
    computed: {
        duplicatedArtists() {            
            return [...this.artists, ...this.artists, ...this.artists, ...this.artists];
        },
        
        totalWidth() {
            return this.artists.length * this.slideWidth;
        }
    },
    
    mounted() {
        if (this.direction === 'left') {
            this.translateX = 0;
        } else {
            // For right direction, start from negative position to ensure images come from left
            this.translateX = -this.totalWidth;
        }
        
        this.startAnimation();
        this.updateSlideWidth();
        window.addEventListener('resize', this.updateSlideWidth);  
    },
        
    methods: {
        updateSlideWidth() {
            // Adjust if you use different sizes on different screens
            this.slideWidth = 144; // 128px + 16px padding
        },
        
        startAnimation() {
            this.lastTimestamp = performance.now();
            this.animateSlides();
        },
                        
        animateSlides(timestamp = performance.now()) {
            if (!this.lastTimestamp) this.lastTimestamp = timestamp;
            
            const elapsed = timestamp - this.lastTimestamp;
            this.lastTimestamp = timestamp;
            
            const movementFactor = 0.05 * elapsed * this.speed;
            
            if (this.direction === 'left') {
                this.translateX -= movementFactor;
                
                if (Math.abs(this.translateX) >= this.totalWidth) {
                    this.translateX += this.totalWidth;
                }
            } else {
                this.translateX += movementFactor;
                
                // Reset position for right-moving slides
                if (this.translateX >= 0) {
                    this.translateX -= this.totalWidth;
                }
            }
            
            this.animationId = requestAnimationFrame(this.animateSlides);
        }
    }
};
</script>

<style>
    .artist-slideshow-container {
        width: 100%;
        height: auto;
        margin: 0 auto;
    }

    .slideshow-track {
        will-change: transform;
    }

    .artist-slide img {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
</style>