<?php

namespace XM;

class Phrase
{
	protected string $name;
	protected array $params;

	public function __construct($name, $params = [])
	{
		$this->name = $name;
		$this->params = $params;
	}

	public function render()
	{
		$phrase = \XM::app()->language()->phrase($this->name);

		foreach ($this->params as $param => $value)
		{
			$param = '{' . $param . '}';
			$phrase = str_replace($param, $value, $phrase);
		}

		return $phrase ?? '';
	}

	public function __toString()
	{
		return $this->render();
	}
}