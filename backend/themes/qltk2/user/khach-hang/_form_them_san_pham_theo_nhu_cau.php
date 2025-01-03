<?php use backend\models\NhuCauKhachHang;
use backend\models\SanPham;
use common\models\myAPI;
/**@var $khach_hang \backend\models\QuanLyKhachHang*/
use yii\bootstrap\Html;

$form = \yii\widgets\ActiveForm::begin([
    'options' => [
        'id' => 'form-khach-hang',
    ]
]); ?>
<?= Html::hiddenInput('khach_hang_id',$khach_hang->id,['id'=>'khach_hang_nhu_cau_id'])?>
<div class="row">
    <div class="col-md-6">
        <div class="view-khach-hang">
            <a type="button" class="" data-toggle="collapse" data-target="#khach-hang-<?= $khach_hang->id ?>">
                <h4 class="text-primary">THÔNG TIN KHÁCH HÀNG #<?= $khach_hang->id ?>:<?= $khach_hang->hoten ?></h4>
            </a>
            <div id="khach-hang-<?= $khach_hang->id ?>" class="collapse in">
                <div class="row">
                    <!--                Họ tên-->
                    <div class="col-md-3 col-xs-6"><p><strong>Họ tên:</strong></p></div>
                    <div class="col-md-4 col-xs-6">
                        <p><?= isset($khach_hang->hoten) && $khach_hang->hoten != '' ? $khach_hang->hoten : '<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>' ?></p>
                    </div>
                </div>
                <div class="row">

                    <!--                Số điện thoại-->
                    <div class="col-md-3 col-xs-6"><p><strong>Điện thoại:</strong></p></div>
                    <div class="col-md-4 col-xs-6">
                        <p><?= isset($khach_hang->dien_thoai) && $khach_hang->dien_thoai != '' ? $khach_hang->dien_thoai : '<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>' ?></p>
                    </div>
                </div>
                <div class="row">
                    <!--                Email-->
                    <div class="col-md-3 col-xs-6"><p><strong>Email:</strong></p></div>
                    <div class="col-md-4 col-xs-6">
                        <p><?= isset($khach_hang->email) && $khach_hang->email != '' ? $khach_hang->email : '<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>' ?></p>
                    </div>
                </div>
                <div class="row">
                    <!--                Ngày sinh-->
                    <div class="col-md-3 col-xs-6"><p><strong>Ngày sinh:</strong></p></div>
                    <div class="col-md-4 col-xs-6">
                        <p><?= isset($khach_hang->ngay_sinh) && $khach_hang->ngay_sinh != '' ? $khach_hang->ngay_sinh : '<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>' ?></p>
                    </div>
                </div>
                <div class="row">
                    <!--                Email-->
                    <div class="col-md-3 col-xs-6"><p><strong>Nguồn khách:</strong></p></div>
                    <div class="col-md-4 col-xs-6">
                        <p><?= isset($khach_hang->name) && $khach_hang->name != '' ? $khach_hang->name : '<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>' ?></p>
                    </div>
                </div>
                <div class="row">
                    <!--                Ngày sinh-->
                    <div class="col-md-3 col-xs-6"><p><strong>Nhân viên sale:</strong></p></div>
                    <div class="col-md-4 col-xs-6">
                        <p><?= isset($khach_hang->nhan_vien_sale) && $khach_hang->nhan_vien_sale != '' ? $khach_hang->nhan_vien_sale : '<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>' ?></p>
                    </div>
                </div>
                <div class="row">
                    <!--                Địa chỉ-->
                    <div class="col-md-3 col-xs-6"><p><strong>Địa chỉ:</strong></p></div>
                    <div class="col-md-4 col-xs-6">
                        <p><?= isset($khach_hang->dia_chi) && $khach_hang->dia_chi != '' ? $khach_hang->dia_chi : '<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>' ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <h4 class="text-primary">NHU CẦU KHÁCH HÀNG</h4>
        <div class="row">
            <div class="col-md-3">
                <p><strong>Loại hình: </strong></p>
            </div>
            <div class="col-md-9">
                <p><?=isset($nhu_cau_khach_hang->nhu_cau_loai_hinh)?$nhu_cau_khach_hang->nhu_cau_loai_hinh:'<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>'?></p>
            </div>
            <div class="col-md-3">
                <p> <strong>
                        Diện tích
                    </strong></p>
            </div>
            <div class="col-md-9">
                <p><?=!empty($nhu_cau_khach_hang->dien_tich)&&$nhu_cau_khach_hang->dien_tich!='Khác'?$nhu_cau_khach_hang->dien_tich.' m<sup>2</sup>':'<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>'?></p>
            </div>
            <div class="col-md-3">
                <p> <strong>
                        Giá:
                    </strong></p>
            </div>
            <div class="col-md-9">
                <p><?=!empty($nhu_cau_khach_hang->gia)&&$nhu_cau_khach_hang->gia!='Khác'?$nhu_cau_khach_hang->gia.' tỷ':'<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>'?></p>
            </div>
            <div class="col-md-3">
                <p><strong>Hướng:</strong></p>
            </div>
            <div class="col-md-9">
                <p><?=isset($nhu_cau_khach_hang->nhu_cau_ve_huong)?$nhu_cau_khach_hang->nhu_cau_ve_huong:'<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>'?></p>
            </div>
            <div class="col-md-3">
                <p> <strong>
                        Quận huyện:
                    </strong></p>
            </div>
            <div class="col-md-9">
                <p><?=isset($nhu_cau_khach_hang->quan_huyen)?$nhu_cau_khach_hang->quan_huyen:'<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>'?></p>
            </div>
            <div class="col-md-3">
                <p> <strong>
                        Phường xã:
                    </strong></p>
            </div>
            <div class="col-md-9">
                <p><?=isset($nhu_cau_khach_hang->phuong_xa)?$nhu_cau_khach_hang->phuong_xa:'<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>'?></p>
            </div>
            <div class="col-md-3">
                <p> <strong>
                        Đường phố:
                    </strong></p>
            </div>
            <div class="col-md-9">
                <p><?=isset($nhu_cau_khach_hang->duong_pho)?$nhu_cau_khach_hang->duong_pho:'<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>'?></p>
            </div>
            <div class="col-md-3">
                <p> <strong>
                        Ghi chú:
                    </strong></p>
            </div>
            <div class="col-md-9">
                <p><?=isset($nhu_cau_khach_hang->ghi_chu)?$nhu_cau_khach_hang->ghi_chu:'<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>'?></p>
            </div>
        </div>
    </div>
</div>

<div class="chon-san-pham">
    <ul id="san-pham-da-chon" class="list-inline list-unstyled"></ul>
    <?= $view_chon_san_pham?>
</div>
<?php \yii\widgets\ActiveForm::end() ?>
<script>
    $(document).ready(function (){
        $("#nhucaukhachhang-nhu_cau_huong,#nhucaukhachhang-nhu_cau_quan_huyen,#nhucaukhachhang-nhu_cau_phuong_xa,#nhucaukhachhang-nhu_cau_duong_pho").select2();
    });
</script>