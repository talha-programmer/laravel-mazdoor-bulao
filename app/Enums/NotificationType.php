<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class NotificationType extends Enum
{
    const MessageNotification =   0;
    const OrderNotification =   1;
}
