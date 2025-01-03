<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\User */
?>
<div class="user-update">
    <?php if ($model->nhom == \common\models\User::THANH_VIEN):?>
        <?= $this->render('_form_nguoi_dung', [
            'model' => $model,
            'vaitros' => $vaitros,
            'vaitrouser' => $vaitrouser,
        ]) ?>
    <?php else: ?>
        <?= $this->render('_form', [
            'model' => $model,
            'quan_huyen' => $quan_huyen
        ]) ?>
    <?php endif;?>


</div>
