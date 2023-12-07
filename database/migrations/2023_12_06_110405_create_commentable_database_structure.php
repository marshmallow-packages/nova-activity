<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('nova-commentable.table_name'), function (Blueprint $table) {
            $table->id();
            $table->morphs('nova_commentable');
            $table->unsignedBigInteger('user_id');
            $table->string('type_key')->nullable()->default(null);
            $table->string('type_label')->nullable()->default(null);
            $table->text('comment')->nullable()->default(null);
            $table->boolean('is_starred')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_hidden')->default(false);
            $table->boolean('is_resolved')->default(false);
            $table->json('meta')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('nova-commentable.table_name'));
    }
};
