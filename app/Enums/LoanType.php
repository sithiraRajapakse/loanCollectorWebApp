<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static DAILY()
 * @method static static WEEKLY()
 * @method static static MONTHLY()
 * @method static static CUSTOM()
 */
final class LoanType extends Enum
{
    const DAILY = 'DAILY';
    const WEEKLY = 'WEEKLY';
    const MONTHLY = 'MONTHLY';
    const CUSTOM = 'CUSTOM';
}
