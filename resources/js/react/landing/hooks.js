import { useEffect, useRef, useState } from 'react';

export function useReveal(options = {}) {
    const ref = useRef(null);
    const [visible, setVisible] = useState(false);

    useEffect(() => {
        const node = ref.current;

        if (! node) {
            return undefined;
        }

        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) {
                    setVisible(true);
                    observer.disconnect();
                }
            },
            { threshold: options.threshold ?? 0.12, rootMargin: options.rootMargin ?? '0px 0px -8% 0px' },
        );

        observer.observe(node);

        return () => observer.disconnect();
    }, [options.rootMargin, options.threshold]);

    return { ref, visible };
}

export function useCountUp(end, { decimals = 0, prefix = '', suffix = '', active = false, duration = 1700 } = {}) {
    const [value, setValue] = useState(0);

    useEffect(() => {
        if (! active) {
            return undefined;
        }

        const start = performance.now();

        const tick = (now) => {
            let progress = Math.min(1, (now - start) / duration);
            progress = 1 - Math.pow(1 - progress, 3);
            setValue(end * progress);

            if (progress < 1) {
                requestAnimationFrame(tick);
            }
        };

        requestAnimationFrame(tick);
    }, [active, decimals, duration, end]);

    const formatted = decimals > 0
        ? value.toFixed(decimals)
        : Math.round(value).toLocaleString('en-US');

    return `${prefix}${formatted}${suffix}`;
}
