<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Phum Cafe POS';
$this->params['breadcrumbs'][] = 'Admin';

?>
<div class="row">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <table class="table table-borderless table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Contact Number</th>
                            <th>Email</th>
                            <th>Created at</th>
                        </tr>
                    </thead>

                    <?= Html::a('Logout', ['default/logout'], ['data' => ['method' => 'post'], 'class' => 'dropdown-item text-dark']) ?>


                </table>
            </div>
        </div>
    </div>
</div>