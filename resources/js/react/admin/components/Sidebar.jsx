import { useRef } from 'react';
import { useLanguage } from '../LanguageContext';
import { Icon } from './icons';

function LogoutButton() {
    const { t, routes, csrfToken } = useLanguage();
    const formRef = useRef(null);

    return (
        <>
            <form ref={formRef} method="POST" action={routes.logout} className="hidden">
                <input type="hidden" name="_token" value={csrfToken} />
            </form>
            <button
                type="button"
                aria-label={t.logout}
                onClick={() => formRef.current?.submit()}
                className="flex cursor-pointer border-none bg-transparent text-[#8fa080] transition hover:text-[#cdd8bf]"
            >
                <Icon name="logout" className="h-[18px] w-[18px]" />
            </button>
        </>
    );
}

export function Sidebar({ open, onClose }) {
    const { isAr, nav, assets, user, t } = useLanguage();

    return (
        <>
            <button
                type="button"
                aria-label={t.closeMenu}
                className={`gh-admin-overlay fixed inset-0 z-40 bg-black/40 lg:hidden ${open ? 'open' : ''}`}
                onClick={onClose}
            />
            <aside
                className={`gh-admin-sidebar fixed inset-y-0 start-0 z-50 flex h-screen w-[260px] shrink-0 flex-col bg-[#243619] px-[18px] py-6 text-[#cdd8bf] lg:sticky lg:z-auto lg:translate-x-0 ${open ? 'open' : ''}`}
            >
                <div className="mb-[18px] flex items-center gap-[11px] border-b border-[rgba(150,167,145,0.18)] px-2 pb-6">
                    <img src={assets.logo} alt="GHOSN" className="h-[38px] w-auto brightness-0 invert" />
                    <div className="flex flex-col leading-none">
                        <span className="text-[17px] font-bold tracking-[3px] text-[#F2F1EA]">GHOSN</span>
                        <span className="mt-[3px] text-[8px] font-semibold tracking-[3px] text-[#96A791]">{t.panel}</span>
                    </div>
                </div>

                <nav className="gh-admin-sidebar-nav flex flex-1 flex-col gap-1 overflow-y-auto" aria-label={t.adminNav}>
                    {nav.map((item) => (
                        <a
                            key={item.key}
                            href={item.url}
                            className={`flex items-center gap-[13px] rounded-xl px-[14px] py-3 text-[14.5px] font-semibold no-underline transition ${item.active ? 'active' : 'text-[#cdd8bf]'}`}
                        >
                            <span className="flex shrink-0">
                                <Icon name={item.icon} />
                            </span>
                            <span className="flex-1">{isAr ? item.label_ar : item.label_en}</span>
                        </a>
                    ))}
                </nav>

                <div className="mt-3 flex items-center gap-[11px] border-t border-[rgba(150,167,145,0.18)] pt-4">
                    <span className="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-[#819562] to-[#406139] text-[15px] font-bold text-[#F7F6F0]">
                        {user.initial}
                    </span>
                    <div className="min-w-0 flex-1">
                        <div className="truncate text-[13.5px] font-bold text-[#F2F1EA]">{user.name}</div>
                        <div className="text-[11.5px] text-[#8fa080]">{isAr ? (user.role_ar || t.role) : (user.role_en || t.role)}</div>
                    </div>
                    <LogoutButton />
                </div>
            </aside>
        </>
    );
}
