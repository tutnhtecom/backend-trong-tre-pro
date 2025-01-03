<?php

use backend\models\NhuCauKhachHang;
use backend\models\SanPham;
use yii\widgets\ActiveForm;

use yii\bootstrap\Html;
use common\models\myAPI;

/** @var $khach_hang */
/** @var $quan_huyen [] */
/** @var $phuong_xa [] */
/** @var $duong_pho [] */
/** @var $thanh_pho [] */
/** @var $arr_huong [] */
/** @var $model \backend\models\NhuCauKhachHang */
?>
<?php $form = ActiveForm::begin([
    'options' => ['autocomplete' => 'off', 'id' => 'form-khach-hang']
]);

?>
<div class="hidden">
    <?= $form->field($model, 'khach_hang_id')->textInput(['type' => 'hidden', 'value' => $khach_hang->id])->label() ?>
</div>
<div class="thong-bao-them-khach-hang">

</div>
<a type="button" class="" data-toggle="collapse" data-target="#nhu-cau">
    <h4 class="text-primary">NHU CẦU KHÁCH HÀNG </h4>
</a>
<div id="nhu-cau" class="collapse in ">
        <div class="row">
            <div class="col-md-3">
                <?= $form->field($model, 'nhu_cau_loai_hinh')->dropDownList([
                    SanPham::NHA => SanPham::NHA,
                    SanPham::DAT => SanPham::DAT,
                    SanPham::DU_AN => SanPham::DU_AN,
                    SanPham::CHO_THUE => SanPham::CHO_THUE
                ], ['prompt' => '--Chọn--'])->label('Loại hình') ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'nhu_cau_khoang_dien_tich')->dropDownList(NhuCauKhachHang::arr_khoang_dien_tich, ['prompt' => '--Chọn--'])->label("Diện tích") ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'nhu_cau_gia_tu')->widget(\yii\widgets\MaskedInput::className(), [
                    'clientOptions' => [
                        'alias' => 'numeric',
                        'allowMinus' => false,
                        'groupSize' => 3,
                        'radixPoint' => ",",
                        'groupSeparator' => '.',
                        'autoGroup' => true,
                        'removeMaskOnSubmit' => true
                    ], 'options' => ['min' => 0]
                ])->label('Giá từ (Tỷ)'); ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'nhu_cau_gia_den')->widget(\yii\widgets\MaskedInput::className(), [
                    'clientOptions' => [
                        'alias' => 'numeric',
                        'allowMinus' => false,
                        'groupSize' => 3,
                        'radixPoint' => ",",
                        'groupSeparator' => '.',
                        'autoGroup' => true,
                        'removeMaskOnSubmit' => true
                    ], 'options' => ['min' => 0]
                ])->label('Giá đến (Tỷ)'); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <?= $form->field($model, 'nhu_cau_huong')->dropDownList(
                    $arr_huong,
                    ['prompt' => '--Chọn--']
                )->label('Hướng') ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'nhu_cau_quan_huyen')->dropDownList($quan_huyen, ['prompt' => '--Chọn--'])->label('Quận huyện') ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'nhu_cau_phuong_xa')->dropDownList($phuong_xa, ['prompt' => '--Chọn--'])->label('Phường xã') ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'nhu_cau_duong_pho')->dropDownList($duong_pho, ['prompt' => '--Chọn--'])->label('Đường phố') ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'ghi_chu')->textInput()->label('Ghi chú') ?>
            </div>
        </div>
        <p>
            <?= Html::a('<i class="fa fa-search"></i> Tìm sản phẩm', '', ['class' => 'btn btn-primary btn-tim-san-pham']) ?>
        </p>
        <div class="form-chon-san-pham">

        </div>
    </div>
<?php ActiveForm::end(); ?>
