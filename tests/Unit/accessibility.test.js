import { test } from 'node:test';
import assert from 'node:assert/strict';
import { initializeAccessibility, normalizePreferences } from '../../resources/js/accessibility.js';

test('invalid stored preferences fall back to safe defaults', () => {
    assert.deepEqual(normalizePreferences({ textSize: '999', contrast: 'false', motion: 1 }), {
        textSize: '100', theme: 'default', contrast: false, links: false, spacing: false, motion: false,
    });
    assert.equal(normalizePreferences(null).textSize, '100');
});

function browser(stored, blocked = false) {
    const classes = new Set();
    const events = [];
    const status = { textContent: '' };
    const reset = { addEventListener: (_, handler) => { reset.click = handler; } };
    const controls = ['textSize', 'theme', 'contrast', 'links', 'spacing', 'motion'].map((key) => ({
        dataset: { accessPreference: key },
        type: ['textSize', 'theme'].includes(key) ? 'select-one' : 'checkbox',
        addEventListener(_, handler) { this.change = handler; },
    }));
    const panel = {
        hidden: true,
        querySelectorAll: () => controls,
        querySelector: (selector) => selector === '[data-access-reset]' ? reset : status,
    };
    globalThis.document = {
        querySelector: () => panel,
        querySelectorAll: () => [],
        documentElement: {
            style: {},
            dataset: {},
            classList: { toggle: (name, enabled) => enabled ? classes.add(name) : classes.delete(name) },
        },
        dispatchEvent: (event) => events.push(event),
    };
    globalThis.CustomEvent = class { constructor(type, options) { this.type = type; this.detail = options.detail; } };
    globalThis.localStorage = {
        getItem() { if (blocked) throw new Error('blocked'); return stored; },
        setItem(_, value) { if (blocked) throw new Error('blocked'); stored = value; },
    };
    return { panel, controls, reset, classes, events, status, stored: () => stored };
}

test('saved preferences apply on page load, changes persist, and reset restores defaults', () => {
    const ui = browser('{"textSize":"125","motion":true,"contrast":true}');
    initializeAccessibility();
    assert.equal(ui.panel.hidden, false);
    assert.equal(document.documentElement.style.fontSize, '125%');
    assert.ok(ui.classes.has('access-motion'));
    assert.ok(ui.classes.has('access-contrast'));
    const links = ui.controls.find((control) => control.dataset.accessPreference === 'links');
    links.checked = true;
    links.change();
    assert.equal(JSON.parse(ui.stored()).links, true);
    assert.equal(ui.events.at(-1).detail.motion, true);
    ui.reset.click();
    assert.equal(document.documentElement.style.fontSize, '100%');
    assert.equal(ui.classes.size, 0);
    assert.equal(JSON.parse(ui.stored()).motion, false);
    assert.equal(document.documentElement.dataset.accessTheme, 'default');
});

test('color themes persist and invalid saved themes fall back to original colors', () => {
    assert.equal(normalizePreferences({ theme: 'unknown' }).theme, 'default');
    const ui = browser('{"theme":"warm"}');
    initializeAccessibility();
    assert.equal(document.documentElement.dataset.accessTheme, 'warm');
    const theme = ui.controls.find((control) => control.dataset.accessPreference === 'theme');
    theme.value = 'dark';
    theme.change();
    assert.equal(document.documentElement.dataset.accessTheme, 'dark');
    assert.equal(JSON.parse(ui.stored()).theme, 'dark');
});

test('corrupt or blocked storage does not prevent controls from working', () => {
    for (const [stored, blocked] of [['invalid json', false], [null, true]]) {
        const ui = browser(stored, blocked);
        initializeAccessibility();
        assert.equal(ui.panel.hidden, false);
        const size = ui.controls[0];
        size.value = '150';
        size.change();
        assert.equal(document.documentElement.style.fontSize, '150%');
        assert.match(ui.status.textContent, blocked ? /storage is unavailable/ : /saved/);
    }
});
