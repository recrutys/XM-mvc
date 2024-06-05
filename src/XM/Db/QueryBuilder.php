<?php

namespace XM\Db;

class QueryBuilder
{
	public array $sql = [];
	public array $values = [];

	public function reset()
	{
		$this->sql = [];
		$this->values = [];
	}

	public function from($table)
	{
		$this->sql['from'] = "FROM {$table}";

		return $this;
	}

	public function delete(string $table)
	{
		$this->reset();

		$this->sql['from'] = "DELETE FROM {$table}";

		return $this;
	}

	public function insert($fields = '*')
	{
		$this->reset();

		$this->sql['insert'] = "INSERT INTO {$fields}";

		return $this;
	}

	public function limit($offset)
	{
		$this->sql['limit'] = "LIMIT {$offset}";

		return $this;
	}

	public function orderBy($field, $order = 'ASC')
	{
		$this->sql['order_by'] = "ORDER BY {$field} {$order}";

		return $this;
	}

	public function select($fields = '*')
	{
		$this->reset();

		$this->sql['select'] = "SELECT {$fields} ";

		return $this;
	}

	public function update($table)
	{
		$this->reset();

		$this->sql['update'] = "UPDATE {$table} ";

		return $this;
	}

	public function where($column, $value, $operator = '')
	{
		$this->sql['where'][] = "{$column} {$operator} ?";
		$this->values[] = $value;

		return $this;
	}

	public function sql(): string
	{
		$sql = '';

		if (!$this->sql)
		{
			return $sql;
		}

		foreach ($this->sql as $key => $value)
		{
			if ($key === 'where')
			{
				$sql .= ' WHERE ';

				$whereConditions = [];
				foreach ($value as $where)
				{
					$whereConditions[] = $where;
				}

				$sql .= implode(' AND ', $whereConditions);
			}
			else
			{
				$sql .= $value;
			}
		}

		return $sql;
	}
}