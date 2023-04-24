<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static ADMINISTRATOR()
 * @method static static SYSTEM_USER()
 * @method static static COLLECTOR()
 */
final class UserType extends Enum
{
    const ADMINISTRATOR = 'ADMINISTRATOR';
    const SYSTEM_USER = 'SYSTEM_USER';
    const COLLECTOR = 'COLLECTOR';
}
