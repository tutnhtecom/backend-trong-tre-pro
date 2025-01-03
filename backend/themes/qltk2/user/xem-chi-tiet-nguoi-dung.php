<?php
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
/** @var $khach_hang */
?>
<?php $form = ActiveForm::begin(['options' => ['id' => 'form-cap-nhat-ho-so','class'=>'row','status'=>'1']]); ?>
    <div class="row">
        <div class="col-md-3" style="display: flex; justify-content: center;align-items: center">
            <div class="avatar-upload">
                <div class="avatar-edit">
                    <input type='file' name="anh_nguoi_dung" id="imageUpload" />
                    <label for="imageUpload"></label>
                </div>
                <div class="avatar-preview">
                    <div id="imagePreview" style="background-image: url(<?=isset(Yii::$app->user->identity->anh_nguoi_dung)?Yii::$app->request->baseUrl.'/images/'.Yii::$app->user->identity->anh_nguoi_dung:Yii::$app->request->baseUrl.'/backend/themes/qltk2/assets/admin/layout/img/avatar3_small.jpg' ?>)">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="row">
                <div class="col-md-12 ">
                    <div class="col-md-12">
                        <label>Họ tên</label>
                    </div>
                    <div class="col-md-12">
                        <input  name="hoten" value="<?=$khach_hang->hoten?>" class="form-control " type="text"   >
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-6 ">
                    <div class="col-md-12 required">
                        <label>Email</label>
                    </div>
                    <div class="col-md-12 required div-email">
                        <input onchange="CheckEmail(<?=Yii::$app->user->identity->id?>)" name="email" value="<?=$khach_hang->email?>" class="form-control email" type="text"   >
                    </div>
                </div>
                <div class="col-md-6   ">
                    <div class="col-md-12 required ">
                        <label>Điện thoại</label>
                    </div>
                    <div class="col-md-12 required div-dien-thoai">
                        <input onchange="CheckDienThoai(<?=$khach_hang->id?>)"  name="dien_thoai" value="<?=$khach_hang->dien_thoai?>" class="form-control dien-thoai" type="text"   >
                    </div>
                </div>
                <div class="col-md-6  ">
                    <div class="col-md-12">
                        <label>Ngày sinh</label>
                    </div>
                    <div class="col-md-12 required has-success">
                        <input  name="ngay_sinh" value="<?=$khach_hang->ngay_sinh?>" class="form-control  date " type="text"   >
                    </div>
                </div>
                <div class="col-md-6 ">
                    <div class="col-md-12">
                        <label>Địa chỉ</label>
                    </div>
                    <div class="col-md-12 required ">
                        <input  name="dia_chi" value="<?=$khach_hang->dia_chi?>" class="form-control  " type="text"   >
                    </div>
                </div>
            </div>
<!--            <div class="row">-->
<!--                <div class="col-md-4 ">-->
<!--                    <div class="col-md-12 ">-->
<!--                        <label>Mật khẩu cũ</label>-->
<!--                    </div>-->
<!--                    <div class="col-md-12 div-password_hash">-->
<!--                        <input  name="password_hash"  class="form-control password_hash" type="password" >-->
<!--                    </div>-->
<!--                </div>-->
<!--                <div class="col-md-4 ">-->
<!--                    <div class="col-md-12">-->
<!--                        <label>Mật khẩu mới</label>-->
<!--                    </div>-->
<!--                    <div class="col-md-12 div-password_new">-->
<!--                        <input  name="password_new"  class="form-control password_new" type="password" >-->
<!--                    </div>-->
<!--                </div>-->
<!--                <div class="col-md-4  ">-->
<!--                    <div class="col-md-12">-->
<!--                        <label>Nhập lại mật khẩu</label>-->
<!--                    </div>-->
<!--                    <div class="col-md-12 div-password_config">-->
<!--                        <input   class="form-control password_config" type="password" >-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
        </div>
    </div>
    <style>

        .avatar-upload {
            position: relative;
            max-width: 205px;
        }
        .avatar-upload .avatar-edit {
            position: absolute;
            right: 12px;
            z-index: 1;
            top: 10px;
        }
        .avatar-upload .avatar-edit input {
            display: none;
        }
        .avatar-upload .avatar-edit input + label {
            display: inline-block;
            width: 34px;
            height: 34px;
            margin-bottom: 0;
            border-radius: 100% !important;
            background: #FFFFFF;
            border: 1px solid transparent;
            box-shadow: 0px 2px 4px 0px rgba(0,0,0,0.12);
            cursor: pointer;
            font-weight: normal;
            transition: all .2s ease-in-out;
        }
        .avatar-upload .avatar-edit input + label:hover {
            background: #f1f1f1;
            border-color: #d6d6d6;
        }
        .avatar-upload .avatar-edit input + label:after {
            content: "\f040";
            font-family: 'FontAwesome';
            color: #757575;
            position: absolute;
            top: 10px;
            left: 0;
            right: 0;
            text-align: center;
            margin: auto;
        }
        .avatar-upload .avatar-preview {
            width: 192px;
            height: 192px;
            position: relative;
            border-radius: 100% !important;
            border: 6px solid #F8F8F8;
            box-shadow: 0px 2px 4px 0px rgba(0,0,0,0.1);
        }
        .avatar-upload .avatar-preview > div {
            width: 100%;
            height: 100%;
            border-radius: 100% !important;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }
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
<?php $this->registerCssFile(Yii::$app->request->baseUrl.'/backend/assets/css/jquery-ui.css'); ?>
<?php $this->registerJsFile(Yii::$app->request->baseUrl.'/backend/assets/js-view/user7.js');