import { useEffect, useRef, useState } from 'react';
import { formatMoney, pickLocalized } from '../content';
import { useCountUp, useReveal } from '../hooks';
import { useLanguage } from '../LanguageContext';
import { Reveal } from './ui';

export function AboutSection({ fullPage = false }) {
    const { t, routes, aboutVideo, aboutImage, aboutImageAlt } = useLanguage();
    const [playing, setPlaying] = useState(false);
    const hasVideo = Boolean(aboutVideo?.embedUrl);
    const poster = aboutVideo?.poster || aboutImage || null;

    const onPlay = () => {
        if (hasVideo) {
            setPlaying(true);
        }
    };

    return (
        <section id="about" className="relative scroll-mt-20 overflow-hidden bg-[#F2F1EA] px-6 py-20 md:py-24">
            <div className="pointer-events-none absolute -end-16 -top-20 h-[360px] w-[360px] rounded-full bg-[radial-gradient(circle,rgba(129,149,98,.12),transparent_70%)]" />
            <div className="relative mx-auto flex max-w-[1160px] flex-wrap items-center gap-16">
                <Reveal className="min-w-[300px] flex-1">
                    <span className="text-xs font-semibold uppercase tracking-[2px] text-[#819562]">{t.about.eyebrow}</span>
                    <h2 className="mb-5 mt-3 text-[clamp(1.9rem,3.8vw,2.75rem)] font-bold leading-tight text-[#2f4327]">{t.about.title}</h2>
                    <div className="mb-7 flex flex-col gap-4">
                        {t.about.paragraphs.map((paragraph) => (
                            <p key={paragraph.slice(0, 32)} className="whitespace-pre-line text-base text-pretty text-[#586150]">{paragraph}</p>
                        ))}
                    </div>
                    {! fullPage && (
                        <a
                            href={routes.about ?? '/about'}
                            className="mb-7 inline-flex items-center gap-2 rounded-full border border-[#406139]/25 bg-[#fffdf8] px-5 py-2.5 text-sm font-semibold text-[#406139] no-underline shadow-sm transition hover:border-[#406139]/40 hover:bg-white"
                        >
                            {t.about.readMore}
                            <span aria-hidden="true" className="inline-block rtl:-scale-x-100">→</span>
                        </a>
                    )}
                    <div className="overflow-hidden rounded-[18px] border border-[#406139]/12 bg-[#fffdf8] shadow-lg">
                        <div className="flex">
                            {t.about.stats.map((stat) => (
                                <div key={stat.label} className="flex-1 border-s border-[#406139]/14 px-3 py-5 text-center first:border-s-0">
                                    <div className="text-[26px] font-bold leading-none text-[#406139]">{stat.value}</div>
                                    <div className="mt-1.5 text-xs font-medium text-[#8a9280]">{stat.label}</div>
                                </div>
                            ))}
                        </div>
                    </div>
                </Reveal>

                <Reveal delay={0.18} className="relative min-w-[300px] flex-1">
                    <div className="absolute inset-5 -end-5 -bottom-5 rotate-[-2.5deg] rounded-[26px] bg-gradient-to-br from-[#819562] to-[#406139] opacity-90" />
                    <div className="relative aspect-video overflow-hidden rounded-3xl bg-gradient-to-br from-[#33502e] to-[#406139] shadow-2xl">
                        {playing && hasVideo ? (
                            <iframe
                                src={aboutVideo.embedUrl}
                                title={t.about.watch}
                                className="absolute inset-0 h-full w-full border-0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowFullScreen
                            />
                        ) : (
                            <>
                                {poster ? (
                                    <img
                                        src={poster}
                                        alt={aboutImageAlt}
                                        className="absolute inset-0 h-full w-full object-cover transition-transform duration-500 hover:scale-[1.06]"
                                    />
                                ) : null}
                                <div className="absolute inset-0 bg-gradient-to-b from-[#2f4327]/10 to-[#2f4327]/45" />
                                <button
                                    type="button"
                                    onClick={onPlay}
                                    disabled={! hasVideo}
                                    className={`absolute left-1/2 top-1/2 flex h-[84px] w-[84px] -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border-none bg-[#406139] gh-animate-play-ring transition hover:bg-[#33502e] hover:scale-105 ${hasVideo ? 'cursor-pointer' : 'cursor-default opacity-90'}`}
                                    aria-label={t.about.watch}
                                >
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="#F7F6F0" className="ms-1"><path d="M8 5v14l11-7z" /></svg>
                                </button>
                            </>
                        )}
                    </div>
                    {! playing && (
                        <div className="absolute -bottom-4 -start-3.5 z-[2] flex animate-[ghosnFloat_6s_ease-in-out_infinite] items-center gap-2.5 rounded-full border border-[#406139]/14 bg-[#fffdf8] px-4 py-2.5 shadow-xl">
                            <span className="flex h-7 w-7 items-center justify-center rounded-full bg-[#406139]/12">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="#406139"><path d="M8 5v14l11-7z" /></svg>
                            </span>
                            <span className="text-sm font-semibold text-[#2f4327]">{t.about.watch}</span>
                        </div>
                    )}
                </Reveal>
            </div>
        </section>
    );
}

function StatCard({ stat, label, delay }) {
    const { ref, visible } = useReveal();
    const display = useCountUp(stat.end, {
        decimals: stat.decimals,
        prefix: stat.prefix,
        suffix: stat.suffix,
        active: visible,
    });

    return (
        <div
            ref={ref}
            className="rounded-[20px] border border-white/50 bg-[#F2F1EA]/72 p-6 text-center transition hover:-translate-y-1 hover:shadow-xl"
            style={{ transitionDelay: delay }}
        >
            <div className="text-[clamp(2rem,4vw,2.85rem)] font-bold leading-none tracking-tight text-[#406139]">{display}</div>
            <div className="mt-3 text-sm font-medium text-[#4a5340]">{label}</div>
        </div>
    );
}

export function ImpactSection() {
    const { t } = useLanguage();
    const stats = t.impact.stats.map((stat, index) => ({
        ...stat,
        delay: `${index * 0.1}s`,
    }));

    return (
        <section className="bg-gradient-to-r from-[#BCCAA7] to-[#96A791] px-6 py-16">
            <div className="mx-auto max-w-[1220px]">
                <Reveal className="mb-11 text-center text-[clamp(1.5rem,3vw,2.1rem)] font-bold text-[#2f4327]">{t.impact.title}</Reveal>
                <div className="grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] gap-6">
                    {stats.map((stat) => (
                        <StatCard key={stat.key} stat={stat} label={stat.label} delay={stat.delay} />
                    ))}
                </div>
            </div>
        </section>
    );
}

export function HowWorksSection() {
    const { t } = useLanguage();

    return (
        <section className="bg-[#F2F1EA] px-6 py-20 md:py-24">
            <div className="mx-auto max-w-[1080px]">
                <Reveal className="mb-12 text-center">
                    <span className="text-xs font-semibold uppercase tracking-[2px] text-[#819562]">{t.howWorks.eyebrow}</span>
                    <h2 className="mb-3 mt-3 text-[clamp(1.75rem,3.6vw,2.6rem)] font-bold text-[#2f4327]">{t.howWorks.title}</h2>
                    <p className="mx-auto max-w-xl text-pretty text-[16.5px] text-[#586150]">{t.howWorks.intro}</p>
                </Reveal>
                <div className="grid grid-cols-[repeat(auto-fit,minmax(230px,1fr))] gap-6">
                    {t.howWorks.steps.map((step, index) => (
                        <Reveal key={step.title} delay={index * 0.1} className="rounded-[22px] border border-[#406139]/12 bg-[#fffdf8] p-7 text-center shadow-md">
                            <div className="mx-auto mb-5 flex h-[60px] w-[60px] items-center justify-center rounded-full bg-gradient-to-br from-[#819562] to-[#406139] text-[22px] font-bold text-[#F7F6F0] shadow-lg">
                                {index + 1}
                            </div>
                            <h3 className="mb-2.5 text-lg font-bold text-[#2f4327]">{step.title}</h3>
                            <p className="text-sm text-pretty text-[#5f6857]">{step.body}</p>
                        </Reveal>
                    ))}
                </div>
            </div>
        </section>
    );
}

const WAY_ICONS = [
    <svg key="heart" width="27" height="27" viewBox="0 0 24 24" fill="none" stroke="#406139" strokeWidth="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" /></svg>,
    <svg key="people" width="27" height="27" viewBox="0 0 24 24" fill="none" stroke="#406139" strokeWidth="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M22 21v-2a4 4 0 0 0-3-3.87" /></svg>,
    <svg key="building" width="27" height="27" viewBox="0 0 24 24" fill="none" stroke="#406139" strokeWidth="2"><path d="M3 21h18" /><path d="M5 21V7l8-4v18" /><path d="M19 21V11l-6-4" /></svg>,
];

export function WaysSection() {
    const { t, routes } = useLanguage();
    const hrefs = [routes.donate, routes.volunteer ?? routes.joinUs, routes.about];

    return (
        <section className="bg-[#fffdf8] px-6 py-20">
            <div className="mx-auto max-w-[1080px]">
                <Reveal className="mb-12 text-center">
                    <span className="text-xs font-semibold uppercase tracking-[2px] text-[#819562]">{t.ways.eyebrow}</span>
                    <h2 className="mb-3 mt-3 text-[clamp(1.75rem,3.6vw,2.6rem)] font-bold text-[#2f4327]">{t.ways.title}</h2>
                    <p className="mx-auto max-w-xl text-pretty text-[16.5px] text-[#586150]">{t.ways.intro}</p>
                </Reveal>
                <div className="grid grid-cols-[repeat(auto-fit,minmax(250px,1fr))] gap-6">
                    {t.ways.cards.map((card, index) => (
                        <Reveal key={card.title} delay={index * 0.1} className="rounded-[22px] border border-[#406139]/12 bg-[#F2F1EA] p-7 shadow-sm">
                            <div className="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#406139]/10">{WAY_ICONS[index]}</div>
                            <h3 className="mb-2 text-xl font-bold text-[#2f4327]">{card.title}</h3>
                            <p className="mb-5 text-sm text-pretty text-[#5f6857]">{card.body}</p>
                            <a href={hrefs[index] ?? '#'} className="text-sm font-semibold text-[#406139] no-underline">{card.cta} →</a>
                        </Reveal>
                    ))}
                </div>
            </div>
        </section>
    );
}

export function TestimonialsSection() {
    const { t } = useLanguage();

    return (
        <section className="bg-gradient-to-b from-[#F2F1EA] to-[#EDEEE4] px-6 py-20">
            <div className="mx-auto max-w-[1080px]">
                <Reveal className="mb-12 text-center">
                    <span className="text-xs font-semibold uppercase tracking-[2px] text-[#819562]">{t.testimonials.eyebrow}</span>
                    <h2 className="mt-3 text-[clamp(1.75rem,3.6vw,2.6rem)] font-bold text-[#2f4327]">{t.testimonials.title}</h2>
                </Reveal>
                <div className="grid grid-cols-[repeat(auto-fit,minmax(280px,1fr))] gap-6">
                    {t.testimonials.items.map((item, index) => (
                        <Reveal key={item.name} delay={index * 0.1} className="rounded-[22px] border border-[#406139]/10 bg-[#fffdf8] p-7 shadow-sm">
                            <p className="mb-6 text-pretty text-[15px] leading-relaxed text-[#586150]">&ldquo;{item.quote}&rdquo;</p>
                            <div className="flex items-center gap-3">
                                <span className="flex h-10 w-10 items-center justify-center rounded-full bg-[#406139] text-sm font-bold text-[#F2F1EA]">{item.name.slice(0, 1)}</span>
                                <div>
                                    <div className="font-semibold text-[#2f4327]">{item.name}</div>
                                    <div className="text-xs text-[#8a9280]">{item.role}</div>
                                </div>
                            </div>
                        </Reveal>
                    ))}
                </div>
            </div>
        </section>
    );
}

export function CampaignsSection() {
    const { t, lang, campaigns, routes, campaignsSectionVisible } = useLanguage();

    if (! campaignsSectionVisible) {
        return null;
    }

    return (
        <section id="campaigns" className="scroll-mt-20 bg-[#F2F1EA] px-6 py-20 md:py-24">
            <div className="mx-auto max-w-[1220px]">
                <Reveal className="mb-12 text-center">
                    <span className="text-xs font-semibold uppercase tracking-[2px] text-[#819562]">{t.campaigns.eyebrow}</span>
                    <h2 className="mb-3 mt-3 text-[clamp(1.75rem,3.6vw,2.6rem)] font-bold text-[#2f4327]">{t.campaigns.title}</h2>
                    <p className="mx-auto max-w-2xl text-pretty text-[16.5px] text-[#586150]">{t.campaigns.intro}</p>
                </Reveal>

                {campaigns.length === 0 ? (
                    <Reveal className="rounded-3xl border border-dashed border-[#406139]/20 bg-[#fffdf8]/70 px-8 py-14 text-center text-[#586150]">
                        {t.campaigns.empty}
                    </Reveal>
                ) : (
                    <div className="grid grid-cols-[repeat(auto-fit,minmax(280px,1fr))] gap-6">
                        {campaigns.map((campaign, index) => (
                            <Reveal key={campaign.id} delay={index * 0.1} className="overflow-hidden rounded-[22px] border border-[#406139]/10 bg-[#fffdf8] shadow-md transition hover:-translate-y-1 hover:shadow-xl">
                                <a href={campaign.url} className="block">
                                    {campaign.image ? (
                                        <img src={campaign.image} alt="" className="h-48 w-full object-cover" />
                                    ) : (
                                        <div className="flex h-48 items-center justify-center bg-[#406139]/10 text-sm font-semibold text-[#406139]/50">GHOSN</div>
                                    )}
                                </a>
                                <div className="p-6">
                                    <span className="mb-3 inline-block rounded-full px-3 py-1 text-[11px] font-semibold uppercase tracking-wide text-[#F2F1EA]" style={{ background: campaign.tag === 'urgent' ? '#406139' : '#819562' }}>
                                        {t.campaigns.tags[campaign.tag] ?? t.campaigns.tags.ongoing}
                                    </span>
                                    <h3 className="mb-2 text-lg font-bold text-[#2f4327]">
                                        <a href={campaign.url} className="no-underline text-inherit">{pickLocalized(campaign, lang, 'title')}</a>
                                    </h3>
                                    <p className="mb-4 line-clamp-3 text-sm text-[#586150]">{pickLocalized(campaign, lang, 'excerpt')}</p>
                                    <div className="mb-2 h-2 overflow-hidden rounded-full bg-[#406139]/10">
                                        <div className="h-full rounded-full bg-[#819562]" style={{ width: `${campaign.pct}%` }} />
                                    </div>
                                    <div className="mb-4 flex justify-between text-xs text-[#8a9280]">
                                        <span>{formatMoney(campaign.raised, campaign.currency)} {t.campaigns.funded}</span>
                                        <span>{campaign.pct}%</span>
                                    </div>
                                    <a href={campaign.url} className="inline-flex rounded-full bg-[#406139] px-4 py-2 text-sm font-semibold text-[#F2F1EA] no-underline">{t.campaigns.donate}</a>
                                </div>
                            </Reveal>
                        ))}
                    </div>
                )}

                <div className="mt-10 text-center">
                    <a href={routes.campaigns ?? '/campaigns'} className="text-sm font-semibold text-[#406139] no-underline">{t.campaigns.viewAll} →</a>
                </div>
            </div>
        </section>
    );
}

export function BlogSection() {
    const { t, lang, posts, routes, blogSectionVisible } = useLanguage();

    if (! blogSectionVisible) {
        return null;
    }

    return (
        <section id="blog" className="scroll-mt-20 bg-[#fffdf8] px-6 py-20">
            <div className="mx-auto max-w-[1220px]">
                <Reveal className="mb-12 text-center">
                    <span className="text-xs font-semibold uppercase tracking-[2px] text-[#819562]">{t.blog.eyebrow}</span>
                    <h2 className="mt-3 text-[clamp(1.75rem,3.6vw,2.6rem)] font-bold text-[#2f4327]">{t.blog.title}</h2>
                </Reveal>

                {posts.length === 0 ? (
                    <Reveal className="rounded-3xl border border-dashed border-[#406139]/20 bg-[#F2F1EA]/70 px-8 py-14 text-center text-[#586150]">
                        {t.blog.empty}
                    </Reveal>
                ) : (
                    <div className="grid grid-cols-[repeat(auto-fit,minmax(280px,1fr))] gap-6">
                        {posts.map((post, index) => (
                            <Reveal key={post.id} delay={index * 0.12} className="overflow-hidden rounded-[22px] border border-[#406139]/10 bg-[#F2F1EA] shadow-sm">
                                <a href={post.url}>
                                    {post.image ? (
                                        <img src={post.image} alt="" className="h-44 w-full object-cover" />
                                    ) : (
                                        <div className="flex h-44 items-center justify-center bg-[#406139]/10 text-sm font-semibold text-[#406139]/50">GHOSN</div>
                                    )}
                                </a>
                                <div className="p-6">
                                    <div className="mb-2 text-xs font-semibold uppercase tracking-wide text-[#819562]">{pickLocalized(post, lang, 'category')}</div>
                                    <h3 className="mb-2 text-lg font-bold text-[#2f4327]">
                                        <a href={post.url} className="no-underline text-inherit">{pickLocalized(post, lang, 'title')}</a>
                                    </h3>
                                    <p className="mb-4 line-clamp-3 text-sm text-[#586150]">{pickLocalized(post, lang, 'excerpt')}</p>
                                    <div className="flex items-center justify-between text-xs text-[#8a9280]">
                                        <span>{post.date}</span>
                                        <a href={post.url} className="font-semibold text-[#406139] no-underline">{t.blog.readMore}</a>
                                    </div>
                                </div>
                            </Reveal>
                        ))}
                    </div>
                )}

                <div className="mt-10 text-center">
                    <a href={routes.news ?? '/news'} className="text-sm font-semibold text-[#406139] no-underline">{t.blog.viewAll} →</a>
                </div>
            </div>
        </section>
    );
}

const EMAIL_RE = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
const BLANK_FORM = { name: '', phone: '', email: '', area: '', message: '' };

export function JoinTeamSection({ standalone = false }) {
    const { t, routes } = useLanguage();
    const [form, setForm] = useState({ ...BLANK_FORM });
    const [errors, setErrors] = useState({});
    const [status, setStatus] = useState('idle');

    const onField = (event) => {
        const { name, value } = event.target;
        setForm((current) => ({ ...current, [name]: value }));
        setErrors((current) => ({ ...current, [name]: '' }));
    };

    const onSubmit = async (event) => {
        event.preventDefault();
        const nextErrors = {};

        if (! form.name.trim()) nextErrors.name = t.join.err.required;
        if (! form.email.trim()) nextErrors.email = t.join.err.required;
        else if (! EMAIL_RE.test(form.email)) nextErrors.email = t.join.err.email;
        if (! form.area) nextErrors.area = t.join.err.required;

        if (Object.keys(nextErrors).length) {
            setErrors(nextErrors);
            setStatus('idle');

            return;
        }

        setErrors({});
        setStatus('loading');

        try {
            const { getRecaptchaToken } = await import('../recaptcha');
            const token = await getRecaptchaToken('volunteer');

            const response = await fetch(routes.volunteerApplications ?? '/volunteer-applications', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                },
                body: JSON.stringify({
                    ...form,
                    'g-recaptcha-response': token || undefined,
                }),
            });

            if (! response.ok) {
                throw new Error('submit failed');
            }

            setStatus('success');
            setForm({ ...BLANK_FORM });
        } catch {
            setStatus('error');
        }
    };

    return (
        <section
            id={standalone ? undefined : 'team'}
            className={standalone
                ? 'scroll-mt-20 min-h-[calc(100vh-72px)] bg-gradient-to-br from-[#406139] to-[#243619] px-6 py-20 text-[#F2F1EA] md:py-24'
                : 'scroll-mt-20 bg-gradient-to-br from-[#406139] to-[#243619] px-6 py-20 text-[#F2F1EA]'}
        >
            <div className="mx-auto grid max-w-[1080px] gap-10 lg:grid-cols-2">
                <Reveal>
                    <span className="text-xs font-semibold uppercase tracking-[2px] text-[#BCCAA7]">{t.join.eyebrow}</span>
                    <h2 className="mb-4 mt-3 text-[clamp(1.75rem,3.6vw,2.6rem)] font-bold">{t.join.title}</h2>
                    <p className="mb-6 text-pretty text-[#E8ECDD]">{t.join.copy}</p>
                    <ul className="space-y-2 text-sm text-[#DCE4CC]">
                        {t.join.bullets.map((bullet) => (
                            <li key={bullet} className="flex items-center gap-2">
                                <span className="h-1.5 w-1.5 rounded-full bg-[#BCCAA7]" />
                                {bullet}
                            </li>
                        ))}
                    </ul>
                </Reveal>

                <Reveal delay={0.15} className="rounded-[22px] border border-white/10 bg-[#F2F1EA]/10 p-6 backdrop-blur-sm">
                    {status === 'success' && (
                        <p className="mb-4 rounded-xl bg-[#BCCAA7]/20 px-4 py-3 text-sm font-medium text-[#F2F1EA]">{t.join.success}</p>
                    )}
                    {status === 'error' && (
                        <p className="mb-4 rounded-xl bg-red-500/20 px-4 py-3 text-sm font-medium text-[#F2F1EA]">{t.join.error}</p>
                    )}
                    <form className="grid gap-4" onSubmit={onSubmit}>
                        {[
                            ['name', 'text', t.join.name, t.join.namePh],
                            ['phone', 'tel', t.join.phone, t.join.phonePh],
                            ['email', 'email', t.join.email, t.join.emailPh],
                        ].map(([name, type, label, placeholder]) => (
                            <label key={name} className="block text-sm">
                                <span className="mb-1 block font-medium text-[#DCE4CC]">{label}</span>
                                <input
                                    type={type}
                                    name={name}
                                    value={form[name]}
                                    onChange={onField}
                                    placeholder={placeholder}
                                    className="w-full rounded-xl border border-white/15 bg-[#fffdf8]/95 px-4 py-3 text-[#2f4327] outline-none"
                                />
                                {errors[name] && <span className="mt-1 block text-xs text-[#ffd4d4]">{errors[name]}</span>}
                            </label>
                        ))}
                        <label className="block text-sm">
                            <span className="mb-1 block font-medium text-[#DCE4CC]">{t.join.areaLabel}</span>
                            <select name="area" value={form.area} onChange={onField} className="w-full rounded-xl border border-white/15 bg-[#fffdf8]/95 px-4 py-3 text-[#2f4327]">
                                <option value="">{t.join.areaPh}</option>
                                {t.join.areas.map((area) => (
                                    <option key={area.value} value={area.value}>{area.label}</option>
                                ))}
                            </select>
                            {errors.area && <span className="mt-1 block text-xs text-[#ffd4d4]">{errors.area}</span>}
                        </label>
                        <label className="block text-sm">
                            <span className="mb-1 block font-medium text-[#DCE4CC]">{t.join.message}</span>
                            <textarea name="message" value={form.message} onChange={onField} rows={3} placeholder={t.join.messagePh} className="w-full rounded-xl border border-white/15 bg-[#fffdf8]/95 px-4 py-3 text-[#2f4327]" />
                        </label>
                        <button type="submit" disabled={status === 'loading'} className="cursor-pointer rounded-xl border-none bg-[#819562] px-4 py-4 text-[15px] font-semibold text-[#F2F1EA] disabled:opacity-70">
                            {status === 'loading' ? t.join.sending : t.join.submit}
                        </button>
                    </form>
                </Reveal>
            </div>
        </section>
    );
}

export function NewsletterSection() {
    const { t, routes, newsletterEnabled } = useLanguage();
    const [email, setEmail] = useState('');
    const [status, setStatus] = useState('idle');

    if (! newsletterEnabled) {
        return null;
    }

    const onSubmit = async (event) => {
        event.preventDefault();

        if (! EMAIL_RE.test(email)) {
            return;
        }

        setStatus('loading');

        try {
            const response = await fetch(routes.newsletterSubscriptions ?? '/newsletter-subscriptions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                },
                body: JSON.stringify({ email }),
            });

            if (! response.ok) {
                throw new Error('subscribe failed');
            }

            setStatus('success');
            setEmail('');
        } catch {
            setStatus('error');
        }
    };

    return (
        <section className="gh-newsletter-section bg-[#BCCAA7]/35 px-6 py-16">
            <Reveal className="mx-auto max-w-2xl text-center">
                <h2 className="mb-3 text-2xl font-bold text-[#2f4327]">{t.newsletter.title}</h2>
                <p className="mb-6 text-[#586150]">{t.newsletter.subtitle}</p>
                {status === 'success' ? (
                    <p className="rounded-xl bg-[#406139]/10 px-4 py-3 text-sm font-semibold text-[#406139]">{t.newsletter.success}</p>
                ) : (
                    <>
                        {status === 'error' && (
                            <p className="mb-4 rounded-xl bg-red-500/10 px-4 py-3 text-sm font-semibold text-[#a24a37]">{t.newsletter.error}</p>
                        )}
                        <form className="mx-auto flex max-w-md flex-col gap-3 sm:flex-row" onSubmit={onSubmit}>
                            <input
                                type="email"
                                value={email}
                                onChange={(event) => setEmail(event.target.value)}
                                placeholder={t.newsletter.placeholder}
                                required
                                disabled={status === 'loading'}
                                className="flex-1 rounded-full border border-[#406139]/20 bg-[#fffdf8] px-5 py-3 text-[#2f4327] disabled:opacity-70"
                            />
                            <button
                                type="submit"
                                disabled={status === 'loading'}
                                className="rounded-full bg-[#406139] px-6 py-3 text-sm font-semibold text-[#F2F1EA] disabled:opacity-70"
                            >
                                {status === 'loading' ? '…' : t.newsletter.button}
                            </button>
                        </form>
                    </>
                )}
            </Reveal>
        </section>
    );
}

export function FooterSection() {
    const { t, assets, socialLinks, contact, routes, lang, siteName, navLinks: configuredNav, footerChrome } = useLanguage();
    const year = new Date().getFullYear();
    const name = siteName?.[lang] ?? 'GHOSN Relief Team';

    const navLinks = (configuredNav ?? []).map((link) => ({
        label: link.label?.[lang] ?? link.label?.en ?? '',
        href: link.href,
    }));

    const exploreLinks = (footerChrome?.links ?? []).map((link) => ({
        label: link.label?.[lang] ?? link.label?.en ?? '',
        href: link.href,
    }));

    const footerDesc = footerChrome?.desc?.[lang] ?? t.footer.desc;
    const footerTagline = footerChrome?.tagline?.[lang] ?? t.footer.tagline;
    const quickTitle = footerChrome?.quickTitle?.[lang] ?? t.footer.quick;
    const linksTitle = footerChrome?.linksTitle?.[lang] ?? t.footer.explore ?? 'Explore';
    const contactTitle = footerChrome?.contactTitle?.[lang] ?? t.contact.title;
    const followTitle = footerChrome?.followTitle?.[lang] ?? t.footer.follow;
    const rights = footerChrome?.rights?.[lang] ?? t.footer.rights;
    const address = contact?.address?.[lang] ?? footerChrome?.address?.[lang] ?? t.contact.address;

    return (
        <footer id="contact" className="gh-site-footer scroll-mt-20">
            <div className="gh-site-footer__grid">
                <div className="gh-site-footer__brand">
                    <div className="gh-site-footer__logo-row">
                        <img src={assets.logo} alt="GHOSN" className="gh-site-footer__logo" />
                        <div className="gh-site-footer__logo-text">
                            <span className="gh-site-footer__logo-title">GHOSN</span>
                            <span className="gh-site-footer__logo-subtitle">RELIEF TEAM</span>
                        </div>
                    </div>
                    <p className="gh-site-footer__desc">{footerDesc}</p>
                    <p className="gh-site-footer__tagline">{footerTagline}</p>
                    {(socialLinks?.length ?? 0) > 0 && (
                        <>
                            <div className="gh-site-footer__follow-label">{followTitle}</div>
                            <div className="gh-site-footer__social">
                                {(socialLinks ?? []).map((link) => (
                                    <a
                                        key={link.url}
                                        href={link.url}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="gh-site-footer__social-link"
                                        aria-label={link.label ?? link.platform}
                                        title={link.label ?? link.platform}
                                    >
                                        <i className={link.iconClass || 'fa-solid fa-link'} aria-hidden="true" />
                                    </a>
                                ))}
                            </div>
                        </>
                    )}
                </div>

                <div className="gh-site-footer__column">
                    <div className="gh-site-footer__column-title">{quickTitle}</div>
                    <div className="gh-site-footer__links">
                        {navLinks.map((link) => (
                            <a key={link.href} href={link.href}>{link.label}</a>
                        ))}
                    </div>
                </div>

                {exploreLinks.length > 0 && (
                    <div className="gh-site-footer__column">
                        <div className="gh-site-footer__column-title">{linksTitle}</div>
                        <div className="gh-site-footer__links">
                            {exploreLinks.map((link) => (
                                <a key={`${link.href}-${link.label}`} href={link.href}>{link.label}</a>
                            ))}
                        </div>
                    </div>
                )}

                <div className="gh-site-footer__column">
                    <div className="gh-site-footer__column-title">{contactTitle}</div>
                    <div className="gh-site-footer__contact">
                        {contact.phone && (
                            <div>
                                <div className="gh-site-footer__contact-label">{t.contact.phoneLabel}</div>
                                <div dir="ltr">{contact.phone}</div>
                            </div>
                        )}
                        <div>
                            <div className="gh-site-footer__contact-label">{t.contact.emailLabel}</div>
                            <a href={`mailto:${contact.email}`} dir="ltr">{contact.email}</a>
                        </div>
                        {address && (
                            <div>
                                <div className="gh-site-footer__contact-label">{t.contact.addressLabel}</div>
                                <div>{address}</div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
            <div className="gh-site-footer__bottom">
                &copy; {year} {name}. {rights}
            </div>
        </footer>
    );
}

// Reveal observer for all [data-reveal] nodes
export function useGlobalReveal() {
    useEffect(() => {
        const nodes = document.querySelectorAll('[data-reveal]');
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('gh-reveal--shown');
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.12, rootMargin: '0px 0px -8% 0px' },
        );

        nodes.forEach((node) => observer.observe(node));

        return () => observer.disconnect();
    }, []);
}
