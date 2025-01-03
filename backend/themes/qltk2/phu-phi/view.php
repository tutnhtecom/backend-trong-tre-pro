<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\PhuPhi */
?>
<div class="phu-phi-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'tong_tien',
            'ghi_chu:ntext',
            'active',
            'created',
            'updated',
            'user_id',
            'tieu_de',
            'type_id',
        ],
    ]) ?>

</div>
