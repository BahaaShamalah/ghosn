const base = {
    stroke: 'currentColor',
    fill: 'none',
    strokeWidth: 2,
    strokeLinecap: 'round',
    strokeLinejoin: 'round',
};

export function Icon({ name, className = 'h-[19px] w-[19px]', ...props }) {
    switch (name) {
        case 'dashboard':
            return (
                <svg className={className} viewBox="0 0 24 24" {...base} {...props}>
                    <rect x="3" y="3" width="7" height="9" rx="1" />
                    <rect x="14" y="3" width="7" height="5" rx="1" />
                    <rect x="14" y="12" width="7" height="9" rx="1" />
                    <rect x="3" y="16" width="7" height="5" rx="1" />
                </svg>
            );
        case 'settings':
            return (
                <svg className={className} viewBox="0 0 24 24" {...base} {...props}>
                    <circle cx="12" cy="12" r="3" />
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" />
                </svg>
            );
        case 'pages':
            return (
                <svg className={className} viewBox="0 0 24 24" {...base} {...props}>
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <path d="M14 2v6h6M16 13H8M16 17H8M10 9H8" />
                </svg>
            );
        case 'posts':
            return (
                <svg className={className} viewBox="0 0 24 24" {...base} {...props}>
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                </svg>
            );
        case 'content':
            return (
                <svg className={className} viewBox="0 0 24 24" {...base} {...props}>
                    <path d="M4 6h16M4 12h16M4 18h10" />
                </svg>
            );
        case 'categories':
            return (
                <svg className={className} viewBox="0 0 24 24" {...base} {...props}>
                    <path d="M4 4h7v7H4zM13 4h7v7h-7zM4 13h7v7H4zM13 13h7v7h-7z" />
                </svg>
            );
        case 'media':
            return (
                <svg className={className} viewBox="0 0 24 24" {...base} {...props}>
                    <rect x="3" y="3" width="18" height="18" rx="2" />
                    <circle cx="8.5" cy="8.5" r="1.5" />
                    <path d="m21 15-5-5L5 21" />
                </svg>
            );
        case 'campaigns':
            return (
                <svg className={className} viewBox="0 0 24 24" {...base} {...props}>
                    <path d="M3 11 21 4v16l-7-2.6" />
                    <path d="M3 11v5a1 1 0 0 0 1 1h3l1 4h3l-1-4" />
                </svg>
            );
        case 'donations':
            return (
                <svg className={className} viewBox="0 0 24 24" {...base} {...props}>
                    <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" />
                </svg>
            );
        case 'donors':
            return (
                <svg className={className} viewBox="0 0 24 24" {...base} {...props}>
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                </svg>
            );
        case 'volunteers':
            return (
                <svg className={className} viewBox="0 0 24 24" {...base} {...props}>
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                </svg>
            );
        case 'messages':
            return (
                <svg className={className} viewBox="0 0 24 24" {...base} {...props}>
                    <rect x="2" y="4" width="20" height="16" rx="2" />
                    <path d="m22 7-10 6L2 7" />
                </svg>
            );
        case 'users':
            return (
                <svg className={className} viewBox="0 0 24 24" {...base} {...props}>
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
            );
        case 'roles':
            return (
                <svg className={className} viewBox="0 0 24 24" {...base} {...props}>
                    <path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z" />
                    <path d="m9 12 2 2 4-4" />
                </svg>
            );
        case 'search':
            return (
                <svg className={className} viewBox="0 0 24 24" {...base} {...props}>
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.3-4.3" />
                </svg>
            );
        case 'bell':
            return (
                <svg className={className} viewBox="0 0 24 24" {...base} {...props}>
                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9" />
                    <path d="M13.7 21a2 2 0 0 1-3.4 0" />
                </svg>
            );
        case 'logout':
            return (
                <svg className={className} viewBox="0 0 24 24" {...base} {...props}>
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                    <path d="m16 17 5-5-5-5" />
                    <path d="M21 12H9" />
                </svg>
            );
        case 'menu':
            return (
                <svg className={className} viewBox="0 0 24 24" {...base} {...props}>
                    <path d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            );
        case 'kpi-raised':
            return (
                <svg className={className} viewBox="0 0 24 24" {...base} {...props}>
                    <path d="M12 1v22" />
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                </svg>
            );
        case 'kpi-campaign':
            return (
                <svg className={className} viewBox="0 0 24 24" {...base} {...props}>
                    <path d="M3 11 21 4v16l-7-2.6" />
                    <path d="M3 11v5a1 1 0 0 0 1 1h3l1 4h3l-1-4" />
                </svg>
            );
        case 'kpi-pages':
            return (
                <svg className={className} viewBox="0 0 24 24" {...base} {...props}>
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <path d="M14 2v6h6" />
                </svg>
            );
        case 'kpi-sections':
            return (
                <svg className={className} viewBox="0 0 24 24" {...base} {...props}>
                    <rect x="3" y="3" width="7" height="7" rx="1" />
                    <rect x="14" y="3" width="7" height="7" rx="1" />
                    <rect x="3" y="14" width="7" height="7" rx="1" />
                    <rect x="14" y="14" width="7" height="7" rx="1" />
                </svg>
            );
        default:
            return null;
    }
}

export function kpiIconName(key) {
    const map = {
        raised: 'kpi-raised',
        campaigns: 'kpi-campaign',
        pages: 'kpi-pages',
        sections: 'kpi-sections',
    };

    return map[key] ?? 'kpi-pages';
}
