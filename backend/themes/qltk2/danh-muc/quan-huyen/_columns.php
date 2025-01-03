<?php
use yii\helpers\Url;

return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '1%',
        'header' => 'STT'
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'name',
    ],
    [
        'value' => function($data){
            return \yii\bootstrap\Html::a('<i class="fa fa-edit"></i>',Url::to(['update-quan-huyen','id'=>$data->id]), ['class' => 'text-gray','role'=>'modal-remote','title'=>'Cập nhật', 'data-toggle'=>'tooltip']);
        },
        'label' => 'Sửa',
        'format' => 'raw',
        'headerOptions' => ['class' => 'text-center','style'=>'width:1%;'],
        'contentOptions' => ['class' => 'text-center'],
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
    ],
    [
        'header' => 'Khôi phục',
        'headerOptions' => ['class' => 'text-center', 'width' => '1%'],
        'contentOptions' => ['class' => 'text-center'],
        'value' => function($data){
            $model = \backend\models\DanhMuc::findOne($data->id);
            if($model->active == 0)
                return \yii\bootstrap\Html::a('<i class="fa fa-repeat"></i>',Url::toRoute(['danh-muc/back-up', 'id' => $data->id]), ['class' => 'text-gray','role'=>'modal-remote','title'=>'Khôi phục', 'data-toggle'=>'tooltip']);
        },
        'format' => 'raw'
    ]

];   