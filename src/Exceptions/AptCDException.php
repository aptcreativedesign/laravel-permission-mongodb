<?php

namespace AptCD\Permission\Exceptions;

use InvalidArgumentException;
use Throwable;

/**
 * Class AptCDException
 * @package AptCD\Permission\Exceptions
 */
class AptCDException extends InvalidArgumentException
{
    /**
     * AptCDException constructor.
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = null, int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        if (\config('permission.log_registration_exception')) {
            $logger = \app('log');
            $logger->alert($message);
        }
    }
}
