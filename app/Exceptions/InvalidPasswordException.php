<?php

namespace App\Exceptions;


class InvalidPasswordException extends AuthException implements AuthExceptionInterface
{

    /**
     * Returns default error message
     * @return string
     */
    public function getDefaultMessage()
    {
        return 'Invalid password';
    }
}
