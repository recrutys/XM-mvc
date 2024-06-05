<?php

namespace XM\Service;

class AbstractService
{
	protected string $class;
	protected string $shortName;

	public function __call($name, $arguments)
	{
		if (!class_exists($this->class))
		{
			throw new \LogicException("Service $this->shortName (class: $this->class) could not be found");
		}

		return call_user_func_array([$this->class, $name], $arguments);
	}

	public function __construct($shortName)
	{
		$this->shortName = $shortName;
		$this->class = \XM::stringToClass($shortName, '\%s\Service\%s');
	}
}