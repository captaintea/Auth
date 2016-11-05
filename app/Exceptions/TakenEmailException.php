<?php

namespace App\Exceptions;


class TakenEmailException extends AuthException
{

    /**
     * Returns default error message
     * @return string
     */
    public function getDefaultMessage()
    {
        return 'This email is taken';
    }
}
