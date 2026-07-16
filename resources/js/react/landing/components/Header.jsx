import { useState } from 'react';
import { useLanguage } from '../LanguageContext';
import { getBootstrap } from '../content';

export function Header() {
    const { t, lang, routes, assets, navLinks, donateLabel } = useLanguage();
    const bootstrap = getBootstrap();
    const [menuOpen, setMenuOpen] = useState(false);

    const links = (navLinks ?? bootstrap.navLinks ?? []).map((link) => ({
        label: link.label?.[lang] ?? link.label?.en ?? '',
        href: link.href,
    }));

    const donateText = donateLabel ?? bootstrap.donateLabel?.[lang] ?? t.nav.donate;
    return (
        <header className="gh-header sticky top-0 z-[60] border-b border-[#406139]/12 bg-[#F2F1EA]/82 backdrop-blur-[14px]">
            <div className="mx-auto flex max-w-[1220px] flex-wrap items-center justify-between gap-5 px-6 py-3.5">
                <a href={routes.home ?? '/'} className="flex items-center gap-3 no-underline">
                    <img src={assets.logo} alt="GHOSN" className="h-11 w-auto" />
                    <span className="hidden sm:flex flex-col leading-none">
                        <span className="text-[19px] font-bold tracking-[3px] text-[#406139]">GHOSN</span>
                        <span className="mt-0.5 text-[9px] font-semibold tracking-[4px] text-[#819562]">RELIEF TEAM</span>
                    </span>
                </a>

                <nav className="gh-desktop-nav hidden items-center gap-6 md:flex">
                    {links.map((link) => (
                        <a key={link.href} href={link.href} className="gh-nav-link text-[14.5px] font-medium text-[#4a5340] no-underline">
                            {link.label}
                        </a>
                    ))}
                </nav>

                <div className="flex items-center gap-3.5">
                    <div className="flex overflow-hidden rounded-full border border-[#406139]/28">
                        <a
                            href={routes.localeEn ?? '/locale/en'}
                            className={`cursor-pointer border-none px-3.5 py-1.5 text-xs font-semibold ${lang === 'en' ? 'bg-[#406139] text-[#F2F1EA]' : 'bg-transparent text-[#406139]'}`}
                        >
                            EN
                        </a>
                        <a
                            href={routes.localeAr ?? '/locale/ar'}
                            className={`cursor-pointer border-none px-3.5 py-1.5 text-xs font-semibold ${lang === 'ar' ? 'bg-[#406139] text-[#F2F1EA]' : 'bg-transparent text-[#406139]'}`}
                        >
                            AR
                        </a>
                    </div>
                    <a href={routes.donate ?? '/donate'} className="gh-desktop-donate hidden rounded-full bg-[#406139] px-5 py-2.5 text-sm font-semibold text-[#F2F1EA] no-underline shadow-lg shadow-[#406139]/28 sm:inline-flex">
                        {donateText}
                    </a>
                    <button
                        type="button"
                        className="gh-hamburger flex h-11 w-11 shrink-0 cursor-pointer items-center justify-center rounded-xl border border-[#406139]/24 bg-[#fffdf8] text-[#406139] md:hidden"
                        aria-label="Menu"
                        onClick={() => setMenuOpen((open) => ! open)}
                    >
                        {menuOpen ? (
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2"><path d="M18 6 6 18M6 6l12 12" /></svg>
                        ) : (
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2"><path d="M3 6h18M3 12h18M3 18h18" /></svg>
                        )}
                    </button>
                </div>
            </div>

            <div className={`gh-mobile-menu overflow-hidden border-t border-transparent transition-all md:hidden ${menuOpen ? 'gh-open max-h-[640px] border-[#406139]/12' : 'max-h-0'}`}>
                <div className="flex flex-col gap-0.5 px-6 pb-5 pt-2">
                    {links.map((link) => (
                        <a
                            key={link.href}
                            href={link.href}
                            className="border-b border-[#406139]/8 px-2 py-3.5 text-base font-semibold text-[#3a4234] no-underline"
                            onClick={() => setMenuOpen(false)}
                        >
                            {link.label}
                        </a>
                    ))}
                    <a href={routes.donate ?? '/donate'} className="mt-3.5 rounded-xl bg-[#406139] px-4 py-3.5 text-center text-[15px] font-bold text-[#F2F1EA] no-underline shadow-lg">
                        {donateText}
                    </a>
                </div>
            </div>
        </header>
    );
}
