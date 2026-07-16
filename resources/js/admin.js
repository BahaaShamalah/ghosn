import { initCampaignEditor } from './admin/campaign-editor';
import { initCmsEditors } from './admin/cms-editor';
import { initDonorEmailForm } from './admin/donor-email-form';
import { initMediaPickers } from './admin/media-picker';
import { initPageTemplateToggle } from './admin/page-template';

document.addEventListener('DOMContentLoaded', () => {
    initMediaPickers();
    initCmsEditors();
    initDonorEmailForm();
    initPageTemplateToggle();
    initCampaignEditor();
});
