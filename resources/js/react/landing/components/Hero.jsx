import { useLanguage } from '../LanguageContext';
import { Reveal } from './ui';

export function Hero() {
    const { t, assets, heroBackground, heroBackgroundAlt } = useLanguage();
    const hasBackground = Boolean(heroBackground);

    return (
        <section id="home" className="gh-hero relative flex min-h-[92vh] items-center overflow-hidden">
            {hasBackground ? (
                <>
                    <img
                        src={heroBackground}
                        alt={heroBackgroundAlt || ''}
                        className="pointer-events-none absolute inset-0 h-full w-full object-cover"
                    />
                    <div className="pointer-events-none absolute inset-0 bg-gradient-to-br from-[#243619]/88 via-[#406139]/72 to-[#2f4327]/78" />
                    <div className="pointer-events-none absolute inset-0 bg-gradient-to-r from-[#243619]/75 via-[#406139]/50 to-[#406139]/30" />
                </>
            ) : (
                <>
                    <div className="pointer-events-none absolute inset-0 bg-gradient-to-br from-[#243619] via-[#406139] to-[#2f4327] bg-[length:240%_240%] gh-animate-gradient" />
                    <div className="pointer-events-none absolute inset-0 bg-gradient-to-r from-[#243619]/82 via-[#406139]/55 to-[#406139]/34" />
                </>
            )}
            <div className="pointer-events-none absolute -start-12 -top-16 h-[340px] w-[340px] rounded-full bg-[radial-gradient(circle,rgba(188,202,167,.30),transparent_70%)] gh-animate-drift" />
            <div className="pointer-events-none absolute bottom-[-90px] end-[6%] h-[300px] w-[300px] rounded-full bg-[radial-gradient(circle,rgba(129,149,98,.34),transparent_70%)] gh-animate-float" />

            <div className="relative z-[2] mx-auto w-full max-w-[1220px] px-6 pb-20 pt-32 md:pb-24 md:pt-36">
                <div className="mx-auto max-w-[720px] text-center">
                    <Reveal className="mb-10 flex items-center justify-center gap-4">
                        <img src={assets.logo} alt="GHOSN" className="h-16 w-auto brightness-0 invert drop-shadow-lg" />
                        <span className="flex flex-col border-s border-[#F7F6F0]/35 ps-4 leading-none">
                            <span className="text-[25px] font-bold tracking-[5px] text-[#F7F6F0]">GHOSN</span>
                            <span className="mt-1.5 text-[10.5px] font-semibold tracking-[6px] text-[#DCE4CC]">RELIEF TEAM</span>
                        </span>
                    </Reveal>

                    <Reveal delay={0.12} className="mb-6 inline-flex items-center gap-2 rounded-full bg-[#F2F1EA]/16 px-4 py-2 text-xs font-semibold tracking-wide text-[#F2F1EA] backdrop-blur-sm">
                        <span className="inline-block h-2 w-2 rounded-full bg-[#BCCAA7]" />
                        {t.hero.badge}
                    </Reveal>

                    <Reveal delay={0.24}>
                        <h1 className="mb-5 text-balance text-[clamp(2.4rem,5.6vw,4.1rem)] font-bold leading-[1.1] tracking-tight text-[#F7F6F0] text-shadow-hero">
                            {t.hero.title}
                        </h1>
                    </Reveal>

                    <Reveal delay={0.38}>
                        <p className="mx-auto mb-9 max-w-xl text-pretty text-[clamp(1rem,1.7vw,1.25rem)] leading-relaxed text-[#E8ECDD]">
                            {t.hero.subtitle}
                        </p>
                    </Reveal>

                    <Reveal delay={0.52} className="flex flex-wrap justify-center gap-4">
                        <a href={t.hero.ctaPrimaryUrl} className="gh-btn-primary rounded-full bg-[#819562] px-8 py-3.5 text-[15px] font-semibold text-[#F7F6F0] no-underline shadow-xl">
                            {t.hero.ctaPrimary}
                        </a>
                        <a href={t.hero.ctaSecondaryUrl} className="gh-btn-outline rounded-full border-[1.5px] border-[#F7F6F0]/80 px-8 py-3.5 text-[15px] font-semibold text-[#F7F6F0] no-underline">
                            {t.hero.ctaSecondary}
                        </a>
                    </Reveal>
                </div>
            </div>

            <a href="#about" aria-label="Scroll down" className="gh-scroll-cue absolute bottom-6 left-1/2 z-[2] -translate-x-1/2 text-[#F7F6F0]/90 no-underline">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="m6 9 6 6 6-6" /></svg>
            </a>
        </section>
    );
}
