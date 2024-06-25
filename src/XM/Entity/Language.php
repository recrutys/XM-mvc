<?php

namespace XM\Entity;

use XM\Mvc\Entity\Entity;
use XM\Mvc\Entity\Structure;

class Language extends Entity
{
	protected function getStructure(): Structure
	{
		$structure = new Structure();

		$structure->shortName = 'XM:Language';
		$structure->table = 'xm_language';
		$structure->primaryKey = 'language_id';
		$structure->columns = [
			'language_id'   => ['type' => self::INT, 'autoIncrement' => true],
			'title'         => ['type' => self::STR, 'required' => true, 'maxLength' => 100],
			'language_code' => ['type' => self::STR, 'required' => true, 'maxLength' => 100]
		];
		$structure->relations = [];

		return $structure;
	}
}