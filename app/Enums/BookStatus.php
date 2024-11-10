<?php

namespace App\Enums;

enum BookStatus : string
{

    case AVAILABLE = 'available';
    case CHECKED_OUT = 'checked-out';
}
