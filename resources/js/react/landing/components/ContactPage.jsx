import { useMemo, useState } from 'react';
import { getBootstrap } from '../content';
import { useLanguage } from '../LanguageContext';
import { Reveal } from './ui';

const EMAIL_RE = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
const BLANK = { name: '', email: '', subject: '', message: '' };
const INPUT_CLASS = 'gh-field-input';

const FORM_COPY = {
    en: {
        title: 'Send us a message',
        subtitle: 'We usually respond within one business day.',
        name: 'Full name',
        namePh: 'Your name',
        email: 'Email',
        emailPh: 'you@email.com',
        subject: 'Subject',
        subjectPh: 'Choose a topic',
        subjects: ['General inquiry', 'Partnership', 'Volunteering', 'Media & press', 'Donation support'],
        message: 'Message',
        messagePh: 'How can we help you?',
        submit: 'Send message',
        sending: 'Sending…',
        success: 'Thank you! Your message has been sent — we will be in touch soon.',
        error: 'Something went wrong. Please try again.',
        err: { required: 'This field is required', email: 'Enter a valid email address' },
        linksTitle: 'Explore the site',
        followTitle: 'Follow us',
        ctaTitle: 'Turn your support into lasting impact',
        ctaSubtitle: 'While you are here — a single gift plants hope that keeps growing for families in need.',
        ctaPrimary: 'Donate Now',
        ctaSecondary: 'Join Our Team',
        phone: 'Phone',
        mail: 'Email',
        office: 'Office',
    },
    ar: {
        title: 'أرسل لنا رسالة',
        subtitle: 'نردّ عادةً خلال يوم عملٍ واحد.',
        name: 'الاسم الكامل',
        namePh: 'اسمك',
        email: 'البريد الإلكتروني',
        emailPh: 'you@email.com',
        subject: 'الموضوع',
        subjectPh: 'اختر موضوعًا',
        subjects: ['استفسار عام', 'شراكة', 'تطوّع', 'إعلام وصحافة', 'دعم التبرّعات'],
        message: 'الرسالة',
        messagePh: 'كيف يمكننا مساعدتك؟',
        submit: 'إرسال الرسالة',
        sending: 'جارٍ الإرسال…',
        success: 'شكرًا لك! تم إرسال رسالتك — سنتواصل معك قريبًا.',
        error: 'حدث خطأ ما. حاول مرة أخرى.',
        err: { required: 'هذا الحقل مطلوب', email: 'أدخل بريدًا إلكترونيًا صحيحًا' },
        linksTitle: 'استكشف الموقع',
        followTitle: 'تابعنا',
        ctaTitle: 'حوّل دعمك إلى أثرٍ دائم',
        ctaSubtitle: 'وما دمت هنا — عطاءٌ واحد يزرع أملًا يستمرّ في النمو لأسرٍ محتاجة.',
        ctaPrimary: 'تبرّع الآن',
        ctaSecondary: 'انضم إلى فريقنا',
        phone: 'الهاتف',
        mail: 'البريد الإلكتروني',
        office: 'المكتب',
    },
};

function pick(lang, pair, fallback = '') {
    return pair?.[lang] || fallback;
}

function ContactIcon({ type }) {
    if (type === 'phone') {
        return (
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#406139" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.9.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z" />
            </svg>
        );
    }

    if (type === 'mail') {
        return (
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#406139" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <rect x="2" y="4" width="20" height="16" rx="2" />
                <path d="m22 7-10 6L2 7" />
            </svg>
        );
    }

    return (
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#406139" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
            <circle cx="12" cy="10" r="3" />
        </svg>
    );
}

export function ContactPageContent() {
    const { lang, routes, contact, socialLinks, navLinks, donateLabel, contactPage } = useLanguage();
    const bootstrap = getBootstrap();
    const fallback = FORM_COPY[lang] ?? FORM_COPY.en;
    const [form, setForm] = useState({ ...BLANK });
    const [errors, setErrors] = useState({});
    const [status, setStatus] = useState('idle');

    const sections = contactPage?.sections ?? {
        hero: true,
        details: true,
        form: true,
        cta: true,
    };

    const copy = useMemo(() => {
        const pageForm = contactPage?.form;
        const pageLinks = contactPage?.links;
        const pageCta = contactPage?.cta;

        return {
            ...fallback,
            title: pick(lang, pageForm?.title, fallback.title),
            subtitle: pick(lang, pageForm?.subtitle, fallback.subtitle),
            name: pick(lang, pageForm?.name, fallback.name),
            namePh: pick(lang, pageForm?.namePh, fallback.namePh),
            email: pick(lang, pageForm?.email, fallback.email),
            emailPh: pick(lang, pageForm?.emailPh, fallback.emailPh),
            subject: pick(lang, pageForm?.subject, fallback.subject),
            subjectPh: pick(lang, pageForm?.subjectPh, fallback.subjectPh),
            subjects: pageForm?.subjects?.[lang]?.length ? pageForm.subjects[lang] : fallback.subjects,
            message: pick(lang, pageForm?.message, fallback.message),
            messagePh: pick(lang, pageForm?.messagePh, fallback.messagePh),
            submit: pick(lang, pageForm?.submit, fallback.submit),
            sending: pick(lang, pageForm?.sending, fallback.sending),
            success: pick(lang, pageForm?.success, fallback.success),
            error: pick(lang, pageForm?.error, fallback.error),
            linksTitle: pick(lang, pageLinks?.title, fallback.linksTitle),
            followTitle: pick(lang, pageLinks?.follow, fallback.followTitle),
            ctaTitle: pick(lang, pageCta?.title, fallback.ctaTitle),
            ctaSubtitle: pick(lang, pageCta?.subtitle, fallback.ctaSubtitle),
            ctaPrimary: pick(lang, pageCta?.primary, fallback.ctaPrimary),
            ctaSecondary: pick(lang, pageCta?.secondary, fallback.ctaSecondary),
            ctaPrimaryUrl: pageCta?.primaryUrl || routes.donate || '/donate',
            ctaSecondaryUrl: pageCta?.secondaryUrl || routes.volunteer || '/volunteer',
        };
    }, [contactPage, fallback, lang, routes.donate, routes.volunteer]);

    const hero = useMemo(() => ({
        eyebrow: pick(lang, contactPage?.hero?.eyebrow, copy.linksTitle),
        title: pick(lang, contactPage?.hero?.title, ''),
        subtitle: pick(lang, contactPage?.hero?.subtitle, ''),
    }), [contactPage, copy.linksTitle, lang]);

    const info = useMemo(() => ({
        eyebrow: pick(lang, contactPage?.info?.eyebrow, ''),
        title: pick(lang, contactPage?.info?.title, ''),
        body: pick(lang, contactPage?.info?.body, ''),
    }), [contactPage, lang]);

    const office = pick(lang, contactPage?.office, pick(lang, contact?.address, ''));

    const contactItems = [
        contact.phone ? { key: 'phone', label: copy.phone, value: contact.phone, href: `tel:${contact.phone.replace(/\s/g, '')}` } : null,
        contact.email ? { key: 'mail', label: copy.mail, value: contact.email, href: `mailto:${contact.email}` } : null,
        office ? { key: 'pin', label: copy.office, value: office, href: routes.contact ?? '/contact' } : null,
    ].filter(Boolean);

    const siteLinks = (navLinks ?? bootstrap.navLinks ?? []).map((link) => ({
        label: link.label?.[lang] ?? link.label?.en ?? '',
        href: link.href,
    }));

    const onField = (event) => {
        const { name, value } = event.target;
        setForm((current) => ({ ...current, [name]: value }));
        setErrors((current) => ({ ...current, [name]: '' }));
    };

    const onSubmit = async (event) => {
        event.preventDefault();
        const nextErrors = {};

        if (!form.name.trim()) nextErrors.name = fallback.err.required;
        if (!form.email.trim()) nextErrors.email = fallback.err.required;
        else if (!EMAIL_RE.test(form.email)) nextErrors.email = fallback.err.email;
        if (!form.message.trim()) nextErrors.message = fallback.err.required;

        if (Object.keys(nextErrors).length) {
            setErrors(nextErrors);
            setStatus('idle');
            return;
        }

        setErrors({});
        setStatus('loading');

        try {
            const { getRecaptchaToken } = await import('../recaptcha');
            const token = await getRecaptchaToken('contact');

            const response = await fetch(routes.contactMessages ?? '/contact-messages', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': bootstrap.csrfToken ?? '',
                },
                body: JSON.stringify({
                    ...form,
                    'g-recaptcha-response': token || undefined,
                }),
            });

            if (!response.ok) {
                throw new Error('request failed');
            }

            setStatus('success');
            setForm({ ...BLANK });
        } catch {
            setStatus('error');
        }
    };

    return (
        <>
            {sections.hero !== false && (
                <section className="relative overflow-hidden bg-gradient-to-br from-[#243619] via-[#3a5330] to-[#2f4327] bg-[length:240%_240%]">
                    <div className="relative z-[2] mx-auto max-w-[820px] px-6 py-16 text-center md:py-[72px]">
                        <Reveal>
                            <div className="mb-5 inline-flex items-center gap-2 rounded-full bg-[#F2F1EA]/16 px-4 py-2 text-xs font-semibold tracking-wide text-[#F2F1EA] backdrop-blur-sm">
                                <span className="h-2 w-2 rounded-full bg-[#BCCAA7]" />
                                {hero.eyebrow}
                            </div>
                        </Reveal>
                        <Reveal delay={0.08}>
                            <h1 className="mx-auto mb-4 max-w-[640px] text-[clamp(2rem,4.6vw,3.25rem)] font-bold leading-tight text-[#F7F6F0] text-balance">
                                {hero.title}
                            </h1>
                        </Reveal>
                        <Reveal delay={0.16}>
                            <p className="mx-auto max-w-[520px] text-[clamp(1rem,1.7vw,1.2rem)] text-[#E8ECDD] text-pretty">
                                {hero.subtitle}
                            </p>
                        </Reveal>
                    </div>
                </section>
            )}

            {(sections.details !== false || sections.form !== false) && (
                <section className="mx-auto grid max-w-[1120px] items-start gap-12 px-6 py-16 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.15fr)] lg:gap-14">
                    {sections.details !== false && (
                        <Reveal className="min-w-0">
                            <span className="text-xs font-semibold uppercase tracking-[0.2em] text-[#819562]">{info.eyebrow}</span>
                            <h2 className="mt-3 text-[clamp(1.6rem,3.2vw,2.25rem)] font-bold leading-tight text-[#2f4327]">{info.title}</h2>
                            <p className="mt-3 text-base text-[#586150] text-pretty">{info.body}</p>

                            <div className="mt-7 flex flex-col gap-3.5">
                                {contactItems.map((item) => (
                                    <a
                                        key={item.key}
                                        href={item.href}
                                        className="flex items-center gap-4 rounded-2xl border border-[#406139]/10 bg-[#fffdf8] p-4 shadow-[0_4px_16px_rgba(47,67,39,0.05)] transition hover:-translate-y-0.5 hover:border-[#406139]/25 hover:shadow-[0_14px_30px_rgba(47,67,39,0.12)] no-underline"
                                    >
                                        <span className="flex h-12 w-12 shrink-0 items-center justify-center rounded-[14px] bg-[#819562]/16">
                                            <ContactIcon type={item.key} />
                                        </span>
                                        <span className="min-w-0">
                                            <span className="block text-xs font-semibold uppercase tracking-wide text-[#8a9280]">{item.label}</span>
                                            <span className="block text-[15.5px] font-semibold text-[#3a4234]" dir={item.key === 'mail' || item.key === 'phone' ? 'ltr' : undefined}>{item.value}</span>
                                        </span>
                                    </a>
                                ))}
                            </div>

                            <div className="mt-8 rounded-[20px] border border-[#406139]/12 bg-[#fffdf8] p-6">
                                <div className="mb-4 text-xs font-semibold uppercase tracking-wide text-[#8a9280]">{copy.linksTitle}</div>
                                <div className="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                    {siteLinks.map((link) => (
                                        <a key={link.href} href={link.href} className="flex items-center gap-2 text-sm font-medium text-[#4a5340] no-underline transition hover:text-[#406139]">
                                            <span className="text-[#819562]">→</span>
                                            {link.label}
                                        </a>
                                    ))}
                                </div>
                                {(socialLinks?.length ?? 0) > 0 && (
                                    <div className="mt-5 border-t border-[#406139]/12 pt-5">
                                        <div className="mb-3 text-xs font-semibold uppercase tracking-wide text-[#8a9280]">{copy.followTitle}</div>
                                        <div className="flex flex-wrap gap-2.5">
                                            {(socialLinks ?? []).map((link) => (
                                                <a
                                                    key={link.url}
                                                    href={link.url}
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    className="inline-flex h-11 w-11 items-center justify-center rounded-full bg-[#819562]/16 text-[17px] text-[#406139] no-underline transition hover:bg-[#406139] hover:text-[#F2F1EA]"
                                                    aria-label={link.label ?? link.platform}
                                                    title={link.label ?? link.platform}
                                                >
                                                    <i className={link.iconClass || 'fa-solid fa-link'} aria-hidden="true" />
                                                </a>
                                            ))}
                                        </div>
                                    </div>
                                )}
                            </div>
                        </Reveal>
                    )}

                    {sections.form !== false && (
                        <Reveal delay={0.12} className="min-w-0 lg:sticky lg:top-24">
                            <form onSubmit={onSubmit} noValidate className="w-full rounded-3xl border border-[#406139]/10 bg-[#fffdf8] p-8 shadow-[0_20px_50px_rgba(47,67,39,0.12)]">
                                <h2 className="text-[22px] font-bold text-[#2f4327]">{copy.title}</h2>
                                <p className="mt-1.5 text-sm text-[#8a9280]">{copy.subtitle}</p>

                                <div className="mt-6 flex flex-col gap-4">
                                    <div className="grid gap-4 sm:grid-cols-2">
                                        <label className="flex flex-col gap-1.5 text-sm font-semibold text-[#4a5340]">
                                            {copy.name}
                                            <input name="name" value={form.name} onChange={onField} placeholder={copy.namePh} className={INPUT_CLASS} />
                                            {errors.name && <span className="text-xs font-medium text-[#a24a37]">{errors.name}</span>}
                                        </label>
                                        <label className="flex flex-col gap-1.5 text-sm font-semibold text-[#4a5340]">
                                            {copy.email}
                                            <input type="email" name="email" value={form.email} onChange={onField} placeholder={copy.emailPh} dir="ltr" className={INPUT_CLASS} />
                                            {errors.email && <span className="text-xs font-medium text-[#a24a37]">{errors.email}</span>}
                                        </label>
                                    </div>

                                    <label className="flex flex-col gap-1.5 text-sm font-semibold text-[#4a5340]">
                                        {copy.subject}
                                        <select name="subject" value={form.subject} onChange={onField} className={`${INPUT_CLASS} cursor-pointer`}>
                                            <option value="">{copy.subjectPh}</option>
                                            {copy.subjects.map((subject) => (
                                                <option key={subject} value={subject}>{subject}</option>
                                            ))}
                                        </select>
                                    </label>

                                    <label className="flex flex-col gap-1.5 text-sm font-semibold text-[#4a5340]">
                                        {copy.message}
                                        <textarea name="message" rows={5} value={form.message} onChange={onField} placeholder={copy.messagePh} className={INPUT_CLASS} />
                                        {errors.message && <span className="text-xs font-medium text-[#a24a37]">{errors.message}</span>}
                                    </label>

                                    {status === 'success' && (
                                        <div className="rounded-xl border border-[#819562] bg-[#819562]/18 px-4 py-3 text-center text-sm font-medium text-[#33502e]">
                                            {copy.success}
                                        </div>
                                    )}
                                    {status === 'error' && (
                                        <div className="rounded-xl border border-[#a24a37] bg-[#a24a37]/12 px-4 py-3 text-center text-sm font-medium text-[#8a3d2d]">
                                            {copy.error}
                                        </div>
                                    )}

                                    <button type="submit" disabled={status === 'loading'} className="rounded-xl border-none bg-[#406139] px-4 py-4 text-[15px] font-semibold text-[#F2F1EA] transition hover:bg-[#33502e] disabled:opacity-70">
                                        {status === 'loading' ? copy.sending : copy.submit}
                                    </button>
                                </div>
                            </form>
                        </Reveal>
                    )}
                </section>
            )}

            {sections.cta !== false && (
                <section className="mx-auto max-w-[1120px] px-6 pb-20">
                    <Reveal>
                        <div className="relative overflow-hidden rounded-[28px] bg-gradient-to-br from-[#3a5330] via-[#406139] to-[#2f4327] p-10 md:p-14">
                            <div className="relative flex flex-wrap items-center justify-between gap-8">
                                <div className="min-w-[280px] flex-1">
                                    <h2 className="text-[clamp(1.5rem,3vw,2.125rem)] font-bold leading-tight text-[#F7F6F0] text-balance">{copy.ctaTitle}</h2>
                                    <p className="mt-2 max-w-md text-base text-[#E8ECDD] text-pretty">{copy.ctaSubtitle}</p>
                                </div>
                                <div className="flex flex-wrap gap-3.5">
                                    <a href={copy.ctaPrimaryUrl} className="rounded-full bg-[#F7F6F0] px-8 py-4 text-[15px] font-bold text-[#406139] no-underline transition hover:bg-white">
                                        {donateLabel ?? copy.ctaPrimary}
                                    </a>
                                    <a href={copy.ctaSecondaryUrl} className="rounded-full border-[1.5px] border-[#F7F6F0]/80 px-8 py-4 text-[15px] font-semibold text-[#F7F6F0] no-underline transition hover:bg-[#F7F6F0] hover:text-[#406139]">
                                        {copy.ctaSecondary}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </Reveal>
                </section>
            )}
        </>
    );
}
