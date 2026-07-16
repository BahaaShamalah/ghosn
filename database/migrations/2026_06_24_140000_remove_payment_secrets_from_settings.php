<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Payment secrets must live in .env only — remove any legacy DB copies.
     */
    public function up(): void
    {
        DB::table('settings')->whereIn('key', [
            'payments.stripe_public_key',
            'payments.stripe_secret_key',
            'payments.stripe_webhook_secret',
            'payments.paypal_client_id',
            'payments.paypal_client_secret',
            'payments.paypal_webhook_id',
        ])->delete();
    }

    public function down(): void
    {
        // Secrets are not restored on rollback.
    }
};
