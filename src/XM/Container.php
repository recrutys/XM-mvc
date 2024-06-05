<?php

namespace XM;

use Exception;

class Container implements \ArrayAccess
{
	protected array $data = [];

	public function offsetExists($offset): bool
	{
		return isset($this->data[$offset]);
	}

	/**
	 * @throws Exception
	 */
	public function offsetGet($offset)
	{
		if (!$this->offsetExists($offset))
		{
			throw new Exception("Container key <b>$offset</b> not found");
		}

		return $this->data[$offset];
	}

	public function offsetSet($offset, $value)
	{
		$this->data[$offset] = $value;
	}

	public function offsetUnset($offset)
	{
		unset($this->data[$offset]);
	}
}