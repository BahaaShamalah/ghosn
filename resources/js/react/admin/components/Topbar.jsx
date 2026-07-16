import { Icon } from './icons';
import { useLanguage } from '../LanguageContext';
import { navLabel } from '../utils/navLabel';

export function Topbar({ onMenuOpen }) {
    const { lang, isAr, user, t, localeBase, nav } = useLanguage();

    const switchLocale = (next) => {
        window.location.href = `${localeBase}/${next}`;
    };

    return (
        <header className="sticky top-0 z-40 flex flex-wrap items-center gap-5 border-b border-[rgba(64,97,57,0.12)] bg-[rgba(237,238,228,0.9)] px-5 py-4 backdrop-blur-md md:px-[30px]">
            <button
                type="button"
                className="flex h-10 w-10 items-center justify-center rounded-xl border border-[rgba(64,97,57,0.18)] bg-[#fffdf8] text-[#406139] lg:hidden"
                aria-label={t.openMenu}
                onClick={onMenuOpen}
            >
                <Icon name="menu" />
            </button>

            <div className="min-w-0 flex-1">
                <h1 className="m-0 text-[22px] font-bold text-[#2f4327]">
                    {navLabel(nav, 'dashboard', isAr) || t.dashboardTitle}
                </h1>
                <div className="mt-0.5 text-[13px] text-[#8a9280]">{t.welcome(user.name)}</div>
            </div>

            <div className="flex flex-wrap items-center gap-[14px]">
                <div className="relative hidden items-center sm:flex">
                    <span className="pointer-events-none absolute inset-inline-start-3 flex text-[#8a9280]">
                        <Icon name="search" className="h-[17px] w-[17px]" />
                    </span>
                    <input
                        disabled
                        placeholder={t.search}
                        className="w-[210px] rounded-[11px] border border-[rgba(64,97,57,0.18)] bg-[#fffdf8] py-2.5 ps-9 pe-3.5 text-[13.5px] text-[#3a4234] outline-none"
                    />
                </div>

                <div className="flex overflow-hidden rounded-full border border-[rgba(64,97,57,0.24)]">
                    <button
                        type="button"
                        onClick={() => switchLocale('en')}
                        className={`cursor-pointer border-none px-[13px] py-[7px] text-xs font-semibold transition ${lang === 'en' ? 'bg-[#406139] text-[#F2F1EA]' : 'bg-transparent text-[#406139]'}`}
                    >
                        EN
                    </button>
                    <button
                        type="button"
                        onClick={() => switchLocale('ar')}
                        className={`cursor-pointer border-none px-[13px] py-[7px] text-xs font-semibold transition ${lang === 'ar' ? 'bg-[#406139] text-[#F2F1EA]' : 'bg-transparent text-[#406139]'}`}
                    >
                        AR
                    </button>
                </div>

                <button
                    type="button"
                    aria-label={t.notifications}
                    className="relative flex h-[42px] w-[42px] cursor-pointer items-center justify-center rounded-xl border border-[rgba(64,97,57,0.18)] bg-[#fffdf8] text-[#406139]"
                >
                    <Icon name="bell" />
                </button>
            </div>
        </header>
    );
}
