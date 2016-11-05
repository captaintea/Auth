<?php

namespace App\Services;

use App\Dao\UserDao;
use App\Exceptions\BaseException;

class SameIpRegPreventor
{
    const IP_DISABLE_REG_TIME = 4; //hour

    public static function prevent($ip) {
        $subInterval = new \DateInterval('PT'.self::IP_DISABLE_REG_TIME.'H');
        $repeatedIpCount = UserDao::getRepeatedIpCount($ip, (new \DateTime())->sub($subInterval));
        if ($repeatedIpCount !== 0) {
            throw new BaseException('Account has already been created from your ip address');
        }
    }
}
