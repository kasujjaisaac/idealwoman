<div class="access-tools">
    <div class="access-tools-inner">
        <details class="access-panel">
            <summary aria-label="Translate"><x-icon name="globe"/><span class="access-tool-label">Translate</span></summary>
            <div class="access-panel-body">
                <div class="translation-form" data-translation-controls>
                    <div class="translation-heading"><x-icon name="globe"/><p>Choose your language</p></div>
                    <div class="language-grid" role="group" aria-label="Languages" translate="no">
                        <button type="button" class="language-option" data-language="en" aria-pressed="true"><img src="{{ asset('images/flags/gb.png') }}" width="24" height="16" alt=""><span lang="en" dir="auto">English</span></button>
                        <button type="button" class="language-option" data-language="lg" aria-pressed="false"><img src="{{ asset('images/flags/ug.png') }}" width="24" height="16" alt=""><span lang="lg" dir="auto">Luganda</span></button>
                        <button type="button" class="language-option" data-language="sw" aria-pressed="false"><img src="{{ asset('images/flags/tz.png') }}" width="24" height="16" alt=""><span lang="sw" dir="auto">Kiswahili</span></button>
                        <button type="button" class="language-option" data-language="fr" aria-pressed="false"><img src="{{ asset('images/flags/fr.png') }}" width="24" height="16" alt=""><span lang="fr" dir="auto">Français</span></button>
                        <button type="button" class="language-option" data-language="ar" aria-pressed="false"><img src="{{ asset('images/flags/sa.png') }}" width="24" height="16" alt=""><span lang="ar" dir="auto">العربية</span></button>
                        <button type="button" class="language-option" data-language="es" aria-pressed="false"><img src="{{ asset('images/flags/es.png') }}" width="24" height="16" alt=""><span lang="es" dir="auto">Español</span></button>
                        <button type="button" class="language-option" data-language="pt" aria-pressed="false"><img src="{{ asset('images/flags/pt.png') }}" width="24" height="16" alt=""><span lang="pt" dir="auto">Português</span></button>
                        <button type="button" class="language-option" data-language="zh-CN" aria-pressed="false"><img src="{{ asset('images/flags/cn.png') }}" width="24" height="16" alt=""><span lang="zh-CN" dir="auto">中文（简体）</span></button>
                    </div>
                    <p class="sr-only" role="status" data-translation-status translate="no"></p>
                    <noscript><p>Enable JavaScript to translate when you select a language.</p></noscript>
                </div>
            </div>
        </details>
        <details class="access-panel" data-accessibility-panel hidden>
            <summary aria-label="Accessibility"><x-icon name="accessibility"/><span class="access-tool-label">Accessibility</span></summary>
            <dialog class="access-panel-body accessibility-settings" aria-labelledby="accessibility-heading">
                <div class="accessibility-dialog-header"><div class="translation-heading"><x-icon name="accessibility"/><h2 id="accessibility-heading">Accessibility</h2></div><button type="button" class="accessibility-close" data-access-close aria-label="Close accessibility settings" autofocus><span aria-hidden="true">×</span></button></div>
                <div class="access-settings-grid">
                <div class="access-setting"><label for="access-text-size">Text size</label>
                <select id="access-text-size" data-access-preference="textSize">
                    <option value="100">Standard</option>
                    <option value="112.5">Larger</option>
                    <option value="125">Extra large</option>
                    <option value="150">Largest</option>
                </select></div>
                <div class="access-setting"><label for="access-color-theme">Color theme</label>
                <select id="access-color-theme" data-access-preference="theme" aria-describedby="access-theme-note">
                    <option value="default">Original colors</option>
                    <option value="warm">Warm cream</option>
                    <option value="cool">Cool blue</option>
                    <option value="dark">Dark</option>
                </select></div>
                </div>
                <p id="access-theme-note" class="access-settings-note">Higher contrast overrides the selected color theme.</p>
                <div class="access-toggles">
                <label class="access-check"><input type="checkbox" data-access-preference="contrast"> Higher contrast</label>
                <label class="access-check"><input type="checkbox" data-access-preference="links"> Underline links</label>
                <label class="access-check"><input type="checkbox" data-access-preference="spacing"> More reading space</label>
                <label class="access-check"><input type="checkbox" data-access-preference="motion"> Reduce motion and pause slideshow</label>
                </div>
                <button type="button" data-access-reset>Reset preferences</button>
                <p class="access-settings-note">Saved automatically on this device.</p>
                <p class="sr-only" role="status" data-access-status></p>
            </dialog>
        </details>
    </div>
</div>
