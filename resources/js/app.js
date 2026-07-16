import './public/landing.js';
import { initDonateForm } from './public/donate.js';
import { initPayPalDonate } from './public/donate-paypal.js';
import { initShareButtons } from './public/share.js';
import { initInternalPages } from './public/internal-pages.js';
import { initConsentBanner } from './public/consent.js';

document.addEventListener('DOMContentLoaded', () => {
    initDonateForm();
    initPayPalDonate();
    initShareButtons();
    initInternalPages();
    initConsentBanner();
});
