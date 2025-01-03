<?php

use common\models\myAPI;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

/** @var $chi_nhanh [] */
/** @var  $model_khach_hang [] */
/** @var  $khach_hang \backend\models\QuanLyKhachHang */
/** @var $model \backend\models\ChamSocKhachHang */
?>
<div>
    <h4 class="text-primary">Thông tin khách hàng</h4>
    <div class="row">
        <!--                Họ tên-->
        <div class="col-md-2 col-xs-6"><p><strong>Họ tên:</strong></p></div>
        <div class="col-md-4 col-xs-6">
            <p><?= $khach_hang->hoten; ?></p>
        </div>

        <!--                Số điện thoại-->
        <div class="col-md-2 col-xs-6"><p><strong>Điện thoại:</strong></p></div>
        <div class="col-md-2 col-xs-6">
            <p><?= $khach_hang->dien_thoai ?></p>
        </div>
    </div>
    <div class="row">
        <!--                Email-->
        <div class="col-md-2 col-xs-6"><p><strong>Email:</strong></p></div>
        <div class="col-md-4 col-xs-6">
            <p><?= $khach_hang->email != '' ? $khach_hang->email : '<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>' ?></p>
        </div>
        <!--                Ngày sinh-->
        <div class="col-md-2 col-xs-6"><p><strong>Ngày sinh:</strong></p></div>
        <div class="col-md-2 col-xs-6">
            <p><?= $khach_hang->ngay_sinh != '' ? $khach_hang->ngay_sinh : '<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>' ?></p>
        </div>
    </div>
    <div class="row">
        <!--                Email-->
        <div class="col-md-2 col-xs-6"><p><strong>Nguồn khách:</strong></p></div>
        <div class="col-md-4 col-xs-6">
            <p><?= $khach_hang->name != '' ? $khach_hang->name : '<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>' ?></p>
        </div>
        <!--                Ngày sinh-->
        <div class="col-md-2 col-xs-6"><p><strong>Nhân viên sale:</strong></p></div>
        <div class="col-md-2 col-xs-6">
            <p><?= $khach_hang->nhan_vien_sale != '' ? $khach_hang->nhan_vien_sale : '<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>' ?></p>
        </div>
    </div>
    <div class="row">
        <!--                Địa chỉ-->
        <div class="col-md-2 col-xs-6"><p><strong>Địa chỉ:</strong></p></div>
        <div class="col-md-4 col-xs-6">
            <p><?= $khach_hang->dia_chi != '' ? $khach_hang->dia_chi : '<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>' ?></p>
        </div>
    </div>
</div>

<div class="tabbale-line">
    <ul class="nav nav-tabs ">

        <li class="active">
            <a href="#tab_15_1" data-toggle="tab">THÔNG TIN CHĂM SÓC</a>
        </li>
        <li>
            <a href="#tab_15_2" data-toggle="tab">LỊCH SỬ CHĂM SÓC</a>
        </li>
    </ul>

</div>
<?php $form = \yii\widgets\ActiveForm::begin([
    'options' => [
        'id' => 'form-cham-soc',
    ]
]); ?>
<div class="tab-content">
    <div class="tab-pane active" id="tab_15_1">
        <?= Html::activeHiddenInput($model, 'khach_hang_id'); ?>
        <div class="row">
            <?php if (!\common\models\User::hasVaiTro(\backend\models\VaiTro::GIAM_DOC)):?>
            <div class="col-md-3">
                <?= $form->field($model, 'chi_nhanh_nhan_vien_id')->dropDownList($chi_nhanh, ['class' => 'form-control', 'prompt' => '--Chọn CN--'])->label("Chi nhánh (<i class='text-danger'>*</i>) ") ?>
                <span class="text-danger error hidden">Vui lòng chọn chi nhánh</span>
            </div>
            <?php endif;?>
            <div class="col-md-3">
                <?= $form->field($model, 'nhan_vien_cham_soc_id')->dropDownList($nhan_vien, ['class' => 'form-control', 'prompt' => '--Chọn--'])->label('Nhân viên (<i class="text-danger">*</i>)') ?>
                <span class="text-danger error hidden">Vui lòng chọn nhân viên chăm sóc</span>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <h4 class="text-primary">Chăm sóc</h4>
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <?= myAPI::activeDateField2($form, $model, 'ngay_cham_soc', 'Ngày (<i class="text-danger">*</i>)', (date("Y") - 10) . ':' . (date("Y") + 2)) ?>
                                <span class="text-danger error hidden">Vui lòng chọn ngày chăm sóc</span>
                            </div>
                            <div class="col-md-3">
                                <?= $form->field($model, 'gio')->textInput(['type' => 'number','max'=>'23','min'=>'0'])->label('Giờ  ') ?>
                            </div>
                            <div class="col-md-3">
                                <?= $form->field($model, 'phut')->textInput(['type' => 'number','max'=>'23','min'=>'0'])->label('Phút ') ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?= $form->field($model, 'noi_dung_cham_soc')->textarea()->label('Nội dung chăm sóc (<i class="text-danger">*</i>)') ?>
                        <span class="text-danger error hidden">Vui lòng nhập nội chăm sóc</span>

                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <h4 class="text-primary">Hẹn lịch</h4>
                <div class="row">
                    <div class="col-md-6">
                        <?= myAPI::activeDateField2($form, $model, 'ngay_hen', 'Ngày  ', (date("Y") - 10) . ':' . (date("Y") + 2)) ?>
                        <span class="text-danger error hidden">Vui lòng chọn ngày hẹn</span>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($model, 'gio_hen')->textInput(['type' => 'number','max'=>'23','min'=>'0'])->label('Giờ') ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($model, 'phut_hen')->textInput(['type' => 'number','max'=>'59','min'=>'0'])->label('Phút') ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?= $form->field($model, 'noi_dung_hen')->textarea()->label('Nội dung hẹn') ?>
                        <span class="text-danger error hidden">Vui lòng nhập nội dung hẹn</span>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="tab-pane " id="tab_15_2">
        <div class="view-lich-su-cham-soc">
            <?= $view_lich_su_cham_soc ?>
        </div>
    </div>
    <?php ActiveForm::end() ?>
</div>
