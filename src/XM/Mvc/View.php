<?php

namespace XM\Mvc;

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

		$this->renderTemplate();
	}

	public function getParams(): array
	{
		return $this->params;
	}

	public function renderTemplate()
	{
		try
		{
			$this->templater->render($this->templateName, $this->params);

			$content = $this->templater->render('PAGE_CONTAINER.html', [
				'content' => $this->templater->render($this->templateName, $this->params),
				'lessUrl' => $this->getLessUrl()
			]);
		}
		catch (\Throwable $e)
		{
			$content = $e->getMessage();
		}

		echo $content;
	}

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