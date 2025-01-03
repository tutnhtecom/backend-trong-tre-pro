<?php
use yii\helpers\Url;

return [
    [
        'class' => 'kartik\grid\CheckboxColumn',
        'width' => '20px',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'name',
    ],

    [
        'class'=>'\kartik\grid\DataColumn',
        'label' => 'SL CV',
        'value' => function($data){
            /** @var $data \backend\models\DanhMuc */
            $soluong = count($data->viTriCongViecs);
            return $soluong > 0 ? $soluong : '';
        },
        'headerOptions' => ['width' => '3%', 'class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right']
    ],
    [
        'value' => function($data){
            return \yii\bootstrap\Html::a('<i class="fa fa-eye"></i>',Url::to(['xem-vi-tri-cong-viec','id'=>$data->id]), ['class' => 'text-gray']);
        },
        'label' => 'Xem',
        'format' => 'raw',
        'contentOptions' => ['class' => 'text-center'],
        'headerOptions' => ['class' => 'text-center', 'width' => '3%']
    ],
    [
        'value' => function($data){
            return \yii\bootstrap\Html::a('<i class="fa fa-tags"></i>',Url::toRoute(['phong-ban/them-cong-viec-muc-tieu', 'id' => $data->id]), ['class' => 'text-gray']);
        },
        'label' => 'CVMT',
        'format' => 'raw',
        'contentOptions' => ['class' => 'text-center','style'=>'width:3%;'],
        'headerOptions' => ['class' => 'text-center','style'=>'width:3%;']
    ],
    [
        'value' => function($data){
            return \yii\bootstrap\Html::a('<i class="fa fa-edit"></i>',Url::toRoute(['danh-muc/sua-vi-tri-cong-viec', 'id' => $data->id]), ['class' => 'text-gray']);
        },
        'label' => 'Sửa',
        'format' => 'raw',
        'contentOptions' => ['class' => 'text-center','style'=>'width:3%;'],
        'headerOptions' => ['class' => 'text-center','style'=>'width:3%;']
    ],
    [
        'header' => 'Xóa',
        'headerOptions' => ['class' => 'text-center', 'width' => '3%'],
        'contentOptions' => ['class' => 'text-center'],
        'value' => function($data){
            return \yii\helpers\Html::a('<i class="fa fa-trash"></i>', '#', ['class' => 'btn-delete-vitricongviec text-danger', 'data-value' => $data->id]);
        },
        'format' => 'raw'
    ]

];   
