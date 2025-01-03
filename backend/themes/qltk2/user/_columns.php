<?php
use yii\helpers\Url;
/* @var $searchModel Backend\models\search\UserSearch */

return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'header' => 'STT',
        'headerOptions' => ['class' => 'text-primary', 'width' => '3%']
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'username',
        'label' => 'Tên đăng nhập'
    ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'id',
    // ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'hoten',
        'label' => 'Họ tên',
        'headerOptions' => ['width' => '3%']
    ],

    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'dien_thoai',
        'label' => 'Điện thoại',
        'headerOptions' => ['width' => '3%']
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'email',
        'headerOptions' => ['width' => '3%']
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'vai_tro_name',
        'value' => function($data){
            return str_replace(',', '<br/>', $data->vai_tro_name);
        },
        'format' => 'raw',
        'label' => 'Vai trò',
        'headerOptions' => ['width' => '3%']
    ],

    [
        'header' => 'Sửa',
        'value' => function($data) {
            return \yii\bootstrap\Html::a('<i class="fa fa-edit"></i>',Url::toRoute(['user/update', 'id' => $data->id]), ['role' => 'modal-remote', 'data-toggle' => 'tooltip','id'=>'select2']);
        },
        'format' => 'raw',
        'headerOptions' => ['width' => '3%', 'class' => 'text-center text-primary'],
        'contentOptions' => ['class' => 'text-center']
    ],

    [
        'header' => 'Xoá',
        'value' => function($data) {
            return \yii\bootstrap\Html::a('<i class="fa fa-trash"></i>','#', ['class'=>'btn-xoa-khach-hang text-danger','data-value'=>$data->id]);
        },
        'format' => 'raw',
        'headerOptions' => ['width' => '3%', 'class' => 'text-center text-primary'],
        'contentOptions' => ['class' => 'text-center']
    ],
];
?>

