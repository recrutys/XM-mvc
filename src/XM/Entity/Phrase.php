<?php

namespace XM\Entity;

use XM\Mvc\Entity\Entity;
use XM\Mvc\Entity\Structure;

class Phrase extends Entity
{
	protected function getStructure(): Structure
	{
		$structure = new Structure();

		$structure->shortName = 'XM:Phrase';
		$structure->table = 'xm_phrase';
		$structure->primaryKey = 'phrase_id';
		$structure->columns = [
			'phrase_id'   => ['type' => self::INT, 'autoIncrement' => true],
			'language_id' => ['type' => self::INT, 'required' => true],
			'title'       => ['type' => self::STR, 'required' => true, 'maxLength' => 100],
			'phrase_text' => ['type' => self::STR, 'required' => true]
		];
		$structure->relations = [];

		return $structure;
	}
}