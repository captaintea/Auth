<?php

namespace App\Dao;

use App\Exceptions\BaseException;
use App\Services\AuthService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class UserDao extends Dao
{
    public static function register($rawData, $device)
    {
        if (empty($rawData['email']) || empty($rawData['password'])) {
            throw new BaseException('Wrong user data');
        }
        $rawData['salt'] = sha1($rawData['email'] . $device . mt_rand(1,999999999) . microtime(1));
        $password = $rawData['password'];
        $rawData['password'] = AuthService::passToHash($rawData['password'], $rawData['salt']);
        if (empty($rawData['password']) || empty($rawData['salt'])) {
            throw new BaseException('Wrong auth user data');
        }
        list($fields, $subValues, $values) = self::prepareInsertSet($rawData);
        $statement = "insert into users ({$fields}, `created_at`) values ({$subValues}, NOW())";
        $preparedStatement = self::db()->prepare($statement);
        try {
            $preparedStatement->execute($values);
        } catch (UniqueConstraintViolationException $e) {
            throw new BaseException('This email is busy');
        }

        return self::getByEmailPassword($rawData['email'], $password);
    }

    public static function getByEmailPassword($email, $password)
    {
        $email = trim($email);
        $password = trim($password);
        if (empty($email) || empty($password)) {
            throw new BaseException('Wrong user data');
        }

        $statement = "select a.* from users a where a.email = ? limit 1";
        $preparedStatement = self::db()->prepare($statement);
        $preparedStatement->bindValue(1, $email);
        $preparedStatement->execute();
        $userData = $preparedStatement->fetch();
        if (empty($userData)) {
            throw new BaseException('User not found');
        }
        $passwordHash = AuthService::passToHash($password, $userData['salt']);
        if ($passwordHash !== $userData['password']) {
            throw new BaseException('Invalid password');
        }
        return $userData;
    }

    public static function getByToken($token, $device)
    {
        $token = trim($token);
        if (empty($token)) {
            return null;
        }
        $statement = "
			select
				u.*
			from
				user_tokens ut
			left join
				users u
			on
				ut.user_id = u.id
			where
				ut.device = ?
				and ut.token = ?
			limit 1
		";
        $preparedStatement = self::db()->prepare($statement);
        $preparedStatement->bindValue(1, $device);
        $preparedStatement->bindValue(2, $token);
        $preparedStatement->execute();
        $userData = $preparedStatement->fetch();
        if (empty($userData)) {
            return null;
        }
        return $userData;
    }

    public static function createToken($userId, $device)
    {
        $token = sha1("$userId $device " . mt_rand(1,999999999) . " " . microtime(1));
        list($fields, $subValues, $values) = self::prepareInsertSet([
            'user_id' => $userId,
            'device' => $device,
            'token' => $token,
        ]);
        $statement = "insert into user_tokens ({$fields}, `created_at`) values ({$subValues}, NOW())";
        $preparedStatement = self::db()->prepare($statement);
        $preparedStatement->execute($values);
        return $token;
    }

    public static function getToken($userId, $device)
    {
        if (empty($userId)) {
            return null;
        }
        $statement = "
			select
				ut.token
			from
				user_tokens ut
			where
				ut.user_id = ?
				and ut.device = ?
			limit 1
		";
        $preparedStatement = self::db()->prepare($statement);
        $preparedStatement->bindValue(1, $userId);
        $preparedStatement->bindValue(2, $device);
        $preparedStatement->execute();
        $row = $preparedStatement->fetch();
        return empty($row['token']) ? $row['token'] : null;
    }

    public static function touchToken($userId, $device)
    {
        return self::getToken($userId, $device) ?: self::createToken($userId, $device);
    }

    public static function getRepeatedIpCount($ip, $time) {
        $statement = "
			select 
			    count( u.ip ) as ip_count
			from
				users u
			where
				u.ip = ?
				and u.created_at > ?
		";
        $preparedStatement = self::db()->prepare($statement);
        $preparedStatement->bindValue(1, $ip);
        $preparedStatement->bindValue(2, $time, "datetime");
        $preparedStatement->execute();
        return (int) $preparedStatement->fetch()['ip_count'];
    }
}
