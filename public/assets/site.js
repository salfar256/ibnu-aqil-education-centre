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

    // Floating WhatsApp topic menu: close on outside click or Escape.
    const waFloat = document.querySelector('.wa-float');
    if (waFloat) {
        document.addEventListener('click', event => {
            if (waFloat.open && !waFloat.contains(event.target)) waFloat.open = false;
        });
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && waFloat.open) {
                waFloat.open = false;
                waFloat.querySelector('summary').focus();
            }
        });
    }

    // Level finder: two or three questions lead to a unit recommendation and a prefilled WhatsApp message.
    document.querySelectorAll('[data-finder]').forEach(finder => {
        const section = finder.closest('[data-finder-section]');
        if (section) section.hidden = false;
        const steps = {};
        finder.querySelectorAll('[data-step]').forEach(step => { steps[step.dataset.step] = step; });
        const results = [...finder.querySelectorAll('[data-result]')];
        const restart = finder.querySelector('[data-finder-restart]');
        const stageText = {
            mi: 'putra/putri saya akan masuk sekolah dasar',
            smp: 'putra/putri saya kelas 6 SD/MI atau baru lulus',
            next: 'putra/putri saya kelas 9 SMP/MTs atau baru lulus'
        };
        const boardText = {
            ya: 'Kami juga ingin ia mondok di pesantren.',
            pertimbangkan: 'Kami masih mempertimbangkan apakah ia akan mondok.',
            tidak: 'Rencananya ia pulang ke rumah (tidak mondok).'
        };
        const state = {};

        function show(name) {
            Object.values(steps).forEach(step => { step.hidden = step.dataset.step !== name; });
            results.forEach(result => { result.hidden = true; });
            const first = steps[name]?.querySelector('button');
            if (first && state.started) first.focus();
        }

        function finish() {
            Object.values(steps).forEach(step => { step.hidden = true; });
            const result = results.find(item => item.dataset.result === state.unit);
            if (!result) return;
            const message = finder.dataset.greeting + 'Saya mengisi panduan jenjang di website: ' + stageText[state.stage] + '. ' + (boardText[state.board] ? boardText[state.board] + ' ' : '') + 'Rekomendasinya ' + result.dataset.name + '. Mohon informasi pendaftarannya.';
            result.querySelector('[data-finder-wa]').href = finder.dataset.wa + '?text=' + encodeURIComponent(message);
            result.querySelector('.finder-board-note').hidden = !state.board || state.board === 'tidak';
            result.hidden = false;
            restart.hidden = false;
            result.setAttribute('tabindex', '-1');
            result.focus();
        }

        finder.addEventListener('click', event => {
            const button = event.target.closest('[data-answer]');
            if (!button) return;
            state.started = true;
            const step = button.closest('[data-step]').dataset.step;
            const answer = button.dataset.answer;
            if (step === 'stage') {
                state.stage = answer;
                if (answer === 'next') {
                    show('goal');
                } else if (answer === 'mi') {
                    state.unit = 'mi';
                    state.board = null;
                    finish();
                } else {
                    state.unit = answer;
                    steps.board.querySelector('[data-board-number]').textContent = '2';
                    show('board');
                }
            } else if (step === 'goal') {
                state.unit = answer;
                steps.board.querySelector('[data-board-number]').textContent = '3';
                show('board');
            } else {
                state.board = answer;
                finish();
            }
        });

        restart?.addEventListener('click', () => {
            restart.hidden = true;
            show('stage');
        });
    });

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
