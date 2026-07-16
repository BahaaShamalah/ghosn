const config = window.__cmsAdmin || {};

let modal;
let grid;
let emptyState;
let loadingState;
let searchInput;
let activeCallback = null;
let activeMediaType = 'image';

export function initMediaPickers() {
    modal = document.getElementById('cms-media-modal');
    grid = modal?.querySelector('[data-media-modal-grid]');
    emptyState = modal?.querySelector('[data-media-modal-empty]');
    loadingState = modal?.querySelector('[data-media-modal-loading]');
    searchInput = modal?.querySelector('[data-media-modal-search]');

    modal?.querySelector('[data-media-modal-close]')?.addEventListener('click', closeMediaLibrary);
    modal?.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeMediaLibrary();
        }
    });

    searchInput?.addEventListener('input', () => {
        loadMediaItems(searchInput.value.trim());
    });

    document.querySelectorAll('[data-media-picker]').forEach((picker) => {
        const input = picker.querySelector('[data-media-input]');
        const preview = picker.querySelector('[data-media-preview]');
        const clearButton = picker.querySelector('[data-media-clear]');
        const dropzone = picker.querySelector('[data-media-dropzone]');
        const fileInput = picker.querySelector('[data-media-file-input]');
        const mediaType = picker.dataset.mediaType || 'image';

        picker.querySelector('[data-media-library-open]')?.addEventListener('click', () => {
            openMediaLibrary({
                mediaType,
                onSelect: (media) => applySelection(picker, media),
            });
        });

        clearButton?.addEventListener('click', () => {
            if (input) {
                input.value = '';
            }

            preview?.classList.add('hidden');
            clearButton.classList.add('hidden');
        });

        dropzone?.addEventListener('click', () => fileInput?.click());

        dropzone?.addEventListener('dragover', (event) => {
            event.preventDefault();
            dropzone.classList.add('border-ghosn/40', 'bg-cream/60');
        });

        dropzone?.addEventListener('dragleave', () => {
            dropzone.classList.remove('border-ghosn/40', 'bg-cream/60');
        });

        dropzone?.addEventListener('drop', (event) => {
            event.preventDefault();
            dropzone.classList.remove('border-ghosn/40', 'bg-cream/60');

            const file = event.dataTransfer?.files?.[0];

            if (file) {
                if (! fileMatchesType(file, mediaType)) {
                    showUploadError(new Error(`Please select a ${mediaType} file.`));
                    return;
                }

                uploadFile(file).then((media) => applySelection(picker, media)).catch(showUploadError);
            }
        });

        fileInput?.addEventListener('change', () => {
            const file = fileInput.files?.[0];

            if (! file) {
                return;
            }

            if (! fileMatchesType(file, mediaType)) {
                showUploadError(new Error(`Please select a ${mediaType} file.`));
                fileInput.value = '';
                return;
            }

            uploadFile(file).then((media) => applySelection(picker, media)).catch(showUploadError);
            fileInput.value = '';
        });
    });

    initGalleryPickers();
}

export function initGalleryPickers() {
    document.querySelectorAll('[data-media-gallery]').forEach((gallery) => {
        const input = gallery.querySelector('[data-gallery-input]');
        const preview = gallery.querySelector('[data-gallery-preview]');

        const readIds = () => {
            try {
                return JSON.parse(input?.value || '[]');
            } catch {
                return [];
            }
        };

        const writeIds = (ids) => {
            if (input) {
                input.value = JSON.stringify(ids);
            }
        };

        const renderItem = (media) => {
            const wrap = document.createElement('div');
            wrap.className = 'relative overflow-hidden rounded-xl border border-ghosn/10';
            wrap.dataset.galleryItem = String(media.id);
            wrap.innerHTML = `
                <img src="${media.thumbnail_url || media.url}" alt="" class="h-20 w-28 object-cover">
                <button type="button" data-gallery-remove="${media.id}" class="absolute right-1 top-1 rounded-full bg-ghosn/80 px-1.5 text-xs text-offwhite">×</button>
            `;
            wrap.querySelector('[data-gallery-remove]')?.addEventListener('click', () => {
                writeIds(readIds().filter((id) => Number(id) !== Number(media.id)));
                wrap.remove();
            });
            preview?.appendChild(wrap);
        };

        gallery.querySelector('[data-gallery-add]')?.addEventListener('click', () => {
            openMediaLibrary({
                mediaType: 'image',
                onSelect: (media) => {
                    const ids = readIds();
                    if (! ids.includes(media.id)) {
                        writeIds([...ids, media.id]);
                        renderItem(media);
                    }
                },
            });
        });

        gallery.querySelectorAll('[data-gallery-remove]').forEach((button) => {
            button.addEventListener('click', () => {
                const id = Number(button.getAttribute('data-gallery-remove'));
                writeIds(readIds().filter((item) => Number(item) !== id));
                button.closest('[data-gallery-item]')?.remove();
            });
        });
    });
}

export function openMediaLibrary({ onSelect, mediaType = 'image' }) {
    if (! modal) {
        return;
    }

    activeCallback = onSelect;
    activeMediaType = mediaType;
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    if (searchInput) {
        searchInput.value = '';
    }

    loadMediaItems('');
}

function closeMediaLibrary() {
    if (! modal) {
        return;
    }

    modal.classList.add('hidden');
    modal.classList.remove('flex');
    activeCallback = null;
}

async function loadMediaItems(query) {
    if (! grid || ! loadingState || ! emptyState) {
        return;
    }

    loadingState.classList.remove('hidden');
    emptyState.classList.add('hidden');
    grid.innerHTML = '';

    const url = new URL(config.mediaPickerUrl, window.location.origin);
    url.searchParams.set('type', activeMediaType);

    if (query) {
        url.searchParams.set('q', query);
    }

    const response = await fetch(url.toString(), {
        headers: { Accept: 'application/json' },
        credentials: 'same-origin',
    });

    if (! response.ok) {
        loadingState.classList.add('hidden');
        emptyState.classList.remove('hidden');

        return;
    }

    const payload = await response.json();
    const items = payload.data || [];

    loadingState.classList.add('hidden');

    if (! items.length) {
        emptyState.classList.remove('hidden');

        return;
    }

    emptyState.classList.add('hidden');

    items.forEach((media) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'group overflow-hidden rounded-2xl border border-ghosn/10 bg-cream/30 text-left transition hover:border-ghosn/25 hover:shadow-md';
        const visual = media.is_video
            ? `<div class="flex aspect-[4/3] w-full items-center justify-center bg-ghosn/10 text-3xl text-ghosn">▶</div>`
            : `<img src="${media.thumbnail_url || media.url}" alt="" class="aspect-[4/3] w-full object-cover">`;
        button.innerHTML = `
            ${visual}
            <p class="truncate px-2 py-2 text-xs font-medium text-ghosn">${media.original_filename || ''}</p>
        `;

        button.addEventListener('click', () => {
            activeCallback?.(media);
            closeMediaLibrary();
        });

        grid.appendChild(button);
    });
}

async function uploadFile(file) {
    const body = new FormData();
    body.append('file', file);

    const response = await fetch(config.mediaUploadUrl, {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'X-CSRF-TOKEN': config.csrfToken,
        },
        credentials: 'same-origin',
        body,
    });

    const payload = await response.json();

    if (! response.ok) {
        throw new Error(payload.message || 'Upload failed');
    }

    return payload.media;
}

function applySelection(picker, media) {
    const input = picker.querySelector('[data-media-input]');
    const preview = picker.querySelector('[data-media-preview]');
    const previewImg = picker.querySelector('[data-media-preview-img]');
    const previewVideo = picker.querySelector('[data-media-preview-video]');
    const clearButton = picker.querySelector('[data-media-clear]');

    if (input) {
        input.value = media.id;
    }

    if (previewImg) {
        previewImg.src = media.thumbnail_url || media.url;
    }

    if (previewVideo) {
        previewVideo.src = media.url;
        previewVideo.load();
    }

    preview?.classList.remove('hidden');
    clearButton?.classList.remove('hidden');
}

function fileMatchesType(file, mediaType) {
    if (! mediaType || mediaType === 'all') {
        return true;
    }

    return file.type.startsWith(`${mediaType}/`);
}

function showUploadError(error) {
    window.alert(error?.message || 'Upload failed');
}
