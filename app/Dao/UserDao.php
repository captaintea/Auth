<?php

namespace App\Dao;

use App\Exceptions\InvalidPasswordException;
use App\Exceptions\TakenEmailException;
use App\Exceptions\UserDataException;
use App\Exceptions\UserNotFoundException;
use App\Services\AuthService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class UserDao extends Dao
{
    public static function register($rawData, $device)
    {
        try {
            if (empty($rawData['email']) || empty($rawData['password'])) {
                throw new UserDataException();
            }
            $rawData['salt'] = sha1($rawData['email'] . $device . mt_rand(1,999999999) . microtime(1));
            $password = $rawData['password'];
            $rawData['password'] = AuthService::passToHash($rawData['password'], $rawData['salt']);
            if (empty($rawData['password']) || empty($rawData['salt'])) {
                throw new UserDataException();
            }
            $statement = "insert into users (`name`, `email`, `password`, `salt`, `created_at`) values (?, ?, ?, ?, ?)";
            $preparedStatement = self::db()->prepare($statement);
            $preparedStatement->bindValue(1, $rawData['name']);
            $preparedStatement->bindValue(2, $rawData['email']);
            $preparedStatement->bindValue(3, $rawData['password']);
            $preparedStatement->bindValue(4, $rawData['salt']);
            $preparedStatement->bindValue(5, new \DateTime(), "datetime");

            $preparedStatement->execute();
        } catch (UniqueConstraintViolationException  $e) {
            throw new TakenEmailException();
        }
        return self::getByEmailPassword($rawData['email'], $password);
    }

    public static function getByEmailPassword($email, $password)
    {
        $email = trim($email);
        $password = trim($password);
        if (empty($email) || empty($password)) {
            throw new UserDataException();
        }
        $statement = "select a.* from users a where a.email = ? limit 1";
        $preparedStatement = self::db()->prepare($statement);
        $preparedStatement->bindValue(1, $email);
        $preparedStatement->execute();
        $userData = $preparedStatement->fetch();
        if (empty($userData) || !is_array($userData)) {
            throw new UserNotFoundException();
        }
        $passwordHash = AuthService::passToHash($password, $userData['salt']);
        if (password_verify($userData['password'], $passwordHash)) {
            throw new InvalidPasswordException();
        }
        return $userData;
    }

    public static function getByToken($token, $device)
    {
        $token = trim($token);
        if (empty($token)) {
            throw new UserNotFoundException();
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
            throw new UserNotFoundException();
        }
        return $userData;
    }

    public static function createToken($userId, $device)
    {
        $token = sha1("$userId $device " . mt_rand(1,999999999) . " " . microtime(1));
        $statement = "insert into user_tokens (`user_id`, `token`, `device`, `created_at`) values (?, ?, ?, ?)";
        $preparedStatement = self::db()->prepare($statement);
        $preparedStatement->bindValue(1, $userId);
        $preparedStatement->bindValue(2, $token);
        $preparedStatement->bindValue(3, $device);
        $preparedStatement->bindValue(4, new \DateTime(), "datetime");
        $preparedStatement->execute();
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
        return !empty($row['token']) ? $row['token'] : null;
    }

    public static function touchToken($userId, $device)
    {
        return self::getToken($userId, $device) ?: self::createToken($userId, $device);
    }

    public static function deleteToken($token)
    {
        $statement = "delete from user_tokens where token = ?";
        $preparedStatement = self::db()->prepare($statement);
        $preparedStatement->bindValue(1, $token);
        $preparedStatement->execute();
    }

}
