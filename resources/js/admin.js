document.querySelectorAll('[data-filter]').forEach((input) => {
    input.addEventListener('input', () => {
        const value = input.value.toLowerCase();
        document.querySelectorAll('[data-searchable]').forEach((element) => {
            element.hidden = !element.textContent.toLowerCase().includes(value);
        });
    });
});
let changed = false;
document.querySelectorAll('form[data-editor]').forEach((form) => {
    form.addEventListener('input', () => { changed = true; });
    form.addEventListener('submit', () => { changed = false; });
});
window.addEventListener('beforeunload', (event) => {
    if (changed) { event.preventDefault(); event.returnValue = ''; }
});
document.querySelectorAll('[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!window.confirm(form.dataset.confirm)) { event.preventDefault(); }
    });
});
