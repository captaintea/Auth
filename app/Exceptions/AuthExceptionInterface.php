<?php

namespace App\Exceptions;

interface AuthExceptionInterface {
    
    /**
     * Returns default error message
     * @return string
     */
    public function getDefaultMessage();
    
}