import { createContext, useContext, useMemo, useState } from 'react';

import { CONTENT, getBootstrap } from './content';

const LanguageContext = createContext(null);

function pick(lang, pair, fallback = '') {
    return pair?.[lang] || fallback;
}

function mergeHeroCopy(lang, bootstrap) {
    const defaults = CONTENT[lang].hero;
    const configured = bootstrap.hero ?? {};

    return {
        badge: pick(lang, configured.badge, defaults.badge),
        title: pick(lang, configured.title, defaults.title),
        subtitle: pick(lang, configured.subtitle, defaults.subtitle),
        ctaPrimary: pick(lang, configured.ctaPrimary, defaults.ctaPrimary),
        ctaSecondary: pick(lang, configured.ctaSecondary, defaults.ctaSecondary),
        ctaPrimaryUrl: configured.ctaPrimaryUrl || '#campaigns',
        ctaSecondaryUrl: configured.ctaSecondaryUrl || '#team',
    };
}

function mergeAboutCopy(lang, bootstrap) {
    const defaults = CONTENT[lang].about;
    const configured = bootstrap.about ?? {};
    const configuredParagraphs = configured.paragraphs?.[lang];

    const paragraphs = Array.isArray(configuredParagraphs)
        ? configuredParagraphs.filter((text) => String(text).trim() !== '')
        : defaults.paragraphs;

    const stats = (configured.stats?.length ? configured.stats : defaults.stats).map((stat, index) => ({
        value: pick(lang, stat.value, defaults.stats[index]?.value),
        label: pick(lang, stat.label, defaults.stats[index]?.label),
    }));

    return {
        eyebrow: pick(lang, configured.eyebrow, defaults.eyebrow),
        title: pick(lang, configured.title, defaults.title),
        watch: pick(lang, configured.watch, defaults.watch),
        readMore: pick(lang, configured.readMore, defaults.readMore),
        paragraphs,
        stats,
    };
}

function mergeImpactCopy(lang, bootstrap) {
    const defaults = CONTENT[lang].impact;
    const configured = bootstrap.impact ?? {};
    const stats = (configured.stats?.length ? configured.stats : defaults.labels.map((label, index) => ({
        key: ['beneficiaries', 'campaigns', 'volunteers', 'donations'][index],
        end: [128000, 24, 1450, 3.2][index],
        decimals: [0, 0, 0, 1][index],
        prefix: ['', '', '', '$'][index],
        suffix: ['+', '', '+', 'M'][index],
        label: { en: CONTENT.en.impact.labels[index], ar: CONTENT.ar.impact.labels[index] },
    }))).map((stat) => ({
        key: stat.key,
        end: stat.end,
        decimals: stat.decimals ?? 0,
        prefix: stat.prefix ?? '',
        suffix: stat.suffix ?? '',
        label: pick(lang, stat.label, ''),
    }));

    return {
        title: pick(lang, configured.title, defaults.title),
        stats,
    };
}

function mergeHowWorksCopy(lang, bootstrap) {
    const defaults = CONTENT[lang].howWorks;
    const configured = bootstrap.howWorks ?? {};

    return {
        eyebrow: pick(lang, configured.eyebrow, defaults.eyebrow),
        title: pick(lang, configured.title, defaults.title),
        intro: pick(lang, configured.intro, defaults.intro),
        steps: (configured.steps?.length ? configured.steps : defaults.steps).map((step, index) => ({
            title: pick(lang, step.title, defaults.steps[index]?.title),
            body: pick(lang, step.body, defaults.steps[index]?.body),
        })),
    };
}

function mergeWaysCopy(lang, bootstrap) {
    const defaults = CONTENT[lang].ways;
    const configured = bootstrap.ways ?? {};

    return {
        eyebrow: pick(lang, configured.eyebrow, defaults.eyebrow),
        title: pick(lang, configured.title, defaults.title),
        intro: pick(lang, configured.intro, defaults.intro),
        cards: (configured.cards?.length ? configured.cards : defaults.cards).map((card, index) => ({
            title: pick(lang, card.title, defaults.cards[index]?.title),
            body: pick(lang, card.body, defaults.cards[index]?.body),
            cta: pick(lang, card.cta, defaults.cards[index]?.cta),
        })),
    };
}

function mergeTestimonialsCopy(lang, bootstrap) {
    const defaults = CONTENT[lang].testimonials;
    const configured = bootstrap.testimonials ?? {};

    return {
        eyebrow: pick(lang, configured.eyebrow, defaults.eyebrow),
        title: pick(lang, configured.title, defaults.title),
        items: (configured.items?.length ? configured.items : defaults.items).map((item, index) => ({
            quote: pick(lang, item.quote, defaults.items[index]?.quote),
            name: pick(lang, item.name, defaults.items[index]?.name),
            role: pick(lang, item.role, defaults.items[index]?.role),
        })),
    };
}

function mergeJoinCopy(lang, bootstrap) {
    const defaults = CONTENT[lang].join;
    const configured = bootstrap.join ?? {};

    return {
        eyebrow: pick(lang, configured.eyebrow, defaults.eyebrow),
        title: pick(lang, configured.title, defaults.title),
        copy: pick(lang, configured.copy, defaults.copy),
        bullets: (configured.bullets?.length ? configured.bullets : defaults.bullets).map((bullet, index) => (
            typeof bullet === 'string' ? bullet : pick(lang, bullet, defaults.bullets[index])
        )),
        name: pick(lang, configured.name, defaults.name),
        namePh: pick(lang, configured.namePh, defaults.namePh),
        phone: pick(lang, configured.phone, defaults.phone),
        phonePh: pick(lang, configured.phonePh, defaults.phonePh),
        email: pick(lang, configured.email, defaults.email),
        emailPh: pick(lang, configured.emailPh, defaults.emailPh),
        areaLabel: pick(lang, configured.areaLabel, defaults.areaLabel),
        areaPh: pick(lang, configured.areaPh, defaults.areaPh),
        message: pick(lang, configured.message, defaults.message),
        messagePh: pick(lang, configured.messagePh, defaults.messagePh),
        submit: pick(lang, configured.submit, defaults.submit),
        sending: pick(lang, configured.sending, defaults.sending),
        success: pick(lang, configured.success, defaults.success),
        error: pick(lang, configured.error, defaults.error),
        areas: (configured.areas?.length ? configured.areas : defaults.areas).map((area, index) => ({
            value: area.value ?? defaults.areas[index]?.value,
            label: pick(lang, area.label, defaults.areas[index]?.label),
        })),
        err: defaults.err,
    };
}

function mergeCampaignsCopy(lang, bootstrap) {
    const defaults = CONTENT[lang].campaigns;
    const configured = bootstrap.campaignSection ?? {};

    return {
        ...defaults,
        eyebrow: pick(lang, configured.eyebrow, defaults.eyebrow),
        title: pick(lang, configured.title, defaults.title),
        intro: pick(lang, configured.intro, defaults.intro),
    };
}

function mergeBlogCopy(lang, bootstrap) {
    const defaults = CONTENT[lang].blog;
    const configured = bootstrap.blogSection ?? {};

    return {
        ...defaults,
        eyebrow: pick(lang, configured.eyebrow, defaults.eyebrow),
        title: pick(lang, configured.title, defaults.title),
    };
}

function mergeNewsletterCopy(lang, bootstrap) {
    const defaults = CONTENT[lang].newsletter;
    const configured = bootstrap.newsletter ?? {};

    return {
        title: pick(lang, configured.title, defaults.title),
        subtitle: pick(lang, configured.subtitle, defaults.subtitle),
        placeholder: pick(lang, configured.placeholder, defaults.placeholder),
        button: pick(lang, configured.button, defaults.button),
        success: pick(lang, configured.success, defaults.success),
        error: defaults.error ?? CONTENT.en.newsletter.error,
    };
}

export function LanguageProvider({ children }) {
    const bootstrap = getBootstrap();
    const [lang, setLang] = useState(bootstrap.locale === 'ar' ? 'ar' : 'en');

    const value = useMemo(() => {
        const base = CONTENT[lang];

        return {
            lang,
            setLang,
            isAr: lang === 'ar',
            t: {
                ...base,
                hero: mergeHeroCopy(lang, bootstrap),
                about: mergeAboutCopy(lang, bootstrap),
                impact: mergeImpactCopy(lang, bootstrap),
                howWorks: mergeHowWorksCopy(lang, bootstrap),
                ways: mergeWaysCopy(lang, bootstrap),
                testimonials: mergeTestimonialsCopy(lang, bootstrap),
                join: mergeJoinCopy(lang, bootstrap),
                campaigns: mergeCampaignsCopy(lang, bootstrap),
                blog: mergeBlogCopy(lang, bootstrap),
                newsletter: mergeNewsletterCopy(lang, bootstrap),
            },
            routes: bootstrap.routes ?? {},
            assets: bootstrap.assets ?? {},
            campaigns: bootstrap.campaigns ?? [],
            posts: bootstrap.posts ?? [],
            socialLinks: bootstrap.socialLinks ?? [],
            contact: bootstrap.contact ?? {},
            siteName: bootstrap.siteName ?? { en: 'GHOSN Relief Team', ar: 'فريق غُصن للإغاثة' },
            navLinks: bootstrap.navLinks ?? [],
            donateLabel: bootstrap.donateLabel?.[lang] ?? base.nav.donate,
            footerChrome: bootstrap.footer ?? {},
            contactPage: bootstrap.contactPage ?? {},
            newsletterEnabled: bootstrap.newsletter?.enabled !== false,
            homeSectionsVisible: bootstrap.homeSectionsVisible ?? {},
            campaignsSectionVisible: bootstrap.campaignsSectionVisible !== false,
            blogSectionVisible: bootstrap.blogSectionVisible !== false,
            heroBackground: bootstrap.hero?.backgroundImage ?? null,
            heroBackgroundAlt: bootstrap.hero?.backgroundAlt?.[lang] || '',
            aboutVideo: bootstrap.about?.video ?? null,
            aboutImage: bootstrap.about?.image ?? null,
            aboutImageAlt: bootstrap.about?.imageAlt?.[lang] || '',
        };
    }, [lang, bootstrap]);

    return <LanguageContext.Provider value={value}>{children}</LanguageContext.Provider>;
}

export function useLanguage() {
    const ctx = useContext(LanguageContext);

    if (! ctx) {
        throw new Error('useLanguage must be used within LanguageProvider');
    }

    return ctx;
}
