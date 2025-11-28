type AcceptToken =
    | { kind: 'ext'; value: string }
    | { kind: 'mime'; value: string }
    | { kind: 'wildcard'; value: string };

interface Rejection {
    kind: 'type' | 'size';
    name: string;
    size?: number;
    maxSize?: number;
}
interface I18nStrings {
    someNotAdded: string;
    tooLarge: string;
    limitIs: string;
    typeNotAllowed: string;
}

class FileInput {
    private readonly fileUploadElements: NodeListOf<HTMLElement>;

    constructor() {
        this.fileUploadElements = document.querySelectorAll('[data-file-upload]');
        this.initializeHandlers();
    }

    private initializeHandlers(): void {
        this.fileUploadElements.forEach((wrapper) => this.initSingle(wrapper));
    }

    private initSingle(element: HTMLElement): void {
        const input = element.querySelector('input[type="file"]') as HTMLInputElement | null;
        const list = element.querySelector('[data-attached-files]') as HTMLElement | null;
        if (!input || !list) {
            console.error('File input or file list element not found within:', element);
            return;
        }
        const allowed = this.parseAcceptAttribute(input.getAttribute('accept'));
        const maxSizeAttr = input.getAttribute('data-max-size-mb');
        const maxSizeBytes = maxSizeAttr ? parseFloat(maxSizeAttr) * 1024 * 1024 : null;
        const feedback = this.ensureFeedbackElement(element, list);
        const i18n = this.getI18n(element);

        const process = (files: File[], original: File[]) => {
            const { kept, rejections } = this.filterFiles(files, allowed, maxSizeBytes);
            if (kept.length !== original.length) {
                this.setFiles(input, kept);
            }
            this.updateFeedback(feedback, rejections, i18n, original);
            this.updateFileList(input, list);
            this.updateFileUploadContainerClass(input, element);
        };

        input.addEventListener('change', () => process(Array.from(input.files || []), Array.from(input.files || [])));
        input.addEventListener('drop', (e: DragEvent) => {
            e.preventDefault();
            const dropped = Array.from(e.dataTransfer?.files || []);
            process(dropped, dropped);
        });
        input.addEventListener('dragover', (e) => {
            e.preventDefault();
            element.classList.add('file-dragging');
        });
    }

    // Parse accept attribute into structured tokens
    private parseAcceptAttribute(accept: string | null): AcceptToken[] {
        if (!accept) return [];
        return accept
            .split(',')
            .map((token) => token.trim().toLowerCase())
            .filter(Boolean)
            .map<AcceptToken>((token) => {
                if (token.endsWith('/*')) {
                    // wildcard mime type e.g. image/* => prefix "image/"
                    return { kind: 'wildcard', value: token.slice(0, -1) };
                }
                if (token.startsWith('.')) {
                    return { kind: 'ext', value: token }; // keep dot
                }
                if (token.includes('/')) {
                    return { kind: 'mime', value: token };
                }
                // Fallback: treat as extension even without dot
                return { kind: 'ext', value: `.${token}` };
            });
    }

    private filterFiles(
        files: File[],
        allowed: AcceptToken[],
        maxSizeBytes: number | null
    ): { kept: File[]; rejections: Rejection[] } {
        const rejections: Rejection[] = [];
        const noTypeRestriction = !allowed.length;
        const kept = files.filter((file) => {
            const lowerName = file.name.toLowerCase();
            const mime = file.type.toLowerCase();
            const typeOk = noTypeRestriction
                ? true
                : allowed.some((token) => {
                      switch (token.kind) {
                          case 'ext':
                              return lowerName.endsWith(token.value);
                          case 'mime':
                              return mime === token.value;
                          case 'wildcard':
                              return mime.startsWith(token.value);
                      }
                  });
            if (!typeOk) {
                rejections.push({ kind: 'type', name: file.name });
                return false;
            }
            if (maxSizeBytes && file.size > maxSizeBytes) {
                rejections.push({ kind: 'size', name: file.name, size: file.size, maxSize: maxSizeBytes });
                return false;
            }
            return true;
        });
        return { kept, rejections };
    }

    private updateFeedback(
        feedback: HTMLElement | null,
        rejections: Rejection[],
        i18n: I18nStrings,
        originalFiles: File[]
    ): void {
        if (!feedback) return;
        if (!rejections.length) {
            feedback.innerHTML = '';
            feedback.classList.remove('has-messages');
            return;
        }
        const listItems = rejections
            .map((rej) => {
                if (rej.kind === 'size') {
                    const sizeStr = this.formatBytes(rej.size!);
                    const maxStr = this.formatBytes(rej.maxSize!);
                    return `<li class="rejection size"><strong>${this.escapeHtml(
                        rej.name
                    )}</strong><span class="reason"> – ${this.escapeHtml(i18n.tooLarge)} (${sizeStr}), ${this.escapeHtml(i18n.limitIs)} ${maxStr}.</span></li>`;
                }
                const mime = (originalFiles.find((f) => f.name === rej.name)?.type || '').toLowerCase();
                const typeMsg = i18n.typeNotAllowed.replace(':type', mime || 'unknown');
                return `<li class="rejection type"><strong>${this.escapeHtml(
                    rej.name
                )}</strong><span class="reason"> – ${this.escapeHtml(typeMsg)}.</span></li>`;
            })
            .join('');
        feedback.innerHTML = `<div class="file-rejections"><p class="heading"><strong>${this.escapeHtml(
            i18n.someNotAdded
        )}</strong></p><ul class="file-rejections" role="list">${listItems}</ul></div>`;
        feedback.classList.add('has-messages');
    }

    private ensureFeedbackElement(wrapper: HTMLElement, fileListElement: HTMLElement): HTMLElement {
        let feedback = wrapper.querySelector('[data-file-feedback]') as HTMLElement | null;
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = 'file-feedback';
            feedback.setAttribute('data-file-feedback', '');
            feedback.setAttribute('role', 'status');
            feedback.setAttribute('aria-live', 'polite');
            fileListElement.parentElement?.insertBefore(feedback, fileListElement);
        }
        return feedback;
    }

    private getI18n(element: HTMLElement): I18nStrings {
        return {
            someNotAdded: element.getAttribute('data-i18n-some-not-added') || "Some files weren't added:",
            tooLarge: element.getAttribute('data-i18n-too-large') || 'too large',
            limitIs: element.getAttribute('data-i18n-limit-is') || 'limit is',
            typeNotAllowed: element.getAttribute('data-i18n-type-not-allowed') || 'file type :type is not allowed',
        };
    }

    private escapeHtml(text: string): string {
        return text.replace(
            /[&<>"']/g,
            (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c]!
        );
    }

    private formatBytes(size: number): string {
        const units = ['bytes', 'KB', 'MB', 'GB', 'TB'];
        let idx = 0;
        let value = size;
        while (value >= 1024 && idx < units.length - 1) {
            value /= 1024;
            idx++;
        }
        const decimals = idx === 0 ? 0 : value < 10 ? 2 : 1;
        return `${value.toFixed(decimals)} ${units[idx]}`;
    }

    private setFiles(fileInput: HTMLInputElement, files: File[]): void {
        const dataTransfer = new DataTransfer();
        files.forEach((file) => dataTransfer.items.add(file));
        fileInput.files = dataTransfer.files;
    }

    private updateFileUploadContainerClass(fileInput: HTMLInputElement, fileUploadElement: HTMLElement): void {
        const hasFiles = fileInput.files && fileInput.files.length > 0;
        fileUploadElement.classList.toggle('file-added', hasFiles);
        fileUploadElement.classList.remove('file-dragging');
    }

    private updateFileList(fileInput: HTMLInputElement, fileListElement: HTMLElement): void {
        fileListElement.innerHTML = '';
        const template = document.getElementById('fileItemTemplate') as HTMLTemplateElement;
        Array.from(fileInput.files).forEach((file, index) => {
            const clone = document.importNode(template.content, true);
            const button = clone.querySelector('button');
            let span = clone.querySelector('[data-file-name]') as HTMLElement | null;
            if (!span) {
                span = document.createElement('span');
                span.dataset.fileName = '';
                clone.appendChild(span);
            }
            span.textContent = this.applyMiddleEllipsis(file.name, 40);
            if (button) {
                button.setAttribute('aria-label', `Remove file ${file.name}`);
                button.onclick = (event) => {
                    event.stopPropagation();
                    this.removeFile(fileInput, index, fileListElement);
                };
            }
            fileListElement.appendChild(clone);
        });
    }

    private removeFile(fileInput: HTMLInputElement, index: number, fileListElement: HTMLElement): void {
        const newFileList = new DataTransfer();
        Array.from(fileInput.files).forEach((file, fileIndex) => {
            if (index !== fileIndex) {
                newFileList.items.add(file);
            }
        });
        fileInput.files = newFileList.files;
        this.updateFileList(fileInput, fileListElement);
        this.updateFileUploadContainerClass(fileInput, fileListElement.closest('[data-file-upload]') as HTMLElement);
    }

    private applyMiddleEllipsis(text: string, maxLen: number): string {
        if (text.length <= maxLen) return text;
        const midPoint = Math.ceil(maxLen / 2);
        return `${text.substr(0, midPoint)}…${text.substr(text.length - midPoint)}`;
    }

    static init(): void {
        new FileInput();
    }
}

export default FileInput;
