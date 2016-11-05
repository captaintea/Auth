<?php

namespace App\Exceptions;


use App\Services\AuthService;

class PasswordException extends AuthException implements AuthExceptionInterface
{

    /**
     * Returns default error message
     * @return string
     */
    public function getDefaultMessage()
    {
        return 'Password must contain more then '
        .AuthService::PASSWORD_MIN_LENGTH.' and less then '.AuthService::PASSWORD_MAX_LENGTH.' characters';
    }
}
