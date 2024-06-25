<?php

namespace XM\Mvc\Entity;

class AbstractCollection implements \ArrayAccess
{
	protected array $entities = [];

	public function count(): int
	{
		return count($this->entities);
	}

	public function toArray(): array
	{
		return (array) $this->entities;
	}

	public function offsetExists($offset): bool
	{
		return isset($this->entities[$offset]);
	}

	public function offsetGet($offset)
	{
		return $this->entities[$offset];
	}

	public function offsetSet($offset, $value)
	{
		$this->entities[$offset] = $value;
	}

	public function offsetUnset($offset)
	{
		unset($this->entities[$offset]);
	}
}