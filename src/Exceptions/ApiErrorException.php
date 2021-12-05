<?php

namespace Bluex\SocketIo\Exceptions;

/**
 * HTTP error responses.
 * getCode() will return the response HTTP status code,
 * and getMessage() will return the response body.
 */
class ApiErrorException extends SocketIoException
{
    /**
     * Returns the string representation of the exception.
     *
     * @return string
     */
    public function __toString()
    {
        return "(Status {$this->getCode()}) {$this->getMessage()}";
    }
}
