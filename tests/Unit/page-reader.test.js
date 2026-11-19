import { test } from 'node:test';
import assert from 'node:assert/strict';
import { splitSpeechText, createPageReader, collectPageText } from '../../resources/js/page-reader.js';

function setup() {
    const spoken = [];
    const states = [];
    const calls = [];
    const engine = {
        speak: utterance => spoken.push(utterance),
        cancel: () => calls.push('cancel'),
        pause: () => calls.push('pause'), resume: () => calls.push('resume'),
    };
    const reader = createPageReader(engine, class { constructor(text) { this.text = text; } }, value => states.push(value));
    return { reader, spoken, states, calls };
}

test('long page text is read in bounded chunks without losing words', () => {
    const text = Array(200).fill('Community programmes support young women.').join(' ');
    const chunks = splitSpeechText(text);
    assert.ok(chunks.every(chunk => chunk.length <= 220));
    assert.equal(chunks.join(' '), text);
    assert.deepEqual(splitSpeechText('   '), []);
    assert.equal(splitSpeechText('x'.repeat(500)).join(''), 'x'.repeat(500));
});

test('reader applies language and speed, pauses, resumes, and finishes the page', () => {
    const { reader, spoken, states, calls } = setup();
    const voice = { lang: 'fr-FR' };
    reader.start('Bonjour. '.repeat(40), 'fr', 0.75, voice);
    assert.equal(spoken[0].lang, 'fr');
    assert.equal(spoken[0].rate, 0.75);
    assert.equal(spoken[0].voice, voice);
    reader.pause();
    assert.equal(states.at(-1), 'paused');
    reader.resume();
    assert.equal(states.at(-1), 'reading');
    assert.deepEqual(calls.slice(-2), ['pause', 'resume']);
    spoken[0].onend();
    assert.equal(spoken.length, 2);
    spoken[1].onend();
    assert.equal(states.at(-1), 'finished');
});

test('stop and restart ignore stale completion and cancellation events', () => {
    const { reader, spoken, states } = setup();
    reader.start('Original page. '.repeat(40), 'en', 1);
    const original = spoken[0];
    reader.stop();
    original.onend();
    original.onerror();
    assert.equal(spoken.length, 1);
    assert.equal(states.at(-1), 'idle');
    reader.start('New page.', 'en', 1);
    original.onend();
    assert.equal(spoken.length, 2);
    spoken[1].onerror();
    assert.equal(states.at(-1), 'error');
});

test('empty pages never send speech to the browser', () => {
    const { reader, spoken, states } = setup();
    reader.start('  ', 'en', 1);
    assert.equal(spoken.length, 0);
    assert.equal(states.at(-1), 'empty');
});

test('page reading skips forms, hidden text and invisible content', () => {
    const nodes = [
        ['Welcome to Ideal Woman.', false, true],
        ['Private form value', true, true],
        ['Hidden heading', false, false],
        ['Community programmes.', false, true],
    ].map(([text, excluded, visible]) => ({ nodeValue: text, parentElement: {
        closest: () => excluded, getClientRects: () => visible ? [{}] : [],
    } }));
    globalThis.NodeFilter = { SHOW_TEXT: 4 };
    globalThis.document = { createTreeWalker: () => {
        let index = 0;
        return { nextNode() { this.currentNode = nodes[index++]; return Boolean(this.currentNode); } };
    } };
    globalThis.getComputedStyle = () => ({ visibility: 'visible' });
    try { assert.equal(collectPageText({}), 'Welcome to Ideal Woman. Community programmes.'); }
    finally { delete globalThis.document; delete globalThis.NodeFilter; delete globalThis.getComputedStyle; }
});
