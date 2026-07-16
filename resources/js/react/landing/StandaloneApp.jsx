import { useEffect } from 'react';
import { LanguageProvider, useLanguage } from './LanguageContext';
import { Header } from './components/Header';
import { FooterSection, useGlobalReveal } from './components/Sections';
import { AboutPageContent } from './components/AboutPage';
import { ContactPageContent } from './components/ContactPage';
import { LegalPageContent } from './components/LegalPage';
import { TeamPageContent } from './components/TeamPage';
import { VolunteerPageContent } from './components/VolunteerPage';
import { getBootstrap } from './content';

function VolunteerPage() {
    const { lang, isAr } = useLanguage();
    useGlobalReveal();

    useEffect(() => {
        document.documentElement.lang = lang;
        document.documentElement.dir = isAr ? 'rtl' : 'ltr';
    }, [isAr, lang]);

    return (
        <div className="gh-landing min-h-screen overflow-x-hidden bg-[#F2F1EA] text-[#3a4234] leading-relaxed">
            <Header />
            <main>
                <VolunteerPageContent />
            </main>
            <FooterSection />
        </div>
    );
}

function AboutPage() {
    const { lang, isAr } = useLanguage();
    useGlobalReveal();

    useEffect(() => {
        document.documentElement.lang = lang;
        document.documentElement.dir = isAr ? 'rtl' : 'ltr';
    }, [isAr, lang]);

    return (
        <div className="gh-landing min-h-screen overflow-x-hidden bg-[#F2F1EA] text-[#3a4234] leading-relaxed">
            <Header />
            <main>
                <AboutPageContent />
            </main>
            <FooterSection />
        </div>
    );
}

function ContactPage() {
    const { lang, isAr } = useLanguage();
    useGlobalReveal();

    useEffect(() => {
        document.documentElement.lang = lang;
        document.documentElement.dir = isAr ? 'rtl' : 'ltr';
    }, [isAr, lang]);

    return (
        <div className="gh-landing min-h-screen overflow-x-hidden bg-[#F2F1EA] text-[#3a4234] leading-relaxed">
            <Header />
            <main>
                <ContactPageContent />
            </main>
            <FooterSection />
        </div>
    );
}

function LegalPage() {
    const { lang, isAr } = useLanguage();
    useGlobalReveal();

    useEffect(() => {
        document.documentElement.lang = lang;
        document.documentElement.dir = isAr ? 'rtl' : 'ltr';
    }, [isAr, lang]);

    return (
        <div className="gh-landing min-h-screen overflow-x-hidden bg-[#F2F1EA] text-[#3a4234] leading-relaxed">
            <Header />
            <main>
                <LegalPageContent />
            </main>
            <FooterSection />
        </div>
    );
}

function TeamPage() {
    const { lang, isAr } = useLanguage();
    useGlobalReveal();

    useEffect(() => {
        document.documentElement.lang = lang;
        document.documentElement.dir = isAr ? 'rtl' : 'ltr';
    }, [isAr, lang]);

    return (
        <div className="gh-landing min-h-screen overflow-x-hidden bg-[#F2F1EA] text-[#3a4234] leading-relaxed">
            <Header />
            <main>
                <TeamPageContent />
            </main>
            <FooterSection />
        </div>
    );
}

function StandalonePage() {
    const pageType = getBootstrap().pageType;

    if (pageType === 'volunteer') {
        return <VolunteerPage />;
    }

    if (pageType === 'about' || pageType === 'who-we-are') {
        return <AboutPage />;
    }

    if (pageType === 'contact') {
        return <ContactPage />;
    }

    if (pageType === 'legal') {
        return <LegalPage />;
    }

    if (pageType === 'team') {
        return <TeamPage />;
    }

    return null;
}

export default function StandaloneApp() {
    return (
        <LanguageProvider>
            <StandalonePage />
        </LanguageProvider>
    );
}
