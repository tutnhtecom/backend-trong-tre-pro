<?php

use backend\models\DanhMuc;
use yii\helpers\Html;
use yii\web\View;


/* @var $this yii\web\View */
/* @var $model backend\models\DanhMuc */
/** @var $quan_huyen [] */
?>
<div class="danh-muc-update">
    <?= $this->render('_form', [
        'model' => $model,
        'quan_huyen' => $quan_huyen
    ]) ?>
</div>

<?php $this->registerJsFile(Yii::$app->request->baseUrl.'/backend/assets/js-view/index-danh-muc.js',[ 'depends' => ['backend\assets\Qltk2Asset'], 'position' => \yii\web\View::POS_END ]); ?>
