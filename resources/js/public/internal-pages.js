function initReadingProgress() {
    const bar = document.querySelector('[data-reading-progress]');

    if (! bar) {
        return;
    }

    const update = () => {
        const article = document.querySelector('[data-post-article]');

        if (! article) {
            return;
        }

        const rect = article.getBoundingClientRect();
        const total = article.offsetHeight - window.innerHeight;

        if (total <= 0) {
            bar.style.width = '0%';

            return;
        }

        const scrolled = Math.min(Math.max(-rect.top, 0), total);
        bar.style.width = `${(scrolled / total) * 100}%`;
    };

    window.addEventListener('scroll', update, { passive: true });
    update();
}

function initReveal() {
    const items = document.querySelectorAll('.gh-reveal-internal');

    if (items.length === 0) {
        return;
    }

    if (! ('IntersectionObserver' in window)) {
        items.forEach((el) => el.classList.add('is-shown'));

        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-shown');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    items.forEach((el) => observer.observe(el));
}

function initMobileMenu() {
    const toggle = document.querySelector('[data-internal-menu-toggle]');
    const menu = document.querySelector('[data-internal-mobile-menu]');

    if (! toggle || ! menu) {
        return;
    }

    toggle.addEventListener('click', () => {
        const open = menu.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
}

function initCampaignPage() {
    document.querySelectorAll('[data-campaign-progress]').forEach((bar) => {
        const pct = Number(bar.getAttribute('data-pct') || 0);
        requestAnimationFrame(() => {
            bar.style.width = `${Math.min(100, Math.max(0, pct))}%`;
        });
    });

    const gallery = document.querySelector('[data-campaign-gallery]');

    if (! gallery) {
        return;
    }

    const mainImage = gallery.querySelector('[data-campaign-gallery-image]');
    const thumbs = gallery.querySelectorAll('[data-campaign-gallery-thumb]');

    thumbs.forEach((thumb) => {
        thumb.addEventListener('click', () => {
            const url = thumb.getAttribute('data-image');

            if (! url || ! mainImage) {
                return;
            }

            mainImage.src = url;
            thumbs.forEach((item) => item.classList.remove('is-active'));
            thumb.classList.add('is-active');
        });
    });
}

export function initInternalPages() {
    initReadingProgress();
    initReveal();
    initMobileMenu();
    initCampaignPage();
}
