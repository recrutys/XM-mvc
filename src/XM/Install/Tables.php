<?php

namespace XM\Install;

use XM\Db\Schema\Definitions\Create;

class Tables
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
			$table->addColumn('language_id', 'int');
			$table->addColumn('password', 'varchar', 255);
			$table->addColumn('is_admin', 'tinyint');

			$table->addPrimaryKey('user_id');
		};

		$tables['xm_language'] = function (Create $table)
		{
			$table->addColumn('language_id', 'int')->autoIncrement();
			$table->addColumn('title', 'varchar', 100);
			$table->addColumn('language_code', 'varchar', 100);
		};

		$tables['xm_phrase'] = function (Create $table)
		{
			$table->addColumn('phrase_id', 'int')->autoIncrement();
			$table->addColumn('language_id', 'int');
			$table->addColumn('title', 'varchar', 100);
			$table->addColumn('phrase_text', 'text');
		};

		return $tables;
	}
}