<?php

namespace App\Exceptions;

class SMException extends \Exception {

    public const smCode = 977;

    public function __construct($smMessage) {
        $this->message = $smMessage;
        $this->code = self::smCode;
        parent::__construct();
    }
}
