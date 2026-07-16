<style>
    #ghosn-root[data-ready] [data-reveal] {
        opacity: 0;
        transform: translateY(16px);
        transition: opacity .7s cubic-bezier(.2,.7,.2,1), transform .7s cubic-bezier(.2,.7,.2,1);
    }
    #ghosn-root[data-ready] [data-reveal].in {
        opacity: 1;
        transform: none;
    }
    .donate-input {
        border-radius: 0.875rem;
        border: 1px solid rgb(12 90 46 / 0.15);
        background: rgb(244 238 225 / 0.35);
        padding-block: 0.7rem;
        padding-inline: 0.95rem;
        font-size: 0.9375rem;
        width: 100%;
    }
    .donate-amount-field {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        border-radius: 0.875rem;
        border: 1px solid rgb(12 90 46 / 0.15);
        background: rgb(244 238 225 / 0.35);
        padding: 0.55rem 0.85rem;
    }
    .donate-amount-field__symbol {
        flex-shrink: 0;
        font-size: 0.9375rem;
        font-weight: 600;
        color: rgb(12 90 46 / 0.55);
        line-height: 1;
    }
    .donate-input--amount {
        flex: 1;
        min-width: 0;
        width: auto;
        border: 0;
        background: transparent;
        padding: 0;
        box-shadow: none;
    }
    .donate-amount-field:focus-within {
        border-color: #0c5a2e;
        box-shadow: 0 0 0 2px rgb(12 90 46 / 0.12);
    }
    .donate-input--amount:focus {
        border-color: transparent;
        box-shadow: none;
    }
    .donate-input:focus {
        outline: none;
        border-color: #0c5a2e;
        box-shadow: 0 0 0 2px rgb(12 90 46 / 0.12);
    }
    .donate-amount-btn.is-active {
        border-color: #0c5a2e;
        background: rgb(12 90 46 / 0.08);
        color: #0c5a2e;
    }
    .donate-method-card:has(:checked) {
        border-color: #0c5a2e;
        background: rgb(12 90 46 / 0.05);
    }
</style>
