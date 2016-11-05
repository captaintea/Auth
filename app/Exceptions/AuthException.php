<?php

namespace App\Exceptions;


class AuthException extends BaseException implements AuthExceptionInterface
{

    /**
     * Returns default error message
     * @return string
     */
    public function getDefaultMessage()
    {
        return empty($this->getMessage()) ? 'Auth error' : $this->getMessage();
    }
}
