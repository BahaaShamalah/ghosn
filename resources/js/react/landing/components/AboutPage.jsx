import { useEffect, useRef, useState } from 'react';
import { getBootstrap } from '../content';
import { useLanguage } from '../LanguageContext';
import { Reveal } from './ui';

function pick(lang, pair, fallback = '') {
    return pair?.[lang] || pair?.en || fallback;
}

const PILLAR_ICONS = {
    mission: (
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#F7F6F0" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <circle cx="12" cy="12" r="9" /><circle cx="12" cy="12" r="4.5" /><circle cx="12" cy="12" r="1" />
        </svg>
    ),
    vision: (
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#F7F6F0" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z" /><circle cx="12" cy="12" r="3" />
        </svg>
    ),
    values: (
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#F7F6F0" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" />
        </svg>
    ),
};

const VALUE_ICONS = {
    heart: (
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#406139" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" />
        </svg>
    ),
    shield: (
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#406139" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z" /><path d="m9 12 2 2 4-4" />
        </svg>
    ),
    sprout: (
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#406139" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <path d="M12 21v-8" /><path d="M12 13c0-3 2.5-5 6-5 0 3-2.5 5-6 5z" /><path d="M12 13c0-2.5-2-4.5-5-4.5 0 2.5 2 4.5 5 4.5z" />
        </svg>
    ),
    hands: (
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#406139" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <path d="M12 3v6" /><path d="M8 21a4 4 0 0 1-4-4v-3l4-2" /><path d="M16 21a4 4 0 0 0 4-4v-3l-4-2" /><path d="M9 9a3 3 0 0 1 6 0" />
        </svg>
    ),
};

function IntroMedia({ intro }) {
    const [playing, setPlaying] = useState(false);
    const video = intro?.video ?? {};
    const hasEmbed = Boolean(video.embedUrl);
    const hasFile = video.provider === 'file' && Boolean(video.sourceUrl);
    const hasVideo = hasEmbed || hasFile;
    const poster = video.posterUrl || intro?.imageUrl || null;

    if (playing && hasEmbed) {
        return (
            <div className="relative aspect-[4/3] overflow-hidden rounded-[24px] bg-[#2f4327] shadow-[0_26px_58px_rgba(47,67,39,0.24)]">
                <iframe
                    src={video.embedUrl}
                    title="About GHOSN"
                    className="absolute inset-0 h-full w-full border-0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowFullScreen
                />
            </div>
        );
    }

    if (playing && hasFile) {
        return (
            <div className="relative overflow-hidden rounded-[24px] shadow-[0_26px_58px_rgba(47,67,39,0.24)]">
                <video src={video.sourceUrl} controls autoPlay className="relative block h-[360px] w-full object-cover" poster={poster || undefined} />
            </div>
        );
    }

    if (hasVideo || poster) {
        return (
            <div className="relative overflow-hidden rounded-[24px] shadow-[0_26px_58px_rgba(47,67,39,0.24)]">
                {poster ? (
                    <img src={poster} alt="" className="relative block h-[360px] w-full object-cover" />
                ) : (
                    <div className="relative flex h-[360px] w-full items-center justify-center bg-gradient-to-br from-[#BCCAA7] to-[#819562]">
                        <span className="px-6 text-center text-lg font-semibold text-[#2f4327]/70">GHOSN</span>
                    </div>
                )}
                {hasVideo ? (
                    <>
                        <div className="absolute inset-0 bg-gradient-to-b from-[#2f4327]/10 to-[#2f4327]/45" />
                        <button
                            type="button"
                            onClick={() => setPlaying(true)}
                            className="absolute left-1/2 top-1/2 flex h-[84px] w-[84px] -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border-none bg-[#406139] transition hover:scale-105 hover:bg-[#33502e]"
                            aria-label="Play video"
                        >
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="#F7F6F0" className="ms-1"><path d="M8 5v14l11-7z" /></svg>
                        </button>
                    </>
                ) : null}
            </div>
        );
    }

    return (
        <div className="relative flex h-[360px] w-full items-center justify-center rounded-[24px] bg-gradient-to-br from-[#BCCAA7] to-[#819562] shadow-[0_26px_58px_rgba(47,67,39,0.24)]">
            <span className="px-6 text-center text-lg font-semibold text-[#2f4327]/70">GHOSN</span>
        </div>
    );
}

function CountStat({ end, suffix, label, delay }) {
    const ref = useRef(null);
    const [display, setDisplay] = useState(`${Number(end).toLocaleString('en-US')}${suffix}`);
    const [started, setStarted] = useState(false);

    useEffect(() => {
        const node = ref.current;
        if (! node || started) return undefined;

        const observer = new IntersectionObserver(([entry]) => {
            if (! entry.isIntersecting) return;
            setStarted(true);
            const duration = 1700;
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
        <Reveal delay={delay} className="rounded-[20px] border border-white/50 bg-[#F2F1EA]/72 px-[18px] py-7 text-center">
            <div ref={ref} className="text-[clamp(30px,3.6vw,42px)] font-bold leading-none tracking-[-1px] text-[#406139]">{display}</div>
            <div className="mt-[11px] text-[13.5px] font-medium text-[#4a5340]">{label}</div>
        </Reveal>
    );
}

export function AboutPageContent() {
    const { lang } = useLanguage();
    const about = getBootstrap().aboutPage ?? {};
    const sections = about.sections ?? {};
    const show = (key) => sections[key] !== false;
    const hero = about.hero ?? {};
    const intro = about.intro ?? {};
    const values = about.values ?? {};
    const story = about.story ?? {};
    const team = about.team ?? {};
    const partners = about.partners ?? {};
    const cta = about.cta ?? {};

    const introParagraphs = intro.paragraphs?.[lang] || intro.paragraphs?.en || [];
    const partnerItems = partners.items?.[lang] || partners.items?.en || [];

    return (
        <>
            {/* HERO */}
            {show('hero') && (
            <section className="relative overflow-hidden bg-gradient-to-br from-[#243619] via-[#3a5330] via-[#406139] to-[#2f4327] bg-[length:240%_240%] animate-[ghosnGradient_18s_ease_infinite]">
                {hero.imageUrl ? (
                    <img src={hero.imageUrl} alt="" className="absolute inset-0 z-0 h-full w-full object-cover opacity-[0.28] animate-[ghosnKen_22s_ease-in-out_infinite_alternate]" />
                ) : null}
                <div className="absolute inset-0 z-[1] bg-gradient-to-br from-[#243619]/82 to-[#406139]/50" />
                <div className="pointer-events-none absolute -top-[60px] end-[8%] z-[1] h-[280px] w-[280px] animate-[ghosnDrift_15s_ease-in-out_infinite] rounded-full bg-[radial-gradient(circle,rgba(188,202,167,0.28),transparent_70%)]" />
                <div className="pointer-events-none absolute -bottom-[80px] start-[6%] z-[1] h-[240px] w-[240px] animate-[ghosnFloat_11s_ease-in-out_infinite] rounded-full bg-[radial-gradient(circle,rgba(129,149,98,0.3),transparent_70%)]" />
                <div className="relative z-[2] mx-auto max-w-[1220px] px-6 py-[104px] text-center md:pb-24">
                    <Reveal>
                        <div className="mb-6 inline-flex items-center gap-2 rounded-full bg-[#F2F1EA]/16 px-[18px] py-2 text-[12.5px] font-semibold tracking-wide text-[#F2F1EA] backdrop-blur-sm">
                            <span className="h-2 w-2 rounded-full bg-[#BCCAA7]" />
                            {pick(lang, hero.eyebrow)}
                        </div>
                    </Reveal>
                    <Reveal delay={0.1}>
                        <h1 className="mx-auto mb-5 max-w-[820px] text-[clamp(34px,5vw,58px)] font-bold leading-[1.12] tracking-[-0.5px] text-[#F7F6F0] text-balance drop-shadow-[0_2px_24px_rgba(20,30,16,0.35)]">{pick(lang, hero.title)}</h1>
                    </Reveal>
                    <Reveal delay={0.22}>
                        <p className="mx-auto max-w-[620px] text-[clamp(16px,1.7vw,20px)] text-[#E8ECDD] text-pretty">{pick(lang, hero.subtitle)}</p>
                    </Reveal>
                </div>
            </section>
            )}

            {show('intro') && (
            <section className="mx-auto flex max-w-[1160px] flex-wrap items-center gap-[60px] px-6 py-[88px]">
                <Reveal className="min-w-[300px] flex-1 basis-[420px]">
                    <span className="text-[13px] font-semibold uppercase tracking-[2px] text-[#819562]">{pick(lang, intro.eyebrow)}</span>
                    <h2 className="mb-[22px] mt-3 text-[clamp(28px,3.6vw,42px)] font-bold leading-[1.15] text-[#2f4327]">{pick(lang, intro.title)}</h2>
                    <div className="flex flex-col gap-[15px]">
                        {introParagraphs.map((paragraph) => (
                            <p key={paragraph} className="m-0 text-base text-[#586150] text-pretty">{paragraph}</p>
                        ))}
                    </div>
                </Reveal>
                <Reveal delay={0.15} className="relative min-w-[300px] flex-1 basis-[380px]">
                    <div className="absolute inset-5 -end-5 -bottom-5 rotate-[2.5deg] rounded-[26px] bg-gradient-to-br from-[#819562] to-[#406139] opacity-90" />
                    <IntroMedia intro={intro} />
                </Reveal>
            </section>
            )}

            {show('stats') && (
            <section className="bg-gradient-to-br from-[#BCCAA7] to-[#96A791] px-6 py-14">
                <div className="mx-auto grid max-w-[1160px] gap-[22px]" style={{ gridTemplateColumns: 'repeat(auto-fit, minmax(180px, 1fr))' }}>
                    {(about.stats ?? []).map((stat, index) => (
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
            )}

            {show('pillars') && (
            <section className="mx-auto max-w-[1160px] px-6 pb-10 pt-[88px]">
                <div className="grid gap-[26px]" style={{ gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))' }}>
                    {(about.pillars ?? []).map((pillar, index) => (
                        <Reveal key={pillar.key} delay={index * 0.1}>
                            <div className="rounded-[24px] border border-[#406139]/12 bg-[#fffdf8] px-[30px] py-[34px] shadow-[0_8px_26px_rgba(47,67,39,0.07)] transition hover:-translate-y-1.5 hover:shadow-[0_24px_48px_rgba(47,67,39,0.14)]">
                                <div className="mb-[22px] flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-[#819562] to-[#406139]">
                                    {PILLAR_ICONS[pillar.key] || PILLAR_ICONS.values}
                                </div>
                                <h3 className="mb-3 text-[22px] font-bold text-[#2f4327]">{pick(lang, pillar.title)}</h3>
                                <p className="m-0 text-[15px] text-[#5f6857] text-pretty">{pick(lang, pillar.body)}</p>
                            </div>
                        </Reveal>
                    ))}
                </div>
            </section>
            )}

            {show('values') && (
            <section className="mx-auto max-w-[1160px] px-6 pb-10 pt-12">
                <Reveal>
                    <h2 className="mb-3 text-center text-[clamp(26px,3.2vw,38px)] font-bold text-[#2f4327]">{pick(lang, values.title)}</h2>
                </Reveal>
                <Reveal delay={0.06}>
                    <p className="mx-auto mb-10 max-w-[560px] text-center text-base text-[#586150]">{pick(lang, values.intro)}</p>
                </Reveal>
                <div className="grid gap-5" style={{ gridTemplateColumns: 'repeat(auto-fit, minmax(240px, 1fr))' }}>
                    {(values.items ?? []).map((item, index) => (
                        <Reveal key={item.key || index} delay={index * 0.08}>
                            <div className="flex items-start gap-4 rounded-[18px] border border-[#406139]/10 bg-[#fffdf8] p-[22px]">
                                <span className="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#819562]/16">
                                    {VALUE_ICONS[item.key] || VALUE_ICONS.heart}
                                </span>
                                <div>
                                    <div className="mb-1 text-[16.5px] font-bold text-[#2f4327]">{pick(lang, item.title)}</div>
                                    <div className="text-sm text-[#5f6857] text-pretty">{pick(lang, item.body)}</div>
                                </div>
                            </div>
                        </Reveal>
                    ))}
                </div>
            </section>
            )}

            {show('story') && (
            <section className="bg-gradient-to-b from-[#eef0e4] to-[#F2F1EA] px-6 py-[88px]">
                <div className="mx-auto max-w-[900px]">
                    <Reveal className="mb-[52px] text-center">
                        <span className="text-[13px] font-semibold uppercase tracking-[2px] text-[#819562]">{pick(lang, story.eyebrow)}</span>
                        <h2 className="mt-3 text-[clamp(28px,3.4vw,40px)] font-bold text-[#2f4327]">{pick(lang, story.title)}</h2>
                    </Reveal>
                    <div className="relative ps-[34px]">
                        <div className="absolute bottom-2 start-2 top-2 w-0.5 bg-[#406139]/18" />
                        <div className="flex flex-col gap-[34px]">
                            {(story.milestones ?? []).map((item) => (
                                <Reveal key={`${pick(lang, item.year)}-${pick(lang, item.title)}`} className="relative">
                                    <span className="absolute -start-[34px] top-0.5 h-[18px] w-[18px] rounded-full border-4 border-[#F2F1EA] bg-[#406139] shadow-[0_0_0_1.5px_rgba(64,97,57,0.3)]" />
                                    <div className="mb-2.5 inline-block rounded-full bg-[#406139] px-3.5 py-1 text-[13px] font-bold text-[#F7F6F0]">{pick(lang, item.year)}</div>
                                    <h3 className="mb-1.5 text-[19px] font-bold text-[#2f4327]">{pick(lang, item.title)}</h3>
                                    <p className="m-0 text-[15px] text-[#5f6857] text-pretty">{pick(lang, item.body)}</p>
                                </Reveal>
                            ))}
                        </div>
                    </div>
                </div>
            </section>
            )}

            {show('team') && (
            <section className="mx-auto max-w-[1160px] px-6 py-[88px]">
                <Reveal className="mb-[52px] text-center">
                    <span className="text-[13px] font-semibold uppercase tracking-[2px] text-[#819562]">{pick(lang, team.eyebrow)}</span>
                    <h2 className="mb-2 mt-3 text-[clamp(28px,3.4vw,40px)] font-bold text-[#2f4327]">{pick(lang, team.title)}</h2>
                    <p className="mx-auto max-w-[540px] text-base text-[#586150]">{pick(lang, team.intro)}</p>
                </Reveal>
                <div className="grid gap-[26px]" style={{ gridTemplateColumns: 'repeat(auto-fit, minmax(240px, 1fr))' }}>
                    {(team.members ?? []).map((member, index) => (
                        <Reveal key={pick(lang, member.name)} delay={index * 0.08}>
                            <article className="overflow-hidden rounded-[22px] border border-[#406139]/10 bg-[#fffdf8] shadow-[0_8px_26px_rgba(47,67,39,0.07)] transition hover:-translate-y-1.5 hover:shadow-[0_22px_46px_rgba(47,67,39,0.14)]">
                                <div className="relative h-[250px] overflow-hidden bg-gradient-to-br from-[#BCCAA7] to-[#96A791]">
                                    {member.imageUrl ? (
                                        <img src={member.imageUrl} alt="" className="absolute inset-0 h-full w-full object-cover transition duration-500 hover:scale-105" />
                                    ) : null}
                                </div>
                                <div className="px-[22px] pb-6 pt-[22px]">
                                    <h3 className="mb-1 text-lg font-bold text-[#2f4327]">{pick(lang, member.name)}</h3>
                                    <div className="text-[13.5px] font-semibold text-[#819562]">{pick(lang, member.role)}</div>
                                </div>
                            </article>
                        </Reveal>
                    ))}
                </div>
            </section>
            )}

            {show('partners') && partnerItems.length > 0 && (
                <section className="mx-auto max-w-[1160px] px-6 pb-[60px]">
                    <Reveal>
                        <div className="rounded-[24px] border border-[#406139]/12 bg-[#fffdf8] px-[34px] py-10 text-center">
                            <div className="mb-[26px] text-[13px] font-semibold uppercase tracking-[2px] text-[#819562]">{pick(lang, partners.title)}</div>
                            <div className="flex flex-wrap items-center justify-center gap-5">
                                {partnerItems.map((item) => (
                                    <div key={item} className="flex items-center gap-2.5 rounded-[14px] border border-[#406139]/14 bg-[#F2F1EA] px-[22px] py-3">
                                        <span className="h-3 w-3 rounded-full bg-[#819562]" />
                                        <span className="text-[15px] font-semibold text-[#4a5340]">{item}</span>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </Reveal>
                </section>
            )}

            {show('cta') && (
            <section className="mx-auto max-w-[1160px] px-6 pb-[88px]">
                <Reveal>
                    <div className="relative overflow-hidden rounded-[30px] bg-gradient-to-br from-[#3a5330] via-[#406139] to-[#2f4327] bg-[length:200%_200%] px-10 py-16 text-center animate-[ghosnGradient_16s_ease_infinite]">
                        <div className="pointer-events-none absolute -top-[50px] end-[8%] h-[200px] w-[200px] animate-[ghosnSpin_44s_linear_infinite] rounded-full border-[1.5px] border-dashed border-[#DCE4CC]/22" />
                        <div className="pointer-events-none absolute -bottom-[60px] start-[10%] h-[180px] w-[180px] animate-[ghosnFloat_9s_ease-in-out_infinite] rounded-full bg-[radial-gradient(circle,rgba(188,202,167,0.28),transparent_70%)]" />
                        <h2 className="relative mx-auto mb-3.5 max-w-[640px] text-[clamp(26px,3.4vw,40px)] font-bold leading-[1.18] text-[#F7F6F0] text-balance">{pick(lang, cta.title)}</h2>
                        <p className="relative mx-auto mb-8 max-w-[520px] text-[16.5px] text-[#E8ECDD] text-pretty">{pick(lang, cta.subtitle)}</p>
                        <div className="relative flex flex-wrap justify-center gap-4">
                            <a href={cta.primaryUrl || '/campaigns'} className="rounded-full bg-[#F7F6F0] px-8 py-[15px] text-[15px] font-bold text-[#406139] no-underline transition hover:-translate-y-0.5 hover:bg-white">{pick(lang, cta.primary)}</a>
                            <a href={cta.secondaryUrl || '/our-team'} className="rounded-full border-[1.5px] border-[#F7F6F0]/80 bg-transparent px-8 py-[15px] text-[15px] font-semibold text-[#F7F6F0] no-underline transition hover:-translate-y-0.5 hover:bg-[#F7F6F0] hover:text-[#406139]">{pick(lang, cta.secondary)}</a>
                        </div>
                    </div>
                </Reveal>
            </section>
            )}
        </>
    );
}
