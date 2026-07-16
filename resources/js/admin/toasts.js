const AUTO_DISMISS_MS = 4000;

const TYPE_STYLES = {
    success: 'border-growth/25 bg-growth-soft/95 text-ghosn-800',
    error: 'border-red-200 bg-red-50/95 text-red-800',
    warning: 'border-amber-200 bg-amber-50/95 text-amber-900',
    info: 'border-ghosn/15 bg-offwhite/95 text-ghosn-ink',
};

let root = null;
const activeMessages = new Set();

export function initAdminToasts() {
    if (window.__adminToastsInitialized) {
        return;
    }

    window.__adminToastsInitialized = true;
    root = document.getElementById('admin-toast-root');

    if (! root) {
        root = document.createElement('div');
        root.id = 'admin-toast-root';
        root.className = 'pointer-events-none fixed top-4 end-4 z-[100] flex w-full max-w-sm flex-col items-end gap-2 px-4';
        document.body.appendChild(root);
    }

    window.showAdminToast = showAdminToast;
    consumeFlashPayload();
}

function consumeFlashPayload() {
    const payload = root?.dataset?.flash;

    if (! payload) {
        return;
    }

    delete root.dataset.flash;

    try {
        const data = JSON.parse(payload);

        if (data?.message) {
            showAdminToast(data.message, data.type || 'success');
        }
    } catch {
        // Ignore malformed flash payloads.
    }
}

export function showAdminToast(message, type = 'success', options = {}) {
    if (! message || ! root) {
        return null;
    }

    const key = `${type}:${message}`;

    if (activeMessages.has(key) && ! options.force) {
        return null;
    }

    activeMessages.add(key);

    const toast = document.createElement('div');
    toast.setAttribute('role', 'alert');
    toast.setAttribute('data-admin-toast', '');
    toast.className = [
        'pointer-events-auto flex w-full items-start gap-3 rounded-2xl border px-4 py-3 text-sm font-medium shadow-lg',
        'translate-y-0 opacity-100 transition duration-300',
        TYPE_STYLES[type] || TYPE_STYLES.info,
    ].join(' ');

    const text = document.createElement('p');
    text.className = 'flex-1 leading-snug';
    text.textContent = message;
    toast.appendChild(text);

    const close = document.createElement('button');
    close.type = 'button';
    close.className = 'shrink-0 rounded-full p-1 opacity-60 transition hover:opacity-100';
    close.setAttribute('aria-label', 'Close');
    close.innerHTML = '&times;';
    close.addEventListener('click', () => dismissToast(toast, key));
    toast.appendChild(close);

    root.appendChild(toast);

    const delay = options.duration ?? AUTO_DISMISS_MS;
    const timer = window.setTimeout(() => dismissToast(toast, key), delay);
    toast.dataset.dismissTimer = String(timer);

    return toast;
}

function dismissToast(toast, key) {
    if (! toast?.isConnected) {
        activeMessages.delete(key);

        return;
    }

    const timer = Number(toast.dataset.dismissTimer || 0);

    if (timer) {
        window.clearTimeout(timer);
    }

    toast.classList.add('translate-y-2', 'opacity-0');

    window.setTimeout(() => {
        toast.remove();
        activeMessages.delete(key);
    }, 300);
}
