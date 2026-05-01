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
        Schema::table('assistance_deliveries', function (Blueprint $table): void {
            $table->foreignId('supplier_id')->nullable()->after('assistance_schedule_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('assistance_deliveries', function (Blueprint $table): void {
            $table->dropForeign(['supplier_id']);
            $table->dropColumn('supplier_id');
        });
    }
};
