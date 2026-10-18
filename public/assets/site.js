(() => {
    'use strict';

    const toggle = document.querySelector('.menu-toggle');
    const navigation = document.querySelector('#primary-nav');
    const desktop = window.matchMedia('(min-width: 961px)');
    const dropdowns = [...document.querySelectorAll('.nav-dropdown')];

    function closeMenu(restoreFocus = false) {
        if (!toggle || !navigation) return;
        toggle.setAttribute('aria-expanded', 'false');
        toggle.querySelector('.sr-only').textContent = 'Buka menu navigasi';
        navigation.classList.remove('is-open');
        dropdowns.forEach(dropdown => { dropdown.open = false; });
        if (restoreFocus) toggle.focus();
    }

    if (toggle && navigation) {
        toggle.addEventListener('click', () => {
            const open = toggle.getAttribute('aria-expanded') !== 'true';
            toggle.setAttribute('aria-expanded', String(open));
            toggle.querySelector('.sr-only').textContent = open ? 'Tutup menu navigasi' : 'Buka menu navigasi';
            navigation.classList.toggle('is-open', open);
            if (!open) dropdowns.forEach(dropdown => { dropdown.open = false; });
        });

        navigation.addEventListener('click', event => {
            if (event.target.closest('a') && !desktop.matches) closeMenu();
        });

        document.addEventListener('click', event => {
            if (!event.target.closest('.site-header')) closeMenu();
            dropdowns.forEach(dropdown => {
                if (!dropdown.contains(event.target)) dropdown.open = false;
            });
        });

        document.addEventListener('keydown', event => {
            if (event.key !== 'Escape') return;
            const activeDropdown = dropdowns.find(dropdown => dropdown.open && dropdown.contains(document.activeElement));
            if (activeDropdown) {
                activeDropdown.open = false;
                activeDropdown.querySelector('summary').focus();
            } else if (toggle.getAttribute('aria-expanded') === 'true') {
                closeMenu(true);
            } else {
                dropdowns.forEach(dropdown => { dropdown.open = false; });
            }
        });

        document.addEventListener('focusin', event => {
            dropdowns.forEach(dropdown => {
                if (dropdown.open && !dropdown.contains(event.target)) dropdown.open = false;
            });
        });

        if (desktop.addEventListener) desktop.addEventListener('change', () => closeMenu());
    }
})();
