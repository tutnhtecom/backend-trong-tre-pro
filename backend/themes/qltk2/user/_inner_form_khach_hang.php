<?php
/**
 * @var $form ActiveForm
 * @var $model User
 * @var $thongTin ThongTinBanHang
 */

use backend\models\DanhMuc;
use backend\models\SanPham;
use backend\models\ThongTinBanHang;
use common\models\myAPI;
use common\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<?= Html::activeHiddenInput($model, 'nhom');?>
<div class="row">
    <div class="col-md-4"><?= $form->field($model, 'hoten')->textInput(['maxlength' => true]) ?></div>

    <div class="col-md-4"> <?= $form->field($model, 'dien_thoai')->textInput()->label('Điện thoại') ?></div>

    <div class="col-md-4">
        <?= myAPI::activeDateField2($form, $thongTin, 'ngay_xem', 'Ngày xem', (date("Y") -30 ).':'.(date("Y") + 2))?>
    </div>
</div>
<?= $form->field($model, 'dia_chi')->textInput()->label('Địa chỉ') ?>
<h4 class="text-primary">Nhu cầu khách hàng</h4>
<hr/>
<div class="row">
    <div class="col-md-6">
        <?= $form->field($model, 'nhu_cau_quan')->dropDownList(ArrayHelper::map(DanhMuc::findAll(['type' => DanhMuc::QUAN_HUYEN]), 'id', 'name'),
                ['prompt' => '', 'multiple' => 'multiple'])->label('Quận') ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'nhu_cau_huong')->dropDownList(SanPham::$arr_huong,
            ['prompt' => '',  'multiple' => 'multiple'])->label('Hướng') ?>
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        <?= $form->field($model, 'nhu_cau_gia_tu')->textInput(['type' => 'number', 'step' => 0.01])->label('Giá từ...') ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'nhu_cau_gia_den')->textInput(['type' => 'number', 'step' => 0.01])->label('đến...') ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'nhu_cau_dien_tich_tu')->textInput(['type' => 'number', 'step' => 0.01])->label('Diện tích từ...') ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'nhu_cau_dien_tich_den')->textInput(['type' => 'number', 'step' => 0.01])->label('đến...') ?>
    </div>
</div>
<?= $form->field($model, 'ghi_chu')->textarea(['rows'=>'3'])->label('Ghi chú') ?>

