import Env from '../helpers/env';

const COOKIES_FRAGMENT = '#cookies';

class Cookies {
    protected cookiesLayer: HTMLElement | null;
    protected settingsToggle: HTMLElement | null;
    protected tabs: NodeListOf<HTMLElement>;
    protected cookieGroups: NodeListOf<HTMLElement>;

    init() {
        this.cookiesLayer = document.querySelector('[data-cookies-layer]');
        this.settingsToggle = document.querySelector('[data-js-cookie-toggle]');
        this.tabs = document.querySelectorAll('[data-js-cookie-tabs] .input');
        this.cookieGroups = document.querySelectorAll('[data-js-cookie-group-list] > button');

        this.attachListeners();
        this.initTabs();
        this.initCookieGroupList();
    }

    attachListeners() {
        window.addEventListener('load', this.handleHashChange.bind(this));
        window.addEventListener('hashchange', this.handleHashChange.bind(this));

        const acceptAll = document.querySelector('[data-cookies-accept-all]');
        if (acceptAll) {
            acceptAll.addEventListener('click', (e) => {
                e.preventDefault();
                history.pushState('', document.title, window.location.pathname + window.location.search);
                const href = (e.currentTarget as HTMLAnchorElement).getAttribute('href');
                if (href) window.location.href = href;
            });
        }

        if (this.settingsToggle) {
            this.settingsToggle.addEventListener('click', this.toggleSettings.bind(this));
        }
    }

    handleHashChange() {
        if (window.location.hash === COOKIES_FRAGMENT) {
            if (this.cookiesLayer) this.cookiesLayer.setAttribute('data-hidden', 'false');
            this.toggleSettings();
        }
    }

    initTabs() {
        if (!this.tabs || this.tabs.length < 2) {
            return;
        }

        this.tabs.forEach((tab) => {
            tab.addEventListener('click', (e) => {
                e.preventDefault();
                const target = e.currentTarget as HTMLElement;
                const input = target.querySelector('input');
                if (!input) return;
                const currentSelector = `.${input.value}`;
                const content = document.querySelectorAll('[data-js-tab-content]');
                this.toggleContent(this.tabs, target, content, currentSelector);
            });
        });
    }

    initCookieGroupList() {
        this.cookieGroups.forEach((group) => {
            group.addEventListener('click', (e) => {
                e.preventDefault();
                const target = e.currentTarget as HTMLElement;
                const groupId = target.getAttribute('data-js-cookie-group');
                if (!groupId) return;
                const currentSelector = `#${groupId}`;
                const content = document.querySelectorAll('[data-js-cookie-group-details]');
                this.toggleContent(this.cookieGroups, target, content, currentSelector);
            });
        });
    }

    toggleSettings() {
        const body = document.body;
        if (this.settingsToggle) this.settingsToggle.classList.toggle('active');
        if (this.cookiesLayer) this.cookiesLayer.classList.toggle('expanded');
        if (body.classList.contains('prevent-scroll')) {
            body.classList.remove('prevent-scroll');
        } else if (!Env.isDesktop()) {
            body.classList.add('prevent-scroll');
        }
    }

    toggleContent(
        triggers: NodeListOf<HTMLElement>,
        target: HTMLElement,
        content: NodeListOf<Element>,
        selector: string
    ) {
        triggers.forEach((t) => t.classList.remove('selected'));
        target.classList.add('selected');
        content.forEach((c) => ((c as HTMLElement).style.display = 'none'));
        const showEl = Array.from(content).find((c) => c.matches(selector));
        if (showEl) (showEl as HTMLElement).style.display = '';
    }
}

export default Cookies;
