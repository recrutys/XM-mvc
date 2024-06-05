<?php

namespace XM\Db\Mysqli;

use XM\Db\AbstractAdapter;

class Adapter extends AbstractAdapter
{
	/**
	 * @throws \Exception
	 */
	protected function connect()
	{
		if ($this->isConnected())
		{
			return;
		}

		$db = $this->config['db'];

		$this->db = new \mysqli(
			$db['host'],
			$db['username'],
			$db['password'],
			$db['dbname'],
			$db['port']
		);

		if ($this->db->connect_error)
		{
			throw new \Exception('Database error: ' . $this->db->connect_error);
		}
	}

	/**
	 * @throws \Exception
	 */
	public function query($query, $params = [])
	{
		$this->connect();

		if ($stmt = $this->db->prepare($query))
		{
			if ($params)
			{
				$types = str_repeat('s', count($params));
				$stmt->bind_param($types, ...$params);
			}

			$stmt->execute();

			return $stmt;
		}
		else
		{
			throw new \Exception('Query error: ' . $this->db->error);
		}
	}

	/**
	 * @throws \Exception
	 */
	public function fetchRow($query, $params = [])
	{
		$stmt = $this->query($query, $params);

		return $stmt->get_result()->fetch_assoc();
	}

	/**
	 * @throws \Exception
	 */
	public function fetchAll($query, $params = [])
	{
		$stmt = $this->query($query, $params);

		return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
	}

	/**
	 * @throws \Exception
	 */
	public function fetchOne($query, $params = [], $column = 0)
	{
		$stmt = $this->query($query, $params);

		$result = $stmt->get_result()->fetch_row();

		return $result[$column] ?? null;
	}

	/**
	 * @throws \Exception
	 */
	public function insert(string $table, array $columns, array $values)
	{
		$this->connect();

		$columnsStr = implode(', ', $columns);
		$placeholders = implode(', ', array_fill(0, count($values), '?'));

		$updateColumns = array_map(function ($column)
		{
			return "$column = VALUES($column)";
		}, $columns);
		$updateStr = implode(', ', $updateColumns);

		$query = "INSERT INTO $table ($columnsStr) VALUES ($placeholders) ON DUPLICATE KEY UPDATE $updateStr";

		$stmt = $this->db->prepare($query);

		if (!$stmt)
		{
			throw new \Exception('Prepare error: ' . $this->db->error);
		}

		$types = str_repeat('s', count($values));
		$stmt->bind_param($types, ...$values);

		if (!$stmt->execute())
		{
			throw new \Exception('Insert error: ' . $stmt->error);
		}
	}

	public function lastInsertId()
	{
		return $this->db->insert_id;
	}

	public function escapeString($string)
	{
		return $this->db->real_escape_string($string);
	}

	public function beginTransaction()
	{
		$this->db->begin_transaction();
		$this->inTransaction = true;
	}

	public function commit()
	{
		$this->db->commit();
		$this->inTransaction = false;
	}

	public function rollback()
	{
		$this->db->rollback();
		$this->inTransaction = false;
	}
}