<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class JobStatus extends Enum
{
    const AcceptingBids =   0;
    const WorkInProgress =   1;     // One or more persons are hired for this job
    const Completed = 2;
}
