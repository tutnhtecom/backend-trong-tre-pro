<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\DanhMuc */
/** @var $quan_huyen [] */
?>
<div class="danh-muc-create">
    <?= $this->render('_form', [
        'model' => $model,
        'quan_huyen' => $quan_huyen
    ]) ?>
</div>

<?php $this->registerJsFile(Yii::$app->request->baseUrl.'/backend/assets/js-view/index-danh-muc.js',[ 'depends' => ['backend\assets\Qltk2Asset'], 'position' => \yii\web\View::POS_END ]); ?>
