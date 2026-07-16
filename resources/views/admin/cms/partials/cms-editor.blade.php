@php
    $editorId = $id ?? 'cms-editor-'.uniqid();
    $dir = $dir ?? 'ltr';
    $value = $value ?? '';
    $name = $name ?? 'content';
    $placeholder = $placeholder ?? __('admin.cms.editor_placeholder');
@endphp

<div class="cms-editor" data-cms-editor data-dir="{{ $dir }}" data-placeholder="{{ $placeholder }}">
    <div class="cms-editor-toolbar mb-2 flex flex-wrap items-center gap-1 rounded-xl border border-[rgba(64,97,57,0.12)] bg-[#F2F1EA] p-2" data-cms-toolbar>
        <div class="cms-editor-toolbar-group">
            <button type="button" data-cmd="undo" class="cms-editor-btn" title="{{ __('admin.cms.editor_undo') }}" disabled data-cms-undo>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7v6h6"/><path d="M21 17a9 9 0 0 0-9-9 9 9 0 0 0-6.69 3L3 13"/></svg>
            </button>
            <button type="button" data-cmd="redo" class="cms-editor-btn" title="{{ __('admin.cms.editor_redo') }}" disabled data-cms-redo>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 7v6h-6"/><path d="M3 17a9 9 0 0 1 9-9 9 9 0 0 1 6.69 3L21 13"/></svg>
            </button>
        </div>

        <span class="cms-editor-toolbar-divider" aria-hidden="true"></span>

        <div class="cms-editor-toolbar-group">
            <button type="button" data-cmd="bold" class="cms-editor-btn" title="{{ __('admin.cms.editor_bold') }}"><strong>B</strong></button>
            <button type="button" data-cmd="italic" class="cms-editor-btn" title="{{ __('admin.cms.editor_italic') }}"><em>I</em></button>
            <button type="button" data-cmd="underline" class="cms-editor-btn" title="{{ __('admin.cms.editor_underline') }}"><span class="underline">U</span></button>
            <button type="button" data-cmd="strike" class="cms-editor-btn" title="{{ __('admin.cms.editor_strike') }}"><span class="line-through">S</span></button>
            <button type="button" data-cmd="code" class="cms-editor-btn" title="{{ __('admin.cms.editor_code') }}">&lt;/&gt;</button>
            <button type="button" data-cmd="highlight" class="cms-editor-btn" title="{{ __('admin.cms.editor_highlight') }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M15.24 3.75 20.25 8.8l-2.12 2.12-5.01-5.01 2.12-2.16ZM4.5 14.25l6.01-6.01 5.01 5.01-6.01 6.01H4.5v-5.01Z"/></svg>
            </button>
        </div>

        <span class="cms-editor-toolbar-divider" aria-hidden="true"></span>

        <div class="cms-editor-toolbar-group">
            <button type="button" data-cmd="heading" data-level="2" class="cms-editor-btn" title="{{ __('admin.cms.editor_h2') }}">H2</button>
            <button type="button" data-cmd="heading" data-level="3" class="cms-editor-btn" title="{{ __('admin.cms.editor_h3') }}">H3</button>
            <button type="button" data-cmd="heading" data-level="4" class="cms-editor-btn" title="{{ __('admin.cms.editor_h4') }}">H4</button>
        </div>

        <span class="cms-editor-toolbar-divider" aria-hidden="true"></span>

        <div class="cms-editor-toolbar-group">
            <button type="button" data-cmd="bulletList" class="cms-editor-btn" title="{{ __('admin.cms.editor_bullet_list') }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><circle cx="4" cy="6" r="1" fill="currentColor"/><circle cx="4" cy="12" r="1" fill="currentColor"/><circle cx="4" cy="18" r="1" fill="currentColor"/></svg>
            </button>
            <button type="button" data-cmd="orderedList" class="cms-editor-btn" title="{{ __('admin.cms.editor_ordered_list') }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><line x1="10" y1="6" x2="21" y2="6"/><line x1="10" y1="12" x2="21" y2="12"/><line x1="10" y1="18" x2="21" y2="18"/><path d="M4 6h1v4M4 10h2M6 18H4c0-1 2-2 2-3s-1-1.5-2-1"/></svg>
            </button>
        </div>

        <span class="cms-editor-toolbar-divider" aria-hidden="true"></span>

        <div class="cms-editor-toolbar-group">
            <button type="button" data-cmd="blockquote" class="cms-editor-btn" title="{{ __('admin.cms.editor_blockquote') }}">“</button>
            <button type="button" data-cmd="codeBlock" class="cms-editor-btn" title="{{ __('admin.cms.editor_code_block') }}">{ }</button>
            <button type="button" data-cmd="horizontalRule" class="cms-editor-btn" title="{{ __('admin.cms.editor_hr') }}">—</button>
        </div>

        <span class="cms-editor-toolbar-divider" aria-hidden="true"></span>

        <div class="cms-editor-toolbar-group">
            <button type="button" data-cmd="align" data-align="left" class="cms-editor-btn" title="{{ __('admin.cms.editor_align_left') }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="15" y2="12"/><line x1="3" y1="18" x2="18" y2="18"/></svg>
            </button>
            <button type="button" data-cmd="align" data-align="center" class="cms-editor-btn" title="{{ __('admin.cms.editor_align_center') }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><line x1="3" y1="6" x2="21" y2="6"/><line x1="6" y1="12" x2="18" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg>
            </button>
            <button type="button" data-cmd="align" data-align="right" class="cms-editor-btn" title="{{ __('admin.cms.editor_align_right') }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><line x1="3" y1="6" x2="21" y2="6"/><line x1="9" y1="12" x2="21" y2="12"/><line x1="6" y1="18" x2="21" y2="18"/></svg>
            </button>
        </div>

        <span class="cms-editor-toolbar-divider" aria-hidden="true"></span>

        <div class="cms-editor-toolbar-group">
            <button type="button" data-cmd="link" class="cms-editor-btn" title="{{ __('admin.cms.editor_link') }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
            </button>
            <button type="button" data-cmd="unlink" class="cms-editor-btn" title="{{ __('admin.cms.editor_unlink') }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m18.84 12.25 1.72-1.71h-.02a5.004 5.004 0 0 0-.12-7.07 5.006 5.006 0 0 0-6.95 0l-1.72 1.71"/><path d="m5.17 11.75-1.71 1.71a5.004 5.004 0 0 0 .12 7.07 5.006 5.006 0 0 0 6.95 0l1.71-1.71"/><line x1="8" y1="2" x2="8" y2="5"/><line x1="2" y1="8" x2="5" y2="8"/><line x1="16" y1="19" x2="16" y2="22"/><line x1="19" y1="16" x2="22" y2="16"/></svg>
            </button>
            <button type="button" data-cmd="image" class="cms-editor-btn" title="{{ __('admin.cms.editor_image') }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
            </button>
            <button type="button" data-cmd="youtube" class="cms-editor-btn" title="{{ __('admin.cms.editor_youtube') }}">YT</button>
            <button type="button" data-cmd="social" class="cms-editor-btn" title="{{ __('admin.cms.editor_social') }}">@</button>
        </div>

        <span class="cms-editor-toolbar-divider" aria-hidden="true"></span>

        <div class="cms-editor-toolbar-group">
            <button type="button" data-cmd="clearFormatting" class="cms-editor-btn" title="{{ __('admin.cms.editor_clear') }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7V4h16v3"/><path d="M9 20h6"/><path d="M12 4v16"/></svg>
            </button>
        </div>
    </div>

    <div class="cms-editor-surface min-h-[280px] rounded-xl border border-[rgba(64,97,57,0.15)] bg-white px-4 py-3 text-sm leading-relaxed text-[#3a4234]" data-cms-surface dir="{{ $dir }}"></div>
    <textarea name="{{ $name }}" class="hidden" data-cms-textarea>{{ old($name, $value) }}</textarea>
</div>
