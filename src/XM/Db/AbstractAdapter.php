<?php

namespace XM\Db;

abstract class AbstractAdapter
{
	public array $config;
	public bool $inTransaction = false;
	public $db = null;

	protected QueryBuilder $queryBuilder;

	public function __construct(array $config)
	{
		$this->config = $config;

		if (!$this->isConnected())
		{
			$this->queryBuilder = new QueryBuilder();
		}
	}

	abstract protected function connect();

	abstract public function query($query, $params = []);

	abstract public function fetchRow($query, $params = []);

	abstract public function fetchAll($query, $params = []);

	abstract public function fetchOne($query, $params = [], $column = 0);

	abstract public function insert(string $table, array $columns, array $values);

	abstract public function lastInsertId();

	abstract public function escapeString($string);

	abstract public function beginTransaction();

	abstract public function commit();

	abstract public function rollback();

	public function inTransaction(): bool
	{
		return $this->inTransaction;
	}

	public function isConnected(): bool
	{
		return (bool) $this->db;
	}

	public function queryBuilder(): QueryBuilder
	{
		return $this->queryBuilder;
	}
}