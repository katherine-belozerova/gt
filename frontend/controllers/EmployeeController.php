<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Empl;
use yii\filters\AccessControl;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;
use yii\filters\auth\HttpBasicAuth;

class EmployeeController extends ApiController
{
	public $modelClass = Empl::class;

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
					'actions' => ['view', 'index', 'create', 'update', 'dismiss', 'return'],
					'allow' => false,
					'roles' => ['?'],
				],
				[
					'actions' => ['view', 'index'],
					'allow' => true,
					'roles' => ['viewEmployee'],
				],
				[
                    'actions' => ['create', 'dismiss', 'return'],
                    'allow' => true,
                    'roles' => ['createEmployee'],
                ],
                [
                    'actions' => ['update', 'dismiss', 'return'],
                    'allow' => true,
                    'roles' => ['updateEmployee'],
                ],
			],
		];
		 	return $behaviors;
  	}

  public function actionDismiss($id)
    {
        $model = $this->findModel($id);
        $model->status = 0;
        $model->save(false);
        return $this->redirect(['index']);
    }

    public function actionReturn($id)
	{
	    $model = $this->findModel($id);
	    $model->status = 1;
	    $model->save(false);
	    return $this->redirect(['index']);
	}

  protected function findModel($id)
    {
        if (($model = Empl::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не найдена');
        }
    }
}
