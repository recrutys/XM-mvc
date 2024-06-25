<?php

require_once 'src/XM.php';

XM::start(__DIR__);

// Setup tables
$tables = new \XM\Install\Tables();
$sm = new \XM\Db\Schema\SchemaManager();
foreach ($tables->getTables() as $tableName => $columns)
{
	$sm->createTable($tableName, $columns);
}