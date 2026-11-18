export function normalizePreferences(value) {
    const preferences = value && typeof value === 'object' ? value : {};
    return {
        textSize: ['100', '112.5', '125', '150'].includes(preferences.textSize) ? preferences.textSize : '100',
        theme: ['default', 'warm', 'cool', 'dark'].includes(preferences.theme) ? preferences.theme : 'default',
        contrast: preferences.contrast === true,
        links: preferences.links === true,
        spacing: preferences.spacing === true,
        motion: preferences.motion === true,
    };
}

export function initializeAccessibility() {
    const panel = document.querySelector('[data-accessibility-panel]');
    if (!panel) return;
    const storageKey = 'idealwoman.accessibility';
    const controls = [...panel.querySelectorAll('[data-access-preference]')];
    let preferences = normalizePreferences(null);
    try {
        preferences = normalizePreferences(JSON.parse(localStorage.getItem(storageKey)));
    } catch {}

    const apply = () => {
        document.documentElement.style.fontSize = `${preferences.textSize}%`;
        document.documentElement.dataset.accessTheme = preferences.theme;
        for (const key of ['contrast', 'links', 'spacing', 'motion']) {
            document.documentElement.classList.toggle(`access-${key}`, preferences[key]);
        }
        controls.forEach((control) => {
            const value = preferences[control.dataset.accessPreference];
            if (control.type === 'checkbox') control.checked = value;
            else control.value = value;
        });
        document.dispatchEvent(new CustomEvent('accessibilitychange', { detail: preferences }));
    };
    const save = () => {
        apply();
        try {
            localStorage.setItem(storageKey, JSON.stringify(preferences));
            panel.querySelector('[data-access-status]').textContent = 'Preferences saved.';
        } catch {
            panel.querySelector('[data-access-status]').textContent = 'Preferences applied for this page. Browser storage is unavailable.';
        }
    };
    controls.forEach((control) => control.addEventListener('change', () => {
        preferences[control.dataset.accessPreference] = control.type === 'checkbox' ? control.checked : control.value;
        preferences = normalizePreferences(preferences);
        save();
    }));
    panel.querySelector('[data-access-reset]').addEventListener('click', () => {
        preferences = normalizePreferences(null);
        save();
    });
    apply();
    panel.hidden = false;
    const dialog = panel.querySelector('dialog');
    if (dialog) {
        panel.addEventListener('toggle', () => {
            if (panel.open && !dialog.open) dialog.showModal();
            if (!panel.open && dialog.open) dialog.close();
        });
        dialog.addEventListener('close', () => {
            panel.open = false;
        });
        dialog.querySelector('[data-access-close]').addEventListener('click', () => dialog.close());
        dialog.addEventListener('click', (event) => {
            if (event.target !== dialog) return;
            const bounds = dialog.getBoundingClientRect();
            if (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom) {
                dialog.close();
            }
        });
    }
    document.addEventListener('click', (event) => {
        document.querySelectorAll('.access-panel[open]').forEach((details) => {
            if (!details.contains(event.target)) details.open = false;
        });
    });
    document.querySelectorAll('.access-panel').forEach((details) => {
        details.addEventListener('toggle', () => {
            if (!details.open) return;
            document.querySelectorAll('.access-panel').forEach((other) => {
                if (other !== details) other.open = false;
            });
        });
        details.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && details.open) {
                details.open = false;
                details.querySelector('summary').focus();
            }
        });
    });
}
