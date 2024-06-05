<?php

namespace XM\Mvc;

class Router
{
	protected static array $routes = [];

	public function register($uri, $params)
	{
		self::$routes[$uri] = $params;
	}

	/**
	 * @throws \Exception
	 */
	public function dispatch($uri)
	{
		$parseUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		if ($parseUrl != '/')
		{
			if (file_exists($_SERVER['DOCUMENT_ROOT'] . $parseUrl))
			{
				return false;
			}
		}

		$uriParts = explode('?', $uri, 2);
		$uri = trim($uriParts[0], '/') ?: '/';

		foreach (self::$routes as $routeUri => $params)
		{
			$pattern = '~^' . preg_replace('/\{([^}]+)\}/', '(?<$1>[^/]+)', $routeUri) . '$~';

			if (preg_match($pattern, $uri, $matches))
			{
				$controllerName = $params['controller'];
				$action = $params['action'];
				$controller = new $controllerName();

				$actionMethod = 'action' . ucfirst($action);

				if (method_exists($controller, $actionMethod))
				{
					$parameters = [];
					foreach ($matches as $key => $value)
					{
						if (!is_int($key))
						{
							$parameters[$key] = $value;
						}
					}

					$parameters = new ParameterBag($parameters);

					return $controller->{$actionMethod}($parameters);
				}
				else
				{
					throw new \Exception("Action '{$actionMethod}' not found in controller '{$controllerName}'");
				}
			}
		}

		throw new \Exception("Route '{$uri}' not found");
	}

	public function redirect($link, $params = [])
	{
		if ($params)
		{
			$link .= '?' . http_build_query($params);
		}

		header('Location: ' . $link);
	}
}