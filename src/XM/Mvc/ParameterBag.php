<?php

namespace XM\Mvc;

class ParameterBag
{
	protected array $params;

	public function __construct(array $params = [])
	{
		$this->params = $params;
	}

	public function __get($key)
	{
		return $this->offsetGet($key);
	}

	public function __set($key, $value)
	{
		$this->params[$key] = $value;
	}

	protected function offsetGet($key)
	{
		return $this->params[$key] ?? null;
	}

	public function offsetSet($key, $value)
	{
		$this->params[$key] = $value;
	}

	public function offsetExists($key): bool
	{
		return array_key_exists($key, $this->params);
	}

	public function offsetUnset($key)
	{
		unset($this->params[$key]);
	}

	public function params(): array
	{
		return $this->params;
	}
}