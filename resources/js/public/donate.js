/**
 * Donation checkout form — amount presets, summary sync, payment method toggle.
 */
export function initDonateForm(root = document.getElementById('ghosn-root')) {
    if (!root) {
        return;
    }

    const form = root.querySelector('[data-donate-form]');
    const summary = root.querySelector('[data-donate-summary]');

    if (!form) {
        return;
    }

    const amountInput = form.querySelector('#donation-amount');
    const presetButtons = form.querySelectorAll('[data-amount-preset]');
    const methodInputs = form.querySelectorAll('[data-payment-method]');
    const symbol = form.getAttribute('data-currency-symbol') || '$';
    const submitButton = form.querySelector('[data-donate-submit]');
    const submitDefault = form.querySelector('[data-donate-submit-default]');
    const submitPaypalHint = form.querySelector('[data-donate-submit-paypal]');
    const submitIcon = form.querySelector('[data-donate-submit-icon]');

    const summaryAmount = summary?.querySelector('[data-summary-amount]');
    const summaryMethod = summary?.querySelector('[data-summary-method]');
    const summaryBank = summary?.querySelector('[data-summary-bank]');

    const formatAmount = (value) => {
        const num = Number(value);

        if (!Number.isFinite(num) || num <= 0) {
            return `<span class="inline-flex items-center gap-1.5"><span>${symbol}</span><span>0</span></span>`;
        }

        const formatted = num.toLocaleString(undefined, { maximumFractionDigits: 0 });

        return `<span class="inline-flex items-center gap-1.5"><span>${symbol}</span><span>${formatted}</span></span>`;
    };

    const syncPresetState = () => {
        if (!amountInput) {
            return;
        }

        const current = String(amountInput.value);

        presetButtons.forEach((button) => {
            const preset = button.getAttribute('data-amount-preset');
            const isActive = preset === 'custom'
                ? !Array.from(presetButtons).some((btn) => btn.getAttribute('data-amount-preset') === current)
                : preset === current;

            button.classList.toggle('is-active', isActive);
        });

        if (summaryAmount) {
            summaryAmount.innerHTML = formatAmount(current);
        }
    };

    const selectedPaymentMethod = () => form.querySelector('[data-payment-method]:checked')?.value ?? '';

    const syncMethodState = () => {
        const selected = form.querySelector('[data-payment-method]:checked');

        if (!selected || !summary) {
            return;
        }

        const isBank = selected.value === 'bank_transfer';
        const isPaypal = selected.value === 'paypal_business';
        const lang = root.getAttribute('data-lang') === 'ar' ? 'ar' : 'en';
        let label;

        if (isBank) {
            label = summary.getAttribute(`data-label-bank-${lang}`);
        } else if (isPaypal) {
            label = summary.getAttribute(`data-label-paypal-${lang}`);
        } else {
            label = summary.getAttribute(`data-label-stripe-${lang}`);
        }

        if (summaryMethod && label) {
            summaryMethod.textContent = label;
        }

        summaryBank?.classList.toggle('hidden', !isBank);

        if (submitButton) {
            submitButton.classList.toggle('pointer-events-none', isPaypal);
            submitButton.classList.toggle('opacity-80', isPaypal);
            submitButton.setAttribute('aria-disabled', isPaypal ? 'true' : 'false');
        }

        submitDefault?.classList.toggle('hidden', isPaypal);
        submitPaypalHint?.classList.toggle('hidden', !isPaypal);
        submitIcon?.classList.toggle('hidden', isPaypal);
    };

    presetButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const preset = button.getAttribute('data-amount-preset');

            if (!amountInput) {
                return;
            }

            if (preset === 'custom') {
                amountInput.focus();
                amountInput.select();
            } else {
                amountInput.value = preset;
            }

            syncPresetState();
        });
    });

    form.addEventListener('submit', (event) => {
        if (selectedPaymentMethod() === 'paypal_business') {
            event.preventDefault();

            const lang = root.getAttribute('data-lang') === 'ar' ? 'ar' : 'en';
            const hint = form.getAttribute(`data-paypal-submit-hint-${lang}`)
                || 'Please use the PayPal button below to complete your donation.';

            console.warn('Blocked standard form submit for PayPal. Use PayPal buttons.', {
                payment_method: 'paypal_business',
            });

            const paypalError = form.querySelector('[data-paypal-error]');
            if (paypalError) {
                paypalError.textContent = hint;
                paypalError.classList.remove('hidden');
            }
        }
    });

    amountInput?.addEventListener('input', syncPresetState);
    methodInputs.forEach((input) => input.addEventListener('change', syncMethodState));

    syncPresetState();
    syncMethodState();
}
