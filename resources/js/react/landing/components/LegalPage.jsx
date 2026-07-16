import { useEffect, useMemo } from 'react';
import { getBootstrap } from '../content';
import { useLanguage } from '../LanguageContext';
import { Reveal } from './ui';

function pick(lang, pair, fallback = '') {
    return pair?.[lang] || pair?.en || fallback;
}

function CheckIcon() {
    return (
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#406139" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round">
            <path d="M20 6L9 17l-5-5" />
        </svg>
    );
}

export function LegalPageContent() {
    const { lang, routes, contact } = useLanguage();
    const bootstrap = getBootstrap();
    const legal = bootstrap.legal ?? {};
    const page = legal.page ?? {};
    const tabs = legal.tabs ?? [];
    const ui = legal.ui ?? {};
    const activeKey = legal.activeKey ?? 'donation';

    const title = pick(lang, page.title);
    const subtitle = pick(lang, page.subtitle);
    const updated = pick(lang, page.updated);
    const intro = pick(lang, page.intro);

    const sections = useMemo(() => (page.sections ?? []).map((section) => ({
        ...section,
        heading: pick(lang, section.heading),
        paragraphs: section.paragraphs?.[lang] ?? section.paragraphs?.en ?? [],
        bullets: section.bullets?.[lang] ?? section.bullets?.en ?? [],
    })), [lang, page.sections]);

    useEffect(() => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }, [activeKey]);

    const contactEmail = contact?.email ?? '';

    return (
        <>
            <section className="relative overflow-hidden bg-gradient-to-br from-[#243619] via-[#3a5330] to-[#2f4327] bg-[length:240%_240%] animate-[ghosnGradient_18s_ease_infinite]">
                <div className="pointer-events-none absolute -top-[50px] end-[8%] h-[240px] w-[240px] animate-[ghosnSpin_46s_linear_infinite] rounded-full border border-dashed border-[#DCE4CC]/20" />
                <div className="pointer-events-none absolute -bottom-[70px] start-[6%] h-[220px] w-[220px] animate-[ghosnFloat_11s_ease-in-out_infinite] rounded-full bg-[radial-gradient(circle,rgba(129,149,98,0.3),transparent_70%)]" />
                <div className="relative z-[2] mx-auto max-w-[900px] px-6 py-16 text-center md:py-[72px]">
                    <Reveal>
                        <nav className="mb-5 inline-flex items-center gap-2 text-[13px] text-[#DCE4CC]">
                            <a href={routes.home ?? '/'} className="text-[#DCE4CC]/85 no-underline transition hover:text-[#DCE4CC]">{pick(lang, ui.homeCrumb)}</a>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#DCE4CC" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round" className="rtl:rotate-180"><path d="m9 18 6-6-6-6" /></svg>
                            <span className="font-semibold text-[#F7F6F0]">{title}</span>
                        </nav>
                    </Reveal>
                    <Reveal delay={0.08}>
                        <h1 className="mx-auto mb-4 max-w-[720px] text-[clamp(32px,4.6vw,52px)] font-bold leading-[1.14] tracking-[-0.5px] text-[#F7F6F0] text-balance drop-shadow-[0_2px_24px_rgba(20,30,16,0.35)]">{title}</h1>
                    </Reveal>
                    <Reveal delay={0.16}>
                        <p className="mx-auto mb-5 max-w-[560px] text-[clamp(16px,1.7vw,19px)] text-[#E8ECDD] text-pretty">{subtitle}</p>
                    </Reveal>
                    {updated && (
                        <Reveal delay={0.22}>
                            <div className="inline-flex items-center gap-2 rounded-full bg-[#F2F1EA]/14 px-4 py-1.5 text-[12.5px] font-medium text-[#F2F1EA] backdrop-blur-sm">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#DCE4CC" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" /></svg>
                                {pick(lang, ui.lastUpdated)} {updated}
                            </div>
                        </Reveal>
                    )}
                </div>
            </section>

            <div className="sticky top-[75px] z-50 border-b border-[#406139]/10 bg-[#F2F1EA]/90 backdrop-blur-[10px]">
                <div className="mx-auto flex max-w-[1120px] gap-1 overflow-x-auto px-6">
                    {tabs.map((tab) => {
                        const isActive = tab.key === activeKey;
                        return (
                            <a
                                key={tab.key}
                                href={tab.href}
                                className={`mb-[-1px] cursor-pointer whitespace-nowrap border-b-[2.5px] px-[18px] py-4 text-sm font-semibold no-underline transition ${isActive ? 'border-[#406139] text-[#406139]' : 'border-transparent text-[#6b7360] hover:text-[#406139]'}`}
                            >
                                {pick(lang, tab.label)}
                            </a>
                        );
                    })}
                </div>
            </div>

            <section className="mx-auto grid max-w-[1120px] items-start gap-12 px-6 py-14 lg:grid-cols-[minmax(0,1fr)_260px] lg:gap-[52px]">
                <article className="min-w-0">
                    <Reveal>
                        <p className="mb-2 text-lg leading-[1.7] text-[#4a5340] text-pretty">{intro}</p>
                    </Reveal>

                    {sections.map((section) => (
                        <Reveal key={section.anchor}>
                            <div id={section.anchor} className="scroll-mt-[150px] pt-[34px]">
                                <h2 className="mb-3.5 flex items-baseline gap-3 text-[23px] font-bold leading-snug text-[#2f4327]">
                                    <span className="shrink-0 text-sm font-bold text-[#819562]">{section.num}</span>
                                    {section.heading}
                                </h2>
                                <div className="flex flex-col gap-3.5">
                                    {section.paragraphs.map((paragraph) => (
                                        <p key={paragraph.slice(0, 40)} className="m-0 text-base leading-[1.72] text-[#4f5847] text-pretty">{paragraph}</p>
                                    ))}
                                </div>
                                {section.bullets.length > 0 && (
                                    <ul className="mt-4 flex list-none flex-col gap-[11px] ps-2">
                                        {section.bullets.map((bullet) => (
                                            <li key={bullet.slice(0, 40)} className="flex items-start gap-3 text-[15.5px] leading-[1.65] text-[#4f5847]">
                                                <span className="mt-0.5 flex h-[22px] w-[22px] shrink-0 items-center justify-center rounded-[7px] bg-[#819562]/18">
                                                    <CheckIcon />
                                                </span>
                                                <span className="text-pretty">{bullet}</span>
                                            </li>
                                        ))}
                                    </ul>
                                )}
                            </div>
                        </Reveal>
                    ))}

                    <Reveal>
                        <div className="mt-11 flex items-start gap-[18px] rounded-[20px] border border-[#406139]/12 bg-[#fffdf8] p-7">
                            <span className="flex h-12 w-12 shrink-0 items-center justify-center rounded-[14px] bg-[#819562]/16">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#406139" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="2" y="4" width="20" height="16" rx="2" /><path d="m22 7-10 6L2 7" /></svg>
                            </span>
                            <div>
                                <h3 className="mb-1.5 text-[17px] font-bold text-[#2f4327]">{pick(lang, ui.contactNote?.title)}</h3>
                                <p className="mb-1 text-[14.5px] text-[#5f6857] text-pretty">{pick(lang, ui.contactNote?.body)}</p>
                                {contactEmail && (
                                    <a href={`mailto:${contactEmail}`} className="text-[14.5px] font-semibold text-[#406139] no-underline">{contactEmail}</a>
                                )}
                            </div>
                        </div>
                    </Reveal>
                </article>

                <aside className="sticky top-[150px] hidden min-w-0 lg:block">
                    <Reveal delay={0.1}>
                        <div className="rounded-[20px] border border-[#406139]/12 bg-[#fffdf8] p-[22px]">
                            <div className="mb-3.5 text-xs font-semibold uppercase tracking-wide text-[#8a9280]">{pick(lang, ui.onThisPage)}</div>
                            <div className="flex flex-col gap-0.5">
                                {sections.map((section) => (
                                    <a
                                        key={section.anchor}
                                        href={`#${section.anchor}`}
                                        className="rounded-[9px] border-s-2 border-transparent px-3 py-2 text-[13.5px] leading-snug text-[#5f6857] no-underline transition hover:border-[#819562] hover:bg-[#819562]/10 hover:text-[#406139]"
                                    >
                                        {section.num}. {section.heading}
                                    </a>
                                ))}
                            </div>
                        </div>
                    </Reveal>
                </aside>
            </section>
        </>
    );
}
