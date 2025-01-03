<?php
/** @var $model User */

use backend\models\DanhMuc;
use backend\models\SanPham;
use common\models\myAPI;
use common\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<?php $form = ActiveForm::begin(['id' => 'form-khach-hang']) ?>
<?= Html::activeHiddenInput($model, 'id'); ?>
<?= Html::activeHiddenInput($model, 'nhom');?>
<?= Html::HiddenInput('id_san_pham',null,['id'=>'san_pham']); ?>
<?= Html::HiddenInput('ngay_xem',null,['id'=>'ngay_xem']); ?>
<div class="row">
    <div class="col-md-4"><?= $form->field($model, 'hoten')->textInput(['maxlength' => true]) ?></div>
    <?php if($model->isNewRecord): ?>
        <div class="col-md-4">
            <?= $form->field($model, 'dien_thoai')->textInput()->label('Điện thoại') ?>
        </div>
    <?php else: ?>
        <?php if(Yii::$app->user->id): ?>
            <div class="col-md-4">
                <?= $form->field($model, 'dien_thoai')->textInput()->label('Điện thoại') ?>
            </div>
        <?php else: ?>
            <?php if($model->user_id === Yii::$app->user->id): ?>
                <div class="col-md-4">
                    <?= $form->field($model, 'dien_thoai')->textInput()->label('Điện thoại') ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
    <div class="col-md-4">
        <?= myAPI::activeDateField2($form, $model, 'ngay_xem', 'Ngày xem', (date("Y") - 10 ).':'.(date("Y") + 2)) ?>
    </div>
</div>
<?= $form->field($model, 'dia_chi')->textInput()->label('Địa chỉ') ?>
<h4 class="text-primary">Nhu cầu khách hàng <a class="btn danh-sach-san-pham-phu-trach pull-right" href="#" data-value="<?=Yii::$app->user->id?>"><i class="fa fa-plus"></i> Chọn sản phẩm</a></h4>
<hr/>
<div id="danh-sach"></div>
<div id="nhap-tay">
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'nhu_cau_quan')->dropDownList(
                ArrayHelper::map(DanhMuc::findAll(['type' => DanhMuc::QUAN_HUYEN]), 'id', 'name'),
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
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'tieu_de')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
</div>

<?= $form->field($model, 'ghi_chu')->textarea(['rows'=>2])->label('Ghi chú') ?>

<?php ActiveForm::end() ?>



