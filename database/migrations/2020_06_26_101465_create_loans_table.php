<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('loan_number')->unique();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('scheme_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedDecimal('loan_amount', 10, 2);
            $table->unsignedDecimal('interest_rate', 5, 2);
            $table->unsignedDecimal('interest_total', 10, 2);
            $table->unsignedDecimal('installment_amount', 10, 2);
            $table->unsignedInteger('no_of_installments');
            $table->enum('last_installment_type', [\App\Enums\LastInstallmentType::SINGLE, \App\Enums\LastInstallmentType::ADD_TO_PREVIOUS]);
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('completed_by_id')->nullable();
            $table->boolean('is_extended')->default(false);
            $table->date('extended_date')->nullable();
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
        Schema::dropIfExists('loans');
    }
}
