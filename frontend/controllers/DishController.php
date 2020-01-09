<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Dish;
use frontend\models\Empl;
use yii\filters\AccessControl;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;
use yii\filters\auth\HttpBasicAuth;

class DishController extends ApiController
{
	public $modelClass = Dish::class;

	public function behaviors()
	{
		$behaviors = parent::behaviors();
		$behaviors['authenticator'] = [
			'class' => HttpBasicAuth::class,
			'auth' => function ($login, $pass)
			{
				if ($user = Empl::find()
					->where(['login' => $login])
					->one() and 
					!empty($pass) and 
					$user->validatePassword($pass)) {
					return $user;
				} return null;
			},
		];
		$behaviors['access'] = [
			'class' => AccessControl::class,
			'denyCallback' => function ($rule, $action) 
				{ 
					throw new \Exception('У Вас нет прав для доступа к данной странице'); 
				},
			'rules' => [
				[
					'actions' => ['view', 'index', 'create', 'update', 'delete'],
					'allow' => true,
					'roles' => ['manager'],
				],
			],
		];
		 	return $behaviors;
  	}

}
