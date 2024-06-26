<?php

namespace XM\Db\Schema\Definitions;

use XM\Db\Schema\Definitions\AbstractDefinition;

class Alter extends AbstractDefinition
{
	protected array $droppedColumns = [];
	protected array $renamedColumns = [];

	public function addColumn($name, $type, $length = null): Alter
	{
		$this->columns[$name] = [
			'type'   => strtoupper($type),
			'length' => $length
		];

		return $this;
	}

	public function dropColumns($columnNames): Alter
	{
		foreach ((array) $columnNames as $columnName)
		{
			$this->droppedColumns[] = $columnName;
		}

		return $this;
	}

	public function renameColumn($oldName, $newName)
	{
		$this->renamedColumns[$oldName] = $newName;
	}

	public function execute()
	{
		$query = "ALTER TABLE {$this->getTable()} ";

		// ######### Add new columns #########
		foreach ($this->columns as $columnName => $columnData)
		{
			$query .= "ADD $columnName {$columnData['type']}";

			if (isset($columnData['length']))
			{
				$query .= "({$columnData['length']})";
			}

			$query .= ',';
		}

		// ######### Drop columns #########
		if (!empty($this->droppedColumns))
		{
			foreach ($this->droppedColumns as $column)
			{
				$query .= " DROP {$column},";
			}
		}

		// ######### Rename columns #########
		if (!empty($this->renamedColumns))
		{
			foreach ($this->renamedColumns as $oldName => $newName)
			{
				$oldColumn = $this->getColumnsWithDb()[$oldName];

				$query .= " CHANGE COLUMN {$oldName} {$newName} {$oldColumn['type']}(" . $oldColumn['length'] . "), ";
			}
		}

		$query = trim($query, ', ');

		$this->adapter->query($query);
	}
}