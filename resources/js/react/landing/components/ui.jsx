import { useLanguage } from '../LanguageContext';

export function Reveal({ children, className = '', delay = 0, as: Tag = 'div' }) {
    const style = delay ? { transitionDelay: `${delay}s` } : undefined;

    return (
        <Tag
            className={`gh-reveal ${className}`}
            style={style}
            data-reveal=""
        >
            {children}
        </Tag>
    );
}

export function LangText({ en, ar }) {
    const { lang } = useLanguage();

    return lang === 'ar' ? (ar ?? en) : (en ?? ar);
}
