<?php

namespace XM\Mvc\Entity;

class Structure
{
	public string $shortName;
	public string $table;
	public string $primaryKey;
	public array $columns = [];
	public array $relations = [];
}