<?php

namespace App\Exceptions;

class IpAddressException extends AuthException implements AuthExceptionInterface
{

    /**
     * Returns default error message
     * @return string
     */
    public function getDefaultMessage()
    {
        return 'Account has already been created from your ip address';
    }
}
