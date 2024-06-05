<?php

namespace XM\Db\Schema\Definitions;

class Drop extends AbstractDefinition
{
	public function execute()
	{
		$query = "DROP TABLE IF EXISTS {$this->getTable()}";

		$this->adapter->query($query);
	}
}