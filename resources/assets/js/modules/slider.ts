import { tns, TinySliderSettings, TinySliderInstance } from 'tiny-slider';

class Slider {
    constructor() {
        const sliders = document.querySelectorAll('[data-slider]');
        sliders.forEach(this.initSlider);
    }

    public static init(): void {
        new Slider();
    }

    private initSlider = (sliderEl: HTMLElement) => {
        const sliderContainer = sliderEl.querySelector<HTMLElement>('[data-slider-container]');
        if (!sliderContainer) {
            return;
        }

        const sliderType = sliderEl.getAttribute('data-slider') ?? 'horizontal';
        const extraNavAttr = sliderEl.getAttribute('data-extra-nav');
        const extraNavContainer = extraNavAttr ? document.querySelector<HTMLElement>(`[${extraNavAttr}]`) : null;
        const controlsContainerEl = sliderEl.querySelector<HTMLElement>('[data-slider-controls]');
        const navContainerEl = sliderEl.querySelector<HTMLElement>('[data-slider-nav]');
        const controls = controlsContainerEl
            ? controlsContainerEl.querySelectorAll<HTMLElement>('[data-controls]')
            : null;
        const autoplayAttr = sliderEl.getAttribute('data-autoplay');
        const autoplay = autoplayAttr !== null;
        const autoplayTimeout = autoplayAttr ? parseInt(autoplayAttr, 10) || 5000 : 0;

        if (navContainerEl && navContainerEl.children.length === 0) {
            const slideCount = sliderContainer.children.length;
            const fragment = document.createDocumentFragment();
            for (let index = 0; index < slideCount; index++) {
                const button = document.createElement('button');
                button.style.display = 'none';
                fragment.appendChild(button);
            }
            navContainerEl.appendChild(fragment);
        }

        const img = sliderContainer.querySelector('img') as HTMLImageElement | null;

        if (img) {
            img.classList.add('loading');

            img.onload = () => {
                img.classList.remove('loading');
                img.style.opacity = '1';
            };
        }

        const baseOptions: Partial<TinySliderSettings> = {
            container: sliderContainer,
            controls: controls ? controls.length === 2 : false,
            controlsContainer: controlsContainerEl ?? undefined,
            nav: !!navContainerEl,
            navContainer: navContainerEl ?? undefined,
            slideBy: 'page',
            mouseDrag: true,
            touch: true,
            lazyload: false,
            loop: false,
            speed: 500,
            gutter: 32,
            swipeAngle: 45,
            autoWidth: false,
            items: 1,
            onInit: () => {
                this.updateFocusableElements(sliderContainer);
            },
        };

        const specificOptions: Record<string, Partial<TinySliderSettings>> = {
            horizontal: {
                autoHeight: false,
            },
            vertical: {
                autoHeight: true,
                axis: 'vertical',
                swipeAngle: false,
            },
            row: {
                autoHeight: false,
                responsive: {
                    960: {
                        items: 3,
                        gutter: 32,
                    },
                    768: {
                        items: 2.3,
                        gutter: 24,
                    },
                    420: {
                        items: 1.5,
                    },
                    320: {
                        items: 1.3,
                    },
                    0: {
                        items: 1,
                        gutter: 20,
                    },
                },
            },
            autoplay: {
                autoplay: true,
                autoplayTimeout: autoplayTimeout,
                autoplayButtonOutput: false,
                autoplayHoverPause: true,
                rewind: true,
                animateDelay: false,
            },
        };

        const options: TinySliderSettings = {
            ...baseOptions,
            ...(autoplay ? specificOptions.autoplay : {}),
            ...(specificOptions[sliderType] ?? {}),
        };

        const slider: TinySliderInstance = tns(options);

        slider.events.on('dragEnd', () => slider.pause());
        slider.events.on('dragStart', () => slider.pause());
        slider.events.on('transitionEnd', () => this.updateFocusableElements(sliderContainer));
        sliderContainer.addEventListener('mouseenter', () => slider.pause());

        if (extraNavContainer) {
            const navButtons = Array.from(extraNavContainer.children);
            navButtons.forEach((button, i) => {
                button.addEventListener('click', () => {
                    slider.goTo(i);
                });
            });

            const updateActiveNav = (currentIndex: number) => {
                navButtons.forEach((button, i) => {
                    if (i === currentIndex) {
                        button.classList.add('active');
                    } else {
                        button.classList.remove('active');
                    }
                });
            };

            updateActiveNav(slider.getInfo().index);

            slider.events.on('indexChanged', () => {
                updateActiveNav(slider.getInfo().index);
            });
        }
        this.setupAutoplay(slider, sliderEl, autoplay);
    };

    private updateFocusableElements(sliderContainer: HTMLElement): void {
        const allSlides: HTMLCollection = sliderContainer.children;
        for (const slide of Array.from(allSlides) as HTMLElement[]) {
            const focusableElements: NodeListOf<HTMLElement> = slide.querySelectorAll('a, button');
            const isActiveSlide: boolean = slide.classList.contains('tns-slide-active');

            focusableElements.forEach((el) => {
                el.tabIndex = isActiveSlide ? 0 : -1;
            });
        }
    }

    private setupAutoplay(slider: TinySliderInstance, sliderEl: Element, autoplay: boolean): void {
        if (!autoplay) return;

        const observerCallback: IntersectionObserverCallback = (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    slider.play();
                } else {
                    slider.pause();
                }
            });
        };

        const observerOptions: IntersectionObserverInit = {
            root: null,
            threshold: 0.5,
        };

        const observer: IntersectionObserver = new IntersectionObserver(observerCallback, observerOptions);
        observer.observe(sliderEl);
    }
}

export default Slider;
