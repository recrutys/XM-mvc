<?php

namespace XM\Mvc;

use Latte\Engine;
use Latte\Runtime\Template;

class View
{
	protected $templater;

	protected $templateName;

	public array $params = [];

	public function __construct($templater, $templateName, $viewParams = [])
	{
		$this->templater = $templater;
		$this->templateName = $templateName;
		$this->params = $viewParams;

		return $this->renderTemplate();
	}

	public function getParams(): array
	{
		return $this->params;
	}

	public function renderTemplate()
	{
		try
		{
			/** @var Template $template */
			return $this->templater->render($this->templateName, $this->params);
		}
		catch (\Throwable $e)
		{
			throw $this->templater->render(
				\XM::app()->templater()->getTemplateFullPath('error'),
				[
					'message' => $e->getMessage()
				]
			);
		}
	}

	// TODO: Use this func
	protected function getLessUrl(): string
	{
		$lessFiles = \XM::app()->templater()->getLocalIncludedLessFiles();
		$params = [];

		foreach ($lessFiles as $template => $type)
		{
			$params[] = 'template[]=' . urlencode($template);
			$params[] = 'type[]=' . urlencode($type);
		}

		return \XM::app()->request()->getFullRequestUri() . 'css.php?' . implode('&', $params);
	}
}