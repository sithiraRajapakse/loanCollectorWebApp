<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanInstallmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_installments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_id');
            $table->unsignedInteger('index_number');
            $table->date('due_date');
            $table->unsignedDecimal('installment_amount', 10, 2);
            $table->unsignedDecimal('interest_amount', 10, 2);
            $table->timestamp('paid_at')->nullable();
            $table->decimal('arrears_amount', 10, 2)->default(0.0)->nullable();
            $table->unsignedBigInteger('collector_id')->nullable();
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
        Schema::dropIfExists('loan_installments');
    }
}
