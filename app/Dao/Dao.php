<?php

namespace App\Dao;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;

class Dao
{
    protected static function db()
    {
        return DriverManager::getConnection(config('database.connections.mysql'), new Configuration());
    }

}
