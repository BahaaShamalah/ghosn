<script>
    window.__cmsAdmin = {
        mediaPickerUrl: @json(route('admin.media.picker')),
        mediaUploadUrl: @json(route('admin.media.picker.store')),
        csrfToken: @json(csrf_token()),
    };
    window.__cmsEditor = {
        placeholder: @json(__('admin.cms.editor_placeholder')),
        linkPrompt: @json(__('admin.cms.editor_link_prompt')),
        youtubePrompt: @json(__('admin.cms.editor_youtube_prompt')),
        socialPrompt: @json(__('admin.cms.editor_social_prompt')),
        embedError: @json(__('admin.cms.editor_embed_error')),
    };
    window.__campaignEditor = {
        titlePlaceholder: @json(__('admin.campaigns.preview_title_placeholder')),
        descPlaceholder: @json(__('admin.campaigns.preview_desc_placeholder')),
    };
</script>
@vite('resources/js/admin.js')
