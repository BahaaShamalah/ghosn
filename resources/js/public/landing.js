/**
 * GHOSN Relief public landing page — ported from reference DCLogic component.
 */

const LANG_KEY = 'ghosn-lang';

/**
 * @param {HTMLElement | null} root
 */
export function initLandingPage(root = document.getElementById('ghosn-root')) {
    if (!root) {
        return () => {};
    }

    const motion = root.getAttribute('data-motion') || 'calm';
    const defaultLang = root.getAttribute('data-lang') || 'en';
    const localeBase = root.getAttribute('data-locale-base') || '/locale';

    root.setAttribute('data-ready', '1');

    const setLabel = (lang) => {
        root.querySelectorAll('[data-lang-label]').forEach((el) => {
            el.textContent = lang === 'ar' ? 'English' : 'العربية';
        });
    };

    const syncLocale = (lang) => {
        fetch(`${localeBase}/${lang}`, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        }).catch(() => {
            // Non-blocking: client-side toggle still works if sync fails.
        });
    };

    let currentLang = defaultLang;
    let videoPlayer = null;

    const applyLang = (lang, syncSession = false) => {
        root.setAttribute('data-lang', lang);
        document.documentElement.setAttribute('lang', lang);
        document.documentElement.setAttribute('dir', lang === 'ar' ? 'rtl' : 'ltr');
        setLabel(lang);

        try {
            localStorage.setItem(LANG_KEY, lang);
        } catch {
            // Ignore storage failures (private browsing, etc.).
        }

        if (syncSession) {
            syncLocale(lang);
        }

        videoPlayer?.refreshModalIfOpen?.();
    };

    if (currentLang !== 'en' && currentLang !== 'ar') {
        currentLang = 'en';
    }

    // Server session locale is the source of truth on initial load.
    applyLang(currentLang);

    const langHandlers = [];

    root.querySelectorAll('[data-lang-toggle]').forEach((btn) => {
        const handler = () => {
            const next = root.getAttribute('data-lang') === 'ar' ? 'en' : 'ar';
            applyLang(next, true);
        };

        btn.addEventListener('click', handler);
        langHandlers.push({ btn, handler });
    });

    const menuBtn = root.querySelector('#ghosn-menu-btn');
    const menu = root.querySelector('#ghosn-mobile-menu');
    const menuLinkHandlers = [];

    const closeMenu = () => {
        if (!menu || !menuBtn) {
            return;
        }

        menu.classList.add('hidden');
        menuBtn.setAttribute('aria-expanded', 'false');
    };

    let menuClickHandler = null;

    if (menuBtn && menu) {
        menuClickHandler = () => {
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                menuBtn.setAttribute('aria-expanded', 'true');
            } else {
                closeMenu();
            }
        };

        menuBtn.addEventListener('click', menuClickHandler);

        menu.querySelectorAll('a').forEach((link) => {
            const handler = () => closeMenu();
            link.addEventListener('click', handler);
            menuLinkHandlers.push({ link, handler });
        });
    }

    const hero = root.querySelector('#home');
    const textCol = hero?.querySelector('.hero-text-col') ?? null;
    const vidCol = root.querySelector('#hero-vid-col');

    let onHeroMove = null;
    let onHeroLeave = null;

    if (hero && textCol && vidCol) {
        onHeroMove = (event) => {
            const rect = hero.getBoundingClientRect();
            const x = (event.clientX - rect.left - rect.width * 0.5) / rect.width;
            const y = (event.clientY - rect.top - rect.height * 0.5) / rect.height;
            textCol.style.transform = `translate(${x * -7}px, ${y * -5}px)`;
            vidCol.style.transform = `translate(${x * 9}px, ${y * 6}px)`;
        };

        onHeroLeave = () => {
            textCol.style.transform = '';
            vidCol.style.transform = '';
        };

        hero.addEventListener('mousemove', onHeroMove, { passive: true });
        hero.addEventListener('mouseleave', onHeroLeave);
    }

    const nav = root.querySelector('#ghosn-nav');
    let onScroll = null;

    if (nav) {
        onScroll = () => {
            nav.classList.toggle('nav-solid', window.scrollY > 40);
        };

        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
    }

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const items = Array.from(root.querySelectorAll('[data-reveal]'));

    const revealAll = () => {
        items.forEach((el) => el.classList.add('in'));
    };

    let revealCheck = null;
    let revealTimer = null;

    if (reduceMotion || motion === 'off') {
        revealAll();
    } else {
        revealCheck = () => {
            const viewport = window.innerHeight || document.documentElement.clientHeight || 800;

            items.forEach((el) => {
                if (el.classList.contains('in')) {
                    return;
                }

                const rect = el.getBoundingClientRect();

                if (rect.top < viewport * 0.92 && rect.bottom > 0) {
                    el.classList.add('in');
                }
            });
        };

        revealCheck();
        window.addEventListener('scroll', revealCheck, { passive: true });
        window.addEventListener('resize', revealCheck, { passive: true });
        revealTimer = window.setTimeout(revealAll, 3200);
    }

    videoPlayer = initHeroVideoPlayer(root);

    return () => {
        videoPlayer?.cleanup?.();
        langHandlers.forEach(({ btn, handler }) => btn.removeEventListener('click', handler));

        if (menuBtn && menuClickHandler) {
            menuBtn.removeEventListener('click', menuClickHandler);
        }

        menuLinkHandlers.forEach(({ link, handler }) => link.removeEventListener('click', handler));

        if (hero && onHeroMove && onHeroLeave) {
            hero.removeEventListener('mousemove', onHeroMove);
            hero.removeEventListener('mouseleave', onHeroLeave);
        }

        if (onScroll) {
            window.removeEventListener('scroll', onScroll);
        }

        if (revealCheck) {
            window.removeEventListener('scroll', revealCheck);
            window.removeEventListener('resize', revealCheck);
        }

        if (revealTimer) {
            window.clearTimeout(revealTimer);
        }
    };
}

/**
 * @param {HTMLElement} root
 */
function initHeroVideoPlayer(root) {
    const triggers = Array.from(root.querySelectorAll('[data-video-url], [data-video-embed]'));

    if (!triggers.length) {
        return {
            cleanup: () => {},
            refreshModalIfOpen: () => {},
        };
    }

    let modal = null;
    let mediaHost = null;
    let onKeyDown = null;
    let activeTrigger = null;

    const ensureModal = () => {
        if (modal) {
            return;
        }

        modal = document.createElement('div');
        modal.id = 'ghosn-video-modal';
        modal.className = 'ghosn-video-modal';
        modal.innerHTML = `
            <div class="ghosn-video-modal__backdrop" data-video-close></div>
            <div class="ghosn-video-modal__panel" role="dialog" aria-modal="true" aria-label="Video player">
                <button type="button" class="ghosn-video-modal__close" data-video-close aria-label="Close">&times;</button>
                <div class="ghosn-video-modal__media"></div>
                <div class="ghosn-video-modal__actions">
                    <a href="#support" data-action="donate" class="ghosn-video-modal__btn ghosn-video-modal__btn--primary">
                        <span data-action-label><span data-en>Donate</span><span data-ar>تبرّع</span></span>
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
                    </a>
                    <a href="#about" data-action="about" class="ghosn-video-modal__btn ghosn-video-modal__btn--secondary">
                        <span data-action-label><span data-en>Who We Are</span><span data-ar>من نحن</span></span>
                    </a>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        mediaHost = modal.querySelector('.ghosn-video-modal__media');

        modal.querySelectorAll('[data-video-close]').forEach((el) => {
            el.addEventListener('click', closeModal);
        });

        modal.querySelectorAll('.ghosn-video-modal__actions a').forEach((link) => {
            link.addEventListener('click', closeModal);
        });
    };

    const clearMedia = () => {
        if (!mediaHost) {
            return;
        }

        const video = mediaHost.querySelector('video');

        if (video) {
            video.pause();
            video.removeAttribute('src');
            video.load();
        }

        const iframe = mediaHost.querySelector('iframe');

        if (iframe) {
            iframe.removeAttribute('src');
        }

        mediaHost.innerHTML = '';
    };

    const populateModalActions = (trigger) => {
        if (!modal) {
            return;
        }

        const donateBtn = modal.querySelector('[data-action="donate"]');
        const aboutBtn = modal.querySelector('[data-action="about"]');

        if (!donateBtn || !aboutBtn) {
            return;
        }

        donateBtn.href = trigger.getAttribute('data-video-donate-url') || '#support';
        aboutBtn.href = trigger.getAttribute('data-video-about-url') || '#about';

        const setBilingual = (button, en, ar, fallbackEn, fallbackAr) => {
            const enEl = button.querySelector('[data-en]');
            const arEl = button.querySelector('[data-ar]');

            if (enEl) {
                enEl.textContent = en || fallbackEn;
            }

            if (arEl) {
                arEl.textContent = ar || fallbackAr;
            }
        };

        setBilingual(
            donateBtn,
            trigger.getAttribute('data-video-donate-en'),
            trigger.getAttribute('data-video-donate-ar'),
            'Donate now',
            'تبرّع الآن',
        );

        setBilingual(
            aboutBtn,
            trigger.getAttribute('data-video-about-en'),
            trigger.getAttribute('data-video-about-ar'),
            'Who We Are',
            'من نحن',
        );
    };

    const closeModal = () => {
        if (!modal) {
            return;
        }

        modal.classList.remove('is-open');
        document.body.style.overflow = '';
        clearMedia();
        activeTrigger = null;

        if (onKeyDown) {
            document.removeEventListener('keydown', onKeyDown);
            onKeyDown = null;
        }
    };

    const openModal = (trigger, playButton) => {
        ensureModal();

        if (!modal || !mediaHost) {
            return;
        }

        const lang = root.getAttribute('data-lang') || 'en';
        const ariaLabel = lang === 'ar'
            ? playButton?.getAttribute('data-ar-aria')
            : playButton?.getAttribute('aria-label');

        if (ariaLabel) {
            modal.querySelector('[role="dialog"]')?.setAttribute('aria-label', ariaLabel);
        }

        clearMedia();

        const provider = trigger.getAttribute('data-video-provider') || 'file';
        const embedUrl = trigger.getAttribute('data-video-embed');
        const fileUrl = trigger.getAttribute('data-video-url');

        if (provider !== 'file' && embedUrl) {
            const iframe = document.createElement('iframe');
            iframe.className = 'ghosn-video-modal__iframe';
            iframe.setAttribute('allow', 'autoplay; fullscreen; picture-in-picture');
            iframe.setAttribute('allowfullscreen', '');
            iframe.setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');
            iframe.src = embedUrl;
            mediaHost.appendChild(iframe);
        } else if (fileUrl) {
            const video = document.createElement('video');
            video.className = 'ghosn-video-modal__player';
            video.controls = true;
            video.playsInline = true;
            video.preload = 'metadata';
            video.src = fileUrl;
            mediaHost.appendChild(video);
            video.play().catch(() => {});
        } else {
            return;
        }

        populateModalActions(trigger);
        activeTrigger = trigger;

        modal.classList.add('is-open');
        document.body.style.overflow = 'hidden';

        onKeyDown = (event) => {
            if (event.key === 'Escape') {
                closeModal();
            }
        };

        document.addEventListener('keydown', onKeyDown);
    };

    const handlers = triggers.map((trigger) => {
        const handler = (event) => {
            event.preventDefault();

            const playButton = trigger.querySelector('button[type="button"]');
            openModal(trigger, playButton);
        };

        trigger.addEventListener('click', handler);

        return { trigger, handler };
    });

    const refreshModalIfOpen = () => {
        if (modal?.classList.contains('is-open') && activeTrigger) {
            populateModalActions(activeTrigger);
        }
    };

    return {
        cleanup: () => {
            handlers.forEach(({ trigger, handler }) => trigger.removeEventListener('click', handler));
            closeModal();
            modal?.remove();
            modal = null;
            mediaHost = null;
            activeTrigger = null;
        },
        refreshModalIfOpen,
    };
}

document.addEventListener('DOMContentLoaded', () => {
    initLandingPage();
});
