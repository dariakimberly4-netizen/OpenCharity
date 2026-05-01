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
        Schema::table('family_members', function (Blueprint $table): void {
            $table->dropIndex(['national_id']);
            $table->unique('national_id');
        });
    }

    public function down(): void
    {
        Schema::table('family_members', function (Blueprint $table): void {
            $table->dropUnique(['national_id']);
            $table->index('national_id');
        });
    }
};
