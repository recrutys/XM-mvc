<?php

namespace XM\Db\Schema\Definitions;

class Create extends AbstractDefinition
{
	protected array $primaryColumns = [];
	protected string $autoIncrementColumn = '';

	public function addColumn($name, $type, $length = null): Create
	{
		$this->columns[$name] = [
			'type'   => strtoupper($type),
			'length' => $length
		];

		return $this;
	}

	public function autoIncrement(): Create
	{
		// Get column key
		$keys = array_keys($this->columns);
		$lastKey = end($keys);

		// Get column data
		$columnData = end($this->columns);
		$columnData['auto_increment'] = true;

		$this->columns[$lastKey] = $columnData;
		$this->autoIncrementColumn = $lastKey;

		return $this;
	}

	public function addPrimaryKey($column): Create
	{
		$this->columns[$column]['primary_key'] = true;
		$this->primaryColumns[$column] = $column;

		return $this;
	}

	public function execute()
	{
		$query = "CREATE TABLE IF NOT EXISTS {$this->getTable()} (";

		// Set column
		foreach ($this->columns as $columnName => $columnData)
		{
			$column = "$columnName {$columnData['type']}";

			if (isset($columnData['length']))
			{
				$column .= "({$columnData['length']})";
			}

			if (isset($columnData['auto_increment']) && $columnData['auto_increment'])
			{
				$column .= " AUTO_INCREMENT";
			}

			if (isset($columnData['primary_key']))
			{
				$this->primaryColumns[$columnName] = $columnName;
			}

			$query .= "$column, ";
		}

		// Set primary columns
		if (!empty($this->primaryColumns))
		{
			$query .= 'PRIMARY KEY (';
			$primaryColumn = end($this->primaryColumns);

			// Auto Increment
			if ($this->autoIncrementColumn)
			{
				$query .= $this->autoIncrementColumn . ')';
			}
			else
			{
				// Non auto increment
				$column = $this->columns[$primaryColumn];

				if ($column['type'] == 'TEXT')
				{
					$query .= $primaryColumn . '(' . $column['length'] . ')';
				}
				else
				{
					$query .= $column . ')';
				}
			}
		}
		else
		{
			foreach ($this->columns as $columnName => $columnData)
			{
				if (isset($columnData['auto_increment']))
				{
					$query .= ' PRIMARY KEY (' . $columnName . ')';
				}
			}
		}

		$query = trim($query, ', ') . ')';

		$this->adapter->query($query);
	}
}