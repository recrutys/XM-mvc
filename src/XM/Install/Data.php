<?php

namespace XM\Install;

use XM\Db\Schema\Definitions\Create;

class Data
{
	public static function getTables(): array
	{
		$tables = [];

		$tables['xm_user'] = function (Create $table)
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

	public function getLanguages(): array
	{
		return [
			1 => [
				'English' => [
					'code' => 'en'
				]
			]
		];
	}

	public function getPhrases(): array
	{
		return [
			1 => [
				'do_not_have_permission'         => 'You do not have permission to view this page or perform this action.',
				'requested_page_not_found'       => 'The requested page could not be found.',
				'action_available_via_post_only' => 'This action is available via POST only. Please press the back button and try again.'
			]
		];
	}
}