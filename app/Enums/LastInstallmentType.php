<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @var static static SINGLE()
 * @var static static ADD_TO_PREVIOUS()
 */
final class LastInstallmentType extends Enum
{
    const SINGLE = 'SINGLE';
    const ADD_TO_PREVIOUS = 'ADD_TO_PREVIOUS';
}
