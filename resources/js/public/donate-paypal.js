/**
 * PayPal JS SDK buttons — popup checkout on /donate.
 *
 * Expected API payloads:
 * - POST create-order: donor fields + payment_method: "paypal_business"
 * - POST capture-order: { orderID, donation_id, reference } only (+ CSRF header)
 */
const PAYMENT_METHOD_PAYPAL = 'paypal_business';

export function initPayPalDonate(root = document.getElementById('ghosn-root')) {
    const form = root?.querySelector('[data-donate-form]');

    if (!form || form.getAttribute('data-paypal-enabled') !== '1') {
        return;
    }

    const clientId = form.getAttribute('data-paypal-client-id');
    const currency = form.getAttribute('data-paypal-currency') || 'USD';
    const createUrl = form.getAttribute('data-paypal-create-order-url');
    const captureUrl = form.getAttribute('data-paypal-capture-order-url');
    const paypalContainer = form.querySelector('[data-paypal-buttons]');
    const errorContainer = form.querySelector('[data-paypal-error]');

    if (!clientId || !createUrl || !captureUrl || !paypalContainer) {
        return;
    }

    let buttonsRendered = false;
    let pendingDonation = null;

    const isPayPalSelected = () => form.querySelector('[data-payment-method]:checked')?.value === PAYMENT_METHOD_PAYPAL;

    const showPayPalError = (message) => {
        if (!errorContainer) {
            window.alert(message);
            return;
        }

        errorContainer.textContent = message;
        errorContainer.classList.remove('hidden');
    };

    const clearPayPalError = () => {
        if (!errorContainer) {
            return;
        }

        errorContainer.textContent = '';
        errorContainer.classList.add('hidden');
    };

    const loadPayPalSdk = () => new Promise((resolve, reject) => {
        if (window.paypal) {
            resolve();
            return;
        }

        const script = document.createElement('script');
        script.src = `https://www.paypal.com/sdk/js?client-id=${encodeURIComponent(clientId)}&currency=${encodeURIComponent(currency)}&intent=capture`;
        script.async = true;
        script.onload = () => resolve();
        script.onerror = () => reject(new Error('PayPal SDK failed to load'));
        document.body.appendChild(script);
    });

    const collectCreateOrderPayload = () => {
        const data = new FormData(form);

        return {
            payment_method: PAYMENT_METHOD_PAYPAL,
            amount: Number(data.get('amount')),
            donor_name: String(data.get('donor_name') ?? ''),
            donor_email: String(data.get('donor_email') ?? ''),
            donor_phone: data.get('donor_phone') ? String(data.get('donor_phone')) : null,
            message: data.get('message') ? String(data.get('message')) : null,
            is_anonymous: data.get('is_anonymous') === '1',
            campaign_id: data.get('campaign_id') ? Number(data.get('campaign_id')) : null,
            website: data.get('website') ? String(data.get('website')) : '',
        };
    };

    const postJson = async (url, payload) => {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (!token) {
            throw new Error('Security token missing. Please refresh the page and try again.');
        }

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: JSON.stringify(payload),
        });

        const body = await response.json().catch(() => ({}));

        if (!response.ok) {
            console.error('PayPal request failed', {
                url,
                status: response.status,
                message: body.message ?? null,
                errors: body.errors ?? null,
                payload,
            });

            const validationMessage = body.errors
                ? Object.values(body.errors).flat().join(' ')
                : null;

            throw new Error(validationMessage || body.message || 'Payment request failed.');
        }

        return body;
    };

    const renderPayPalButtons = async () => {
        if (buttonsRendered || !isPayPalSelected()) {
            return;
        }

        try {
            await loadPayPalSdk();
        } catch {
            showPayPalError('PayPal could not be loaded. Please refresh and try again.');
            return;
        }

        if (!window.paypal?.Buttons) {
            showPayPalError('PayPal is unavailable right now.');
            return;
        }

        const buttons = window.paypal.Buttons({
            style: {
                layout: 'vertical',
                color: 'blue',
                shape: 'pill',
                label: 'pay',
                height: 45,
            },
            createOrder: async () => {
                clearPayPalError();

                if (!form.reportValidity()) {
                    throw new Error('Please complete the donation form.');
                }

                const result = await postJson(createUrl, collectCreateOrderPayload());
                const orderId = result.orderID || result.order_id;
                const donationId = Number(result.donation_id);
                const reference = result.reference ? String(result.reference) : '';

                if (!orderId || !Number.isInteger(donationId) || donationId <= 0 || reference === '') {
                    throw new Error('PayPal order could not be created.');
                }

                pendingDonation = {
                    donation_id: donationId,
                    reference,
                };

                form.dataset.paypalDonationId = String(donationId);
                form.dataset.paypalDonationReference = reference;

                return orderId;
            },
            onApprove: async (data) => {
                clearPayPalError();

                const donationId = Number(pendingDonation?.donation_id ?? form.dataset.paypalDonationId);
                const reference = pendingDonation?.reference ?? form.dataset.paypalDonationReference ?? '';

                if (!data.orderID) {
                    showPayPalError('Payment could not be confirmed. Please try again.');
                    throw new Error('Missing PayPal order ID.');
                }

                if (!Number.isInteger(donationId) || donationId <= 0 || reference === '') {
                    showPayPalError('Payment could not be confirmed. Please try again.');
                    throw new Error('Missing donation reference from create-order.');
                }

                try {
                    const result = await postJson(captureUrl, {
                        orderID: data.orderID,
                        donation_id: donationId,
                        reference,
                    });

                    if (!result.paid || !result.redirect_url) {
                        showPayPalError('Payment could not be confirmed. Please contact support with your reference.');
                        throw new Error('Payment could not be confirmed.');
                    }

                    window.location.assign(result.redirect_url);
                } catch (error) {
                    showPayPalError(error.message || 'Payment could not be confirmed.');
                    throw error;
                }
            },
            onError: (error) => {
                console.error('PayPal SDK error', error);
                showPayPalError('PayPal reported an error. Please try again.');
            },
            onCancel: () => {
                clearPayPalError();
            },
        });

        if (!buttons.isEligible()) {
            showPayPalError('PayPal is not available for this browser or region.');
            return;
        }

        await buttons.render(paypalContainer);
        buttonsRendered = true;
    };

    const syncPayPalVisibility = () => {
        const isPayPal = isPayPalSelected();

        paypalContainer.classList.toggle('hidden', !isPayPal);

        if (isPayPal) {
            renderPayPalButtons();
        } else {
            clearPayPalError();
        }
    };

    form.querySelectorAll('[data-payment-method]').forEach((input) => {
        input.addEventListener('change', syncPayPalVisibility);
    });

    syncPayPalVisibility();
}
