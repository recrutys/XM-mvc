<?php

namespace XM\Db\Schema\Definitions;

use XM\Db\AbstractAdapter;

abstract class AbstractDefinition
{
	protected string $tableName = '';
	protected array $columns = [];
	protected AbstractAdapter $adapter;

	public function __construct()
	{
		$this->adapter = \XM::app()->db();
	}

	public function setTable($tableName): AbstractDefinition
	{
		$this->tableName = $tableName;

		return $this;
	}

	public function setColumns($columns): AbstractDefinition
	{
		$this->columns = $columns;

		return $this;
	}

	public function getTable(): string
	{
		return $this->tableName;
	}

	public function getColumns(): array
	{
		return $this->columns;
	}

	public function getColumnsWithDb(): array
	{
		$query = "SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, COLUMN_TYPE, EXTRA FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{$this->adapter->config['db']['dbname']}' AND TABLE_NAME = '{$this->getTable()}'";
		$columns = [];

		if ($result = $this->adapter->query($query))
		{
			$rows = $result->get_result()->fetch_all(MYSQLI_ASSOC);
			foreach ($rows as $row)
			{
				preg_match('/\d+/', $row['COLUMN_TYPE'], $matches);

				$columns[$row['COLUMN_NAME']] = [
					'type'   => $row['DATA_TYPE'],
					'length' => $row['CHARACTER_MAXIMUM_LENGTH'] ?: (int) $matches[0],
				];

				if (strpos($row['EXTRA'], 'auto_increment') !== false)
				{
					$columns[$row['COLUMN_NAME']]['auto_increment'] = true;
				}
			}
		}

		return $columns;
	}

	abstract public function execute();
}