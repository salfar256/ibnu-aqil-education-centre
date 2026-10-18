'use strict';
document.getElementById('navigation-toggle').addEventListener('click', function () {
    const open = this.getAttribute('aria-expanded') !== 'true';
    this.setAttribute('aria-expanded', String(open));
    document.getElementById('workspace-navigation').classList.toggle('is-open', open);
});
document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        document.getElementById('workspace-navigation').classList.remove('is-open');
        document.getElementById('navigation-toggle').setAttribute('aria-expanded', 'false');
    }
});
document.querySelectorAll('form[data-confirm]').forEach(form => form.addEventListener('submit', event => {
    if (!window.confirm(form.dataset.confirm)) event.preventDefault();
}));
