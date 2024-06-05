<?php

namespace XM\Mvc\Entity;

use XM\Db\AbstractAdapter;

abstract class Entity
{
	const INT = 'int';
	const FLOAT = 'float';
	const BOOL = 'bool';
	const STR = 'str';

	const TO_ONE = 1;
	const TO_MANY = 2;

	public AbstractAdapter $_adapter;
	public Manager $_em;
	public Structure $_structure;
	public array $_values = [];

	public function __construct(AbstractAdapter $adapter, Manager $em, array $values)
	{
		$this->_adapter = $adapter;
		$this->_em = $em;
		$this->_structure = $this->getStructure();
		$this->_values = $values;
	}

	public function __get($offset)
	{
		// Get relation
		foreach ($this->_structure->relations as $relationName => $relationData)
		{
			if ($offset == $relationName)
			{
				$findData = [$relationData['conditions'] => $this->_values[$relationData['conditions']]];

				if ($relationData['type'] == 1)
				{
					return $this->_em->findOne($relationData['entity'], $findData);
				}
				else
				{
					return $this->_em->findMany($relationData['entity'], $findData);
				}
			}
		}
		// Get value from _values
		if (!isset($this->_structure->columns[$offset]))
		{
			throw new \InvalidArgumentException("Property '$offset' does not exist in field list.");
		}

		if (array_key_exists($offset, $this->_values))
		{
			return $this->_values[$offset];
		}
		else
		{
			throw new \InvalidArgumentException("Property '$offset' does not exist or is not accessible.");
		}
	}

	public function __set($offset, $value)
	{
		$this->_values[$offset] = $value;
	}

	public function bulkSet(array $data)
	{
		foreach ($data as $offset => $value)
		{
			$this->_values[$offset] = $value;
		}
	}

	public function save(): Entity
	{
		$vars = get_object_vars($this);

		foreach ($vars['_structure']->columns as $columnName => $columnData)
		{
			// Default
			if (isset($columnData['default']))
			{
				if (!isset($columnData['autoIncrement']) && !isset($this->_values[$columnName]))
				{
					$vars['_values'][$columnName] = $columnData['default'];
				}
			}

			// Required
			if (isset($columnData['required']) && $columnData['required'])
			{
				if (!isset($vars['_values'][$columnName]))
				{
					throw new \InvalidArgumentException("Field '{$columnName}' required");
				}
			}

			// maxLength
			if (isset($columnData['maxLength']) && $columnData['maxLength'])
			{
				if (mb_strlen($vars['_values'][$columnName]) > $columnData['maxLength'])
				{
					throw new \InvalidArgumentException("The maximum length '{$columnName}' cannot be greater than {$columnData['maxLength']}");
				}
			}
		}

		unset($vars['_adapter'], $vars['_structure'], $vars['_em']);

		$this->_adapter->insert(
			$this->_structure->table,
			array_keys($vars['_values']),
			array_values($vars['_values'])
		);

		$emFindValue = $this->_adapter->lastInsertId() ?: $vars['_values'][$this->_structure->primaryKey];
		$em = $this->_em->findOne(
			$this->_structure->shortName,
			[$this->_structure->primaryKey => $emFindValue],
		);

		$this->_values = $em->_values;

		return $em;
	}

	public function __sleep()
	{
		throw new \LogicException('Entities cannot be serialized or unserialized');
	}

	public function __wakeup()
	{
		throw new \LogicException('Entities cannot be serialized or unserialized');
	}

	abstract protected function getStructure(): Structure;
}