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
        Schema::create('uploads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->morphs('uploadsable');
            $table->string('file_path', 255);
            $table->string('original_file_name', 255)->nullable();
            $table->string('type', 255)->comment('media type')->nullable();
            $table->string('file_type', 255)->comment('')->nullable();
            $table->string('extension', 255)->nullable();
            $table->string('orientation', 255)->default(NULL)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uploads');
    }
};
