<?php
use yii\helpers\Url;

return [
    [
        'class' => 'kartik\grid\CheckboxColumn',
        'width' => '20px',
    ],
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'name',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'label' => 'SLPB',
        'value' => function($data){
            return $data->SL_PhongBan > 0 ? '<span class="text-primary">'.$data->SL_PhongBan.'</span>' : "";
        },
        'format' => 'raw',
        'attribute' => 'SL_PhongBan',
        'headerOptions' => ['width' => '3%', 'class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right']
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'label' => 'Nhân viên',
        'attribute' => 'SL_NhanVien',
        'format' => 'raw',
        'value' => function($data){
            return $data->SL_NhanVien > 0 ? '<span class="text-primary">'.$data->SL_NhanVien.'</span>' : "";
        },
        'headerOptions' => ['width' => '3%', 'class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right']
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'label' => 'Công việc',
        'attribute' => 'SL_CongViec',
        'format' => 'raw',
        'value' => function($data){
            return $data->SL_CongViec > 0 ? '<span class="text-primary">'.$data->SL_CongViec.'</span>' : "";
        },
        'headerOptions' => ['width' => '3%', 'class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right']
    ],
    [
        'value' => function($data){
            return \yii\bootstrap\Html::a('<i class="fa fa-eye"></i>',Url::toRoute(['danh-muc/xem-vi-tri-cong-viec', 'id' => $data->id]), ['data-value' => $data->id, 'class' => 'text-success btn-xem-chit-tiet']);
        },
        'label' => 'Xem',
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