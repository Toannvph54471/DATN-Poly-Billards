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
        Schema::table('tables', function (Blueprint $table) {
            // Kiểm tra nếu tồn tại thì mới xóa
            if (Schema::hasColumn('tables', 'category_id')) {
                $table->dropForeign(['category_id']); // Nếu có khóa ngoại
                $table->dropColumn('category_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
             // Khôi phục lại cột nếu rollback
            $table->unsignedBigInteger('category_id')->nullable()->after('id');

            // Nếu cần, thêm lại foreign key
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }
};
