<?php

namespace App\Exceptions;


class PasswordConfirmException extends AuthException implements AuthExceptionInterface
{

    /**
     * Returns default error message
     * @return string
     */
    public function getDefaultMessage()
    {
        return 'Wrong password confirmation';
    }
}
