<?php

namespace XM\Service;

class Auth
{
	public static function auth($login, $password): bool
	{
		$user = \XM::app()->em()->findOne('XM:User', [
			'username' => $login,
			'password' => self::hashPassword($password)
		]);

		if (!$user)
		{
			return false;
		}

		return $user;
	}

	public static function registerUser($input)
	{
		$user = \XM::app()->em()->create('XM:User');
		$user->bulkSet([
			'username'   => $input['login'],
			'email'      => $input['email'],
			'secret_key' => self::generateSecretKey(),
			'password'   => self::hashPassword($input['password'])
		]);
		$user->save();

		\XM::app()->session()->setCookie('xm_user_key', $user->secret_key);

		return $user;
	}

	public static function hashPassword($password): string
	{
		return password_hash(md5($password), PASSWORD_BCRYPT);
	}

	public static function generateSecretKey()
	{
		return substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 60);
	}
}