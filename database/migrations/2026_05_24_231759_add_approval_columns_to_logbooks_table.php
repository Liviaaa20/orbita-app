<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logbooks', function (Blueprint $table) {
            $table->enum('status', [
                'draft',
                'pending_kanit',
                'approved_kanit',
                'rejected_kanit',
                'pending_koordinator',
                'approved_final',
                'rejected_koordinator',
            ])->default('draft')->after('terakhir_diperbarui');

            // Kanit
            $table->foreignId('approved_kanit_by')
                  ->nullable()->after('status')
                  ->constrained('users')->onDelete('set null');
            $table->timestamp('approved_kanit_at')->nullable()->after('approved_kanit_by');
            $table->text('catatan_kanit')->nullable()->after('approved_kanit_at');

            // Koordinator
            $table->foreignId('approved_koordinator_by')
                  ->nullable()->after('catatan_kanit')
                  ->constrained('users')->onDelete('set null');
            $table->timestamp('approved_koordinator_at')->nullable()->after('approved_koordinator_by');
            $table->text('catatan_koordinator')->nullable()->after('approved_koordinator_at');
        });
    }

    public function down(): void
    {
        Schema::table('logbooks', function (Blueprint $table) {
            $table->dropForeign(['approved_kanit_by']);
            $table->dropForeign(['approved_koordinator_by']);
            $table->dropColumn([
                'status',
                'approved_kanit_by',
                'approved_kanit_at',
                'catatan_kanit',
                'approved_koordinator_by',
                'approved_koordinator_at',
                'catatan_koordinator',
            ]);
        });
    }
};