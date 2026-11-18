import { languages, translateText, textLanguage } from './translations.js';

export function initializeTranslation() {
    const panel = document.querySelector('[data-translation-controls]');
    if (!panel) return;
    const languageButtons = [...panel.querySelectorAll('[data-language]')];
    const status = panel.querySelector('[data-translation-status]');
    const originals = new WeakMap();
    const rendered = new WeakMap();
    const attributes = new Map();
    const storageKey = 'idealwoman.language';
    const originalTitle = document.title;
    let language = 'en';
    try {
        const saved = localStorage.getItem(storageKey);
        if (languages.includes(saved)) language = saved;
    } catch {}

    const excluded = 'script, style, textarea, [translate="no"], [contenteditable], .editor-toolbar';
    const observer = new MutationObserver(() => apply());
    const apply = () => {
        observer.disconnect();
        const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
        while (walker.nextNode()) {
            const node = walker.currentNode;
            if (!node.parentElement || node.parentElement.closest(excluded)) continue;
            if (!originals.has(node) || (rendered.has(node) && node.nodeValue !== rendered.get(node))) {
                originals.set(node, node.nodeValue);
            }
            const text = translateText(originals.get(node), language);
            if (/\p{L}/u.test(originals.get(node))) {
                node.parentElement.lang = textLanguage(originals.get(node), language);
            }
            if (node.nodeValue !== text) node.nodeValue = text;
            rendered.set(node, text);
        }
        document.querySelectorAll('[aria-label], [placeholder], img[alt]').forEach((element) => {
            if (element.closest(excluded)) return;
            if (!attributes.has(element)) {
                attributes.set(element, Object.fromEntries(['aria-label', 'placeholder', 'alt']
                    .filter((name) => element.hasAttribute(name))
                    .map((name) => [name, element.getAttribute(name)])));
            }
            Object.entries(attributes.get(element)).forEach(([name, source]) => {
                element.setAttribute(name, translateText(source, language));
                if (name === 'alt') element.lang = textLanguage(source, language);
            });
        });
        document.documentElement.lang = language;
        document.documentElement.dir = language === 'ar' ? 'rtl' : 'ltr';
        document.title = originalTitle.split(' | ').map((part) => translateText(part, language)).join(' | ');
        languageButtons.forEach((button) => {
            button.setAttribute('aria-pressed', String(button.dataset.language === language));
        });
        observer.observe(document.body, { subtree: true, childList: true, characterData: true });
    };
    languageButtons.forEach((button) => button.addEventListener('click', () => {
        if (!languages.includes(button.dataset.language)) return;
        language = button.dataset.language;
        apply();
        try { localStorage.setItem(storageKey, language); } catch {}
        status.textContent = button.textContent.trim();
        status.lang = language;
    }));
    apply();
}
