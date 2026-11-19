export function initializeTranslation() {
    const panel = document.querySelector('[data-translation-controls]');
    if (!panel) return;
    const buttons = [...panel.querySelectorAll('[data-language]')];
    const status = panel.querySelector('[data-translation-status]');
    const retry = panel.querySelector('[data-translation-retry]');
    const wrapper = panel.querySelector('[data-translation-provider]');
    const storageKey = 'idealwoman.language';
    let selected = 'en';
    let loading = false;
    let ready = false;
    let timer;
    const announce = (message) => {
        status.textContent = message;
        status.hidden = !message;
    };
    const sync = () => {
        document.documentElement.lang = selected;
        document.documentElement.dir = ['ar', 'ur', 'fa'].includes(selected) ? 'rtl' : 'ltr';
        buttons.forEach(button => button.setAttribute('aria-pressed', String(button.dataset.language === selected)));
    };
    const translate = () => {
        const select = wrapper.querySelector('select');
        if (!select) return;
        if (![...select.options].some(option => option.value === `en|${selected}`)) {
            if (selected !== 'lg') return;
            select.add(new Option('Luganda', 'en|lg'));
        }
        select.value = `en|${selected}`;
        select.dispatchEvent(new Event('change', { bubbles: true }));
        sync();
        announce('Automatic translation requested. If text stays in English, try again.');
        retry.hidden = false;
        try { localStorage.setItem(storageKey, selected); } catch {}
    };
    const failed = () => {
        clearTimeout(timer);
        loading = false;
        document.getElementById('idealwoman-translation-provider')?.remove();
        announce('Translation could not load. Please check your connection and try again.');
        retry.hidden = false;
    };
    const load = () => {
        if (ready) { translate(); return; }
        if (loading) return;
        loading = true;
        retry.hidden = true;
        announce('Loading automatic translation…');
        window.gtranslateSettings = {
            default_language: 'en',
            languages: buttons.map(button => button.dataset.language).filter(language => language !== 'lg'),
            native_language_names: true,
            detect_browser_language: false,
            wrapper_selector: '#idealwoman-language-provider',
        };
        const script = document.createElement('script');
        script.id = 'idealwoman-translation-provider';
        script.src = 'https://cdn.gtranslate.net/widgets/latest/dropdown.js';
        script.async = true;
        script.addEventListener('error', failed, { once: true });
        script.addEventListener('load', () => {
            clearTimeout(timer);
            if (!wrapper.querySelector('select')) { failed(); return; }
            loading = false;
            ready = true;
            translate();
        }, { once: true });
        // A slow request stays single-flight; a timeout never starts a second initializer.
        timer = setTimeout(() => announce('Translation is taking longer than expected. Please check your connection or reload the page.'), 20000);
        document.head.appendChild(script);
    };
    document.querySelectorAll('input, textarea, [contenteditable], .brand, .footer-brand, .footer-name, .editor-toolbar').forEach(element => {
        element.setAttribute('translate', 'no');
        element.classList.add('notranslate');
    });
    buttons.forEach(button => button.addEventListener('click', () => {
        selected = button.dataset.language;
        if (selected === 'en' && !ready && !loading) {
            sync();
            announce('');
            try { localStorage.removeItem(storageKey); } catch {}
            return;
        }
        load();
    }));
    retry.addEventListener('click', load);
    const menu = panel.closest('details');
    menu?.addEventListener('toggle', () => { if (menu.open) load(); });
    try {
        const saved = localStorage.getItem(storageKey);
        if (buttons.some(button => button.dataset.language === saved) && saved !== 'en') {
            selected = saved;
            load();
        }
    } catch {}
}
