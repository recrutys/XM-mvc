<?php

namespace XM;

use XM\Db\AbstractAdapter;
use XM\Http\Request;
use XM\Http\Response;
use XM\Http\Session;
use XM\Mvc\Entity\Manager;
use XM\Mvc\Router;
use XM\Service\AbstractService;
use XM\Templater\Templater;

class App
{
	protected Container $container;

	public function __construct(Container $container)
	{
		$this->container = $container;
		$this->initialize();
	}

	protected function initialize()
	{
		$container = $this->container;

		$container['time'] = !empty($_SERVER["REQUEST_TIME_FLOAT"]) ? $_SERVER["REQUEST_TIME_FLOAT"] : microtime(true);
		$container['app.type'] = '';

		$container['config.default'] = [
			'db'              => [
				'adapter' => '\XM\Db\Mysqli\Adapter'
			],
			'debug'           => false,
			'developmentMode' => false
		];
		$container['config.file'] = \XM::getRootDirectory() . '/src/config.php';

		$container['config'] = function ()
		{
			$default = $this->container('config.default');
			$file = $this->container('config.file');

			if (file_exists($file))
			{
				$config = [];
				require($file);

				$config = array_replace_recursive($default, $config);
			}
			else
			{
				$config = $default;
			}

			return $config;
		};

		$container['router'] = function ()
		{
			return new Router();
		};

		$container['request'] = function ()
		{
			return new Request();
		};

		$container['response'] = function ()
		{
			return new Response();
		};

		$container['session'] = function ()
		{
			return new Session();
		};

		$container['templater'] = function ()
		{
			return new Templater();
		};

		$container['db'] = function ()
		{
			$adapterClass = $this->container('config')['db']['adapter'];

			return new $adapterClass($this->container('config'));
		};

		$container['entityManager'] = function ()
		{
			return new Manager($this->db());
		};

		$container['visitor'] = function ()
		{
			return $this->container('session')->visitor();
		};

		$container['language'] = function ($languageId)
		{
			return new Language($languageId);
		};

		$container['phrase'] = function ($name, $params)
		{
			return new Phrase($name, $params);
		};

		$container['service'] = function ($class)
		{
			return new AbstractService($class);
		};

		$container['local.lessFiles'] = [
			'extra.less' => 'pub'
		];
	}

	public function container($key = null, ...$args)
	{
		if ($key === null)
		{
			return $this->container;
		}

		$containerItem = $this->container[$key];

		if (is_object($containerItem))
		{
			return $containerItem(...$args);
		}

		return $containerItem;
	}

	/**
	 * @return Router
	 */
	public function router()
	{
		return $this->container('router');
	}

	/**
	 * @return Request
	 */
	public function request()
	{
		return $this->container('request');
	}

	/**
	 * @return Response
	 */
	public function response()
	{
		return $this->container('response');
	}

	/**
	 * @return Session
	 */
	public function session()
	{
		return $this->container('session');
	}

	/**
	 * @return AbstractAdapter
	 */
	public function db()
	{
		return $this->container('db');
	}

	/**
	 * @return Manager
	 */
	public function em()
	{
		return $this->container('entityManager');
	}

	public function service($class): AbstractService
	{
		return $this->container('service', $class);
	}

	public function visitor()
	{
		return $this->container('visitor');
	}

	/**
	 * @param int $languageId
	 *
	 * @return Language
	 */
	public function language(int $languageId = 0)
	{
		return $this->container('language', $languageId);
	}

	/**
	 * @param       $name
	 * @param array $params
	 *
	 * @return Phrase
	 */
	public function phrase($name, array $params = []): Phrase
	{
		return $this->container('phrase', $name, $params);
	}

	/**
	 * @return Templater
	 */
	public function templater()
	{
		return $this->container('templater');
	}

	public function config()
	{
		return $this->container('config');
	}
}