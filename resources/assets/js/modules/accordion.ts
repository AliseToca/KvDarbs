const ANIMATION_DURATION = 200;
const ANIMATION_EASING = 'ease-out';

export default class Accordion {
    private readonly element: HTMLDetailsElement;
    private readonly summary: HTMLElement | null;
    private readonly content: HTMLElement | null;
    private animation: Animation | null;
    private isClosing: boolean;
    private isExpanding: boolean;

    constructor(element: HTMLDetailsElement) {
        this.element = element;
        this.element = element;
        this.summary = element.querySelector('summary');
        this.content = element.querySelector('[data-accordion-content]');
        this.animation = new Animation();
        this.isClosing = false;
        this.isExpanding = false;

        if (this.summary !== null) {
            this.summary.addEventListener('click', this.onClick);
            this.summary.setAttribute('aria-controls', this.content?.id);
        } else {
            throw new Error('Accordion requires a <summary> element');
        }

        if (this.content !== null) {
            this.content.setAttribute('aria-hidden', 'true');
        }
    }

    static init(): void {
        const accordionElements: NodeListOf<HTMLDetailsElement> = document.querySelectorAll('details[data-accordion]');
        accordionElements?.forEach((element: HTMLDetailsElement) => {
            new Accordion(element);
        });
    }

    onClick = (event: MouseEvent): void => {
        event.preventDefault();
        this.element.style.overflow = 'hidden';
        if (this.isClosing || !this.element.open) {
            this.open();
        } else if (this.isExpanding || this.element.open) {
            this.close();
        }
    };

    open(): void {
        this.element.style.height = `${this.element.offsetHeight}px`;
        this.element.open = true;
        window.requestAnimationFrame(() => {
            this.expand();
        });

        if (this.content !== null) {
            this.content.setAttribute('aria-hidden', 'false');
        }
    }

    close(): void {
        this.isClosing = true;
        const startHeight = `${this.element.offsetHeight}px`;
        const endHeight = this.summary !== null ? `${this.summary.offsetHeight}px` : '0px';

        this.animation?.cancel();
        this.animation = this.element.animate(
            {
                height: [startHeight, endHeight],
            },
            {
                duration: ANIMATION_DURATION,
                easing: ANIMATION_EASING,
            }
        );

        this.animation.onfinish = () => {
            this.onAnimationFinish(false);
        };
        this.animation.oncancel = () => {
            this.isClosing = false;
        };

        if (this.content !== null) {
            this.content.setAttribute('aria-hidden', 'true');
        }
    }

    expand(): void {
        if (this.summary !== null && this.content !== null) {
            this.isExpanding = true;
            const startHeight = `${this.element.offsetHeight}px`;
            const endHeight = `${this.summary.offsetHeight + this.content.offsetHeight}px`;

            this.animation?.cancel();
            this.animation = this.element.animate(
                {
                    height: [startHeight, endHeight],
                },
                {
                    duration: ANIMATION_DURATION,
                    easing: ANIMATION_EASING,
                }
            );
            this.animation.onfinish = () => {
                this.onAnimationFinish(true);
            };
            this.animation.oncancel = () => {
                this.isExpanding = false;
            };
        }
    }

    onAnimationFinish(isOpen: boolean): void {
        this.element.open = isOpen;
        this.animation?.cancel();
        this.isClosing = false;
        this.isExpanding = false;
        this.element.style.height = '';
        this.element.style.overflow = '';
    }
}
