<?php

namespace App\Exceptions;

class UserNotFoundException extends AuthException implements AuthExceptionInterface
{

    /**
     * Returns default error message
     * @return string
     */
    public function getDefaultMessage()
    {
        return 'User not found';
    }
}
