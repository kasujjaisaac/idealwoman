import { test } from 'node:test';
import assert from 'node:assert/strict';
import { dictionaries, languages, translateText, textLanguage } from '../../resources/js/translations.js';
import { translationBatches, splitTranslationText, requestTranslations } from '../../resources/js/translation-client.js';

test('French translates the support form and preserves surrounding whitespace', () => {
    assert.equal(translateText('Support Us', 'fr'), 'Nous soutenir');
    assert.equal(translateText(' Name\n', 'fr'), ' Nom\n');
    assert.equal(translateText('Equipment & programme support', 'fr'), 'Équipement et soutien aux programmes');
});

test('long homepage statements are translated in every offered language', () => {
    for (const language of languages.filter((value) => value !== 'en')) {
        for (const source of [
            'We work with girls, young women and communities across Greater Masaka to build skills, confidence and independent futures.',
            'Through practical learning, self-discovery and community connection, we help create space for women and girls to shape their own paths.',
        ]) {
            assert.notEqual(translateText(source, language), source);
            assert.equal(textLanguage(source, language), language);
        }
    }
});

test('English and uncatalogued content retain their exact original text', () => {
    assert.equal(translateText('Support Us', 'en'), 'Support Us');
    assert.equal(translateText('A new community update.', 'fr'), 'A new community update.');
    assert.equal(translateText('idealwoman764@gmail.com', 'ar'), 'idealwoman764@gmail.com');
    assert.equal(translateText('Support Us', 'unknown'), 'Support Us');
});

test('screen readers use English for untranslated content and the selected language for translated text', () => {
    assert.equal(textLanguage('Support Us', 'fr'), 'fr');
    assert.equal(textLanguage('A new community update.', 'fr'), 'en');
    assert.equal(textLanguage('Support Us', 'en'), 'en');
});

test('every offered non-English language has complete entries in the shared catalogue', () => {
    const keys = Object.keys(dictionaries.fr);
    for (const language of languages.filter((value) => value !== 'en')) {
        assert.deepEqual(Object.keys(dictionaries[language]), keys);
        for (const translation of Object.values(dictionaries[language])) {
            assert.equal(typeof translation, 'string');
            assert.ok(translation.trim().length > 0);
        }
        assert.notEqual(translateText('Send support enquiry', language), 'Send support enquiry');
    }
});

test('automatic requests deduplicate text and stay within batch limits', () => {
    const batches = translationBatches(['same', 'same', ...Array.from({ length: 35 }, (_, index) => `Paragraph ${index}`)]);
    assert.equal(batches.length, 2);
    assert.equal(batches[0].length, 30);
    assert.equal(batches.flat().filter((text) => text === 'same').length, 1);
    assert.equal(translationBatches(Array.from({ length: 5 }, (_, index) => `${index}${'a'.repeat(2999)}`)).length, 2);
    assert.ok(splitTranslationText('A new paragraph. '.repeat(400)).every((part) => part.length <= 2400));
});

test('previously unknown text is sent to the server with CSRF protection and its selected language', async () => {
    const calls = [];
    const result = await requestTranslations({
        endpoint: '/translate', token: 'test-csrf', language: 'fr', texts: ['Brand new content.'],
        fetcher: async (url, options) => {
            calls.push({ url, options });
            return { ok: true, json: async () => ({ translations: ['Un nouveau contenu.'] }) };
        },
    });
    assert.equal(result.get('Brand new content.'), 'Un nouveau contenu.');
    assert.equal(calls[0].url, '/translate');
    assert.equal(calls[0].options.headers['X-CSRF-TOKEN'], 'test-csrf');
    assert.deepEqual(JSON.parse(calls[0].options.body), { language: 'fr', texts: ['Brand new content.'] });
});

test('automatic translation rejects missing or failed provider responses', async () => {
    for (const response of [{ ok: false }, { ok: true, json: async () => ({ translations: [] }) }]) {
        await assert.rejects(requestTranslations({
            endpoint: '/translate', token: 'test', language: 'fr', texts: ['Hello'], fetcher: async () => response,
        }));
    }
});
