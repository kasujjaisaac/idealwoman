const editors = [...document.querySelectorAll('form[data-editor]')];
let changed = false;
let pendingUploads = 0;
const markChanged = () => {
    changed = true;
    document.querySelectorAll('[data-save-status]').forEach((status) => { status.textContent = 'You have unsaved changes'; });
};
editors.forEach((form) => {
    if (form.dataset.initialDirty === 'true') markChanged();
    form.addEventListener('input', markChanged);
    form.addEventListener('change', markChanged);
    form.addEventListener('submit', (event) => {
        if (pendingUploads) {
            event.preventDefault();
            form.querySelector('[data-save-status]').textContent = 'Please wait for the photograph upload to finish.';
            return;
        }
        if (!event.defaultPrevented) changed = false;
    });
});
window.addEventListener('beforeunload', (event) => {
    if (changed || pendingUploads) { event.preventDefault(); event.returnValue = ''; }
});
document.querySelectorAll('[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!window.confirm(form.dataset.confirm)) event.preventDefault();
    });
});
const contentCatalog = document.querySelector('[data-content-catalog]');
let catalogFilter = 'all';
const filterCatalog = () => {
    if (!contentCatalog) return;
    const search = contentCatalog.querySelector('[data-filter]').value.trim().toLowerCase();
    let matches = 0;
    contentCatalog.querySelectorAll('[data-searchable]').forEach((card) => {
        const categoryMatches = catalogFilter === 'all' || (catalogFilter === 'draft' ? card.dataset.hasDraft === 'true' : card.dataset.category === catalogFilter);
        card.hidden = !categoryMatches || !card.textContent.toLowerCase().includes(search);
        if (!card.hidden) matches++;
    });
    contentCatalog.querySelector('[data-catalog-empty]').hidden = matches > 0;
    contentCatalog.querySelector('[data-catalog-status]').textContent = `${matches} content sections shown.`;
    contentCatalog.querySelectorAll('[data-catalog-filter]').forEach((button) => button.setAttribute('aria-pressed', String(button.dataset.catalogFilter === catalogFilter)));
};
contentCatalog?.querySelectorAll('[data-catalog-filter]').forEach((button) => button.addEventListener('click', () => {
    catalogFilter = button.dataset.catalogFilter;
    filterCatalog();
}));
document.querySelector('[data-show-drafts]')?.addEventListener('click', () => {
    catalogFilter = 'draft';
    contentCatalog.querySelector('[data-filter]').value = '';
    filterCatalog();
});
document.querySelectorAll('[data-filter]').forEach((input) => {
    input.addEventListener('input', () => {
        if (contentCatalog?.contains(input)) { filterCatalog(); return; }
        const value = input.value.trim().toLowerCase();
        let matches = 0;
        document.querySelectorAll('[data-searchable]').forEach((element) => {
            const label = element.classList.contains('field') ? [...element.querySelectorAll(':scope > label, :scope > p')].map((node) => node.textContent).join(' ') : element.textContent;
            const text = label + [...element.querySelectorAll('input, textarea, select')].map((field) => field.tagName === 'SELECT' ? field.selectedOptions[0]?.textContent || '' : field.value).join(' ');
            element.hidden = !text.toLowerCase().includes(value);
            if (!element.hidden) matches++;
        });
        document.querySelectorAll('[data-field-group]').forEach((group) => {
            group.hidden = ![...group.querySelectorAll('[data-searchable]')].some((field) => !field.hidden);
            if (value && !group.hidden) group.open = true;
        });
        const summary = document.querySelector('[data-search-summary]');
        if (summary) summary.textContent = value ? `${matches} matching ${input.dataset.matchLabel || 'fields'}${matches ? '' : '. Try another word.'}` : '';
    });
});
const library = document.querySelector('[data-image-library]');
let activePicker;
const updatePreview = (picker) => {
    const select = picker.querySelector('[data-image-select]');
    const image = picker.querySelector('[data-image-preview]');
    const url = select.selectedOptions[0]?.dataset.imageUrl || picker.dataset.originalUrl;
    if (image) {
        image.hidden = !url;
        if (url) image.src = url;
        else image.removeAttribute('src');
    }
};
const renderLibrary = () => {
    const grid = library.querySelector('[data-library-grid]');
    const search = library.querySelector('input').value.toLowerCase();
    grid.replaceChildren();
    [...activePicker.querySelector('select').options].filter((option) => option.value && option.textContent.toLowerCase().includes(search)).forEach((option) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'rounded-none border border-slate-200 p-3 text-left text-xs hover:border-blue-600 focus-visible:outline-2 focus-visible:outline-blue-600';
        const image = document.createElement('img');
        image.src = option.dataset.imageUrl;
        image.alt = '';
        image.loading = 'lazy';
        image.className = 'mb-3 h-28 w-full rounded-none bg-slate-100 object-contain';
        const label = document.createElement('span');
        label.textContent = option.textContent;
        button.append(image, label);
        button.addEventListener('click', () => {
            const select = activePicker.querySelector('select');
            select.value = option.value;
            select.dispatchEvent(new Event('change', { bubbles: true }));
            library.close();
        });
        grid.append(button);
    });
    library.querySelector('[data-library-empty]').hidden = grid.children.length > 0;
};
if (library) {
    library.querySelector('[data-close-library]').addEventListener('click', () => library.close());
    library.querySelector('input').addEventListener('input', renderLibrary);
}
document.querySelectorAll('[data-image-picker]').forEach((picker) => {
    const select = picker.querySelector('[data-image-select]');
    select.addEventListener('change', () => updatePreview(picker));
    const browse = picker.querySelector('[data-open-library]');
    if (browse && library && typeof library.showModal === 'function') {
        browse.hidden = false;
        browse.addEventListener('click', () => {
            activePicker = picker;
            library.querySelector('input').value = '';
            renderLibrary();
            library.showModal();
        });
    }
    const upload = picker.querySelector('[data-inline-upload]');
    upload.addEventListener('change', async () => {
        const file = upload.files[0];
        if (!file) return;
        const status = picker.querySelector('[data-upload-status]');
        status.textContent = 'Uploading and preparing your file...';
        upload.disabled = true;
        pendingUploads++;
        const data = new FormData();
        data.append('file', file);
        data.append('_token', document.querySelector('input[name="_token"]').value);
        try {
            const response = await fetch(picker.dataset.uploadUrl, { method: 'POST', body: data, headers: { Accept: 'application/json' }, credentials: 'same-origin' });
            if (response.status === 401 || response.status === 419) throw new Error('Your session expired. Copy your unsaved text before signing in again.');
            if (!response.headers.get('content-type')?.includes('application/json')) throw new Error('The upload could not finish. Your edits are still here. Please try again.');
            const result = await response.json();
            if (!response.ok) throw new Error(Object.values(result.errors || {}).flat()[0] || result.message || 'Upload failed. Please try again.');
            document.querySelectorAll('[data-image-picker]').forEach((other) => {
                if ((other.dataset.pdf === 'true') === result.is_image) return;
                const option = new Option(result.label, other.dataset.mode === 'asset' ? result.name : result.path);
                option.dataset.imageUrl = result.url;
                other.querySelector('select').add(option);
            });
            select.value = picker.dataset.mode === 'asset' ? result.name : result.path;
            select.dispatchEvent(new Event('change', { bubbles: true }));
            status.textContent = 'Uploaded and selected. Save or publish to use this file.';
        } catch (error) {
            status.textContent = error.message === 'Failed to fetch' ? 'Connection lost. Your edits are still here. Try uploading again.' : error.message;
        } finally {
            pendingUploads--;
            upload.disabled = false;
            upload.value = '';
        }
    });
});
const adminNavigation = document.querySelector('[data-admin-navigation]');
if (adminNavigation) {
    const desktop = matchMedia('(min-width: 1024px)');
    const updateNavigation = () => { adminNavigation.open = desktop.matches; };
    updateNavigation();
    desktop.addEventListener('change', updateNavigation);
}
const storyTitle = document.querySelector('[data-story-title]');
const storySlug = document.querySelector('[data-story-slug]');
if (storyTitle && storySlug && !storySlug.readOnly) {
    let customSlug = !!storySlug.value;
    storySlug.addEventListener('input', () => { customSlug = !!storySlug.value; });
    storyTitle.addEventListener('input', () => {
        if (!customSlug) storySlug.value = storyTitle.value.normalize('NFKD').replace(/[\u0300-\u036f]/g, '').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '').slice(0, 150).replace(/-$/, '');
    });
}
