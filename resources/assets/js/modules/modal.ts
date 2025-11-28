import Dialog from 'a11y-dialog';
import { type ModalOptions } from '@root/definitions/globals';

export default class Modal {
    public readonly container: HTMLElement;
    private readonly content: HTMLElement;
    private readonly core: Dialog;
    private readonly options: ModalOptions;

    constructor(options: ModalOptions = {}) {
        this.container = document.querySelector('[data-modal-container]');
        if (!this.container) {
            throw new Error('Modal container not found');
        }
        this.options = options;
        this.content = this.container.querySelector('[data-modal-content]');
        this.core = new Dialog(this.container);

        this.core.on('hide', this.onHide.bind(this));
    }

    setTrigger(trigger: HTMLElement): this {
        const { modalUrl: contentUrl = null, modalId: templateName = null } = trigger.dataset;

        Object.assign(this.options, { contentUrl, templateName });

        trigger.addEventListener('click', (event: MouseEvent) => {
            const target = event.target as HTMLElement;
            if (!target.isSameNode(trigger)) {
                return;
            }

            this.loadContent().then(this.show);
        });

        return this;
    }

    injectContent(html: string): this {
        this.content.innerHTML = html;
        return this;
    }

    clearContent(): void {
        this.content.innerHTML = '';
    }

    show = (): void => {
        document.body.classList.add('no-scroll');
        this.core.show();

        if (this.options.templateName) {
            this.container.classList.add(this.options.templateName);
        }

        if (typeof this.options.afterOpen === 'function') {
            this.options.afterOpen();
        }
    };

    hide = (): void => {
        this.core.hide();
    };

    private onHide(): void {
        document.body.classList.remove('no-scroll');

        if (this.options.templateName) {
            this.container.classList.remove(this.options.templateName);
        }

        if (typeof this.options.afterClose === 'function') {
            this.options.afterClose();
        } else {
            this.clearContent();
        }
    }

    private async loadContent(): Promise<Modal> {
        const content = this.options.contentUrl
            ? await this.getContentFromRemote()
            : this.getContentFromTemplate(this.options.templateName);

        if (!content?.length) {
            return await Promise.reject('Failed to retrieve modal content');
        }

        this.injectContent(content);

        return this;
    }

    private getContentFromTemplate(id: string): string | null {
        const template: HTMLTemplateElement = document.querySelector(`template[data-modal-template="${id}"]`);

        if (!template) {
            return null;
        }

        return template.innerHTML;
    }

    private async getContentFromRemote(): Promise<string | null> {
        try {
            const response = await fetch(this.options.contentUrl);
            const data = await response.json();
            return data?.html ?? null;
        } catch (error) {
            console.error(error.message);
            return null;
        }
    }

    public readonly afterOpen = (callback: () => void): this => {
        this.options.afterOpen = callback.bind(this);
        return this;
    };

    public readonly afterClose = (callback: () => void): this => {
        this.options.afterClose = callback.bind(this);
        return this;
    };
}
