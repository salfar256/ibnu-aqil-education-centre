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

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
    const header = document.querySelector('.site-header');
    const progress = document.querySelector('.scroll-progress span');
    const backToTop = document.querySelector('.back-to-top');

    let ticking = false;
    function onScroll() {
        ticking = false;
        const y = window.scrollY;
        const max = document.documentElement.scrollHeight - window.innerHeight;
        if (header) header.classList.toggle('is-scrolled', y > 24);
        if (progress) progress.style.setProperty('--progress', max > 0 ? Math.min(1, y / max).toFixed(4) : '0');
        if (backToTop) backToTop.classList.toggle('is-visible', y > window.innerHeight * 0.8);
    }
    window.addEventListener('scroll', () => {
        if (!ticking) { ticking = true; window.requestAnimationFrame(onScroll); }
    }, { passive: true });
    onScroll();

    if (backToTop) {
        backToTop.addEventListener('click', event => {
            event.preventDefault();
            window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
            document.querySelector('#main-content')?.focus({ preventScroll: true });
        });
    }

    const revealItems = [...document.querySelectorAll('[data-reveal]')];
    if (!reduceMotion && 'IntersectionObserver' in window && revealItems.length) {
        document.documentElement.classList.add('has-motion');
        revealItems.forEach(item => {
            const siblings = [...item.parentElement.children].filter(child => child.hasAttribute('data-reveal'));
            item.style.setProperty('--reveal-delay', (Math.min(siblings.indexOf(item), 5) * 0.08) + 's');
        });
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            });
        }, { rootMargin: '0px 0px -8% 0px', threshold: 0.12 });
        revealItems.forEach(item => observer.observe(item));
    }

    if (finePointer && !reduceMotion) {
        document.querySelectorAll('[data-spotlight]').forEach(card => {
            card.addEventListener('pointermove', event => {
                const box = card.getBoundingClientRect();
                card.style.setProperty('--spot-x', (event.clientX - box.left) + 'px');
                card.style.setProperty('--spot-y', (event.clientY - box.top) + 'px');
            });
        });
        document.querySelectorAll('[data-tilt]').forEach(item => {
            item.addEventListener('pointermove', event => {
                const box = item.getBoundingClientRect();
                const x = (event.clientX - box.left) / box.width - 0.5;
                const y = (event.clientY - box.top) / box.height - 0.5;
                item.style.setProperty('--tilt-x', (x * 7).toFixed(2) + 'deg');
                item.style.setProperty('--tilt-y', (y * -7).toFixed(2) + 'deg');
            });
            item.addEventListener('pointerleave', () => {
                item.style.setProperty('--tilt-x', '0deg');
                item.style.setProperty('--tilt-y', '0deg');
            });
        });
    }

    const lightbox = document.querySelector('.lightbox');
    if (lightbox && typeof lightbox.showModal === 'function') {
        const figure = lightbox.querySelector('.lightbox-figure');
        const caption = lightbox.querySelector('.lightbox-caption');
        let opener = null;
        document.querySelectorAll('[data-lightbox]').forEach(link => {
            link.addEventListener('click', event => {
                event.preventDefault();
                const source = link.querySelector('img');
                const image = document.createElement('img');
                image.src = link.href;
                image.alt = source ? source.alt : '';
                figure.replaceChildren(image);
                caption.textContent = link.dataset.caption || '';
                opener = link;
                lightbox.showModal();
            });
        });
        lightbox.addEventListener('click', event => {
            if (event.target === lightbox) lightbox.close();
        });
        lightbox.addEventListener('close', () => {
            figure.replaceChildren();
            if (opener) opener.focus();
        });
    }
})();
