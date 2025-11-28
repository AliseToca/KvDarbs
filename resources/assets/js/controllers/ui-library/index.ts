import Controller from '@controllers/controller';
import Modal from '@modules/modal';

import hljs from 'highlight.js/lib/core';
import javascript from 'highlight.js/lib/languages/javascript';
import typescript from 'highlight.js/lib/languages/typescript';
import php from 'highlight.js/lib/languages/php';
import scss from 'highlight.js/lib/languages/scss';
import html from 'highlight.js/lib/languages/xml';
import json from 'highlight.js/lib/languages/json';

class StyleGuideController extends Controller {
    private static copyListenerAttached = false;
    static onStart() {
        return super.onStart();
    }
    init() {
        hljs.registerLanguage('javascript', javascript);
        hljs.registerLanguage('typescript', typescript);
        hljs.registerLanguage('blade', html);
        hljs.registerLanguage('css', scss);
        hljs.registerLanguage('html', html);
        hljs.registerLanguage('json', json);
        hljs.registerLanguage('php', php);
        hljs.registerLanguage('scss', scss);
        hljs.registerLanguage('xml', html);

        document.querySelectorAll('pre code').forEach((block) => {
            hljs.highlightElement(block as HTMLElement);
        });
        console.log('Code highlighting initialized');

        // Simple ScrollSpy for UI library navigation
        function initScrollSpy(navSelector = '[data-contents]', activeClass = 'active', offset = 100) {
            const nav = document.querySelector(navSelector);
            if (!nav) return;
            const links = Array.from(nav.querySelectorAll('a[href^="#"]'));
            const sections = links
                .map((link) => document.getElementById(link.getAttribute('href').replace('#', '')))
                .filter(Boolean);

            function onScroll() {
                const scrollPos = window.scrollY + offset;
                let currentSection = null;
                for (const section of sections) {
                    if (section.offsetTop <= scrollPos) {
                        currentSection = section;
                    }
                }
                links.forEach((link) => {
                    link.classList.remove(activeClass);
                    if (currentSection && link.getAttribute('href').replace('#', '') === currentSection.id) {
                        link.classList.add(activeClass);
                    }
                });
            }

            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll();
        }

        // Initialize ScrollSpy
        initScrollSpy();

        // Copy-to-clipboard handler (single attachment)
        if (!StyleGuideController.copyListenerAttached) {
            document.addEventListener('click', async (e) => {
                const target = e.target as HTMLElement | null;
                if (!target) return;
                const btn = target.closest('[data-copy]') as HTMLButtonElement | null;
                if (!btn) return;
                const id = btn.getAttribute('data-copy-target');
                if (!id) return;
                const el = document.getElementById(id);
                if (!el) return;
                const text = (el as HTMLElement).innerText;
                const successMs = 1600;
                try {
                    await navigator.clipboard.writeText(text);
                    const original = btn.textContent || '';
                    btn.textContent = 'Copied';
                    btn.disabled = true;
                    setTimeout(() => {
                        btn.textContent = original;
                        btn.disabled = false;
                    }, successMs);
                } catch (err) {
                    console.warn('Copy failed', err);
                    btn.textContent = 'Error';
                    setTimeout(() => {
                        btn.textContent = 'Copy';
                    }, successMs);
                }
            });
            StyleGuideController.copyListenerAttached = true;
        }

        const staticModalTrigger = document.querySelector('[data-show-static-modal]') as HTMLElement;
        if (staticModalTrigger !== null) {
            new Modal().setTrigger(staticModalTrigger);
        }

        const dynamicModalTrigger = document.querySelector('[data-show-dynamic-modal]') as HTMLElement;
        if (dynamicModalTrigger !== null) {
            new Modal().setTrigger(dynamicModalTrigger);
        }
    }
}

StyleGuideController.onStart();
