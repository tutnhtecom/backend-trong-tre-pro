<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Cauhinh */
?>
<div class="cauhinh-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'content:ntext',
            'ghi_chu:ntext',
        ],
    ]) ?>

</div>
