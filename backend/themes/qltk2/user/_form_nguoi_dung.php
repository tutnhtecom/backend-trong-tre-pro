<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\myAPI;
use backend\models\VaiTro;
use yii\helpers\ArrayHelper;
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?=Html::activeHiddenInput($model, 'nhom');?>
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'hoten')->textInput(['maxlength' => true, 'autocomplete' => 'false']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'username')->textInput()->label('Tài khoản')?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'password_hash')->textInput(['maxlength' => true, 'type' => 'password', 'autocomplete' => 'new-password'])->label('Mật khẩu') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'dien_thoai')->textInput()->label('Điện thoại');?>
        </div>
        <div class="col-md-4">
            <?= myAPI::activeDateField2($form, $model, 'ngay_sinh', 'Ngày sinh', (date("Y") - 50).':'.date("Y")) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'he_so_luong')->textInput(['type'=>'number'])->label('Hệ số lương');?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <?= $form->field($model, 'dia_chi')->textInput(['maxlength' => true])->label('Địa chỉ') ?>
        </div>
    </div>

    <h4 class="text-primary">Vai trò</h4><hr/>
    <?= Html::checkboxList('Vaitrouser[]', $vaitros, ArrayHelper::map(VaiTro::find()->all(), 'id', 'name'),
        ['prompt' => '', 'class' => 'list-block']) ?>

    <?php if (!Yii::$app->request->isAjax){ ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>

    <?php ActiveForm::end(); ?>

</div>
