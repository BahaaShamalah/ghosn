export function initPageTemplateToggle() {
    const select = document.querySelector('[data-page-template-select]');

    if (! select) {
        return;
    }

    const sync = () => {
        const template = select.value;

        document.querySelectorAll('[data-template-panel]').forEach((panel) => {
            const hidden = panel.getAttribute('data-template-panel') !== template;
            panel.classList.toggle('hidden', hidden);

            panel.querySelectorAll('input, select, textarea, button').forEach((field) => {
                if (field.type === 'button' || field.type === 'submit') {
                    return;
                }

                field.disabled = hidden;
            });
        });
    };

    select.addEventListener('change', sync);
    sync();
}
