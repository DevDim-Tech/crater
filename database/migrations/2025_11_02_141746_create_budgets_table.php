<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBudgetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->increments('id');
            $table->date('budget_date');
            $table->date('expiry_date')->nullable();
            $table->string('budget_number');
            $table->string('reference_number')->nullable();
            $table->string('status'); // draft, sent, viewed, expired, accepted, rejected
            $table->text('notes')->nullable();

            // AI Integration fields
            $table->boolean('ai_generated')->default(false);
            $table->json('ai_context')->nullable();
            $table->text('ai_description')->nullable();
            $table->string('industry')->nullable();
            $table->string('timeframe')->nullable();

            // Financial fields (stored as integers in cents)
            $table->string('tax_per_item');
            $table->string('discount_per_item');
            $table->string('discount_type')->nullable();
            $table->decimal('discount', 15, 2)->nullable();
            $table->unsignedBigInteger('discount_val')->nullable();
            $table->unsignedBigInteger('sub_total');
            $table->unsignedBigInteger('total');
            $table->unsignedBigInteger('tax');

            // Status tracking
            $table->boolean('sent')->default(false);
            $table->boolean('viewed')->default(false);
            $table->string('unique_hash')->nullable();

            // Foreign keys
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->integer('company_id')->unsigned()->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            $table->integer('creator_id')->unsigned()->nullable();
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');

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
        Schema::dropIfExists('budgets');
    }
}
