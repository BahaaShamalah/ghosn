export function initShareButtons(root = document) {
    root.querySelectorAll('[data-share-copy]').forEach((button) => {
        button.addEventListener('click', async () => {
            const url = button.getAttribute('data-share-copy');
            const toast = button.closest('[data-share-root]')?.querySelector('[data-share-toast]');

            if (! url) {
                return;
            }

            try {
                await navigator.clipboard.writeText(url);
                toast?.classList.add('is-visible');

                window.setTimeout(() => {
                    toast?.classList.remove('is-visible');
                }, 2200);
            } catch {
                window.prompt('Copy link:', url);
            }
        });
    });
}
