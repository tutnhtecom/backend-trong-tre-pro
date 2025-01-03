<?php
use yii\helpers\Url;

return [
    [
        'header' => 'STT',
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
        'headerOptions' => ['style'=>'width:1%'],
    ],
        // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'id',
    // ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'ten',
        'label'=> 'Tên',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'ten_thanh_pho',
        'label'=> 'Cấp trên',
        'headerOptions' => ['style'=>'width:1%'],
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'type',
        'label'=> 'Phân loại',
        'headerOptions' => ['style'=>'width:1%']
    ],
    [
        'value' => function($data){
            return \yii\bootstrap\Html::a('<i class="fa fa-edit"></i>',Url::to(['sua-khu-vuc','id'=>$data->id]), ['class' => 'text-gray','role'=>'modal-remote','title'=>'Update', 'data-toggle'=>'tooltip']);
        },
        'label' => 'Sửa',
        'format' => 'raw',
        'contentOptions' => ['class' => 'text-center','style'=>'width:1%;'],
        'headerOptions' => ['class' => 'text-center','style'=>'width:1%;']
    ],
    [
        'header' => 'Hủy',
        'headerOptions' => ['class' => 'text-center', 'width' => '1%'],
        'contentOptions' => ['class' => 'text-center'],
        'value' => function($data){
            $model = \backend\models\DanhMuc::findOne($data->id);
            if($model->active == 1)
                return \common\models\myAPI::createDeleteBtnInGrid(Url::toRoute(['danh-muc/delete', 'id' => $data->id]));
        },
        'format' => 'raw'
    ],//
    [
        'header' => 'Khôi phục',
        'headerOptions' => ['class' => 'text-center', 'width' => '1%'],
        'contentOptions' => ['class' => 'text-center'],
        'value' => function($data){
            $model = \backend\models\DanhMuc::findOne($data->id);
            if($model->active == 0)
                return \yii\bootstrap\Html::a('<i class="fa fa-repeat"></i>',Url::toRoute(['danh-muc/backup', 'id' => $data->id]), ['class' => 'text-gray','role'=>'modal-remote','title'=>'Khôi phục', 'data-toggle'=>'tooltip']);
        },
        'format' => 'raw'
    ]
];
