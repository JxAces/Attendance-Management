<?php

namespace App\Enum;

enum AttendanceLevel: int
{
    case Free = 0;
    case Present = 1;
    case Late = 2;
    case Excuse = 4;
    case Absent = 5;
    case EC = 6;
}