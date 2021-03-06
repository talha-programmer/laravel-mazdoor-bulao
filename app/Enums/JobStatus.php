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
    const Hiring =   0;
    const HiringCompleted =   1;     // One or more persons are hired for this job
    const JobCompleted = 2;
}
