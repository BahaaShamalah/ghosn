import { useState } from 'react';
import { getBootstrap } from '../content';
import { useLanguage } from '../LanguageContext';
import { Reveal } from './ui';

const EMAIL_RE = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
const AVAIL_KEYS = ['weekdays', 'weekends', 'remote'];
const BLANK_FORM = { name: '', age: '', phone: '', email: '', area: '', message: '', agree: false };

const ERR = {
    en: { required: 'Required', email: 'Enter a valid email', area: 'Please choose an area', agree: 'Please accept to continue' },
    ar: { required: 'مطلوب', email: 'أدخل بريدًا صحيحًا', area: 'يرجى اختيار مجال', agree: 'يرجى الموافقة للمتابعة' },
};

function pick(lang, pair, fallback = '') {
    return pair?.[lang] || fallback;
}

const BENEFIT_ICONS = {
    impact: (
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#F7F6F0" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" />
        </svg>
    ),
    grow: (
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#F7F6F0" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <path d="M12 21v-8" />
            <path d="M12 13c0-3 2.5-5 6-5 0 3-2.5 5-6 5z" />
            <path d="M12 13c0-2.5-2-4.5-5-4.5 0 2.5 2 4.5 5 4.5z" />
        </svg>
    ),
    community: (
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#F7F6F0" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
            <circle cx="9" cy="7" r="4" />
            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
        </svg>
    ),
};

const AREA_ICONS = {
    field: (
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#406139" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <path d="M12 2.7S5 10 5 14.5a7 7 0 0 0 14 0C19 10 12 2.7 12 2.7z" />
        </svg>
    ),
    fund: (
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#406139" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <path d="M12 1v22" />
            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
        </svg>
    ),
    media: (
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#406139" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <path d="M3 11a9 9 0 0 1 9 9M3 4a16 16 0 0 1 16 16" />
            <circle cx="5" cy="19" r="1.5" fill="#406139" />
        </svg>
    ),
    logistics: (
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#406139" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <rect x="1" y="3" width="15" height="13" rx="1" />
            <path d="M16 8h4l3 3v5h-7" />
            <circle cx="5.5" cy="18.5" r="2.5" />
            <circle cx="18.5" cy="18.5" r="2.5" />
        </svg>
    ),
    edu: (
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#406139" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <path d="M22 10 12 5 2 10l10 5 10-5z" />
            <path d="M6 12v5c0 1 2.5 2.5 6 2.5s6-1.5 6-2.5v-5" />
        </svg>
    ),
};

function VolunteerApplicationForm({ page, lang, routes }) {
    const f = page.form;
    const err = ERR[lang] ?? ERR.en;
    const [form, setForm] = useState({ ...BLANK_FORM });
    const [availability, setAvailability] = useState({});
    const [errors, setErrors] = useState({});
    const [status, setStatus] = useState('idle');

    const onField = (event) => {
        const { name, value, type, checked } = event.target;
        setForm((current) => ({ ...current, [name]: type === 'checkbox' ? checked : value }));
        setErrors((current) => ({ ...current, [name]: '' }));
    };

    const toggleAvail = (key) => {
        setAvailability((current) => ({ ...current, [key]: !current[key] }));
    };

    const onSubmit = async (event) => {
        event.preventDefault();
        const nextErrors = {};

        if (!form.name.trim()) nextErrors.name = err.required;
        if (!form.email.trim()) nextErrors.email = err.required;
        else if (!EMAIL_RE.test(form.email)) nextErrors.email = err.email;
        if (!form.area) nextErrors.area = err.area;
        if (!form.agree) nextErrors.agree = err.agree;

        if (Object.keys(nextErrors).length) {
            setErrors(nextErrors);
            setStatus('idle');

            return;
        }

        setErrors({});
        setStatus('loading');

        const availList = AVAIL_KEYS.filter((key) => availability[key]);
        const extras = [];

        if (form.age.trim()) extras.push(`Age: ${form.age.trim()}`);
        if (availList.length) extras.push(`Availability: ${availList.join(', ')}`);

        let message = form.message.trim();

        if (extras.length) {
            message = `${extras.join('\n')}${message ? `\n\n${message}` : ''}`;
        }

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
                    name: form.name.trim(),
                    email: form.email.trim(),
                    phone: form.phone.trim(),
                    area: form.area,
                    message: message || null,
                    'g-recaptcha-response': token || undefined,
                }),
            });

            if (!response.ok) {
                throw new Error('submit failed');
            }

            setStatus('success');
            setForm({ ...BLANK_FORM });
            setAvailability({});
            document.getElementById('apply')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } catch {
            setStatus('error');
        }
    };

    if (status === 'success') {
        const thanks = page.thanks;

        return (
            <div className="py-10 text-center">
                <div className="mx-auto mb-7 flex h-24 w-24 animate-[ghosnPop_.6s_cubic-bezier(.2,.8,.3,1)_both] items-center justify-center rounded-full bg-gradient-to-br from-[#819562] to-[#406139]">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#F7F6F0" strokeWidth="2.4" strokeLinecap="round" strokeLinejoin="round">
                        <path d="M20 6 9 17l-5-5" />
                    </svg>
                </div>
                <h2 className="mb-3.5 text-[clamp(1.625rem,3.2vw,2.25rem)] font-bold text-[#2f4327]">{pick(lang, thanks.title)}</h2>
                <p className="mx-auto mb-8 max-w-[440px] text-pretty text-[17px] text-[#586150]">{pick(lang, thanks.body)}</p>
                <div className="flex flex-wrap justify-center gap-3.5">
                    <a href={routes.home ?? '/'} className="rounded-full bg-[#406139] px-8 py-3.5 text-[15px] font-semibold text-[#F2F1EA] no-underline transition hover:-translate-y-0.5 hover:bg-[#33502e]">
                        {pick(lang, thanks.home)}
                    </a>
                    <a href={routes.campaigns ?? '/campaigns'} className="rounded-full border-[1.5px] border-[#406139] bg-transparent px-8 py-3.5 text-[15px] font-semibold text-[#406139] no-underline transition hover:bg-[#406139] hover:text-[#F2F1EA]">
                        {pick(lang, thanks.explore)}
                    </a>
                </div>
            </div>
        );
    }

    const inputClass = 'w-full rounded-xl border-[1.5px] border-[#d3dcc2] bg-[#F2F1EA] px-4 py-3.5 text-[14.5px] text-[#3a4234] outline-none transition focus:border-[#406139] focus:shadow-[0_0_0_3px_rgba(64,97,57,.15)]';

    return (
        <>
            <Reveal className="mb-9 text-center">
                <span className="text-[13px] font-semibold uppercase tracking-[2px] text-[#819562]">{pick(lang, page.apply.eyebrow)}</span>
                <h2 className="mb-2 mt-3 text-[clamp(1.75rem,3.4vw,2.5rem)] font-bold text-[#2f4327]">{pick(lang, page.apply.title)}</h2>
                <p className="mx-auto max-w-[460px] text-base text-[#586150]">{pick(lang, page.apply.intro)}</p>
            </Reveal>

            <Reveal delay={0.08}>
                <form onSubmit={onSubmit} noValidate className="rounded-3xl border border-[#406139]/10 bg-[#fffdf8] p-8 shadow-[0_20px_50px_rgba(47,67,39,.12)] md:p-10">
                    <div className="flex flex-col gap-[18px]">
                        <div className="flex flex-wrap gap-4">
                            <label className="flex min-w-[160px] flex-1 flex-col gap-1.5 text-[13px] font-semibold text-[#4a5340]">
                                {pick(lang, f.name)}
                                <input type="text" name="name" value={form.name} onChange={onField} placeholder={pick(lang, f.namePh)} className={inputClass} />
                                {errors.name && <span className="text-xs font-medium text-[#a24a37]">{errors.name}</span>}
                            </label>
                            <label className="flex min-w-[160px] flex-1 flex-col gap-1.5 text-[13px] font-semibold text-[#4a5340]">
                                {pick(lang, f.age)}
                                <input type="number" name="age" min="16" value={form.age} onChange={onField} placeholder={pick(lang, f.agePh)} className={inputClass} />
                            </label>
                        </div>
                        <div className="flex flex-wrap gap-4">
                            <label className="flex min-w-[160px] flex-1 flex-col gap-1.5 text-[13px] font-semibold text-[#4a5340]">
                                {pick(lang, f.phone)}
                                <input type="tel" name="phone" value={form.phone} onChange={onField} placeholder={pick(lang, f.phonePh)} className={inputClass} />
                            </label>
                            <label className="flex min-w-[160px] flex-1 flex-col gap-1.5 text-[13px] font-semibold text-[#4a5340]">
                                {pick(lang, f.email)}
                                <input type="email" name="email" value={form.email} onChange={onField} placeholder={pick(lang, f.emailPh)} className={inputClass} />
                                {errors.email && <span className="text-xs font-medium text-[#a24a37]">{errors.email}</span>}
                            </label>
                        </div>
                        <label className="flex flex-col gap-1.5 text-[13px] font-semibold text-[#4a5340]">
                            {pick(lang, f.areaLabel)}
                            <select name="area" value={form.area} onChange={onField} className={`${inputClass} cursor-pointer`}>
                                <option value="">{pick(lang, f.areaPh)}</option>
                                {(f.areas ?? []).map((area) => (
                                    <option key={area.value} value={area.value}>{pick(lang, area.label)}</option>
                                ))}
                            </select>
                            {errors.area && <span className="text-xs font-medium text-[#a24a37]">{errors.area}</span>}
                        </label>
                        <div>
                            <div className="mb-2.5 text-[13px] font-semibold text-[#4a5340]">{pick(lang, f.availability)}</div>
                            <div className="flex flex-wrap gap-2">
                                {[
                                    ['weekdays', f.availWeekdays],
                                    ['weekends', f.availWeekends],
                                    ['remote', f.availRemote],
                                ].map(([key, label]) => (
                                    <button
                                        key={key}
                                        type="button"
                                        onClick={() => toggleAvail(key)}
                                        className={`rounded-full border-[1.5px] px-[18px] py-2.5 text-[13.5px] font-semibold transition ${
                                            availability[key]
                                                ? 'border-[#406139] bg-[#406139] text-[#F7F6F0]'
                                                : 'border-[#406139]/25 bg-transparent text-[#4a5340]'
                                        }`}
                                    >
                                        {pick(lang, label)}
                                    </button>
                                ))}
                            </div>
                        </div>
                        <label className="flex flex-col gap-1.5 text-[13px] font-semibold text-[#4a5340]">
                            {pick(lang, f.message)}
                            <textarea name="message" rows="4" value={form.message} onChange={onField} placeholder={pick(lang, f.messagePh)} className={`${inputClass} resize-y`} />
                        </label>
                        <label className="flex cursor-pointer select-none items-start gap-2.5 text-[13.5px] text-[#4a5340]">
                            <input type="checkbox" name="agree" checked={form.agree} onChange={onField} className="mt-0.5 h-[18px] w-[18px] shrink-0 cursor-pointer accent-[#406139]" />
                            <span>{pick(lang, f.agree)}</span>
                        </label>
                        {errors.agree && <span className="-mt-2 text-xs font-medium text-[#a24a37]">{errors.agree}</span>}
                        {status === 'error' && (
                            <div className="rounded-xl border border-[#a24a37] bg-[rgba(162,74,55,.12)] px-4 py-3 text-center text-sm font-medium text-[#8a3d2d]">
                                {pick(lang, f.error)}
                            </div>
                        )}
                        <button
                            type="submit"
                            disabled={status === 'loading'}
                            className="animate-[ghosnPulse_2.6s_ease-in-out_infinite] rounded-xl border-none bg-[#406139] px-4 py-4 text-[15px] font-semibold text-[#F2F1EA] transition hover:-translate-y-0.5 hover:bg-[#33502e] disabled:opacity-70"
                        >
                            {status === 'loading' ? pick(lang, f.sending) : pick(lang, f.submit)}
                        </button>
                    </div>
                </form>
            </Reveal>
        </>
    );
}

export function VolunteerPageContent() {
    const { lang, routes } = useLanguage();
    const page = getBootstrap().volunteer ?? {};

    return (
        <>
            <section className="relative overflow-hidden bg-gradient-to-br from-[#243619] via-[#3a5330] to-[#2f4327] bg-[length:240%_240%] animate-[ghosnGradient_18s_ease_infinite]">
                {page.hero?.image ? (
                    <img src={page.hero.image} alt="" className="pointer-events-none absolute inset-0 z-0 h-full w-full animate-[ghosnFloat_16s_ease-in-out_infinite] object-cover opacity-[0.26]" />
                ) : null}
                <div className="absolute inset-0 z-[1] bg-gradient-to-br from-[rgba(36,54,25,.7)] to-[rgba(64,97,57,.45)]" />
                <div className="pointer-events-none absolute -top-[50px] end-[8%] z-[1] h-[230px] w-[230px] animate-[ghosnSpin_46s_linear_infinite] rounded-full border border-dashed border-[rgba(220,228,204,.2)]" />
                <div className="pointer-events-none absolute -bottom-[70px] start-[6%] z-[1] h-[210px] w-[210px] animate-[ghosnFloat_11s_ease-in-out_infinite] rounded-full bg-[radial-gradient(circle,rgba(129,149,98,.3),transparent_70%)]" />
                <div className="relative z-[2] mx-auto max-w-[860px] px-6 py-20 pb-[74px] text-center md:py-[80px]">
                    <Reveal className="mb-5 inline-flex items-center gap-2 rounded-full bg-[rgba(242,241,234,.16)] px-[18px] py-2 text-[12.5px] font-semibold tracking-wide text-[#F2F1EA] backdrop-blur-sm">
                        <span className="h-2 w-2 rounded-full bg-[#BCCAA7]" />
                        {pick(lang, page.hero?.eyebrow)}
                    </Reveal>
                    <Reveal delay={0.08}>
                        <h1 className="mx-auto mb-4 max-w-[680px] text-balance text-[clamp(2.125rem,4.8vw,3.5rem)] font-bold leading-[1.12] tracking-[-0.5px] text-[#F7F6F0] text-shadow-[0_2px_24px_rgba(20,30,16,.4)]">
                            {pick(lang, page.hero?.title)}
                        </h1>
                    </Reveal>
                    <Reveal delay={0.16}>
                        <p className="mx-auto mb-8 max-w-[560px] text-pretty text-[clamp(1rem,1.7vw,1.25rem)] text-[#E8ECDD]">
                            {pick(lang, page.hero?.subtitle)}
                        </p>
                    </Reveal>
                    <Reveal delay={0.24}>
                        <a href="#apply" className="inline-block animate-[ghosnPulse_2.6s_ease-in-out_infinite] rounded-full bg-[#819562] px-9 py-4 text-base font-bold text-[#F7F6F0] no-underline shadow-[0_12px_30px_rgba(38,55,31,.4)] transition hover:-translate-y-1 hover:bg-[#6f8452]">
                            {pick(lang, page.hero?.cta)}
                        </a>
                    </Reveal>
                </div>
            </section>

            <section className="mx-auto max-w-[1160px] px-6 pb-10 pt-[84px]">
                <Reveal className="mb-12 text-center">
                    <span className="text-[13px] font-semibold uppercase tracking-[2px] text-[#819562]">{pick(lang, page.why?.eyebrow)}</span>
                    <h2 className="mb-2 mt-3 text-[clamp(1.75rem,3.4vw,2.5rem)] font-bold text-[#2f4327]">{pick(lang, page.why?.title)}</h2>
                    <p className="mx-auto max-w-[540px] text-base text-[#586150]">{pick(lang, page.why?.intro)}</p>
                </Reveal>
                <div className="grid grid-cols-[repeat(auto-fit,minmax(250px,1fr))] gap-6">
                    {(page.why?.benefits ?? []).map((benefit, index) => (
                        <Reveal key={benefit.key ?? index} delay={index * 0.1} className="rounded-3xl border border-[#406139]/12 bg-[#fffdf8] p-8 shadow-[0_8px_26px_rgba(47,67,39,.07)] transition hover:-translate-y-1.5 hover:shadow-[0_22px_46px_rgba(47,67,39,.14)]">
                            <div className="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-[#819562] to-[#406139]">
                                {BENEFIT_ICONS[benefit.key] ?? BENEFIT_ICONS.impact}
                            </div>
                            <h3 className="mb-2.5 text-xl font-bold text-[#2f4327]">{pick(lang, benefit.title)}</h3>
                            <p className="m-0 text-pretty text-[15px] text-[#5f6857]">{pick(lang, benefit.body)}</p>
                        </Reveal>
                    ))}
                </div>
            </section>

            <section className="bg-gradient-to-b from-[#eef0e4] to-[#F2F1EA] px-6 py-[84px]">
                <div className="mx-auto max-w-[1160px]">
                    <Reveal className="mb-12 text-center">
                        <span className="text-[13px] font-semibold uppercase tracking-[2px] text-[#819562]">{pick(lang, page.areas?.eyebrow)}</span>
                        <h2 className="mb-0 mt-3 text-[clamp(1.75rem,3.4vw,2.5rem)] font-bold text-[#2f4327]">{pick(lang, page.areas?.title)}</h2>
                    </Reveal>
                    <div className="grid grid-cols-[repeat(auto-fit,minmax(250px,1fr))] gap-[22px]">
                        {(page.areas?.items ?? []).map((area, index) => (
                            <Reveal key={area.key ?? index} delay={index * 0.07} className="flex items-start gap-4 rounded-[20px] border border-[#406139]/12 bg-[#fffdf8] p-6 transition hover:-translate-y-1">
                                <span className="flex h-[46px] w-[46px] shrink-0 items-center justify-center rounded-[13px] bg-[rgba(129,149,98,.16)]">
                                    {AREA_ICONS[area.key] ?? AREA_ICONS.field}
                                </span>
                                <div>
                                    <h3 className="mb-1 text-[17px] font-bold text-[#2f4327]">{pick(lang, area.title)}</h3>
                                    <p className="m-0 text-pretty text-sm text-[#5f6857]">{pick(lang, area.body)}</p>
                                </div>
                            </Reveal>
                        ))}
                    </div>
                </div>
            </section>

            <section className="mx-auto max-w-[1000px] px-6 pb-10 pt-[84px]">
                <Reveal className="mb-[52px] text-center">
                    <span className="text-[13px] font-semibold uppercase tracking-[2px] text-[#819562]">{pick(lang, page.how?.eyebrow)}</span>
                    <h2 className="mb-0 mt-3 text-[clamp(1.75rem,3.4vw,2.5rem)] font-bold text-[#2f4327]">{pick(lang, page.how?.title)}</h2>
                </Reveal>
                <div className="grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] gap-6">
                    {(page.how?.steps ?? []).map((step, index) => (
                        <Reveal key={index} delay={index * 0.08} className="text-center">
                            <div className="mx-auto mb-[18px] flex h-16 w-16 items-center justify-center rounded-full border-2 border-[#406139]/16 bg-[#fffdf8] text-2xl font-bold text-[#406139] shadow-[0_8px_22px_rgba(47,67,39,.08)]">
                                {index + 1}
                            </div>
                            <h3 className="mb-2 text-[17px] font-bold text-[#2f4327]">{pick(lang, step.title)}</h3>
                            <p className="m-0 text-pretty text-[14.5px] text-[#5f6857]">{pick(lang, step.body)}</p>
                        </Reveal>
                    ))}
                </div>
            </section>

            <section className="mx-auto max-w-[1000px] px-6 pb-[60px] pt-10">
                <Reveal className="relative overflow-hidden rounded-[28px] bg-gradient-to-br from-[#96A791] to-[#406139] px-11 py-12 text-[#F7F6F0]">
                    <div className="pointer-events-none absolute -top-10 end-[6%] h-40 w-40 rounded-full bg-[rgba(242,241,234,.1)]" />
                    <div className="relative mb-2 text-[64px] font-bold leading-[0.6] text-[rgba(247,246,240,.4)]">&ldquo;</div>
                    <p className="relative mb-6 max-w-[680px] text-pretty text-[clamp(1.125rem,2.2vw,1.5rem)] font-semibold leading-snug">
                        {pick(lang, page.testimonial?.quote)}
                    </p>
                    <div className="relative flex items-center gap-3.5">
                        <span className="flex h-[52px] w-[52px] shrink-0 items-center justify-center rounded-full bg-[rgba(247,246,240,.2)] text-lg font-bold">
                            {pick(lang, page.testimonial?.initial)}
                        </span>
                        <div>
                            <div className="text-base font-bold">{pick(lang, page.testimonial?.name)}</div>
                            <div className="text-[13.5px] opacity-90">{pick(lang, page.testimonial?.role)}</div>
                        </div>
                    </div>
                </Reveal>
            </section>

            <section id="apply" className="mx-auto max-w-[820px] scroll-mt-20 px-6 pb-[88px] pt-10">
                <VolunteerApplicationForm page={page} lang={lang} routes={routes} />
            </section>
        </>
    );
}
