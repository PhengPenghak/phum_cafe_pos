<?php

namespace app\modules\admin\controllers;

use app\modules\admin\models\LoginForm;
use Yii;
use yii\base\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;


class AuthController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionLogin()
    {
        $this->layout = "login";
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {

            return $this->redirect(['default/index']);
            // return '123';
        }

        $model->password = '';
        return $this->render('index', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->setHomeUrl(Yii::getAlias("@web/admin"));
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
