<?php

namespace XM\Http;

class Request
{
	protected $server;
	protected $files;
	protected $inputFilterer;
	protected $input;

	public function __construct()
	{
		$this->server = $_SERVER;
		$this->files = $_FILES;
		$this->inputFilterer = array_merge($_GET, $_POST);
	}

	public function getRequestMethod(): string
	{
		return strtolower($this->getServer('REQUEST_METHOD'));
	}

	public function get($key)
	{
		if (array_key_exists($key, $this->input))
		{
			return $this->input[$key];
		}

		return null;
	}

	public function getServer($key)
	{
		if (array_key_exists($key, $this->server))
		{
			return $this->server[$key];
		}

		return null;
	}

	public function isGet(): bool
	{
		return $this->getRequestMethod() == 'get';
	}

	public function isHead(): bool
	{
		return $this->getRequestMethod() == 'head';
	}

	public function isPost(): bool
	{
		return $this->getRequestMethod() == 'post';
	}

	public function isXhr(): bool
	{
		return $this->getServer('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest';
	}

	public function isSecure(): bool
	{
		return (
			$this->getServer('REQUEST_SCHEME') === 'https'
			|| $this->getServer('HTTP_X_FORWARDED_PROTO') === 'https'
			|| $this->getServer('HTTPS') === 'on'
			|| $this->getServer('SERVER_PORT') == 443
		);
	}

	public function isLocalHost($host = null): bool
	{
		if ($host === null)
		{
			$host = $this->getHost();
		}

		return (
			$host == 'localhost'
			|| $host == '127.0.0.1'
			|| $host == '[::1]'
			|| preg_match('#\.(dev|localhost|local|test)$#', $host)
			|| strpos($host, '.') === false
		);
	}

	public function getFullRequestUri(): string
	{
		return $this->getHostUrl() . $this->getRequestUri();
	}

	public function getHost()
	{
		$host = $this->getServer('HTTP_HOST');
		if (!$host)
		{
			$host = $this->getServer('SERVER_NAME');
			$port = intval($this->getServer('SERVER_PORT'));
			if ($port && $port != 80 && $port != 443)
			{
				$host = $host . ":$port";
			}
		}

		return $host;
	}

	public function getHostUrl(): string
	{
		return $this->getProtocol() . '://' . $this->getHost();
	}

	public function getProtocol(): string
	{
		return $this->isSecure() ? 'https' : 'http';
	}

	public function getRequestUri()
	{
		if ($this->getServer('IIS_WasUrlRewritten') === '1')
		{
			$unencodedUrl = $this->getServer('UNENCODED_URL', '');
			if ($unencodedUrl !== '')
			{
				return $unencodedUrl;
			}
		}

		return $this->getServer('REQUEST_URI');
	}

	public function hasHttpRequest(): bool
	{
		return isset($this->server['REQUEST_URI']) && isset($this->server['REQUEST_METHOD']);
	}

	public function filter($name)
	{
		$params = array_merge($_GET, $_POST);
		$param = $params[$name] ?? '';

		return is_string($param) ? htmlspecialchars(htmlentities($param)) : $param;
	}
}