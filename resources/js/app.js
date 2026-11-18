import { initializeAccessibility } from './accessibility';

initializeAccessibility();

const toggle = document.querySelector('.menu-toggle');
const navigation = document.querySelector('#main-navigation');
if (toggle && navigation) {
    toggle.hidden = false;
    document.documentElement.classList.add('js-menu');
    const closeMenu = () => {
        toggle.setAttribute('aria-expanded', 'false');
        navigation.classList.remove('is-open');
    };
    toggle.addEventListener('click', () => {
        const open = toggle.getAttribute('aria-expanded') !== 'true';
        toggle.setAttribute('aria-expanded', String(open));
        navigation.classList.toggle('is-open', open);
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
            closeMenu();
            toggle.focus();
        }
    });
    document.addEventListener('click', (event) => {
        if (!event.target.closest('.site-header')) closeMenu();
    });
    navigation.querySelectorAll('a').forEach((link) => link.addEventListener('click', closeMenu));
    matchMedia('(min-width: 901px)').addEventListener('change', closeMenu);
}

const slider = document.querySelector('.home-slider');
if (slider) {
    const slides = [...slider.querySelectorAll('.slider-slide')];
    const controls = slider.querySelector('.slider-side-controls');
    const announcement = slider.querySelector('.slide-announcement');
    const reducedMotion = matchMedia('(prefers-reduced-motion: reduce)');
    const pauseControl = slider.querySelector('[data-slide-pause]');
    let current = 0;
    let requested = 0;
    let changeId = 0;
    let hovered = false;
    let focused = false;
    let touchStart = null;
    let paused = reducedMotion.matches || document.documentElement.classList.contains('access-motion');
    let motionPreference = document.documentElement.classList.contains('access-motion');
    let timer;
    const updateRotation = () => {
        clearInterval(timer);
        if (!paused && !hovered && !focused && !document.hidden && slides.length > 1) {
            timer = setInterval(() => showSlide(1), 5000);
        }
        announcement.setAttribute('aria-live', paused || hovered || focused ? 'polite' : 'off');
        slider.dataset.paused = String(paused || hovered || focused);
        pauseControl.textContent = paused ? 'Resume slideshow' : 'Pause slideshow';
        pauseControl.setAttribute('aria-pressed', String(paused));
    };
    const showSlide = async (step) => {
        requested = (requested + step + slides.length) % slides.length;
        const target = requested;
        const requestId = ++changeId;
        const image = slides[target].querySelector('img');
        if (image.dataset.src) {
            image.loading = 'eager';
            if (image.dataset.srcset) image.srcset = image.dataset.srcset;
            image.src = image.dataset.src;
            delete image.dataset.src;
            delete image.dataset.srcset;
        }
        try {
            await image.decode();
        } catch {
            return;
        }
        if (requestId !== changeId) return;
        current = target;
        slides.forEach((slide, index) => {
            slide.classList.toggle('is-active', index === current);
            slide.setAttribute('aria-hidden', String(index !== current));
        });
        announcement.textContent = `Photo ${current + 1} of ${slides.length}`;
    };
    const manualSlide = (step) => {
        showSlide(step);
        updateRotation();
    };
    controls.hidden = false;
    pauseControl.addEventListener('click', () => {
        paused = !paused;
        updateRotation();
    });
    slider.addEventListener('mouseenter', () => { hovered = true; updateRotation(); });
    slider.addEventListener('mouseleave', () => { hovered = false; updateRotation(); });
    slider.addEventListener('focusin', () => { focused = true; updateRotation(); });
    slider.addEventListener('focusout', (event) => {
        focused = slider.contains(event.relatedTarget);
        updateRotation();
    });
    controls.querySelectorAll('[data-slide-step]').forEach((button) => {
        button.addEventListener('click', () => manualSlide(Number(button.dataset.slideStep)));
    });
    document.addEventListener('visibilitychange', updateRotation);
    reducedMotion.addEventListener('change', () => { paused = reducedMotion.matches || document.documentElement.classList.contains('access-motion'); updateRotation(); });
    document.addEventListener('accessibilitychange', (event) => {
        if (motionPreference === event.detail.motion) return;
        motionPreference = event.detail.motion;
        paused = event.detail.motion || reducedMotion.matches;
        updateRotation();
    });
    slider.addEventListener('keydown', (event) => {
        if (event.target !== slider) return;
        if (event.key === 'ArrowRight' || event.key === 'ArrowLeft') {
            event.preventDefault();
            manualSlide(event.key === 'ArrowRight' ? 1 : -1);
        }
    });
    slider.addEventListener('touchstart', (event) => {
        touchStart = { x: event.changedTouches[0].clientX, y: event.changedTouches[0].clientY };
    }, { passive: true });
    slider.addEventListener('touchend', (event) => {
        if (!touchStart) return;
        const dx = event.changedTouches[0].clientX - touchStart.x;
        const dy = event.changedTouches[0].clientY - touchStart.y;
        if (Math.abs(dx) > 60 && Math.abs(dx) > Math.abs(dy)) manualSlide(dx < 0 ? 1 : -1);
        touchStart = null;
    }, { passive: true });
    updateRotation();
}
