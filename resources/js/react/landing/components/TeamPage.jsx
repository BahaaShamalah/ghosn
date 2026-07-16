import { useEffect, useRef, useState } from 'react';
import { getBootstrap } from '../content';
import { useLanguage } from '../LanguageContext';
import { Reveal } from './ui';

const DEPT_ICONS = {
    field: (
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#F7F6F0" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <path d="M12 2.7S5 10 5 14.5a7 7 0 0 0 14 0C19 10 12 2.7 12 2.7z" />
        </svg>
    ),
    fund: (
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#F7F6F0" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <path d="M12 1v22" /><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
        </svg>
    ),
    media: (
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#F7F6F0" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <path d="M3 11a9 9 0 0 1 9 9M3 4a16 16 0 0 1 16 16" /><circle cx="5" cy="19" r="1.5" fill="#F7F6F0" />
        </svg>
    ),
    edu: (
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#F7F6F0" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <path d="M22 10 12 5 2 10l10 5 10-5z" /><path d="M6 12v5c0 1 2.5 2.5 6 2.5s6-1.5 6-2.5v-5" />
        </svg>
    ),
};

function pick(lang, pair, fallback = '') {
    return pair?.[lang] || pair?.en || fallback;
}

function CountStat({ end, suffix, label, delay }) {
    const ref = useRef(null);
    const [display, setDisplay] = useState(`${end}${suffix}`);
    const [started, setStarted] = useState(false);

    useEffect(() => {
        const node = ref.current;
        if (! node || started) return undefined;

        const observer = new IntersectionObserver(([entry]) => {
            if (! entry.isIntersecting) return;
            setStarted(true);
            const duration = 1500;
            const t0 = performance.now();
            const step = (t) => {
                let p = Math.min(1, (t - t0) / duration);
                p = 1 - (1 - p) ** 3;
                const value = Math.round(end * p).toLocaleString('en-US');
                setDisplay(p < 1 ? `${value}${suffix}` : `${Math.round(end).toLocaleString('en-US')}${suffix}`);
                if (p < 1) requestAnimationFrame(step);
            };
            requestAnimationFrame(step);
            observer.disconnect();
        }, { threshold: 0.2 });

        observer.observe(node);
        return () => observer.disconnect();
    }, [end, suffix, started]);

    return (
        <Reveal delay={delay} className="text-center">
            <div ref={ref} className="text-[clamp(28px,3.4vw,40px)] font-bold leading-none text-[#2f4327]">{display}</div>
            <div className="mt-2 text-[13.5px] font-medium text-[#3a4234]">{label}</div>
        </Reveal>
    );
}

function LeaderSocials({ leader }) {
    const links = [
        leader.linkedinUrl ? { href: leader.linkedinUrl, icon: 'linkedin' } : null,
        leader.xUrl ? { href: leader.xUrl, icon: 'x' } : null,
        leader.email ? { href: `mailto:${leader.email}`, icon: 'mail' } : null,
    ].filter(Boolean);

    if (! links.length) return null;

    return (
        <div className="flex gap-2">
            {links.map((link) => (
                <a
                    key={link.icon}
                    href={link.href}
                    target={link.icon === 'mail' ? undefined : '_blank'}
                    rel="noreferrer"
                    className="flex h-[34px] w-[34px] items-center justify-center rounded-[10px] bg-[#819562]/14 transition hover:-translate-y-0.5 hover:bg-[#406139]/20"
                >
                    {link.icon === 'linkedin' && (
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="#406139"><path d="M4.98 3.5A2.5 2.5 0 1 1 0 3.5a2.5 2.5 0 0 1 4.98 0zM.2 8h4.6v13H.2V8zm7.4 0h4.4v1.8h.06c.6-1.1 2.1-2.3 4.3-2.3 4.6 0 5.44 3 5.44 6.9V21h-4.6v-5.8c0-1.4 0-3.2-1.95-3.2-1.96 0-2.26 1.5-2.26 3.1V21H7.6V8z" /></svg>
                    )}
                    {link.icon === 'x' && (
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="#406139"><path d="M18.9 2H22l-7 8 8.2 12h-6.4l-5-7.3L5.6 22H2.5l7.5-8.6L2 2h6.6l4.5 6.6zm-1.1 18h1.7L7.3 3.9H5.5z" /></svg>
                    )}
                    {link.icon === 'mail' && (
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#406139" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="2" y="4" width="20" height="16" rx="2" /><path d="m22 7-10 6L2 7" /></svg>
                    )}
                </a>
            ))}
        </div>
    );
}

export function TeamPageContent() {
    const { lang } = useLanguage();
    const team = getBootstrap().team ?? {};
    const hero = team.hero ?? {};
    const leadership = team.leadership ?? {};
    const departments = team.departments ?? {};
    const culture = team.culture ?? {};
    const cta = team.cta ?? {};

    return (
        <>
            <section className="relative overflow-hidden bg-gradient-to-br from-[#243619] via-[#3a5330] to-[#2f4327] bg-[length:240%_240%] animate-[ghosnGradient_18s_ease_infinite]">
                <div className="pointer-events-none absolute -top-[50px] end-[8%] h-[240px] w-[240px] animate-[ghosnSpin_46s_linear_infinite] rounded-full border border-dashed border-[#DCE4CC]/20" />
                <div className="pointer-events-none absolute -bottom-[70px] start-[6%] h-[220px] w-[220px] animate-[ghosnFloat_11s_ease-in-out_infinite] rounded-full bg-[radial-gradient(circle,rgba(129,149,98,0.3),transparent_70%)]" />
                <div className="relative z-[2] mx-auto max-w-[860px] px-6 py-16 text-center md:py-[72px]">
                    <Reveal>
                        <div className="mb-5 inline-flex items-center gap-2 rounded-full bg-[#F2F1EA]/16 px-[18px] py-2 text-[12.5px] font-semibold tracking-wide text-[#F2F1EA] backdrop-blur-sm">
                            <span className="h-2 w-2 rounded-full bg-[#BCCAA7]" />
                            {pick(lang, hero.eyebrow)}
                        </div>
                    </Reveal>
                    <Reveal delay={0.08}>
                        <h1 className="mx-auto mb-4 max-w-[680px] text-[clamp(32px,4.6vw,52px)] font-bold leading-[1.14] tracking-[-0.5px] text-[#F7F6F0] text-balance drop-shadow-[0_2px_24px_rgba(20,30,16,0.35)]">{pick(lang, hero.title)}</h1>
                    </Reveal>
                    <Reveal delay={0.16}>
                        <p className="mx-auto max-w-[560px] text-[clamp(16px,1.7vw,19px)] text-[#E8ECDD] text-pretty">{pick(lang, hero.subtitle)}</p>
                    </Reveal>
                </div>
            </section>

            <section className="bg-gradient-to-br from-[#BCCAA7] to-[#96A791] px-6 py-11">
                <div className="mx-auto grid max-w-[1120px] grid-cols-2 gap-5 md:grid-cols-4">
                    {(team.stats ?? []).map((stat, index) => (
                        <CountStat
                            key={stat.label?.en ?? index}
                            end={stat.end}
                            suffix={stat.suffix}
                            label={pick(lang, stat.label)}
                            delay={index * 0.08}
                        />
                    ))}
                </div>
            </section>

            <section className="mx-auto max-w-[1160px] px-6 pb-10 pt-[84px]">
                <Reveal className="mb-12 text-center">
                    <span className="text-[13px] font-semibold uppercase tracking-[2px] text-[#819562]">{pick(lang, leadership.eyebrow)}</span>
                    <h2 className="mt-3 text-[clamp(28px,3.4vw,40px)] font-bold text-[#2f4327]">{pick(lang, leadership.title)}</h2>
                    <p className="mx-auto mt-2 max-w-[520px] text-base text-[#586150]">{pick(lang, leadership.intro)}</p>
                </Reveal>
                <div className="grid gap-7 sm:grid-cols-2 xl:grid-cols-4">
                    {(team.leaders ?? []).map((leader, index) => (
                        <Reveal key={pick(lang, leader.name)} delay={index * 0.08}>
                            <article className="overflow-hidden rounded-[24px] border border-[#406139]/10 bg-[#fffdf8] shadow-[0_8px_26px_rgba(47,67,39,0.07)] transition hover:-translate-y-2 hover:shadow-[0_24px_48px_rgba(47,67,39,0.15)]">
                                <div className="relative h-[280px] overflow-hidden bg-gradient-to-br from-[#BCCAA7] to-[#96A791]">
                                    {leader.imageUrl ? (
                                        <img src={leader.imageUrl} alt="" className="absolute inset-0 h-full w-full object-cover transition duration-500 hover:scale-105" />
                                    ) : null}
                                    <div className="pointer-events-none absolute inset-0 bg-gradient-to-t from-[#1c2a16]/55 via-transparent via-55% to-transparent" />
                                    <div className="absolute inset-x-0 bottom-0 px-5 py-[18px]">
                                        <h3 className="mb-0.5 text-[18.5px] font-bold text-[#F7F6F0] drop-shadow-[0_2px_10px_rgba(20,30,16,0.5)]">{pick(lang, leader.name)}</h3>
                                        <div className="text-[13px] font-semibold text-[#DCE4CC]">{pick(lang, leader.role)}</div>
                                    </div>
                                </div>
                                <div className="px-[22px] py-5">
                                    <p className="mb-4 text-sm text-[#5f6857] text-pretty">{pick(lang, leader.bio)}</p>
                                    <LeaderSocials leader={leader} />
                                </div>
                            </article>
                        </Reveal>
                    ))}
                </div>
            </section>

            <section className="bg-gradient-to-b from-[#eef0e4] to-[#F2F1EA] px-6 py-[84px]">
                <div className="mx-auto max-w-[1160px]">
                    <Reveal className="mb-12 text-center">
                        <span className="text-[13px] font-semibold uppercase tracking-[2px] text-[#819562]">{pick(lang, departments.eyebrow)}</span>
                        <h2 className="mt-3 text-[clamp(28px,3.4vw,40px)] font-bold text-[#2f4327]">{pick(lang, departments.title)}</h2>
                    </Reveal>
                    <div className="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
                        {(departments.items ?? []).map((dept, index) => (
                            <Reveal key={dept.key} delay={index * 0.08}>
                                <div className="rounded-[22px] border border-[#406139]/12 bg-[#fffdf8] p-[30px] shadow-[0_6px_22px_rgba(47,67,39,0.06)] transition hover:-translate-y-1.5 hover:shadow-[0_20px_42px_rgba(47,67,39,0.13)]">
                                    <div className="mb-5 flex h-[54px] w-[54px] items-center justify-center rounded-[15px] bg-gradient-to-br from-[#819562] to-[#406139]">
                                        {DEPT_ICONS[dept.key] ?? DEPT_ICONS.field}
                                    </div>
                                    <h3 className="mb-1.5 text-[19px] font-bold text-[#2f4327]">{pick(lang, dept.name)}</h3>
                                    <div className="mb-3 text-[13px] font-semibold text-[#819562]">{dept.count} {pick(lang, departments.membersLabel)}</div>
                                    <p className="m-0 text-[14.5px] text-[#5f6857] text-pretty">{pick(lang, dept.desc)}</p>
                                </div>
                            </Reveal>
                        ))}
                    </div>
                </div>
            </section>

            <section className="mx-auto flex max-w-[1160px] flex-wrap items-center gap-[60px] px-6 py-[84px]">
                <Reveal className="relative min-w-[300px] flex-1">
                    <div className="absolute inset-5 -end-5 -bottom-5 -rotate-[2.5deg] rounded-[26px] bg-gradient-to-br from-[#819562] to-[#406139] opacity-90" />
                    {culture.imageUrl ? (
                        <img src={culture.imageUrl} alt="" className="relative block h-[380px] w-full rounded-[24px] object-cover shadow-[0_26px_58px_rgba(47,67,39,0.24)]" />
                    ) : (
                        <div className="relative flex h-[380px] w-full items-center justify-center rounded-[24px] bg-gradient-to-br from-[#BCCAA7] to-[#819562] shadow-[0_26px_58px_rgba(47,67,39,0.24)]">
                            <span className="px-6 text-center text-lg font-semibold text-[#2f4327]/70">GHOSN Team</span>
                        </div>
                    )}
                </Reveal>
                <Reveal delay={0.15} className="min-w-[300px] flex-1">
                    <span className="text-[13px] font-semibold uppercase tracking-[2px] text-[#819562]">{pick(lang, culture.eyebrow)}</span>
                    <h2 className="mb-[18px] mt-3 text-[clamp(26px,3.2vw,38px)] font-bold leading-tight text-[#2f4327]">{pick(lang, culture.title)}</h2>
                    <p className="mb-[26px] text-base text-[#586150] text-pretty">{pick(lang, culture.body)}</p>
                    <div className="flex flex-col gap-4">
                        {(culture.points ?? []).map((point) => (
                            <div key={pick(lang, point.title)} className="flex items-start gap-3">
                                <span className="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-[9px] bg-[#819562]/18">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#406139" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round"><path d="M20 6L9 17l-5-5" /></svg>
                                </span>
                                <div>
                                    <div className="mb-0.5 text-base font-bold text-[#2f4327]">{pick(lang, point.title)}</div>
                                    <div className="text-[14.5px] text-[#5f6857] text-pretty">{pick(lang, point.body)}</div>
                                </div>
                            </div>
                        ))}
                    </div>
                </Reveal>
            </section>

            <section className="mx-auto max-w-[1160px] px-6 pb-[84px]">
                <Reveal>
                    <div className="relative overflow-hidden rounded-[30px] bg-gradient-to-br from-[#3a5330] via-[#406139] to-[#2f4327] bg-[length:200%_200%] px-10 py-16 text-center animate-[ghosnGradient_16s_ease_infinite]">
                        <div className="pointer-events-none absolute -top-[50px] end-[8%] h-[200px] w-[200px] animate-[ghosnSpin_44s_linear_infinite] rounded-full border border-dashed border-[#DCE4CC]/22" />
                        <div className="pointer-events-none absolute -bottom-[60px] start-[10%] h-[180px] w-[180px] animate-[ghosnFloat_9s_ease-in-out_infinite] rounded-full bg-[radial-gradient(circle,rgba(188,202,167,0.28),transparent_70%)]" />
                        <h2 className="relative mx-auto mb-3.5 max-w-[600px] text-[clamp(26px,3.4vw,40px)] font-bold leading-[1.18] text-[#F7F6F0] text-balance">{pick(lang, cta.title)}</h2>
                        <p className="relative mx-auto mb-8 max-w-[500px] text-[16.5px] text-[#E8ECDD] text-pretty">{pick(lang, cta.subtitle)}</p>
                        <div className="relative flex flex-wrap justify-center gap-4">
                            <a href={cta.primaryUrl} className="rounded-full bg-[#F7F6F0] px-8 py-[15px] text-[15px] font-bold text-[#406139] no-underline transition hover:-translate-y-0.5 hover:bg-white">{pick(lang, cta.primary)}</a>
                            <a href={cta.secondaryUrl} className="rounded-full border-[1.5px] border-[#F7F6F0]/80 bg-transparent px-8 py-[15px] text-[15px] font-semibold text-[#F7F6F0] no-underline transition hover:-translate-y-0.5 hover:bg-[#F7F6F0] hover:text-[#406139]">{pick(lang, cta.secondary)}</a>
                        </div>
                    </div>
                </Reveal>
            </section>
        </>
    );
}
