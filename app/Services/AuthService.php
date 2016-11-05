<?php

namespace App\Services;

use App\Dao\UserDao;
use App\Exceptions\AuthException;

class AuthService
{
    const PASS_HASH_ENTROPY_IMPROVE_PARAM = 'nvhWVOHPhCSyaC5iEYwFPLs9';
    const COOKIE_TOKEN_NAME = 'token';
    const COOKIE_LIFETIME_BOOST = 60*60*24*365*2; //2 years
    const COOKIE_UNTIL_BROWSER_CLOSE_LIFETIME = 0;
    const COOKIE_DELETE_LIFETIME = -1;
    const EMAIL_REGEXP = '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD';
    const PASSWORD_MIN_LENGTH = 3;
    const PASSWORD_MAX_LENGTH = 20;
    const USERNAME_MIN_LENGTH = 3;
    const USERNAME_MAX_LENGTH = 15;

    public static function passToHash($pass, $salt)
    {
        return password_hash($pass . self::PASS_HASH_ENTROPY_IMPROVE_PARAM . $salt . $pass, PASSWORD_BCRYPT);
    }

    public static function authByToken($token, $device)
    {
        return UserDao::getByToken($token, $device);
    }

    public static function authByPassword($email, $password, $device, $remember = false)
    {
        $userData = UserDao::getByEmailPassword($email, $password);
        if (!empty($userData)) {
            $token = UserDao::touchToken($userData['id'], $device);
            if ($remember) {
                self::setAuthCookie($token, time() + self::COOKIE_LIFETIME_BOOST);
            } else {
                self::setAuthCookie($token, self::COOKIE_UNTIL_BROWSER_CLOSE_LIFETIME);
            }
        } else {
            throw new AuthException();
        }
        return $userData;
    }

    public static function register($data, $device)
    {
        $sameIpRegPreventor = new SameIpRegPreventor($data['ip']);
        $sameIpRegPreventor->prevent();
        $userData = UserDao::register($data, $device);
        $sameIpRegPreventor->rememberIp();
        $token = UserDao::createToken($userData['id'], $device);
        self::setAuthCookie($token, time() + self::COOKIE_LIFETIME_BOOST);
        return $userData;
    }

    public static function logout($token)
    {
        UserDao::deleteToken($token);
        self::setAuthCookie("", self::COOKIE_DELETE_LIFETIME);
    }

    private static function setAuthCookie($token, $time)
    {
        setcookie(self::COOKIE_TOKEN_NAME, $token, $time, "/");
    }

}
