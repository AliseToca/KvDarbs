export default class Tabs {
    private readonly element: HTMLElement;
    private readonly tabs: HTMLElement[];
    private readonly panels: HTMLElement[];

    constructor(element: HTMLElement) {
        this.element = element;
        this.tabs = Array.from(this.element.querySelectorAll('[data-tab]'));
        this.panels = Array.from(this.element.querySelectorAll('[data-tab-panel]'));

        this.tabs.forEach((tab, index) => {
            tab.addEventListener('click', () => {
                this.activateTab(index);
                tab.focus();
            });
            tab.setAttribute('aria-controls', this.panels[index]?.id || '');
        });

        this.element.addEventListener('keydown', (event: KeyboardEvent) => {
            const activeIndex = this.tabs.findIndex((tab) => tab.getAttribute('aria-selected') === 'true');
            if (activeIndex === -1) return;
            if (event.key === 'ArrowRight' || event.key === 'ArrowDown') {
                event.preventDefault();
                this.activateTab((activeIndex + 1) % this.tabs.length);
                this.tabs[(activeIndex + 1) % this.tabs.length].focus();
            } else if (event.key === 'ArrowLeft' || event.key === 'ArrowUp') {
                event.preventDefault();
                this.activateTab((activeIndex - 1 + this.tabs.length) % this.tabs.length);
                this.tabs[(activeIndex - 1 + this.tabs.length) % this.tabs.length].focus();
            }
        });

        this.element.addEventListener(
            'focus',
            () => {
                const activeTab = this.tabs.find((tab) => tab.getAttribute('aria-selected') === 'true') || this.tabs[0];
                activeTab.focus();
            },
            true
        );

        this.activateTab(0);
    }

    static init(): void {
        document.querySelectorAll('[data-tabbed-content]').forEach((element) => {
            new Tabs(element as HTMLElement);
        });
    }

    activateTab(index: number): void {
        this.tabs.forEach((tab, i) => {
            const isActive = i === index;
            tab.setAttribute('aria-selected', isActive.toString());
            tab.setAttribute('tabindex', isActive ? '0' : '-1');
        });
        this.panels.forEach((panel, i) => {
            panel.setAttribute('aria-hidden', (i !== index).toString());
        });
    }
}
