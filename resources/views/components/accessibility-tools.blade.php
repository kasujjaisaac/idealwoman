<div class="access-tools">
    <div class="access-tools-inner">
        <details class="access-panel">
            <summary>Translate</summary>
            <div class="access-panel-body">
                <form action="https://translate.google.com/translate" method="GET" target="_blank" rel="noopener noreferrer">
                    <input type="hidden" name="sl" value="en">
                    <input type="hidden" name="u" value="{{ url()->current() }}">
                    <label for="translation-language">Read this page in</label>
                    <select id="translation-language" name="tl" aria-describedby="translation-note">
                        <option value="lg">Luganda</option>
                        <option value="sw">Kiswahili</option>
                        <option value="fr">Français</option>
                        <option value="ar">العربية</option>
                        <option value="es">Español</option>
                        <option value="pt">Português</option>
                        <option value="zh-CN">中文（简体）</option>
                        <option value="hi">हिन्दी</option>
                        <option value="de">Deutsch</option>
                        <option value="it">Italiano</option>
                        <option value="ru">Русский</option>
                        <option value="ja">日本語</option>
                        <option value="ko">한국어</option>
                        <option value="tr">Türkçe</option>
                        <option value="nl">Nederlands</option>
                        <option value="am">አማርኛ</option>
                        <option value="so">Soomaali</option>
                        <option value="rw">Kinyarwanda</option>
                        <option value="zu">isiZulu</option>
                        <option value="af">Afrikaans</option>
                    </select>
                    <button type="submit">Open translation <span class="sr-only">in a new tab</span></button>
                </form>
                <p id="translation-note">English is the original. Automatic translation opens in Google Translate in a new tab; wording may vary. More languages are available there. Public pages only; for a local preview, use your browser’s Translate option.</p>
            </div>
        </details>
        <details class="access-panel" data-accessibility-panel hidden>
            <summary><span aria-hidden="true">Aa</span> Accessibility</summary>
            <div class="access-panel-body">
                <label for="access-text-size">Text size</label>
                <select id="access-text-size" data-access-preference="textSize">
                    <option value="100">Standard</option>
                    <option value="112.5">Larger</option>
                    <option value="125">Extra large</option>
                    <option value="150">Largest</option>
                </select>
                <label class="access-check"><input type="checkbox" data-access-preference="contrast"> Higher contrast</label>
                <label class="access-check"><input type="checkbox" data-access-preference="links"> Underline links</label>
                <label class="access-check"><input type="checkbox" data-access-preference="spacing"> More reading space</label>
                <label class="access-check"><input type="checkbox" data-access-preference="motion"> Reduce motion and pause slideshow</label>
                <button type="button" data-access-reset>Reset preferences</button>
                <p>Your preferences are saved in this browser. Use Tab to move between links and controls, and Enter or Space to activate them.</p>
                <p role="status" data-access-status></p>
            </div>
        </details>
    </div>
</div>
