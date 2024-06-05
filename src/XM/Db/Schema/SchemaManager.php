<?php

namespace XM\Db\Schema;

use XM\Db\Schema\Definitions\Alter;
use XM\Db\Schema\Definitions\Create;
use XM\Db\Schema\Definitions\Drop;

class SchemaManager
{
	public function createTable($tableName, $callback)
	{
		$create = new Create();
		$callback($create);

		$create->setTable($tableName);
		$create->setColumns($create->getColumns());
		$create->execute();
	}

	public function dropTable($tableName)
	{
		$drop = new Drop();
		$drop->setTable($tableName);
		$drop->execute();
	}

	public function alterTable($tableName, $callback)
	{
		$alter = new Alter();
		$callback($alter);

		$alter->setTable($tableName);
		$alter->setColumns($alter->getColumns());
		$alter->execute();
	}
}