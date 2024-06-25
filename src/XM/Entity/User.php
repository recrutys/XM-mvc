<?php

namespace XM\Entity;

use XM\Mvc\Entity\Entity;
use XM\Mvc\Entity\Structure;

class User extends Entity
{
	protected function getStructure(): Structure
	{
		$structure = new Structure();

		$structure->shortName = 'XM:User';
		$structure->table = 'xm_users';
		$structure->primaryKey = 'user_id';
		$structure->columns = [
			'user_id'       => ['type' => self::INT, 'autoIncrement' => true],
			'username'      => ['type' => self::STR, 'required' => true, 'maxLength' => 30],
			'email'         => ['type' => self::STR, 'required' => true, 'maxLength' => 150],
			'custom_title'  => ['type' => self::STR, 'default' => '', 'maxLength' => 30],
			'register_date' => ['type' => self::INT, 'default' => \XM::$time],
			'last_activity' => ['type' => self::INT, 'default' => 0],
			'secret_key'    => ['type' => self::STR, 'required' => true, 'maxLength' => 255],
			'language_id' => ['type' => self::INT, 'required' => true, 'default' => 1],
			'password'      => ['type' => self::STR, 'required' => true, 'maxLength' => 255],
			'is_admin'      => ['type' => self::INT, 'default' => false]
		];
		$structure->relations = [];

		return $structure;
	}
}