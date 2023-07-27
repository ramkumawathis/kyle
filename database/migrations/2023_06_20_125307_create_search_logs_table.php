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
        Schema::create('search_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');

            $table->string('address')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('zip_code')->nullable();

            $table->double('price')->nullable();

            $table->integer('bedroom_min')->nullable();
            $table->integer('bedroom_max')->nullable();
            $table->integer('bath_min')->nullable();
            $table->integer('bath_max')->nullable();
            $table->integer('size_min')->nullable();
            $table->integer('size_max')->nullable();
            $table->integer('lot_size_min')->nullable();
            $table->integer('lot_size_max')->nullable();
            $table->integer('build_year_min')->nullable();
            $table->integer('build_year_max')->nullable();            
            $table->integer('arv_min')->nullable();
            $table->integer('arv_max')->nullable();

            $table->integer('parking')->nullable();
            $table->integer('property_type');
            $table->json('property_flaw')->nullable();

            $table->tinyInteger('solar')->nullable();
            $table->tinyInteger('pool')->nullable();
            $table->tinyInteger('septic')->nullable();
            $table->tinyInteger('well')->nullable();
            $table->tinyInteger('age_restriction')->nullable();
            $table->tinyInteger('rental_restriction')->nullable();
            $table->tinyInteger('hoa')->nullable();
            $table->tinyInteger('tenant')->nullable();
            $table->tinyInteger('post_possession')->nullable();
            $table->tinyInteger('building_required')->nullable();
            $table->tinyInteger('foundation_issues')->nullable();
            $table->tinyInteger('mold')->nullable();
            $table->tinyInteger('fire_damaged')->nullable();
            $table->tinyInteger('rebuild')->nullable();
            $table->tinyInteger('squatters')->nullable();

            $table->json('purchase_method');

            $table->double('max_down_payment_percentage', 15, 2)->nullable();
            $table->double('max_down_payment_money', 15, 2)->nullable();
            $table->double('max_interest_rate', 15, 2)->nullable();
            $table->tinyInteger('balloon_payment')->nullable();

            $table->integer('total_units')->nullable();

            $table->integer('unit_min')->nullable();
            $table->integer('unit_max')->nullable();
            $table->json('building_class')->nullable();
            $table->tinyInteger('value_add')->nullable();

            $table->boolean('status')->default(1)->comment('0=> deactive, 1=> active');
            $table->unsignedBigInteger('created_by');
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
        Schema::dropIfExists('search_logs');
    }
};
