<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', 'HomeController@index')->name('home');
Auth::routes(['register' => false]);

Route::get('customers', 'CustomerController@index')->name('customers');
Route::get('customers/new', 'CustomerController@register')->name('customers.register');
Route::get('customers/edit/{id}', 'CustomerController@edit')->name('customers.edit');

Route::post('customers/process-registration', 'CustomerController@processRegistration')->name('customers.process-registration');
Route::post('customers/process-update/{id}', 'CustomerController@processUpdate')->name('customers.process-update');
Route::post('customers/process-delete/{id}', 'CustomerController@processDelete')->name('customers.process-delete');
Route::post('customers/process-upload/{id}', 'CustomerController@processDocumentUpload')->name('customers.process-document-upload');

Route::post('customer/document/lock/{id}', 'CustomerController@processLockingDocument')->name('customers.lock-document');
Route::post('customer/document/unlock/{id}', 'CustomerController@processUnlockingDocument')->name('customers.unlock-document');
Route::post('customer/document/delete/{id}', 'CustomerController@processDeleteDocument')->name('customers.delete-document');

Route::get('customer/list/json', 'CustomerController@getCustomersListJSON')->name('customers.list.json');
Route::get('customer/json', 'CustomerController@getCustomerJSON')->name('customer.json');

Route::get('collectors', 'CollectorController@index')->name('collectors');
Route::get('collectors/edit/{id}', 'CollectorController@edit')->name('collectors.edit');
Route::post('collectors', 'CollectorController@create')->name('collectors.create');
Route::post('collectors/edit/{id}', 'CollectorController@update')->name('collectors.update');
Route::post('collectors/delete/{id}', 'CollectorController@delete')->name('collectors.delete');
Route::post('collectors/update-user/{id}', 'CollectorController@updateUserAccount')->name('collectors.update-user');

Route::get('loans/schemes', 'SchemeController@index')->name('loans.schemes');
Route::post('loans/schemes', 'SchemeController@create')->name('loans.schemes.create');
Route::get('loans/schemes/{id}', 'SchemeController@edit')->name('loans.schemes.edit');
Route::post('loans/schemes/{id}', 'SchemeController@update')->name('loans.schemes.update');
Route::post('loans/schemes/delete/{id}', 'SchemeController@delete')->name('loans.schemes.delete');
Route::get('scheme/find/json', 'SchemeController@getSchemeDetailsByIdJson')->name('scheme.find.json');

Route::post('keep-live', 'SkoteController@live');

Route::get('users/index', 'UserController@index')->name('users.index');
Route::post('users/create', 'UserController@create')->name('users.create');

# customer loans
Route::get('customer-loans/list', 'CustomerLoanController@index')->name('customer-loans.list');

# get
Route::get('loans', 'LoanController@index')->name('loans.register');

# get
Route::get('loan/{id}/installments/edit', 'LoanInstallmentController@index')->name('loan.installments.edit');
Route::get('loan/{id}/installments/list/json', 'LoanInstallmentController@listJson')->name('loan.installments.list');
Route::get('loan/installment/find/json', 'LoanInstallmentController@findInstallment')->name('loan.installment.find');
Route::get('loan/installments/payable-installment/json', 'LoanInstallmentController@getFirstPayableInstallment')->name('loan.installment.payable-installment');
Route::get('loan/installments/payment/installment/json', 'LoanInstallmentController@getPayableInstallmentByIdJSON')->name('loan.installment.installment-details');
# post
Route::post('loan/{id}/installments/add', 'LoanInstallmentController@addInstallment')->name('loan.installments.add');
Route::post('loan/installment/delete/json', 'LoanInstallmentController@deleteInstallment')->name('loan.installment.delete');
Route::post('loan/installment/payment/json', 'LoanInstallmentController@receivePayment')->name('loan.installment.payment');
Route::post('loan/installment/payment/reverse', 'LoanInstallmentController@reverseInstallmentPayment')->name('loan.installment.payment.reverse');

# daily
# get
Route::get('customer-loans/daily', 'CustomerLoanController@dailyLoanView')->name('customer-loans.daily');
Route::get('customer-loans/installments/daily', 'CustomerLoanController@getInstallmentsJSON')->name('customer-loans.installments.daily');
# post
Route::post('customer-loans/daily', 'CustomerLoanController@createDailyLoan')->name('customer-loans.daily.create');

# weekly
# get
Route::get('customer-loans/weekly', 'CustomerLoanController@weeklyLoanView')->name('customer-loans.weekly');
Route::get('customer-loans/installments/weekly', 'CustomerLoanController@getInstallmentsJSON')->name('customer-loans.installments.weekly');
# post
Route::post('customer-loans/weekly', 'CustomerLoanController@createWeeklyLoan') ->name('customer-loans.weekly.create');

# monthly
# get
Route::get('customer-loans/monthly', 'CustomerLoanController@monthlyLoanView')->name('customer-loans.monthly');
# post
Route::post('customer-loans/monthly', 'CustomerLoanController@createMonthlyLoan') ->name('customer-loans.monthly.create');
Route::post('customer-loans/{id}/payment/monthly', 'LoanInstallmentController@createMonthlyLoanPayment') ->name('customer-loans.payments.monthly.create');

# bi-weekly
# get
Route::get('customer-loans/bi-weekly', 'CustomerLoanController@biWeeklyLoanView')->name('customer-loans.bi-weekly');
Route::post('customer-loans/bi-weekly', 'CustomerLoanController@createBiWeelyLoan')->name('customer-loans.bi-weekly.create');

# get
Route::get('customer-loans/details/{id}', 'CustomerLoanController@getLoanDetailsJson')->name('customer-loans.details-loan');

# loan installments

# settings
# holiday calendar
Route::get('settings/holidays', 'HolidayController@index')->name('settings.holidays');
Route::post('settings/holidays', 'HolidayController@create')->name('settings.holidays.create');
Route::get('settings/holidays/{id}', 'HolidayController@edit')->name('settings.holidays.edit');
Route::post('settings/holidays/{id}', 'HolidayController@update')->name('settings.holidays.update');
Route::post('settings/holidays/delete/{id}', 'HolidayController@delete')->name('settings.holidays.delete');
Route::post('settings/holidays/calendar/select-month', 'HolidayController@selectCalendarMonth')->name('settings.holidays.calendar.select-month');

# reports
Route::get('reports/index', 'ReportsController@index')->name('reports.index');
Route::get('reports/customers/{type}', 'ReportsController@customers')->name('reports.customers');
Route::get('reports/loans-list/{type}', 'ReportsController@loanList')->name('reports.loan-list');
Route::get('reports/loans-list/scheme/{type}', 'ReportsController@loanListByScheme')->name('reports.loan-list-scheme');
