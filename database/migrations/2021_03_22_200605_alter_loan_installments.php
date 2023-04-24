<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLoanInstallments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_installments', function (Blueprint $table) {
            $table->unsignedDecimal('paid_amount', 10, 2)->default(0.00);
        });
        Schema::table('loans', function (Blueprint $table) {
            $table->decimal('total_arrears', 10, 2)->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loan_installments', function (Blueprint $table) {
            $table->dropColumn('paid_amount');
        });
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn('total_arrears');
        });
    }
}
