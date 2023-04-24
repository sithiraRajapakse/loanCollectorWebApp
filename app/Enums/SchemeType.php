<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @var static static DAILY()
 * @var static static WEEKLY()
 * @var static static MONTHLY()
 * @var static static BI_WEEKLY()
 * @var static static CUSTOM()
 */
final class SchemeType extends Enum
{
    const DAILY = 'DAILY';
    const WEEKLY = 'WEEKLY';
    const MONTHLY = 'MONTHLY';
    const BI_WEEKLY = 'BI_WEEKLY';
    const CUSTOM = 'CUSTOM';
}
