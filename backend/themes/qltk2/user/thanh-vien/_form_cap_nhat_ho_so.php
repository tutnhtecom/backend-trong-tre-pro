<?php

use common\models\myAPI;
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
/** @var $model \common\models\User*/
?>
<?php $form = ActiveForm::begin(['options' => ['id' => 'form-cap-nhat-ho-so','class'=>'row','status'=>'1']]); ?>
    <div class="row">
        <div class="col-md-4" style="display: flex; justify-content: center;align-items: center">
            <div class="avatar-upload">
                <div class="avatar-edit">
                    <input type='file' name="User[anh_nguoi_dung]" id="imageUpload" />
                    <label for="imageUpload"></label>
                </div>
                <div class="avatar-preview">
                    <img
                            id="imagePreview" style="background-image: url(<?=isset($model->anh_nguoi_dung)?'https://homeland.andin.io/images/'.$model->anh_nguoi_dung:Yii::$app->request->baseUrl.'/backend/themes/qltk2/assets/admin/layout/img/avatar3_small.jpg' ?>)"
                    />
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-4 ">
                    <?=$form->field($model,'username')->textInput(['disabled'=>'disabled'])->label("Username")?>
                </div>
                <div class="col-md-4 ">
                    <?=$form->field($model,'hoten')->textInput(['disabled'=>'disabled'])->label("Họ tên")?>
                </div>
                <div class="col-md-4 ">
                    <?=$form->field($model,'email')->textInput()->label("Email(<i class='text-danger'>*</i>)")?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 ">
                    <?=$form->field($model,'dien_thoai')->textInput()->label("Điện thoại(<i class='text-danger'>*</i>)")?>
                </div>
                <div class="col-md-4 ">
                    <?= myAPI::activeDateField2($form, $model, 'ngay_sinh', 'Ngày (<i class="text-danger">*</i>)', (date("Y") - 10) . ':' . (date("Y") + 2)) ?>
                </div>
                <div class="col-md-4 ">
                    <?=$form->field($model,'dia_chi')->textInput()->label("Địa chỉ")?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 ">
                    <?=$form->field($model,'password_cu')->textInput()->label("Mật khẩu cũ")?>
                </div>
                <div class="col-md-4 ">
                    <?=$form->field($model,'password_new')->textInput()->label("Mật khẩu mới")?>
                </div>
                <div class="col-md-4 ">
                    <?=$form->field($model,'password_config')->textInput()->label("Nhập lại mật khẩu")?>
                </div>
            </div>
        </div>
    </div>
    <style>

    </style>
<?php ActiveForm::end() ?>
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').css('background-image', 'url('+e.target.result +')');
                    $('#imagePreview').hide();
                    $('#imagePreview').fadeIn(650);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#imageUpload").change(function() {
            readURL(this);
        });
        $( function() {
            $( ".date" ).datepicker();
        } );
    </script>
