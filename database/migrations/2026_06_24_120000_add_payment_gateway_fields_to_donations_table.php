<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table): void {
            if (! Schema::hasColumn('donations', 'gateway')) {
                $table->string('gateway', 32)->nullable()->after('payment_method');
            }

            if (! Schema::hasColumn('donations', 'gateway_transaction_id')) {
                $table->string('gateway_transaction_id')->nullable()->after('gateway');
            }

            if (! Schema::hasColumn('donations', 'gateway_reference')) {
                $table->string('gateway_reference')->nullable()->after('gateway_transaction_id');
            }

            if (! Schema::hasColumn('donations', 'metadata')) {
                $table->json('metadata')->nullable()->after('gateway_reference');
            }

            if (! Schema::hasColumn('donations', 'refunded_at')) {
                $table->timestamp('refunded_at')->nullable()->after('paid_at');
            }
        });

        if (Schema::hasColumn('donations', 'stripe_checkout_session_id')) {
            foreach (DB::table('donations')->get() as $donation) {
                $updates = [];

                if ($donation->payment_method === 'stripe') {
                    $updates['payment_method'] = 'stripe_card';
                    $updates['gateway'] = 'stripe';
                    $updates['gateway_reference'] = $donation->stripe_checkout_session_id;
                    $updates['gateway_transaction_id'] = $donation->stripe_payment_intent_id;
                }

                if ($donation->payment_method === 'bank_transfer') {
                    $updates['gateway'] = 'bank_transfer';
                }

                if ($donation->status === 'awaiting_transfer') {
                    $updates['status'] = 'pending';
                }

                if ($updates !== []) {
                    DB::table('donations')->where('id', $donation->id)->update($updates);
                }
            }
        }

        Schema::table('donations', function (Blueprint $table): void {
            if (Schema::hasColumn('donations', 'stripe_checkout_session_id')) {
                $table->dropIndex(['stripe_checkout_session_id']);
            }

            if (Schema::hasColumn('donations', 'stripe_payment_intent_id')) {
                $table->dropIndex(['stripe_payment_intent_id']);
            }
        });

        Schema::table('donations', function (Blueprint $table): void {
            $columnsToDrop = array_filter([
                Schema::hasColumn('donations', 'stripe_checkout_session_id') ? 'stripe_checkout_session_id' : null,
                Schema::hasColumn('donations', 'stripe_payment_intent_id') ? 'stripe_payment_intent_id' : null,
                Schema::hasColumn('donations', 'confirmed_at') ? 'confirmed_at' : null,
            ]);

            if ($columnsToDrop !== []) {
                $table->dropColumn($columnsToDrop);
            }

            if (! $this->indexExists('donations', 'donations_gateway_reference_index')) {
                $table->index('gateway_reference');
            }

            if (! $this->indexExists('donations', 'donations_gateway_transaction_id_index')) {
                $table->index('gateway_transaction_id');
            }
        });

        if (! Schema::hasTable('payment_gateway_events')) {
            Schema::create('payment_gateway_events', function (Blueprint $table): void {
                $table->id();
                $table->string('gateway', 32);
                $table->string('event_id');
                $table->string('event_type')->nullable();
                $table->foreignId('donation_id')->nullable()->constrained('donations')->nullOnDelete();
                $table->string('status', 32)->default('processed');
                $table->json('payload')->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->timestamps();

                $table->unique(['gateway', 'event_id']);
                $table->index(['donation_id', 'gateway']);
            });
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection();

        if ($connection->getDriverName() === 'sqlite') {
            $indexes = $connection->select("PRAGMA index_list('{$table}')");

            foreach ($indexes as $index) {
                if (($index->name ?? '') === $indexName) {
                    return true;
                }
            }

            return false;
        }

        $indexes = $connection->select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);

        return $indexes !== [];
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_gateway_events');

        Schema::table('donations', function (Blueprint $table): void {
            $table->string('stripe_checkout_session_id')->nullable();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->timestamp('confirmed_at')->nullable();
        });

        foreach (DB::table('donations')->get() as $donation) {
            $updates = [];

            if ($donation->payment_method === 'stripe_card') {
                $updates['payment_method'] = 'stripe';
                $updates['stripe_checkout_session_id'] = $donation->gateway_reference;
                $updates['stripe_payment_intent_id'] = $donation->gateway_transaction_id;
            }

            if ($updates !== []) {
                DB::table('donations')->where('id', $donation->id)->update($updates);
            }
        }

        Schema::table('donations', function (Blueprint $table): void {
            $table->dropIndex(['gateway_reference']);
            $table->dropIndex(['gateway_transaction_id']);
            $table->dropColumn(['gateway', 'gateway_transaction_id', 'gateway_reference', 'metadata', 'refunded_at']);
        });
    }
};
