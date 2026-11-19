export function splitSpeechText(text, limit = 220) {
    const words = text.trim().split(/\s+/).filter(Boolean);
    const chunks = [];
    let chunk = '';
    for (let word of words) {
        if (chunk && chunk.length + word.length + 1 > limit) {
            chunks.push(chunk);
            chunk = '';
        }
        while (word.length > limit) {
            chunks.push(word.slice(0, limit));
            word = word.slice(limit);
        }
        chunk = chunk ? `${chunk} ${word}` : word;
    }
    if (chunk) chunks.push(chunk);
    return chunks;
}

export function createPageReader(synthesis, Utterance, onState) {
    let generation = 0;
    let current = null;
    let state = 'idle';
    const update = (value) => { state = value; onState(value); };
    const stop = () => {
        generation++;
        current = null;
        synthesis.cancel();
        update('idle');
    };
    return {
        stop,
        pause() {
            if (state !== 'reading') return;
            synthesis.pause();
            update('paused');
        },
        resume() {
            if (state !== 'paused') return;
            synthesis.resume();
            update('reading');
        },
        start(text, language, rate, voice) {
            stop();
            const chunks = splitSpeechText(text);
            if (!chunks.length) { update('empty'); return; }
            const run = generation;
            let index = 0;
            const next = () => {
                if (run !== generation) return;
                if (index === chunks.length) { current = null; update('finished'); return; }
                current = new Utterance(chunks[index++]);
                current.lang = language;
                current.rate = rate;
                if (voice) current.voice = voice;
                current.onend = next;
                current.onerror = () => {
                    if (run !== generation) return;
                    generation++;
                    current = null;
                    synthesis.cancel();
                    update('error');
                };
                synthesis.speak(current);
            };
            update('reading');
            next();
        },
    };
}

export function collectPageText(main) {
    const excluded = 'script, style, noscript, form, input, textarea, select, button, [contenteditable], [hidden], [inert], [aria-hidden="true"], [data-no-read], .sr-only, .slide-announcement';
    const walker = document.createTreeWalker(main, NodeFilter.SHOW_TEXT);
    const texts = [];
    while (walker.nextNode()) {
        const node = walker.currentNode;
        const parent = node.parentElement;
        if (!parent || parent.closest(excluded) || !parent.getClientRects().length) continue;
        const style = getComputedStyle(parent);
        if (style.visibility === 'hidden' || style.visibility === 'collapse') continue;
        const text = node.nodeValue.trim();
        if (text) texts.push(text);
    }
    return texts.join(' ');
}

export function initializePageReader() {
    const panel = document.querySelector('[data-reader-panel]');
    const main = document.querySelector('main');
    if (!panel || !main) return;
    const play = panel.querySelector('[data-reader-play]');
    const pause = panel.querySelector('[data-reader-pause]');
    const stop = panel.querySelector('[data-reader-stop]');
    const speed = panel.querySelector('[data-reader-speed]');
    const status = panel.querySelector('[data-reader-status]');
    panel.hidden = false;
    if (!('speechSynthesis' in window) || !('SpeechSynthesisUtterance' in window)) {
        play.disabled = true;
        status.textContent = 'Read aloud is unavailable in this browser. Try another browser or your device’s screen reader.';
        return;
    }
    let state = 'idle';
    const messages = {
        idle: 'Ready to read this page.', reading: 'Reading this page…', paused: 'Reading paused.',
        finished: 'Finished reading this page.', empty: 'There is no readable text on this page.',
        error: 'Reading could not continue. Please press Play to try again.',
    };
    const reader = createPageReader(window.speechSynthesis, window.SpeechSynthesisUtterance, (value) => {
        state = value;
        status.textContent = messages[value];
        play.textContent = value === 'paused' ? 'Resume' : 'Play';
        play.disabled = value === 'reading';
        pause.disabled = value !== 'reading';
        stop.disabled = !['reading', 'paused'].includes(value);
        speed.disabled = ['reading', 'paused'].includes(value);
    });
    play.addEventListener('click', () => {
        if (state === 'paused') { reader.resume(); return; }
        const language = document.documentElement.lang || 'en';
        const voices = window.speechSynthesis.getVoices();
        const voice = voices.find(voice => voice.lang.toLowerCase() === language.toLowerCase())
            || voices.find(voice => voice.lang.split('-')[0] === language.split('-')[0]);
        if (!voice && language.split('-')[0] !== 'en') {
            status.textContent = 'Your device has no reading voice for this language. Choose English or install a matching voice in your device settings.';
            return;
        }
        reader.start(collectPageText(main), language, Number(speed.value), voice);
    });
    pause.addEventListener('click', () => reader.pause());
    stop.addEventListener('click', () => reader.stop());
    new MutationObserver(() => {
        if (!['reading', 'paused'].includes(state)) return;
        reader.stop();
        status.textContent = 'The language changed. Press Play to read the updated page.';
    }).observe(document.documentElement, { attributes: true, attributeFilter: ['lang'] });
    window.addEventListener('pagehide', () => reader.stop());
    reader.stop();
}
