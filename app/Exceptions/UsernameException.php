<?php

namespace App\Exceptions;


use App\Services\AuthService;

class UsernameException extends AuthException implements AuthExceptionInterface
{

    /**
     * Returns default error message
     * @return string
     */
    public function getDefaultMessage()
    {
        return 'Name must contain more then '.
        AuthService::USERNAME_MIN_LENGTH.' and less then '.AuthService::USERNAME_MAX_LENGTH.' characters';
    }
}
