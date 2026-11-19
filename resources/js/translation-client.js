export function translationBatches(texts) {
    const batches = [];
    let batch = [];
    let length = 0;
    for (const text of [...new Set(texts)]) {
        if (!text || text.length > 3000) throw new Error('Invalid translation text.');
        if (batch.length === 30 || length + text.length > 12000) {
            batches.push(batch);
            batch = [];
            length = 0;
        }
        batch.push(text);
        length += text.length;
    }
    if (batch.length) batches.push(batch);
    return batches;
}

export function splitTranslationText(text) {
    const chunks = [];
    while (text.length > 2400) {
        const space = text.lastIndexOf(' ', 2400);
        const end = space > 1200 ? space : 2400;
        chunks.push(text.slice(0, end));
        text = text.slice(end).trimStart();
    }
    if (text) chunks.push(text);
    return chunks;
}

export async function requestTranslations({ endpoint, token, language, texts, signal, fetcher = globalThis.fetch }) {
    const pieces = new Map(texts.map((text) => [text, splitTranslationText(text)]));
    const translations = new Map();
    for (const batch of translationBatches([...pieces.values()].flat())) {
        const response = await fetcher(endpoint, {
            method: 'POST', credentials: 'same-origin', signal,
            headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify({ language, texts: batch }),
        });
        if (!response.ok) throw new Error('Translation unavailable.');
        const result = await response.json();
        if (!Array.isArray(result.translations) || result.translations.length !== batch.length || result.translations.some((text) => typeof text !== 'string')) {
            throw new Error('Invalid translation response.');
        }
        batch.forEach((text, index) => translations.set(text, result.translations[index]));
    }
    return new Map([...pieces].map(([source, chunks]) => [source, chunks.map((chunk) => translations.get(chunk)).join(' ')]));
}
