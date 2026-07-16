import { useEffect, useState } from 'react';
import { LanguageProvider, useLanguage } from './LanguageContext';
import { Dashboard } from './components/Dashboard';
import { Sidebar } from './components/Sidebar';
import { Topbar } from './components/Topbar';

function AdminShell() {
    const { lang, isAr } = useLanguage();
    const [sidebarOpen, setSidebarOpen] = useState(false);

    useEffect(() => {
        document.documentElement.lang = lang;
        document.documentElement.dir = isAr ? 'rtl' : 'ltr';
    }, [isAr, lang]);

    return (
        <div id="ghosnAdminRoot" className="gh-admin flex min-h-screen overflow-x-hidden bg-[#EDEEE4] text-[#3a4234] leading-normal">
            <Sidebar open={sidebarOpen} onClose={() => setSidebarOpen(false)} />
            <div className="flex min-w-0 flex-1 flex-col lg:ms-0">
                <Topbar onMenuOpen={() => setSidebarOpen(true)} />
                <main className="flex-1">
                    <Dashboard />
                </main>
            </div>
        </div>
    );
}

export default function App() {
    return (
        <LanguageProvider>
            <AdminShell />
        </LanguageProvider>
    );
}
