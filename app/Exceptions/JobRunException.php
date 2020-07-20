<?php

namespace App\Exceptions;

use App\SimpleResponse;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class JobRunException extends NotificationException
{

    protected $job;

    public function __construct(ShouldQueue $job, $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->job = $job;
    }


    /**
     * @return ShouldQueue
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * 记录一下日志
     * @return SimpleResponse|void
     */
    public function render()
    {
        Log::channel('job')->warning(get_class($this->getJob()).":".$this->getMessage());
        return parent::render();
    }

}
