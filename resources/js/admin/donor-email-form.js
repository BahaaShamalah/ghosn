export function initDonorEmailForm() {
    const repeater = document.querySelector('[data-youtube-repeater]');

    if (! repeater) {
        return;
    }

    const list = repeater.querySelector('[data-youtube-list]');
    const addButton = repeater.querySelector('[data-youtube-add]');

    const syncRemoveButtons = () => {
        const rows = list?.querySelectorAll('[data-youtube-row]') ?? [];

        rows.forEach((row, index) => {
            const removeButton = row.querySelector('[data-youtube-remove]');

            if (! removeButton) {
                return;
            }

            removeButton.classList.toggle('hidden', rows.length <= 1);
        });
    };

    addButton?.addEventListener('click', () => {
        if (! list) {
            return;
        }

        const row = document.createElement('div');
        row.className = 'flex gap-2';
        row.dataset.youtubeRow = '';
        row.innerHTML = `
            <input type="url" name="youtube_urls[]" class="ghosn-input flex-1" maxlength="500" placeholder="https://www.youtube.com/watch?v=..." dir="ltr">
            <button type="button" data-youtube-remove class="shrink-0 rounded-full border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">×</button>
        `;

        row.querySelector('[data-youtube-remove]')?.addEventListener('click', () => {
            row.remove();
            syncRemoveButtons();
        });

        list.appendChild(row);
        syncRemoveButtons();
    });

    list?.querySelectorAll('[data-youtube-remove]').forEach((button) => {
        button.addEventListener('click', () => {
            button.closest('[data-youtube-row]')?.remove();
            syncRemoveButtons();
        });
    });

    syncRemoveButtons();
}
