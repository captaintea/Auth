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

    protected static function prepareInsertSet($set)
    {
        $fields = [];
        $values = [];
        $subValues = [];

        foreach ( $set as $k => $v ) {
            $fields[] = $k;
            $values[] = $v;
            $subValues[] = "?";
        }

        $fields = "`".join("`,`", $fields)."`";
        $subValues = join(",", $subValues);

        return [ $fields, $subValues, $values ];
    }
}
