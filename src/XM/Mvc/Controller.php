<?php

namespace XM\Mvc;

use XM\Mvc\Entity\Entity;
use XM\Mvc\Entity\Manager;

class Controller
{
	protected \XM\Http\Request $request;
	public array $params = [];
	protected $templater;

	public function __construct()
	{
		$this->templater = \XM::app()->templater();
		$this->request = \XM::app()->request();
	}

	public function assertPostOnly(): void
	{
		if (\XM::app()->request()->isPost())
		{
			die('Доступ к странице должет осуществляться через POST');
		}
	}

	public function isPost(): bool
	{
		return $this->request->isPost();
	}

	public function view($templateName, $viewParams = []): View
	{
		return $this->templater->renderTemplate($templateName, $viewParams);
	}

	public function error($message): View
	{
		return $this->templater->renderTemplate('error', [
			'message' => $message
		]);
	}

	public function noPermission()
	{
		return $this->templater->renderTemplate('error', [
			'message' => 'No permission' // TODO: Phrase
		]);
	}

	public function assertRecordExist($shortName, $primaryColumn, $id, $phraseError = 'requested_page_not_found')
	{
		$em = $this->em()->findOne($shortName, [$primaryColumn => $id]);

		if (!$em)
		{
			return $this->error($phraseError); // TODO: Phrase
		}

		return $em;
	}

	public function filter($name): string
	{
		return \XM::app()->response()->filter($name);
	}

	public function redirect($link, $params = [])
	{
		return \XM::app()->router()->redirect($link, $params);
	}

	public function em(): Manager
	{
		return \XM::app()->em();
	}
}