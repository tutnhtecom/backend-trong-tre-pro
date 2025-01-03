<?php
use yii\helpers\Url;

return [

    [
        'class' => 'kartik\grid\SerialColumn',
        'header' => 'STT',
        'width' => '30px',
        'headerOptions' => ['class' => 'text-primary'],
    ],

    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'nhom',
        'filter' => \yii\helpers\Html::activeDropDownList(
            $searchModel, 'nhom',
            \yii\helpers\ArrayHelper::map(
                \backend\models\ChucNang::find()->all(),
                'nhom', 'nhom'
            ), [
                'class' => 'form-control',
                'prompt' => 'Tất cả'
            ]
        )
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'name',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'controller_action',
    ],

    [
        'value' => function($data){
            return \yii\bootstrap\Html::a('<i class="fa fa-edit"></i>',Url::to(['update','id'=>$data->id]), ['class' => 'text-gray','role'=>'modal-remote','title'=>'Update', 'data-toggle'=>'tooltip']);
        },
        'label' => 'Sửa',
        'format' => 'raw',
        'contentOptions' => ['class' => 'text-center','style'=>'width:3%;'],
        'headerOptions' => ['class' => 'text-center text-primary','style'=>'width:3%;']
    ],
    [
        'header' => 'Xóa',
        'headerOptions' => ['class' => 'text-center text-primary', 'width' => '3%'],
        'contentOptions' => ['class' => 'text-center'],
        'value' => function($data){
            return \common\models\myAPI::createDeleteBtnInGrid(Url::toRoute(['chuc-nang/delete', 'id' => $data->id]));
        },
        'format' => 'raw'
    ]

];   
