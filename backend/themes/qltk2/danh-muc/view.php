<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\DanhMuc */
?>
<div class="danh-muc-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'type',
        ],
    ]) ?>

</div>
