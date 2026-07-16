<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->where('key', 'site.use_dynamic_homepage')->delete();
    }

    public function down(): void
    {
        // Removed setting — no restore.
    }
};
