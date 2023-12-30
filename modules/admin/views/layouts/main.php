<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap4\Breadcrumbs;
use app\assets\AdminAsset;
use app\assets\SweetAlert2Asset;
use yii\helpers\Url;


AdminAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <link rel="icon" type="image/png" href="<?= Yii::getAlias('@web') ?>/img/fav.png" />
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"> -->
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>

</head>

<body>
    <?php $this->beginBody() ?>

    <div class="app">
        <main class="app-main">
            <?= $content ?>
        </main>
    </div>

    <?php $this->endBody() ?>
</body>

</html>

<?php $this->endPage() ?>