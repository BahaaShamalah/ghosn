import { useEffect, useRef } from 'react';

export function useAnimatedBars(selector, dependency) {
    const rootRef = useRef(null);

    useEffect(() => {
        const root = rootRef.current;
        if (! root) {
            return;
        }

        const bars = root.querySelectorAll(selector);
        bars.forEach((bar) => {
            const target = bar.dataset.height ?? bar.dataset.pct ?? '0';
            requestAnimationFrame(() => {
                if (bar.dataset.height !== undefined) {
                    bar.style.height = `${target}%`;
                } else {
                    bar.style.width = `${target}%`;
                }
            });
        });
    }, [selector, dependency]);

    return rootRef;
}
