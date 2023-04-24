<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLoanInstallmentsArrears extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan_installments', function (Blueprint $table) {
            $table->unsignedDecimal('arrears_settlement_amount')->default(0.00)->nullable();
            $table->timestamp('arrears_settled_at')->nullable();
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
            $table->dropColumn('arrears_settlement_amount');
            $table->dropColumn('arrears_settled_at');
        });
    }
}
