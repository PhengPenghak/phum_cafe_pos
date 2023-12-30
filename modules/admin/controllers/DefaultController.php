<?php

namespace app\modules\admin\controllers;

use app\modules\admin\models\Enquiry;
use Yii;
use yii\base\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use DateInterval;
use DatePeriod;
use DateTime;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * Default controller for the `admin` module
 */
class DefaultController extends Controller
{
    public function actions()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'error'],
                        'allow' => true,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
            'error' => [
                'class' => 'yii\web\ErrorAction',
                'layout' => 'error',
            ],
        ];
    }

    public function beforeAction($action)
    {
        if ($action->id == 'error') {
            $this->layout = 'error';
        }

        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        return $this->render('auth/index');
    }

    public function actionDependent()
    {
        if (Yii::$app->request->isAjax) {
            if (Yii::$app->request->post('action') == 'dashboard') {
            }
        }
    }
}
