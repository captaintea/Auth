<?php

namespace App\Services;

use App\Exceptions\IpAddressException;

class SameIpRegPreventor
{
    const IP_DISABLE_REG_TIME = 4; //hour
    const SECONDS_IN_HOUR = 3600;
    private $redis;
    private $ip;

    public function __construct($ip)
    {
        $this->redis = new \Predis\Client();
        $this->redis->connect(env('REDIS_HOST'), env('REDIS_PORT'));
        $this->ip = $ip;
    }

    public function prevent()
    {
        if (!empty($this->redis->get($this->ip))) {
            throw new IpAddressException();
        }
    }

    public function rememberIp()
    {
        $this->redis->set($this->ip, true);
        $this->redis->expire($this->ip, self::SECONDS_IN_HOUR * self::IP_DISABLE_REG_TIME);
    }
}
