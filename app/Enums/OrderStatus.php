<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class OrderStatus extends Enum
{
    const Started =   0;
    const RequestedForCompletion = 1;
    const  Completed =   2;
    const Failed = 3;
}
