import { initAdminToasts } from './toasts';

export function initAdminShell() {
    if (window.__adminShellInitialized) {
        return;
    }

    window.__adminShellInitialized = true;

    initAdminLogin();
    initAdminToasts();
    initSidebar();
    initUserMenu();
    initDeleteModal();
    initTooltips();
    initBulkSelect();
    initBulkActions();
}

function initSidebar() {
    const toggle = document.querySelector('[data-admin-sidebar-toggle]');
    const overlay = document.querySelector('[data-admin-sidebar-overlay]');
    const sidebar = document.querySelector('[data-admin-sidebar]');

    if (! toggle || ! sidebar) {
        return;
    }

    const open = () => {
        sidebar.classList.add('open');
        overlay?.classList.add('open');
    };

    const close = () => {
        sidebar.classList.remove('open');
        overlay?.classList.remove('open');
    };

    toggle.addEventListener('click', open);
    overlay?.addEventListener('click', close);
}

function initUserMenu() {
    const toggle = document.querySelector('[data-admin-user-toggle]');
    const menu = document.querySelector('[data-admin-user-menu]');

    if (! toggle || ! menu) {
        return;
    }

    toggle.addEventListener('click', (event) => {
        event.stopPropagation();
        menu.classList.toggle('hidden');
    });

    document.addEventListener('click', () => menu.classList.add('hidden'));
    menu.addEventListener('click', (event) => event.stopPropagation());
}

function initDeleteModal() {
    const modal = document.querySelector('[data-admin-delete-modal]');
    if (! modal) {
        return;
    }

    const singleForm = modal.querySelector('[data-admin-delete-form]');
    const bulkForm = modal.querySelector('[data-admin-bulk-delete-form]');
    const message = modal.querySelector('[data-admin-delete-message]');
    const cancel = modal.querySelector('[data-admin-delete-cancel]');
    const bulkSubmit = bulkForm?.querySelector('button[type="submit"]');

    const showModal = (text, mode = 'single') => {
        if (message) {
            message.textContent = text;
        }

        singleForm?.classList.toggle('hidden', mode !== 'single');
        bulkForm?.classList.toggle('hidden', mode !== 'bulk');
        bulkSubmit?.classList.toggle('hidden', mode !== 'bulk');

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };

    document.querySelectorAll('[data-delete-trigger]').forEach((button) => {
        button.addEventListener('click', () => {
            const action = button.getAttribute('data-delete-action');
            const text = button.getAttribute('data-delete-message') || 'Are you sure?';

            if (singleForm && action) {
                singleForm.setAttribute('action', action);
            }

            showModal(text, 'single');
        });
    });

    document.querySelector('[data-bulk-delete-trigger]')?.addEventListener('click', () => {
        const bulkFormEl = document.querySelector('[data-bulk-form]');
        const selected = bulkFormEl?.querySelectorAll('[data-bulk-item]:checked').length ?? 0;

        if (selected === 0 || ! bulkForm) {
            return;
        }

        const idsHost = bulkForm.querySelector('[data-admin-bulk-delete-ids]');
        if (idsHost) {
            idsHost.innerHTML = '';
            bulkFormEl?.querySelectorAll('[data-bulk-item]:checked').forEach((input) => {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'ids[]';
                hidden.value = input.value;
                idsHost.appendChild(hidden);
            });
        }

        showModal(`Delete ${selected} selected page(s)?`, 'bulk');
    });

    cancel?.addEventListener('click', () => closeDeleteModal(modal));
    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeDeleteModal(modal);
        }
    });
}

function closeDeleteModal(modal) {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function initTooltips() {
    document.querySelectorAll('[data-tooltip]').forEach((element) => {
        element.addEventListener('mouseenter', () => {
            element.setAttribute('title', element.getAttribute('data-tooltip') || '');
        });
    });
}

function initBulkSelect() {
    const master = document.querySelector('[data-bulk-master]');
    const items = document.querySelectorAll('[data-bulk-item]');
    const bar = document.querySelector('[data-bulk-bar]');
    const count = document.querySelector('[data-bulk-count]');

    if (! master || items.length === 0) {
        return;
    }

    const sync = () => {
        const selected = [...items].filter((item) => item.checked).length;
        if (count) {
            count.textContent = String(selected);
        }
        bar?.classList.toggle('hidden', selected === 0);
        master.indeterminate = selected > 0 && selected < items.length;
        master.checked = selected === items.length;
    };

    master.addEventListener('change', () => {
        items.forEach((item) => {
            item.checked = master.checked;
        });
        sync();
    });

    items.forEach((item) => item.addEventListener('change', sync));
    sync();
}

function initBulkActions() {
    const form = document.querySelector('[data-bulk-form]');
    if (! form) {
        return;
    }

    const actionInput = form.querySelector('[data-bulk-action]');

    form.querySelectorAll('[data-bulk-submit]').forEach((button) => {
        button.addEventListener('click', () => {
            const action = button.getAttribute('data-bulk-submit');
            const selected = form.querySelectorAll('[data-bulk-item]:checked').length;

            if (! action || selected === 0) {
                return;
            }

            if (actionInput) {
                actionInput.value = action;
            }

            form.submit();
        });
    });
}

function initAdminLogin() {
    const form = document.querySelector('[data-admin-login-form]');
    const passwordInput = document.querySelector('[data-admin-password-input]');
    const passwordToggle = document.querySelector('[data-admin-password-toggle]');

    passwordToggle?.addEventListener('click', () => {
        if (! passwordInput) {
            return;
        }

        const show = passwordInput.type === 'password';
        passwordInput.type = show ? 'text' : 'password';

        passwordToggle.querySelector('[data-eye-open]')?.classList.toggle('hidden', ! show);
        passwordToggle.querySelector('[data-eye-closed]')?.classList.toggle('hidden', show);
    });

    if (! form) {
        return;
    }

    const submit = form.querySelector('[data-admin-login-submit]');
    const spinner = form.querySelector('[data-admin-login-spinner]');
    const label = form.querySelector('[data-admin-login-submit-label]');

    form.addEventListener('submit', () => {
        if (! submit || ! label) {
            return;
        }

        submit.disabled = true;
        spinner?.classList.remove('hidden');

        if (label.dataset.signingLabel) {
            label.textContent = label.dataset.signingLabel;
        }
    });
}
