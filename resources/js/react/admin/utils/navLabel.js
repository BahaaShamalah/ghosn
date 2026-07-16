export function navLabel(nav, key, isAr) {
    const item = nav.find((entry) => entry.key === key);

    if (! item) {
        return '';
    }

    return isAr ? item.label_ar : item.label_en;
}
