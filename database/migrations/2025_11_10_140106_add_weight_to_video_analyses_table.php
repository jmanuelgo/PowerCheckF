<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('video_analyses', function (Blueprint $t) {
            $t->decimal('weight', 5, 2)->nullable()->after('analyzed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('video_analyses', function (Blueprint $t) {
            $t->dropColumn('weight');
        });
    }
};
