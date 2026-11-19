import { test } from 'node:test';
import assert from 'node:assert/strict';
import { initializeTranslation } from '../../resources/js/translation.js';

test('admin pages without translation controls never load the provider', () => {
    globalThis.document = { querySelector: () => null };
    try { assert.doesNotThrow(() => initializeTranslation()); }
    finally { delete globalThis.document; }
});

test('opening Translate loads once, reports network failure, and permits retry', () => {
    const listeners = {};
    const status = {};
    const retry = { addEventListener: (name, callback) => { listeners.retry = callback; } };
    const menu = { open: true, addEventListener: (name, callback) => { listeners.toggle = callback; } };
    const wrapper = { querySelector: () => null };
    const button = { dataset: { language: 'fr' }, addEventListener() {} };
    const panel = {
        querySelectorAll: () => [button],
        querySelector: selector => selector.includes('status') ? status : selector.includes('retry') ? retry : wrapper,
        closest: () => menu,
    };
    const scripts = [];
    globalThis.document = {
        querySelector: () => panel, querySelectorAll: () => [],
        getElementById: () => ({ remove() {} }),
        createElement: () => ({ addEventListener(name, callback) { this[name] = callback; } }),
        head: { appendChild: script => scripts.push(script) },
    };
    globalThis.window = {};
    globalThis.localStorage = { getItem: () => null };
    try {
        initializeTranslation();
        assert.equal(scripts.length, 0);
        listeners.toggle();
        listeners.toggle();
        assert.equal(scripts.length, 1);
        assert.equal(scripts[0].src, 'https://cdn.gtranslate.net/widgets/latest/dropdown.js');
        scripts[0].error();
        assert.match(status.textContent, /could not load/);
        assert.equal(retry.hidden, false);
        listeners.retry();
        assert.equal(scripts.length, 2);
        scripts[1].error();
    } finally {
        delete globalThis.document;
        delete globalThis.window;
        delete globalThis.localStorage;
    }
});
