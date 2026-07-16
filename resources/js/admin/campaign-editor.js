const CURRENCY_SYMBOLS = {
    USD: '$',
    EUR: '€',
    GBP: '£',
    SAR: 'ر.س',
    AED: 'د.إ',
};

export function initCampaignEditor() {
    const root = document.querySelector('[data-campaign-editor]');
    if (!root) {
        return;
    }

    const preview = root.querySelector('[data-campaign-preview]');
    if (!preview) {
        return;
    }

    const i18n = window.__campaignEditor || {};
    const titlePlaceholder = i18n.titlePlaceholder || 'Your campaign title';
    const descPlaceholder = i18n.descPlaceholder || 'Your campaign description will appear here as you type.';

    let previewLang = 'en';

    const els = {
        title: preview.querySelector('[data-campaign-preview-title]'),
        desc: preview.querySelector('[data-campaign-preview-desc]'),
        category: preview.querySelector('[data-campaign-preview-category]'),
        bar: preview.querySelector('[data-campaign-preview-bar]'),
        raised: preview.querySelector('[data-campaign-preview-raised]'),
        pct: preview.querySelector('[data-campaign-preview-pct]'),
        img: preview.querySelector('[data-campaign-preview-img]'),
    };

    const categorySelect = root.querySelector('[data-campaign-preview-source="category_id"]');
    const categoryLabels = (() => {
        try {
            return JSON.parse(categorySelect?.dataset.categoryLabels || '{}');
        } catch {
            return {};
        }
    })();

    const readValue = (name) => {
        const field = root.querySelector(`[name="${name}"]`);
        return field ? String(field.value || '').trim() : '';
    };

    const readNumber = (name) => {
        const value = parseFloat(readValue(name));
        return Number.isFinite(value) ? value : 0;
    };

    const currencySymbol = (code) => CURRENCY_SYMBOLS[code] || code;

    const formatAmount = (amount, currency) => {
        const symbol = currencySymbol(currency);
        const formatted = new Intl.NumberFormat(undefined, {
            maximumFractionDigits: 0,
        }).format(amount);

        return `${symbol}${formatted}`;
    };

    const syncCurrencyPrefixes = () => {
        const currency = readValue('currency') || 'USD';
        const symbol = currencySymbol(currency);

        root.querySelectorAll('[data-campaign-currency-prefix]').forEach((node) => {
            node.textContent = symbol;
        });
    };

    const syncCoverImage = () => {
        const pickerImg = root.querySelector('.gh-campaign-cover-picker [data-media-preview-img]');
        const src = pickerImg?.getAttribute('src') || '';

        if (! els.img) {
            return;
        }

        if (src) {
            els.img.src = src;
            els.img.classList.remove('hidden');
        } else {
            els.img.removeAttribute('src');
            els.img.classList.add('hidden');
        }
    };

    const syncPreview = () => {
        const titleEn = readValue('title_en');
        const titleAr = readValue('title_ar');
        const excerptEn = readValue('excerpt_en');
        const excerptAr = readValue('excerpt_ar');
        const goal = readNumber('goal_amount');
        const raised = readNumber('raised_amount');
        const currency = readValue('currency') || 'USD';
        const categoryId = readValue('category_id');
        const categoryLabel = categoryLabels[categoryId] || '';

        const title = previewLang === 'ar' ? (titleAr || titleEn) : (titleEn || titleAr);
        const desc = previewLang === 'ar' ? (excerptAr || excerptEn) : (excerptEn || excerptAr);
        const pct = goal > 0 ? Math.min(100, Math.round((raised / goal) * 100)) : 0;

        if (els.title) {
            els.title.textContent = title || titlePlaceholder;
        }

        if (els.desc) {
            els.desc.textContent = desc || descPlaceholder;
        }

        if (els.category) {
            if (categoryLabel) {
                els.category.textContent = categoryLabel;
                els.category.classList.remove('hidden');
            } else {
                els.category.classList.add('hidden');
            }
        }

        if (els.bar) {
            els.bar.style.width = `${pct}%`;
        }

        if (els.raised) {
            els.raised.textContent = formatAmount(raised, currency);
        }

        if (els.pct) {
            els.pct.textContent = `${pct}%`;
        }

        syncCurrencyPrefixes();
        syncCoverImage();
    };

    preview.querySelectorAll('[data-preview-lang]').forEach((button) => {
        button.addEventListener('click', () => {
            previewLang = button.getAttribute('data-preview-lang') || 'en';

            preview.querySelectorAll('[data-preview-lang]').forEach((node) => {
                const active = node.getAttribute('data-preview-lang') === previewLang;
                node.classList.toggle('bg-[#406139]', active);
                node.classList.toggle('text-[#F2F1EA]', active);
                node.classList.toggle('bg-transparent', ! active);
                node.classList.toggle('text-[#406139]', ! active);
            });

            syncPreview();
        });
    });

    root.addEventListener('input', syncPreview);
    root.addEventListener('change', syncPreview);

    const coverPicker = root.querySelector('.gh-campaign-cover-picker [data-media-preview-img]');
    if (coverPicker) {
        const observer = new MutationObserver(syncCoverImage);
        observer.observe(coverPicker, { attributes: true, attributeFilter: ['src', 'class'] });
    }

    initVideoSourceToggle(root);

    syncPreview();
}

function initVideoSourceToggle(root) {
    const container = root.querySelector('[data-campaign-video]');
    if (!container) {
        return;
    }

    const radios = container.querySelectorAll('[data-video-source]');
    const tabs = container.querySelectorAll('[data-video-tab]');
    const panels = container.querySelectorAll('[data-video-panel]');

    const apply = (source) => {
        tabs.forEach((tab) => {
            tab.setAttribute('data-active', tab.getAttribute('data-video-tab') === source ? 'true' : 'false');
        });
        panels.forEach((panel) => {
            panel.classList.toggle('hidden', panel.getAttribute('data-video-panel') !== source);
        });
    };

    radios.forEach((radio) => {
        radio.addEventListener('change', () => {
            if (radio.checked) {
                apply(radio.value);
            }
        });
    });
}
