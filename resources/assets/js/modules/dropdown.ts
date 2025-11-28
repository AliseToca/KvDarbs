class Dropdown {
    private static dropdownInstances: Dropdown[] = [];
    private readonly dropdownToggle: HTMLElement;
    private readonly dropdownContent: HTMLElement;
    private readonly dropdown: HTMLElement;

    constructor(dropdown: HTMLElement) {
        this.dropdown = dropdown;
        this.dropdownToggle = dropdown.querySelector('[data-dropdown-toggle]');
        this.dropdownContent = dropdown.querySelector('[data-dropdown-content]');
        this.registerEvents();
        this.initAccessibilityFeatures();
        Dropdown.dropdownInstances.push(this);
    }

    static init(): void {
        const dropdownElements: NodeListOf<HTMLElement> = document.querySelectorAll('[data-dropdown]');
        dropdownElements?.forEach((dropdown: HTMLElement) => {
            new Dropdown(dropdown);
        });
    }

    private registerEvents(): void {
        document.addEventListener('click', (event: Event) => {
            if (!this.dropdown.contains(event.target as Node)) {
                this.closeDropdown();
            }
        });

        this.dropdownToggle.addEventListener('click', (event: Event) => {
            event.stopPropagation();
            Dropdown.closeAllDropdowns(this);
            this.toggleDropdown();
        });
    }

    private initAccessibilityFeatures(): void {
        this.dropdownToggle.setAttribute('aria-expanded', 'false');
        this.dropdownContent.setAttribute('aria-hidden', 'true');
    }

    private toggleDropdown(): void {
        const isExpanded = this.dropdownToggle.getAttribute('aria-expanded') === 'true';
        this.dropdownToggle.setAttribute('aria-expanded', String(!isExpanded));
        this.dropdownContent.setAttribute('aria-hidden', String(isExpanded));
        this.dropdown.classList.toggle('show');
    }

    private closeDropdown(): void {
        this.dropdownToggle.setAttribute('aria-expanded', 'false');
        this.dropdownContent.setAttribute('aria-hidden', 'true');
        this.dropdown.classList.remove('show');
    }

    static closeAllDropdowns(except?: Dropdown): void {
        Dropdown.dropdownInstances.forEach((dropdownInstance: Dropdown) => {
            if (dropdownInstance !== except) {
                dropdownInstance.closeDropdown();
            }
        });
    }
}

export default Dropdown;
