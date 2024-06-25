<?php

namespace XM;

class Language
{
	protected array $options = [
		'title'       => '',
		'language_id' => ''
	];
	protected array $phrases = [];
	protected int $languageId;

	public function __construct($languageId = 0)
	{
		$this->languageId = $languageId;

		if (!$this->languageId)
		{
			$this->languageId = \XM::visitor()->language_id;
		}

		$this->setupOptions();
		$this->setupPhrases();
	}

	protected function setupOptions()
	{
		$this->options['title'] = $this->getLanguage()->title;
		$this->options['language_id'] = $this->languageId;
	}

	protected function setupPhrases()
	{
		$phrases = \XM::app()->em()->findMany('XM:Phrase', [
			'language_id' => $this->languageId
		]);

		foreach ($phrases->toArray() as $phrase)
		{
			$this->phrases[$phrase->title] = $phrase->phrase_text;
		}
	}

	public function options(): array
	{
		return $this->options;
	}

	protected function getLanguage()
	{
		return \XM::app()->em()->findOne('XM:Language', [
			'language_id' => $this->languageId
		]);
	}

	public function phrase($name)
	{
		if (isset($this->phrases[$name]))
		{
			return $this->phrases[$name];
		}

		return $name;
	}
}