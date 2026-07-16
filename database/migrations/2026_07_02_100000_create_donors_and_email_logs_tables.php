<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donors', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone', 40)->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->decimal('total_donated', 12, 2)->default(0);
            $table->unsignedInteger('donations_count')->default(0);
            $table->timestamp('last_donation_at')->nullable();
            $table->string('locale', 8)->default('en');
            $table->string('status', 16)->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('last_donation_at');
        });

        Schema::create('email_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('donor_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('donation_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 64);
            $table->string('recipient');
            $table->string('subject');
            $table->string('status', 16)->default('queued');
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['donation_id', 'type', 'status']);
            $table->index(['donor_id', 'created_at']);
        });

        Schema::table('donations', function (Blueprint $table): void {
            $table->foreignId('donor_id')->nullable()->after('campaign_id')->constrained()->nullOnDelete();
        });

        $this->backfillDonors();
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('donor_id');
        });

        Schema::dropIfExists('email_logs');
        Schema::dropIfExists('donors');
    }

    private function backfillDonors(): void
    {
        if (! Schema::hasTable('donations')) {
            return;
        }

        $donations = DB::table('donations')
            ->whereNotNull('donor_email')
            ->orderBy('id')
            ->get();

        $donorIdsByEmail = [];

        foreach ($donations as $donation) {
            $email = strtolower(trim((string) $donation->donor_email));

            if ($email === '') {
                continue;
            }

            if (! isset($donorIdsByEmail[$email])) {
                $donorIdsByEmail[$email] = DB::table('donors')->insertGetId([
                    'name' => $donation->donor_name,
                    'email' => $email,
                    'phone' => $donation->donor_phone,
                    'is_anonymous' => (bool) $donation->is_anonymous,
                    'locale' => $donation->locale ?? 'en',
                    'status' => 'active',
                    'created_at' => $donation->created_at,
                    'updated_at' => $donation->updated_at,
                ]);
            } else {
                DB::table('donors')->where('id', $donorIdsByEmail[$email])->update([
                    'name' => $donation->donor_name,
                    'is_anonymous' => (bool) $donation->is_anonymous,
                    'updated_at' => now(),
                    ...($donation->donor_phone ? ['phone' => $donation->donor_phone] : []),
                ]);
            }

            DB::table('donations')->where('id', $donation->id)->update([
                'donor_id' => $donorIdsByEmail[$email],
            ]);
        }

        foreach ($donorIdsByEmail as $email => $donorId) {
            $stats = DB::table('donations')
                ->where('donor_id', $donorId)
                ->where('status', 'paid')
                ->selectRaw('COUNT(*) as donations_count, COALESCE(SUM(amount), 0) as total_donated, MAX(paid_at) as last_donation_at')
                ->first();

            DB::table('donors')->where('id', $donorId)->update([
                'donations_count' => (int) ($stats->donations_count ?? 0),
                'total_donated' => (float) ($stats->total_donated ?? 0),
                'last_donation_at' => $stats->last_donation_at,
            ]);
        }
    }
};
