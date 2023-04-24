<?php

return [

    /**
     * How the payment gets deducted
     *
     * Options:
     * 1. IGNORE
     * 2. INSTALLMENT_FIRST
     * 3. PAYMENT_FIRST
     */
    'installment_pay_first' => env('INSTALLMENT_FIRST_PAYMENT', 'IGNORE'),

];
