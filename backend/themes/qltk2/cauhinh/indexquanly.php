<?php
/**
 * Created by PhpStorm.
 * User: hungluong
 * Date: 5/21/18
 * Time: 07:28
 */?>
<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\CauhinhSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cấu hình hệ thống';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cauhinh-index">
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}',
                'header' => 'Sửa',
                'headerOptions' => ['width' => '3%']
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?></div>
