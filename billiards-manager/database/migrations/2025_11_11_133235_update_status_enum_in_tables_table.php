<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Cập nhật ENUM trong cột status
        DB::statement("
            ALTER TABLE tables 
            MODIFY COLUMN status ENUM(
                'available',
                'paused',
                'occupied',
                'maintenance',
                'reserved',
                'quick'
            ) NOT NULL DEFAULT 'available'
        ");
    }

    public function down(): void
    {
        // Rollback lại nếu cần (loại bỏ quick và paused)
        DB::statement("
            ALTER TABLE tables 
            MODIFY COLUMN status ENUM(
                'available',
                'occupied',
                'maintenance',
                'reserved'
            ) NOT NULL DEFAULT 'available'
        ");
    }
};
