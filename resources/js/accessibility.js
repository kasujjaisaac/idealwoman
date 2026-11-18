export function normalizePreferences(value) {
    const preferences = value && typeof value === 'object' ? value : {};
    return {
        textSize: ['100', '112.5', '125', '150'].includes(preferences.textSize) ? preferences.textSize : '100',
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
