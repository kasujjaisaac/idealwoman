import { test } from 'node:test';
import assert from 'node:assert/strict';
import { dictionaries, languages, translateText, textLanguage } from '../../resources/js/translations.js';

test('French translates the support form and preserves surrounding whitespace', () => {
    assert.equal(translateText('Support Us', 'fr'), 'Nous soutenir');
    assert.equal(translateText(' Name\n', 'fr'), ' Nom\n');
    assert.equal(translateText('Equipment & programme support', 'fr'), 'Équipement et soutien aux programmes');
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
