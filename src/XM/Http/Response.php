<?php

namespace XM\Http;

class Response
{
	protected $contentType = 'unknown/unknown';
	protected $charset = 'utf-8';
	protected $httpCode = 200;
	protected bool $compressIfAble = true;
	protected $includeContentLength = true;

	protected $headers = [];
	protected $body = '';
	protected $compress = false;

	public function body($body = null)
	{
		if ($body === null)
		{
			return $this->body;
		}

		$this->body = $body;

		return $this;
	}

	public function charset($charset = null)
	{
		if ($charset === null)
		{
			return $this->charset;
		}

		$this->charset = $charset;

		return $this;
	}

	public function contentType($contentType = null, $charset = null)
	{
		if ($contentType === null)
		{
			return $this->contentType;
		}

		if (!preg_match('#^[a-zA-Z0-9]+/[a-zA-Z0-9-+]+$#', $contentType))
		{
			throw new \InvalidArgumentException('Invalid content type');
		}

		$this->contentType = $contentType;

		if ($charset !== null)
		{
			$this->charset($charset);
		}

		return $this;
	}

	public function header($name, $value = null, $overwrite = true)
	{
		$name = $this->standardizeHeaderName($name);

		if ($value === null)
		{
			return isset($this->headers[$name]) ? $this->headers[$name] : false;
		}

		if ($overwrite || !isset($this->headers[$name]))
		{
			$this->headers[$name] = $value;
		}
		else
		{
			$existingValue = $this->headers[$name];
			if (!is_array($existingValue))
			{
				$newValue = [$existingValue];
			}
			else
			{
				$newValue = $existingValue;
			}

			if (is_array($value))
			{
				$newValue = array_merge($newValue, $value);
			}
			else
			{
				$newValue[] = $value;
			}
			$this->headers[$name] = $newValue;
		}

		return $this;
	}

	public function httpCode($httpCode = null)
	{
		if ($httpCode === null)
		{
			return $this->httpCode;
		}

		$this->httpCode = intval($httpCode);

		return $this;
	}

	public function redirect($url = null, $httpCode = null)
	{
		if ($url === null)
		{
			return $this->header('Location');
		}

		$this->header('Location', $url);
		$this->httpCode($httpCode);

		return $this;
	}

	public function removeHeader($name)
	{
		$name = $this->standardizeHeaderName($name);
		unset($this->headers[$name]);

		return $this;
	}

	public function send(): void
	{
		$this->sendHeaders();
		$this->sendBody();
	}

	public function sendBody(): void
	{
		if ($this->body)
		{
			if ($this->compress)
			{
				$toPrint = gzencode($this->body, 1);
			}
			else
			{
				$toPrint = $this->body;
			}

			if ($this->includeContentLength)
			{
				header('Content-Length: ' . strlen($toPrint));
			}

			echo $toPrint;
		}
	}

	public function sendHeaders(): void
	{
		foreach ($this->headers as $key => $value)
		{
			if (is_array($value))
			{
				foreach ($value as $innerValue)
				{
					header("$key: $innerValue", false);
				}
			}
			else
			{
				header("$key: $value", false);
			}
		}

		$sendCode = $this->httpCode;

		if ($this->contentType)
		{
			header('Content-Type: ' . $this->contentType . ($this->charset ? '; charset=' . $this->charset : ''), true, $sendCode);
			$sendCode = false;
		}

		if ($sendCode)
		{
			header('X-No-Headers: true', false, $sendCode);
		}
	}

	protected function standardizeHeaderName($name)
	{
		$name = preg_replace('#\s+#', ' ', str_replace('-', ' ', trim($name)));

		return str_replace(' ', '-', ucwords($name));
	}
}