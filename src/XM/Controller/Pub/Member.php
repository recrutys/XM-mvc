<?php

namespace XM\Controller\Pub;

use XM\Mvc\Controller;
use XM\Mvc\ParameterBag;
use XM\Mvc\View;
use XM\Service\TestService;

class Member extends Controller
{
	public function actionIndex(): View
	{
		//$user = $this->em()->findOne('XM:User', ['user_id' => 1]);

		$viewParams = [];

		return $this->view('member_list', $viewParams);
	}

	public function actionView(ParameterBag $params): View
	{
		$user = $this->assertViewableUser($params->user_id);

		$viewParams = [
			'user' => $user
		];

		return $this->view('member_view', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		echo 'Редактирование юзера';
	}

	public function assertViewableUser($userId)
	{
		return $this->assertRecordExist('XM:User', 'user_id', $userId);
	}
}