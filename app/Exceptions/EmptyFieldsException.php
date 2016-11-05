<?php

namespace App\Exceptions;


class EmptyFieldsException extends AuthException implements AuthExceptionInterface
{

    /**
     * Returns default error message
     * @return string
     */
    public function getDefaultMessage()
    {
        return 'All fields must be filled';
    }
}
