<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Cauhinh */
$this->title = 'Sửa cấu hình: '.$model->name;
?>
<div class="cauhinh-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
