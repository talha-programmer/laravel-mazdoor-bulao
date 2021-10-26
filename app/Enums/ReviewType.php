<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class ReviewType extends Enum
{
    const FromBuyerToWorker =   0;
    const FromWorkerToBuyer =   1;
}
