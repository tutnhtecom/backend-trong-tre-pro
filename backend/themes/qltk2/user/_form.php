<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
use common\models\User;
if($model->nhom == User::KHACH_HANG)
    $this->title = User::KHACH_HANG;
?>

<div class="user-form">

    <?php $form = ActiveForm::begin([
            'options' => ['autocomplete' => 'off']
    ]); ?>
    <?=Html::activeHiddenInput($model, 'nhom');?>
    <h4 class="text-primary">THÔNG TIN KHÁCH HÀNG</h4>
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'hoten')->textInput(['maxlength' => true, 'autocomplete' => 'false']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'dien_thoai')->textInput()->label('Điện thoại');?>
        </div>
    </div>
    <h4 class="text-primary">NHU CẦU KHÁCH HÀNG</h4>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'nhu_cau_quan')->dropDownList($quan_huyen,['prompt'=>'-Chọn-'])->label('Quận huyện');?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'nhu_cau_huong')->dropDownList([ 'Đông' => 'Đông', 'Tây' => 'Tây', 'Nam' => 'Nam', 'Bắc' => 'Bắc', 'Đông Tứ Trạch' => 'Đông Tứ Trạch', ' Tây Tứ Trạch' => ' Tây Tứ Trạch', 'Tây Nam' => 'Tây Nam', 'Đông Nam' => 'Đông Nam', ], ['prompt' => '--Chọn--'])->label('Hướng') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'nhu_cau_dien_tich_tu')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions' => [
                    'alias' => 'numeric',
                    'allowMinus'=>false,
                    'groupSize'=>3,
                    'radixPoint'=> ",",
                    'groupSeparator' => '.',
                    'autoGroup' => true,
                    'removeMaskOnSubmit' => true
                ], 'options' => ['min' => 0]
            ])->label('Diện tích từ (m<sup>2</sup>)'); ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'nhu_cau_dien_tich_den')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions' => [
                    'alias' => 'numeric',
                    'allowMinus'=>false,
                    'groupSize'=>3,
                    'radixPoint'=> ",",
                    'groupSeparator' => '.',
                    'autoGroup' => true,
                    'removeMaskOnSubmit' => true
                ], 'options' => ['min' => 0]
            ])->label('Diện tích đến (m<sup>2</sup>)'); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'nhu_cau_gia_tu')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions' => [
                    'alias' => 'numeric',
                    'allowMinus'=>false,
                    'groupSize'=>3,
                    'radixPoint'=> ",",
                    'groupSeparator' => '.',
                    'autoGroup' => true,
                    'removeMaskOnSubmit' => true
                ], 'options' => ['min' => 0]
            ])->label('Giá (Tỷ) từ ...'); ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'nhu_cau_gia_den')->widget(\yii\widgets\MaskedInput::className(), [
                'clientOptions' => [
                    'alias' => 'numeric',
                    'allowMinus'=>false,
                    'groupSize'=>3,
                    'radixPoint'=> ",",
                    'groupSeparator' => '.',
                    'autoGroup' => true,
                    'removeMaskOnSubmit' => true
                ], 'options' => ['min' => 0]
            ])->label('Đến ...'); ?>
        </div>
    </div>

	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Thêm mới' : 'Cập nhật', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>

</div>
