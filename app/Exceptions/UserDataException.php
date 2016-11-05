<?php

namespace App\Exceptions;


class UserDataException extends BaseException implements AuthExceptionInterface
{

    /**
     * Returns default error message
     * @return string
     */
    public function getDefaultMessage()
    {
        return 'Wrong user data';
    }
}
