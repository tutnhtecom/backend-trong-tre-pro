<?php
use yii\helpers\Url;

/* @var $searchModel backend\models\search\DanhMucSearch */

return [

    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '1%',
        'header' => 'STT',
        'headerOptions' => ['class' => 'text-primary'],
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'name',
    ],
    [
        'headerOptions' => ['class' => 'text-primary', 'width' => '1%'],
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'type',
        'filter' => \yii\helpers\Html::activeTextInput($searchModel, 'type')
    ],
    [
        'value' => function($data){
            return \yii\bootstrap\Html::a('<i class="fa fa-edit"></i>',Url::to(['update','id'=>$data->id]), ['class' => 'text-gray','role'=>'modal-remote','title'=>'Cập nhật', 'data-toggle'=>'tooltip']);
        },
        'label' => 'Sửa',
        'format' => 'raw',
        'headerOptions' => ['class' => 'text-center text-primary','style'=>'width:1%;'],
        'contentOptions' => ['class' => 'text-center'],
    ],
    [
        'header' => 'Hủy',
        'headerOptions' => ['class' => 'text-center text-primary', 'width' => '1%'],
        'contentOptions' => ['class' => 'text-center'],
        'value' => function($data){
            $model = \backend\models\DanhMuc::findOne($data->id);
            if($model->active == 1)
                return \common\models\myAPI::createDeleteBtnInGrid(Url::toRoute(['danh-muc/delete', 'id' => $data->id]));
        },
        'format' => 'raw'
    ],
    [
        'header' => 'Khôi phục',
        'headerOptions' => ['class' => 'text-center text-primary', 'width' => '1%'],
        'contentOptions' => ['class' => 'text-center'],
        'value' => function($data){
            $model = \backend\models\DanhMuc::findOne($data->id);
            if($model->active == 0)
                return \yii\bootstrap\Html::a('<i class="fa fa-repeat"></i>',Url::toRoute(['danh-muc/back-up', 'id' => $data->id]), ['class' => 'text-gray','role'=>'modal-remote','title'=>'Khôi phục', 'data-toggle'=>'tooltip']);
        },
        'format' => 'raw'
    ]

];   
