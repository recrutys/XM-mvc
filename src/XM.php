<?php

use XM\App;

class XM
{
	public static string $version = '1.0.0';
	public static int $time = 0;
	public static string $rootDirectory = '';
	public static bool $debugMode = false;

	public static $autoLoader = null;
	public static $db = null;
	public static $app = null;

	public static function getRootDirectory(): string
	{
		return self::$rootDirectory;
	}

	public static function start($rootDir)
	{
		self::$time = time();
		self::$rootDirectory = $rootDir;
		self::$db = false;

		self::startAutoloader();

		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);

		self::app();
		self::setupRouter();
	}

	/**
	 * @return App
	 */
	public static function app(): ?App
	{
		if (!self::$app)
		{
			return self::appSetup('\XM\App');
		}

		return self::$app;
	}

	public static function appSetup($appClass)
	{
		if (!$appClass)
		{
			throw new \LogicException("The application cannot be started because the 'appType' is not specified");
		}

		$app = new $appClass(new \XM\Container());

		if (self::$app)
		{
			throw new \LogicException('App has already been initialized');
		}

		self::$app = $app;

		return $app;
	}

	public static function startAutoloader()
	{
		if (self::$autoLoader)
		{
			return;
		}

		$autoLoader = require(__DIR__ . '/vendor/autoload.php');
		$autoLoader->register();

		self::$autoLoader = $autoLoader;
	}

	public static function setupRouter()
	{
		$router = self::$app->router();

		$router->register('/', ['controller' => 'XM\Controller\Pub\Index', 'action' => 'index']);
		$router->register('test', ['controller' => 'XM\Controller\Pub\Index', 'action' => 'test']);
		$router->register('members', ['controller' => 'XM\Controller\Pub\Member', 'action' => 'index']);
		$router->register('members/{user_id}', ['controller' => 'XM\Controller\Pub\Member', 'action' => 'view']);
		$router->register('members/{user_id}/edit', ['controller' => 'XM\Controller\Pub\Member', 'action' => 'edit']);

		$router->dispatch($_SERVER['REQUEST_URI']);
	}

	public static function stringToClass($string, $formatter, $defaultInfix = null)
	{
		$parts = explode(':', $string, 3);

		if (count($parts) == 1)
		{
			return $string;
		}

		$prefix = $parts[0];
		if (isset($parts[2]))
		{
			$infix = $parts[1];
			$suffix = $parts[2];
		}
		else
		{
			$infix = $defaultInfix;
			$suffix = $parts[1];
		}

		return $defaultInfix === null
			? sprintf($formatter, $prefix, $suffix)
			: sprintf($formatter, $prefix, $infix, $suffix);
	}

	public static function config()
	{
		return self::$app->config();
	}
}