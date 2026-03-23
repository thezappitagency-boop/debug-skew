/*!
 * Cool Slide — Skew Slider Bundle v1.1.0
 * Sticky-scroll edition: pins slider on entry, scrolls through all slides, then releases.
 * Dependencies (load before this): gsap.min.js, ScrollTrigger.min.js, imagesloaded.pkgd.min.js
 */

/* =====================================================================
   utils.js
   ===================================================================== */

const preloadImages = (selector = 'img') => {
    return new Promise((resolve) => {
        imagesLoaded(document.querySelectorAll(selector), { background: true }, resolve);
    });
};


/* =====================================================================
   slideshow.js
   ===================================================================== */

const NEXT = 1;
const PREV = -1;

class Slideshow {
    DOM = {
        el: null,
        slides: null,
        slidesInner: null,
        slideNumber: null
    };

    current     = 0;
    slidesTotal = 0;
    isAnimating = false;

    constructor(DOM_el) {
        this.DOM.el          = DOM_el;
        this.DOM.slides      = [...this.DOM.el.querySelectorAll('.slide')];
        this.DOM.slidesInner = this.DOM.slides.map(item => item.querySelector('.slide__img'));
        this.DOM.slideNumber = DOM_el.closest('.skew-slider-area')
            ? DOM_el.closest('.skew-slider-area').querySelector('.slides-numbers .active')
            : document.querySelector('.slides-numbers .active');

        gsap.set(this.DOM.el, { perspective: 1000 });
        this.DOM.slides[this.current].classList.add('slide--current');
        this.slidesTotal = this.DOM.slides.length;
        this.updateSlideNumber();
    }

    next() { this.navigate(NEXT); }
    prev() { this.navigate(PREV); }

    navigate(direction) {
        if (this.isAnimating) return false;
        this.isAnimating = true;

        const previous = this.current;
        this.current   = direction === NEXT
            ? (this.current < this.slidesTotal - 1 ? ++this.current : 0)
            : (this.current > 0 ? --this.current : this.slidesTotal - 1);

        this._animateTo(previous, this.current, direction);
    }

    /**
     * Jump directly to any slide index (used by ScrollTrigger).
     * @param {number} targetIndex
     */
    navigateTo(targetIndex) {
        if (targetIndex === this.current || this.isAnimating) return;
        this.isAnimating = true;

        const previous  = this.current;
        this.current    = targetIndex;
        const direction = targetIndex > previous ? NEXT : PREV;

        this._animateTo(previous, this.current, direction);
    }

    _animateTo(from, to, direction) {
        this.updateSlideNumber();

        const currentSlide  = this.DOM.slides[from];
        const upcomingSlide = this.DOM.slides[to];
        const upcomingInner = this.DOM.slidesInner[to];

        gsap.timeline({
            defaults: { duration: 1.2, ease: 'power3.inOut' },
            onStart: () => {
                upcomingSlide.classList.add('slide--current');
                gsap.set(upcomingSlide, { zIndex: 99 });
            },
            onComplete: () => {
                currentSlide.classList.remove('slide--current');
                gsap.set(upcomingSlide, { zIndex: 1 });
                this.isAnimating = false;
            }
        })
        .addLabel('start', 0)
        .to(currentSlide, { yPercent: -direction * 100 }, 'start')
        .fromTo(upcomingSlide, {
            yPercent: 0, autoAlpha: 0, rotationX: 140, scale: 0.1, z: -1000
        }, {
            autoAlpha: 1, rotationX: 0, z: 0, scale: 1
        }, 'start+=0.1')
        .fromTo(upcomingInner, { scale: 1.8 }, { scale: 1 }, 'start+=0.17');
    }

    updateSlideNumber() {
        if (this.DOM.slideNumber) {
            this.DOM.slideNumber.textContent = this.addLeadingZero(this.current + 1);
        }
    }

    addLeadingZero(num) {
        return num < 10 ? `0${num}` : `${num}`;
    }
}


/* =====================================================================
   index.js  – initialiser (sticky-scroll edition)
   ===================================================================== */

function initSkewSliders() {
    document.querySelectorAll('.skew-slider-wrap').forEach(function (container) {
        if (container._skewSliderInit) return;
        container._skewSliderInit = true;

        const area      = container.closest('.skew-slider-area') || container.parentElement;
        const slideshow = new Slideshow(container);
        const numSlides = slideshow.slidesTotal;

        /* ── Prev / Next buttons ─────────────────────────────────── */
        const prevBtn = area.querySelector('.skew-slider-prev');
        const nextBtn = area.querySelector('.skew-slider-next');
        if (prevBtn) prevBtn.addEventListener('click', () => slideshow.prev());
        if (nextBtn) nextBtn.addEventListener('click', () => slideshow.next());

        /* ── Sticky-scroll via ScrollTrigger ─────────────────────── */
        if (typeof ScrollTrigger !== 'undefined' && numSlides > 1) {
            gsap.registerPlugin(ScrollTrigger);

            let activeSnap = 0;

            ScrollTrigger.create({
                trigger: area,
                start: 'top top',
                // One extra viewport AFTER the last slide so the last snap point
                // lands at progress (N-1)/N, not 1.0 — leaving scroll room to
                // release the pin after the final slide.
                end: `+=${numSlides * window.innerHeight}`,
                pin: true,
                anticipatePin: 1,
                snap: {
                    // Snap to 0, 1/N, 2/N … (N-1)/N — never reaches 1.0
                    snapTo: (v) => Math.min(
                        (numSlides - 1) / numSlides,
                        Math.round(v * numSlides) / numSlides
                    ),
                    duration: { min: 0.2, max: 0.45 },
                    ease: 'power1.inOut'
                },
                onUpdate(self) {
                    const snap = Math.min(
                        numSlides - 1,
                        Math.round(self.progress * numSlides)
                    );
                    if (snap !== activeSnap) {
                        activeSnap = snap;
                        slideshow.navigateTo(snap);
                    }
                }
            });

        } else if (typeof Observer !== 'undefined') {
            /* ── Fallback: wheel / touch (no ScrollTrigger available) ── */
            Observer.create({
                target:     container,
                type:       'wheel,touch,pointer',
                onDown:     () => slideshow.prev(),
                onUp:       () => slideshow.next(),
                wheelSpeed: -1,
                tolerance:  10
            });
        }

        /* ── Reveal after background images load ─────────────────── */
        preloadImages('.slide__img').then(() => {
            document.body.classList.remove('loading');
        });
    });
}

/* ── Boot ─────────────────────────────────────────────────────────── */

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSkewSliders);
} else {
    initSkewSliders();
}

// Elementor editor: re-run when widget renders in panel
window.addEventListener('elementor/frontend/init', function () {
    if (!window.elementorFrontend || !window.elementorFrontend.hooks) return;
    window.elementorFrontend.hooks.addAction(
        'frontend/element_ready/cool-slide-skew-slider.default',
        function () { initSkewSliders(); }
    );
});
