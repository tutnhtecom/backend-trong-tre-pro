<?php use backend\models\NhuCauKhachHang;
use backend\models\SanPham;
use common\models\myAPI;
use common\models\User;
use yii\bootstrap\Html;

$form = \yii\widgets\ActiveForm::begin([
    'options' => [
        'id' => 'form-khach-hang',
    ]
]); ?>
<?= \yii\helpers\Html::activeHiddenInput($model, 'id'); ?>
<?= \yii\helpers\Html::activeHiddenInput($nhu_cau, 'id'); ?>
<div class="row">
    <div class="col-md-3">
        <?php if(!$model->isNewRecord):?>
        <?= $form->field($model, 'type_khach_hang')->dropDownList(
            $typeKhachHang,
            array('class' => 'form-control', 'prompt' => '-- Phân loại --')
        )->label('Phân loại' . "(<span class='text-danger'>*</span>)"); ?>
        <?php else:?>
        <?= $form->field($model, 'type_khach_hang')->dropDownList(
            $typeKhachHang,
            array('class' => 'form-control', 'prompt' => '-- Phân loại --')
        )->label('Phân loại' . "(<span class='text-danger'>*</span>)"); ?>
        <?php endif;?>
        <span class="text-danger error hidden error-phan-loai-kh">Chưa chọn phân loại</span>
    </div>
    <div class="col-md-3">
        <?php if($model->isNewRecord):?>
            <?= $form->field($model, 'phan_nhom')->dropDownList([], ['class' => 'form-control', 'prompt' => '--Chọn--'])->label('Chọn nhóm' ) ?>
        <?php else:?>
            <?= $form->field($model, 'phan_nhom')->dropDownList($model->type_khach_hang!=''?User::arr_phan_nhom[$model->type_khach_hang]:[], ['class' => 'form-control', 'prompt' => '--Chọn--'])->label('Chọn nhóm') ?>
        <?php endif;?>
    </div>
    <div class="col-md-6">
        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'phan_tuan')->dropDownList($arrTuanTrongThang, ['class' => 'form-control', 'prompt' => '--Chọn--'])->label("Tuần ") ?>
<!--                <span class="text-danger error hidden error-tuan">Chưa nhập tuần</span>-->
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'thang')->textInput(['type' => 'number', 'class' => 'form-control'])->label('Tháng ' . "(<span class='text-danger'>*</span>)") ?>
                <span class="text-danger error hidden error-tuan">Chưa nhập tháng</span>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'nam')->textInput(['type' => 'number', 'class' => 'form-control'])->label('Năm ' . "(<span class='text-danger'>*</span>)") ?>
                <span class="text-danger error hidden error-tuan">Chưa nhập năm</span>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        <?= $form->field($model, 'chi_nhanh_nhan_vien_id')->dropDownList($chiNhanh, ['class' => 'form-control', 'prompt' => '--Chọn--'])->label('Chi nhánh ' . "(<span class='text-danger'>*</span>)") ?>
        <span class="text-danger error hidden error-chi-nhanh">Chưa chọn chi nhánh</span>
    </div>
    <div class="col-md-3">
        <?php if($model->isNewRecord):?>
            <?= $form->field($model, 'nhan_vien_sale_id')->dropDownList([], ['class' => 'form-control', 'prompt' => '--Chọn--'])->label('Sale ' . "(<span class='text-danger'>*</span>)") ?>
        <?php else:?>
            <?= $form->field($model, 'nhan_vien_sale_id')->dropDownList($nhan_vien, ['class' => 'form-control', 'prompt' => '--Chọn--'])->label('Sale ' . "(<span class='text-danger'>*</span>)") ?>
        <?php endif;?>
        <span class="text-danger error hidden error-chi-nhanh">Chưa chọn Sale</span>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'hoten')->textInput()->label('Họ tên ' . "(<span class='text-danger'>*</span>)") ?>
        <span class="text-danger error hidden error-ho-ten">Chưa nhập họ tên</span>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'dien_thoai')->textInput()->label('Điện thoại ' . "(<span class='text-danger'>*</span>)") ?>
        <span class="text-danger error hidden error-phan-loai-kh">Chưa điền SĐT</span>
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        <?=myAPI::activeDateField2($form, $model, 'ngay_sinh', 'Ngày sinh', '1940:'.date("Y")); ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'dia_chi')->textInput()->label('Địa chỉ') ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'nguon_khach_id')->dropDownList($nguonKhach, ['class' => 'form-control', 'prompt' => '--Chọn--'])->label('Nguồn khách ' . "(<span class='text-danger'>*</span>)") ?>
        <span class="text-danger error hidden error-nguon-khach">Chưa chọn nguồn khách</span>
    </div>
</div>
<a type="button" class="" data-toggle="collapse" data-target="#nhu-cau">
    <h4 class="text-primary">NHU CẦU KHÁCH HÀNG </h4>
</a>
<div id="nhu-cau" class="collapse in ">
    <div class="row">
        <div class="col-md-9">
            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($nhu_cau, 'nhu_cau_loai_hinh')->dropDownList([
                        SanPham::NHA => SanPham::NHA,
                        SanPham::DAT => SanPham::DAT,
                        SanPham::DU_AN => SanPham::DU_AN,
                        SanPham::CHO_THUE => SanPham::CHO_THUE
                    ], ['prompt' => '--Chọn--'])->label('Loại hình') ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($nhu_cau, 'nhu_cau_khoang_dien_tich')->dropDownList(NhuCauKhachHang::arr_khoang_dien_tich, ['prompt' => '--Chọn--'])->label("Diện tích (m<sup>2</sup>) ") ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($nhu_cau, 'nhu_cau_gia_tu')->dropDownList($khoang_gias, ['prompt' => '--Chọn--'])->label("Khoảng giá (Tỷ)") ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($nhu_cau, 'nhu_cau_huong')->dropDownList(
                        $arr_huong,
                        ['prompt' => '--Chọn--', 'multiple' => 'multiple']
                    )->label('Hướng') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($nhu_cau, 'nhu_cau_quan_huyen')->dropDownList($quan_huyen, ['prompt' => '--Chọn--', 'multiple' => 'multiple'])->label('Quận huyện') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($nhu_cau, 'nhu_cau_phuong_xa')->dropDownList($phuong_xa, ['prompt' => '--Chọn--', 'multiple' => 'multiple'])->label('Phường xã') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($nhu_cau, 'nhu_cau_duong_pho')->dropDownList($duong_pho, ['prompt' => '--Chọn--', 'multiple' => 'multiple'])->label('Đường phố') ?>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-12">
                    <?= $form->field($nhu_cau, 'ghi_chu')->textarea(['rows'=>9])->label('Ghi chú') ?>
                </div>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'tieu_de')->textInput(['maxlength' => true])->label("Tiêu đề sản phẩm") ?>
        </div>
    </div>
    <p>
        <?= Html::a('<i class="fa fa-search"></i> Tìm sản phẩm', '', ['class' => 'btn btn-primary btn-tim-san-pham']) ?>
    </p>
    <div class="form-chon-san-pham">
        <ul id="san-pham-da-chon" class="list-inline list-unstyled"></ul>
    </div>
</div>
<?php \yii\widgets\ActiveForm::end() ?>
<script>
    $(document).ready(function (){
        $("#nhucaukhachhang-nhu_cau_huong,#nhucaukhachhang-nhu_cau_quan_huyen,#nhucaukhachhang-nhu_cau_phuong_xa,#nhucaukhachhang-nhu_cau_duong_pho").select2();
    });
</script>