<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
 public function up()
    {
        Schema::create('promotion_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bill_id');
            $table->unsignedBigInteger('promotion_id');
            $table->decimal('applied_discount', 10, 2);
            $table->timestamps();

            $table->foreign('bill_id')->references('id')->on('bills');
            $table->foreign('promotion_id')->references('id')->on('promotions');
            
            $table->unique(['bill_id', 'promotion_id']);
            $table->index('bill_id');
            $table->index('promotion_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('promotion_applications');
    }
};
