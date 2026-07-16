import { createContext, useContext, useMemo, useState } from 'react';
import { CONTENT, getBootstrap } from './content';

const LanguageContext = createContext(null);

export function LanguageProvider({ children }) {
    const bootstrap = getBootstrap();
    const [lang, setLang] = useState(bootstrap.locale === 'ar' ? 'ar' : 'en');

    const value = useMemo(() => ({
        lang,
        setLang,
        isAr: lang === 'ar',
        t: CONTENT[lang],
        routes: bootstrap.routes ?? {},
        assets: bootstrap.assets ?? {},
        user: bootstrap.user ?? {},
        nav: bootstrap.nav ?? [],
        dashboard: bootstrap.dashboard ?? {},
        localeBase: bootstrap.localeBase ?? '/admin/locale',
        csrfToken: bootstrap.csrfToken ?? '',
    }), [lang, bootstrap]);

    return <LanguageContext.Provider value={value}>{children}</LanguageContext.Provider>;
}

export function useLanguage() {
    const ctx = useContext(LanguageContext);

    if (! ctx) {
        throw new Error('useLanguage must be used within LanguageProvider');
    }

    return ctx;
}
