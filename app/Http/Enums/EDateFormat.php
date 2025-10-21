<?php

namespace App\Http\Enums;

enum EDateFormat: string
{
    /**
     * Y-m-d
     */
    case Ymd = "Y-m-d";
    case YmdHis = "Y-m-d H:i:s";
    case Ymdhisa = "Y-m-d h:i:s a";
    case Hia = "H:i a";
    case His = "H:i:s";
    case hisa = "h:i:s a";
    case YmdhisA = "Y-m-d h:i A";
}
