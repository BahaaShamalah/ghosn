export async function getRecaptchaToken(action = 'submit') {
    const siteKey = window.__GHOSN_GOOGLE__?.recaptchaSiteKey;
    if (!siteKey || typeof window.grecaptcha === 'undefined') {
        return '';
    }

    try {
        await new Promise((resolve) => {
            window.grecaptcha.ready(resolve);
        });

        return await window.grecaptcha.execute(siteKey, { action });
    } catch {
        return '';
    }
}
