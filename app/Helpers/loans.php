<?php

use App\Enums\SchemeType;

if (!function_exists('installmentTypes')) {
    function installmentTypes()
    {
        return [
            SchemeType::DAILY => 'Daily Installments',
            SchemeType::WEEKLY => 'Weekly Installments',
            SchemeType::MONTHLY => 'Monthly Installments',
            SchemeType::CUSTOM => 'Customized Installments',
        ];
    }
}


