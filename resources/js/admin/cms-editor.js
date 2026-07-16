import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';
import Image from '@tiptap/extension-image';
import Underline from '@tiptap/extension-underline';
import TextAlign from '@tiptap/extension-text-align';
import Highlight from '@tiptap/extension-highlight';
import Placeholder from '@tiptap/extension-placeholder';
import { openMediaLibrary } from './media-picker';

const editors = new WeakMap();

const labels = () => window.__cmsEditor || {};

export function syncCmsEditorsBeforeSubmit() {
    document.querySelectorAll('[data-cms-editor]').forEach((wrapper) => {
        const editor = editors.get(wrapper);
        const textarea = wrapper.querySelector('[data-cms-textarea]');

        if (editor && textarea) {
            textarea.value = editor.getHTML();
        }
    });
}

export function initCmsEditors() {
    document.querySelectorAll('form').forEach((form) => {
        if (form.dataset.cmsEditorSync === '1') {
            return;
        }

        form.dataset.cmsEditorSync = '1';
        form.addEventListener('submit', syncCmsEditorsBeforeSubmit);
    });

    document.querySelectorAll('[data-cms-editor]').forEach((wrapper) => {
        if (wrapper.dataset.initialized === '1') {
            return;
        }

        wrapper.dataset.initialized = '1';

        const surface = wrapper.querySelector('[data-cms-surface]');
        const textarea = wrapper.querySelector('[data-cms-textarea]');
        const toolbar = wrapper.querySelector('[data-cms-toolbar]');
        const dir = wrapper.dataset.dir || 'ltr';
        const placeholder = wrapper.dataset.placeholder || labels().placeholder || 'Write here…';

        const editor = new Editor({
            element: surface,
            extensions: [
                StarterKit.configure({
                    heading: { levels: [2, 3, 4] },
                    link: false,
                }),
                Underline,
                TextAlign.configure({
                    types: ['heading', 'paragraph'],
                    alignments: ['left', 'center', 'right'],
                }),
                Highlight.configure({
                    multicolor: false,
                }),
                Link.configure({
                    openOnClick: false,
                    HTMLAttributes: { rel: 'noopener noreferrer', target: '_blank' },
                }),
                Image.configure({ inline: false }),
                Placeholder.configure({
                    placeholder,
                    emptyNodeClass: 'is-editor-empty',
                }),
            ],
            content: textarea?.value || '',
            editorProps: {
                attributes: {
                    class: 'ProseMirror',
                    dir,
                },
            },
            onUpdate: ({ editor: instance }) => {
                if (textarea) {
                    textarea.value = instance.getHTML();
                }
            },
        });

        editors.set(wrapper, editor);

        toolbar?.querySelectorAll('[data-cmd]').forEach((button) => {
            button.addEventListener('click', () => {
                runCommand(editor, button);
                syncToolbarState(wrapper, editor);
            });
        });

        editor.on('selectionUpdate', () => syncToolbarState(wrapper, editor));
        editor.on('transaction', () => syncToolbarState(wrapper, editor));
    });
}

function runCommand(editor, button) {
    const cmd = button.dataset.cmd;

    switch (cmd) {
    case 'undo':
        editor.chain().focus().undo().run();
        break;
    case 'redo':
        editor.chain().focus().redo().run();
        break;
    case 'bold':
        editor.chain().focus().toggleBold().run();
        break;
    case 'italic':
        editor.chain().focus().toggleItalic().run();
        break;
    case 'underline':
        editor.chain().focus().toggleUnderline().run();
        break;
    case 'strike':
        editor.chain().focus().toggleStrike().run();
        break;
    case 'code':
        editor.chain().focus().toggleCode().run();
        break;
    case 'highlight':
        editor.chain().focus().toggleHighlight().run();
        break;
    case 'heading': {
        const level = Number(button.dataset.level || 2);
        editor.chain().focus().toggleHeading({ level }).run();
        break;
    }
    case 'bulletList':
        editor.chain().focus().toggleBulletList().run();
        break;
    case 'orderedList':
        editor.chain().focus().toggleOrderedList().run();
        break;
    case 'blockquote':
        editor.chain().focus().toggleBlockquote().run();
        break;
    case 'codeBlock':
        editor.chain().focus().toggleCodeBlock().run();
        break;
    case 'horizontalRule':
        editor.chain().focus().setHorizontalRule().run();
        break;
    case 'align': {
        const alignment = button.dataset.align || 'left';
        editor.chain().focus().setTextAlign(alignment).run();
        break;
    }
    case 'link': {
        const previous = editor.getAttributes('link').href;
        const url = window.prompt(labels().linkPrompt || 'URL', previous || 'https://');

        if (url === null) {
            return;
        }

        if (url === '') {
            editor.chain().focus().extendMarkRange('link').unsetLink().run();
            return;
        }

        editor.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
        break;
    }
    case 'unlink':
        editor.chain().focus().extendMarkRange('link').unsetLink().run();
        break;
    case 'image':
        openMediaLibrary({
            onSelect: (media) => {
                editor.chain().focus().setImage({
                    src: media.url,
                    alt: media.original_filename || '',
                }).run();
            },
        });
        break;
    case 'youtube':
    case 'social': {
        const promptText = cmd === 'youtube'
            ? (labels().youtubePrompt || 'YouTube URL')
            : (labels().socialPrompt || 'Instagram / Facebook / X URL');
        const url = window.prompt(promptText, 'https://');

        if (! url) {
            return;
        }

        insertEmbedMarker(editor, url);
        break;
    }
    case 'clearFormatting':
        editor.chain().focus().clearNodes().unsetAllMarks().run();
        break;
    default:
        break;
    }
}

function syncToolbarState(wrapper, editor) {
    const toolbar = wrapper.querySelector('[data-cms-toolbar]');

    toolbar?.querySelector('[data-cms-undo]')?.toggleAttribute('disabled', ! editor.can().undo());
    toolbar?.querySelector('[data-cms-redo]')?.toggleAttribute('disabled', ! editor.can().redo());

    toolbar?.querySelectorAll('[data-cmd]').forEach((button) => {
        const cmd = button.dataset.cmd;
        let active = false;

        switch (cmd) {
        case 'bold':
            active = editor.isActive('bold');
            break;
        case 'italic':
            active = editor.isActive('italic');
            break;
        case 'underline':
            active = editor.isActive('underline');
            break;
        case 'strike':
            active = editor.isActive('strike');
            break;
        case 'code':
            active = editor.isActive('code');
            break;
        case 'highlight':
            active = editor.isActive('highlight');
            break;
        case 'heading':
            active = editor.isActive('heading', { level: Number(button.dataset.level || 2) });
            break;
        case 'bulletList':
            active = editor.isActive('bulletList');
            break;
        case 'orderedList':
            active = editor.isActive('orderedList');
            break;
        case 'blockquote':
            active = editor.isActive('blockquote');
            break;
        case 'codeBlock':
            active = editor.isActive('codeBlock');
            break;
        case 'align':
            active = editor.isActive({ textAlign: button.dataset.align || 'left' });
            break;
        case 'link':
            active = editor.isActive('link');
            break;
        default:
            break;
        }

        button.classList.toggle('is-active', active);
        button.toggleAttribute('aria-pressed', active);
    });
}

async function insertEmbedMarker(editor, url) {
    const marker = buildEmbedMarker(url.trim());

    if (! marker) {
        window.alert(labels().embedError || 'Unsupported embed URL. Use YouTube, Vimeo, Instagram, Facebook, or X links.');
        return;
    }

    editor.chain().focus().insertContent(marker).run();
}

function buildEmbedMarker(url) {
    const youtube = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([A-Za-z0-9_-]{6,})/i);
    if (youtube) {
        return `<div class="ghosn-embed" data-embed-type="youtube" data-embed-id="${youtube[1]}"></div>`;
    }

    const vimeo = url.match(/vimeo\.com\/(?:video\/)?(\d+)/i);
    if (vimeo) {
        return `<div class="ghosn-embed" data-embed-type="vimeo" data-embed-id="${vimeo[1]}"></div>`;
    }

    if (/instagram\.com|facebook\.com|fb\.watch|twitter\.com|x\.com/i.test(url)) {
        return `<div class="ghosn-embed" data-embed-type="social" data-embed-url="${escapeAttr(url)}"></div>`;
    }

    return null;
}

function escapeAttr(value) {
    return value.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;');
}
