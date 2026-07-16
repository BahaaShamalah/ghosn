import { useEffect } from 'react';
import { LanguageProvider, useLanguage } from './LanguageContext';
import { Header } from './components/Header';
import { Hero } from './components/Hero';
import {
    AboutSection,
    BlogSection,
    CampaignsSection,
    FooterSection,
    HowWorksSection,
    ImpactSection,
    JoinTeamSection,
    NewsletterSection,
    TestimonialsSection,
    useGlobalReveal,
    WaysSection,
} from './components/Sections';

function LandingPage() {
    const { lang, isAr, homeSectionsVisible } = useLanguage();
    useGlobalReveal();

    useEffect(() => {
        document.documentElement.lang = lang;
        document.documentElement.dir = isAr ? 'rtl' : 'ltr';
    }, [isAr, lang]);

    const visible = (key) => homeSectionsVisible?.[key] !== false;

    return (
        <div id="ghosnRoot" className="gh-landing min-h-screen overflow-x-hidden bg-[#F2F1EA] text-[#3a4234] leading-relaxed">
            <Header />
            <main>
                {visible('hero') && <Hero />}
                {visible('about') && <AboutSection />}
                {visible('impact') && <ImpactSection />}
                {visible('how_works') && <HowWorksSection />}
                {visible('ways') && <WaysSection />}
                {visible('testimonials') && <TestimonialsSection />}
                {visible('campaigns') && <CampaignsSection />}
                {visible('latest_news') && <BlogSection />}
                {visible('join') && <JoinTeamSection />}
                <NewsletterSection />
            </main>
            <FooterSection />
        </div>
    );
}

export default function App() {
    return (
        <LanguageProvider>
            <LandingPage />
        </LanguageProvider>
    );
}
