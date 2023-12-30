<?php

namespace app\modules\admin;

use Yii;

/**
 * PreOrder module definition class
 */
class Admin extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\admin\controllers';
    public $layout = 'main';
    public $defaultRoute = 'default/index';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        $this->layout = '@app/modules/admin/views/layouts/main';

        Yii::$app->errorHandler->errorAction = 'admin/default/error';
        Yii::$app->user->loginUrl = ['admin/auth/login'];
        // custom initialization code goes heres

        Yii::$app->set('session', [
            'class' => 'yii\web\Session',
            'name' => '_posAdminSessionId',
        ]);
    }
}
