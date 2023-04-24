<?php

use Illuminate\Http\Request;

Route::post('login', 'Api\ApiController@login')->name('collector-app.login');
Route::middleware('auth:sanctum')->post('logout', 'Api\ApiController@logout')->name('collector-app.logout');
Route::middleware('auth:sanctum')->get('user', 'Api\ApiController@user')->name('collector-app.user');

Route::middleware('auth:sanctum')->get('customers', 'Api\ApiController@getCustomers')->name('collector-app.customers');
Route::middleware('auth:sanctum')->get('customers/find/{id}', 'Api\ApiController@getCustomerById')->name('collector-app.customers.find');
Route::middleware('auth:sanctum')->get('customers/{id}/loans', 'Api\ApiController@getLoansForCustomer')->name('collector-app.customers.loans.list');
Route::middleware('auth:sanctum')->get('customer/{id}/today-payment', 'Api\ApiController@getPayableTotalForTodayByCustomerId')->name('collector-app.customer.today-payment');

Route::middleware('auth:sanctum')->get('loans/{id}', 'Api\ApiController@getLoanById')->name('collector-app.loans.find');
Route::middleware('auth:sanctum')->get('loans/{id}/installments', 'Api\ApiController@getInstallmentsForLoan')->name('collector-app.loans.installments');
Route::middleware('auth:sanctum')->get('loans/{id}/payable-installment', 'Api\ApiController@getNextPayableInstallmentForLoan')->name('collector-app.loans.payable-installment');
Route::middleware('auth:sanctum')->get('loans/{id}/summary', 'Api\ApiController@getLoanSummaryById')->name('collector-app.loans.summary-by-id');
Route::middleware('auth:sanctum')->post('loans/installment/{id}/pay', 'Api\ApiController@setPaymentForInstallment')->name('collector-app.loans.installment.pay');
Route::middleware('auth:sanctum')->post('loans/{id}/pay-amount', 'Api\ApiController@createMonthlyLoanPayment')->name('collector-app.loans.loan-pay-amount');