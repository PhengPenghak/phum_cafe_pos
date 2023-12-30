<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
Yii::$app->setHomeUrl(Yii::getAlias("@web/admin/default"));
?>
<div class="site-error">

    <div class="card">
        <div class="card-header bg-light text-left">
            <i class="fa fa-fw fa-circle text-red"></i> <i class="fa fa-fw fa-circle text-yellow"></i> <i class="fa fa-fw fa-circle text-teal"></i>
        </div>
        <div class="card-body text-center">
            <div class="state-figure">
                <img class="img-fluid" src="<?= Yii::getAlias("@web") ?>/img/illustration/img-2.svg" alt="" style="max-width: 320px">
            </div>
            <h3 class="state-header"> <?= Html::encode($this->title) ?> </h3>
            <p class="state-description lead"> <?= nl2br(Html::encode($message)) ?></p>
            <p class="text-danger">Please contact us if you think this is a server error. Thank you.</p>
            <div class="state-action">
                <a href="<?= Yii::$app->homeUrl; ?>" class="btn btn-lg btn-light"><i class="fa fa-angle-right"></i> Go Home</a>
            </div>
        </div>
    </div>


</div>