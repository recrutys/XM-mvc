<?php

namespace XM\Mvc\Entity;

use XM\Db\AbstractAdapter;
use XM\Db\QueryBuilder;

class Manager
{
	protected $db;
	protected QueryBuilder $queryBuilder;

	public function __construct(AbstractAdapter $db)
	{
		$this->db = $db;
		$this->queryBuilder = new QueryBuilder();
	}

	public function getEntityClassName($shortName)
	{
		$className = \XM::stringToClass($shortName, '%s\Entity\%s');

		if (!class_exists($className))
		{
			throw new \LogicException("Entity $shortName (class: $className) could not be found");
		}

		return $className;
	}

	public function getEntityStructure($shortName)
	{
		$className = $this->getEntityClassName($shortName);

		$entity = new $className($this->db, $this, []);

		return $entity->_structure;
	}

	public function exists($shortName): bool
	{
		$className = \XM::stringToClass($shortName, '%s\Entity\%s');

		if (!class_exists($className))
		{
			return false;
		}

		return true;
	}

	public function create($shortName, array $values = []): Entity
	{
		$entityClass = $this->getEntityClassName($shortName);

		$entity = new $entityClass($this->db, $this, []);

		foreach ($values as $key => $value)
		{
			$values[$key] = $value;
		}

		$entity->_values = $values;

		return $entity;
	}

	public function findOne($shortName, $params = [])
	{
		if (!$params)
		{
			return null;
		}

		$className = $this->getEntityClassName($shortName);
		$structure = $this->getEntityStructure($shortName);

		$query = $this->queryBuilder->select()->from($structure->table);
		$values = [];
		$emValues = [];

		foreach ($params as $column => $value)
		{
			if (array_key_exists($column, $structure->columns))
			{
				$query->where($column, $value, "=");
			}

			$values[] = $value;
		}

		$result = $this->db->fetchRow($query->sql(), $values);

		if ($result)
		{
			foreach ($result as $rowKey => $rowValue)
			{
				$emValues[$rowKey] = $rowValue;
			}

			$em = new $className($this->db, $this, []);
			$em->_values = $emValues;

			return $em;
		}

		return null;
	}

	public function findMany($shortName, $params = []): ?AbstractCollection
	{
		if (!$params)
		{
			return null;
		}

		$className = $this->getEntityClassName($shortName);
		$structure = $this->getEntityStructure($shortName);

		$query = $this->queryBuilder->select()->from($structure->table);
		$values = [];
		$emValues = [];

		foreach ($params as $column => $value)
		{
			if (array_key_exists($column, $structure->columns))
			{
				$query->where($column, $value, "=");
			}

			$values[] = $value;
		}

		$result = $this->db->fetchAll($query->sql(), $values);
		$entities = new AbstractCollection();

		if ($result)
		{
			foreach ($result as $rowKey => $rowValue)
			{
				$emValues[$rowKey] = $rowValue;

				$em = new $className($this->db, $this, []);
				$em->_values = $emValues[0];

				$entities->offsetSet($rowKey, $em);
			}
		}

		return $entities;
	}
}