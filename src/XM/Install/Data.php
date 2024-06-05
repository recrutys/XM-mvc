<?php

namespace XM\Install;

use XM\Db\Schema\Definitions\Create;

class Data
{
	public static function getTables(): array
	{
		$tables = [];

		$tables['xm_users'] = function (Create $table)
		{
			$table->addColumn('user_id', 'int')->autoIncrement();
			$table->addColumn('username', 'varchar', 20);
			$table->addColumn('email', 'varchar', 150);
			$table->addColumn('custom_title', 'varchar', 30);
			$table->addColumn('register_date', 'int');
			$table->addColumn('last_activity', 'int');
			$table->addColumn('secret_key', 'varchar', 255);
			$table->addColumn('password', 'varchar', 255);
			$table->addColumn('is_admin', 'tinyint');

			$table->addPrimaryKey('user_id');
		};

		return $tables;
	}
}