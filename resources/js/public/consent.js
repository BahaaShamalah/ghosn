const COOKIE_NAME = 'ghosn_consent';

function readCookie(name) {
    const match = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/([.$?*|{}()[\]\\/+^])/g, '\\$1') + '=([^;]*)'));
    return match ? decodeURIComponent(match[1]) : null;
}

function writeCookie(name, value, days) {
    const maxAge = Math.max(1, Number(days) || 365) * 24 * 60 * 60;
    document.cookie = `${name}=${encodeURIComponent(value)}; path=/; max-age=${maxAge}; SameSite=Lax`;
}

function applyConsent(prefs) {
    if (typeof window.gtag !== 'function') {
        return;
    }

    window.gtag('consent', 'update', {
        analytics_storage: prefs.analytics ? 'granted' : 'denied',
        ad_storage: prefs.marketing ? 'granted' : 'denied',
        ad_user_data: prefs.marketing ? 'granted' : 'denied',
        ad_personalization: prefs.marketing ? 'granted' : 'denied',
        functionality_storage: prefs.preferences ? 'granted' : 'denied',
        personalization_storage: prefs.preferences ? 'granted' : 'denied',
        security_storage: 'granted',
    });
}

export function initConsentBanner() {
    const root = document.querySelector('[data-consent-root]');
    if (! root) {
        return;
    }

    const config = window.__GHOSN_GOOGLE__ || {};
    if (config.consentEnabled === false) {
        return;
    }

    const existing = readCookie(COOKIE_NAME);
    if (existing) {
        try {
            applyConsent(JSON.parse(existing));
            return;
        } catch {
            // show banner again
        }
    }

    root.hidden = false;
    root.classList.remove('hidden');

    const customizePanel = root.querySelector('[data-consent-customize]');
    const saveBtn = root.querySelector('[data-consent-save]');
    const analytics = root.querySelector('[data-consent-analytics]');
    const marketing = root.querySelector('[data-consent-marketing]');
    const preferences = root.querySelector('[data-consent-preferences]');

    const persist = (prefs) => {
        writeCookie(COOKIE_NAME, JSON.stringify(prefs), config.cookieDays || 365);
        applyConsent(prefs);
        root.hidden = true;
        root.classList.add('hidden');
    };

    root.querySelector('[data-consent-accept]')?.addEventListener('click', () => {
        persist({ necessary: true, analytics: true, marketing: true, preferences: true });
    });

    root.querySelector('[data-consent-reject]')?.addEventListener('click', () => {
        persist({ necessary: true, analytics: false, marketing: false, preferences: false });
    });

    root.querySelector('[data-consent-customize-toggle]')?.addEventListener('click', () => {
        customizePanel?.classList.toggle('hidden');
        saveBtn?.classList.toggle('hidden');
    });

    saveBtn?.addEventListener('click', () => {
        persist({
            necessary: true,
            analytics: Boolean(analytics?.checked),
            marketing: Boolean(marketing?.checked),
            preferences: Boolean(preferences?.checked),
        });
    });
}
