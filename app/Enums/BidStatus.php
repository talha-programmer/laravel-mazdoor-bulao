<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class BidStatus extends Enum
{
    const Applied =   0;
    const Accepted =   1;       // Accepted by the Job provider
    const Rejected = 2;
    const Completed = 3;        // The work offered through this bid is completed
}
