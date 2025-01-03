<?php

use yii\widgets\ActiveForm;
use  yii\bootstrap\Html;

/** @var $type_khach_hang [] */
/** @var $trang_thai_nhom_khach_hang [] */
/** @var $model */
/** @var $quan_huyen [] */
/** @var $phuong_xa [] */
/** @var $duong_pho [] */
/** @var $thanh_pho [] */
/** @var $arr_huong [] */
/** @var $nguon_khach [] */
/** @var $nhan_vien_sale [] */
/** @var $model \backend\models\NhuCauKhachHang */
?>
<?php $form = \yii\widgets\ActiveForm::begin([
    'options' => [
        'id' => 'form-sua-khach-hang',
    ]
]); ?>
<?= Html::hiddenInput('User[id]',$model->id)?>
<div class="row">
    <div class="col-md-3">
        <?=$form->field($model,'type_khach_hang')->dropDownList($type_khach_hang,['class'=>'form-control','prompt'=>'--Chọn--'])->label('Nhóm khách hàng')?>
    </div>
    <div class="col-md-3">
        <?=$form->field($model,'phan_nhom')->dropDownList($phan_nhom,['class'=>'form-control','prompt'=>'--Chọn--'])->label('Phân nhóm')?>
    </div>
    <div class="col-md-3">
        <?=$form->field($model,'phan_tuan')->dropDownList(\common\models\User::getSoTuanTrongThang($tuan),['class'=>'form-control','prompt'=>'--Chọn--'])->label('Phân tuần')?>
    </div>
    <div class="col-md-3">
        <?=$form->field($model,'chi_nhanh_nhan_vien_id')->dropDownList($chi_nhanh,['class'=>'form-control','prompt'=>'--Chọn--'])->label('Chi nhánh')?>
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        <?=$form->field($model,'hoten')->textInput()->label('Họ tên')?>
    </div>
    <div class="col-md-3">
        <?=$form->field($model,'ngay_sinh')->textInput()->label('Ngày sinh')?>
    </div>

    <div class="col-md-3">
        <?=$form->field($model,'email')->textInput()->label('Email')?>
    </div>
    <div class="col-md-3">
        <?=$form->field($model,'dien_thoai')->textInput()->label('Điện thoại')?>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <?=$form->field($model,'dia_chi')->textInput()->label('Địa chỉ')?>
    </div>
    <div class="col-md-3">
        <?=$form->field($model,'nguon_khach_id')->dropDownList($nguon_khach,['class'=>'form-control','prompt'=>'--Chọn--'])->label('Nguồn khách')?>
    </div>
    <div class="col-md-3">
        <?=$form->field($model,'nhan_vien_sale_id')->dropDownList($nhan_vien_sale,['class'=>'form-control','prompt'=>'--Chọn--'])->label('Nhân viên sale')?>
    </div>
</div>
<?php ActiveForm::end() ?>