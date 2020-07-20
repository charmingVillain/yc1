<?php

namespace App\Exceptions;

use App\SimpleResponse;
use Exception;

class NotificationException extends Exception
{

    public function render()
    {
        return SimpleResponse::error($this->getMessage());
    }
}
