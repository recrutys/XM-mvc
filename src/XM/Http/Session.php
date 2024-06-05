<?php

namespace XM\Http;

class Session
{
	public function visitor()
	{
		$userKey = $this->getCookie('xm_user_key');

		if (!$userKey)
		{
			return 0;
		}

		$visitor = \XM::app()->db()->query('SELECT * FROM app_users WHERE secret_key = ?', [$userKey], true);

		if ($visitor)
		{
			return $visitor;
		}

		return 0;
	}

	public function isAuth(): bool
	{
		return (bool) $this->visitor();
	}

	public function getCookie($name)
	{
		$cookies = $_COOKIE;

		if ($cookies && $cookies[$name])
		{
			return $cookies[$name];
		}

		return false;
	}

	public function setCookie($name, $value, $expiredTime = null)
	{
		if (!$expiredTime)
		{
			$expiredTime = \XM::$time + 30 * 24 * 60 * 60; // 30 days
		}

		setcookie($name, $value, $expiredTime);
	}

	public function delCookie($name)
	{
		unset($_COOKIE[$name]);
		setcookie($name, null, -1, '/');
	}
}