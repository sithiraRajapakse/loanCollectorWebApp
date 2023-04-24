<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablesAddForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('organization_id')->references('id')->on('organizations');
        });
        Schema::table('collectors', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
        });
        Schema::table('capital_loans', function (Blueprint $table) {
            $table->foreign('organization_id')->references('id')->on('organizations');
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->foreign('organization_id')->references('id')->on('organizations');
        });
        Schema::table('customer_documents', function (Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('customers');
        });
        Schema::table('loans', function (Blueprint $table) {
            $table->foreign('scheme_id')->references('id')->on('schemes');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('created_by_id')->references('id')->on('users');
            $table->foreign('completed_by_id')->references('id')->on('users');
        });
        Schema::table('loan_installments', function (Blueprint $table) {
            $table->foreign('loan_id')->references('id')->on('loans');
            $table->foreign('collector_id')->references('id')->on('collectors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
        });
        Schema::table('collectors', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::table('capital_loans', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
        });
        Schema::table('customer_documents', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
        });
        Schema::table('loans', function (Blueprint $table) {
            $table->dropForeign(['scheme_id']);
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['created_by_id']);
            $table->dropForeign(['completed_by_id']);
        });
        Schema::table('loan_installments', function (Blueprint $table) {
            $table->dropForeign(['loan_id']);
            $table->dropForeign(['collector_id']);
        });
    }
}
