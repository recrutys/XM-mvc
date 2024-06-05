<?php

namespace XM\Mvc\Entity;

class AbstractCollection implements \ArrayAccess
{
	protected array $entities = [];

	public function count(): int
	{
		return count($this->entities);
	}

	public function offsetExists($offset): bool
	{
		return isset($this->entities[$offset]);
	}

	public function offsetGet($offset): mixed
	{
		return $this->entities[$offset];
	}

	public function offsetSet($offset, $value): void
	{
		$this->entities[$offset] = $value;
	}

	public function offsetUnset($offset): void
	{
		unset($this->entities[$offset]);
	}
}