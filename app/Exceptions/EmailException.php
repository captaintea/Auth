<?php

namespace App\Exceptions;

class EmailException extends AuthException implements AuthExceptionInterface
{

    /**
     * Returns default error message
     * @return string
     */
    public function getDefaultMessage()
    {
        return 'Invalid email';
    }
}
