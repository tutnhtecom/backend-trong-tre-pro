<?php
/** @var $khach_hang */
?>
<div class="view-khach-hang">
    <a type="button" class="" data-toggle="collapse" data-target="#khach-hang-<?= $khach_hang->id ?>">
        <h4 class="text-primary">THÔNG TIN KHÁCH HÀNG #<?= $khach_hang->id ?>:<?= $khach_hang->hoten ?></h4>
    </a>
    <div id="khach-hang-<?= $khach_hang->id ?>" class="collapse in">
        <div class="row">
            <!--                Họ tên-->
            <div class="col-md-2 col-xs-6">
                <p><strong>Họ tên:</strong></p>
            </div>
            <div class="col-md-4 col-xs-6">
                <p><?= isset($khach_hang->hoten) && $khach_hang->hoten != '' ? $khach_hang->hoten : '<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>' ?></p>
            </div>

            <!--                Số điện thoại-->
            <div class="col-md-2 col-xs-6"><p><strong>Điện thoại:</strong></p></div>
            <div class="col-md-4 col-xs-6">
                <p><?= isset($khach_hang->dien_thoai) && $khach_hang->dien_thoai != '' ? $khach_hang->dien_thoai : '<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>' ?></p>
            </div>
        </div>
        <div class="row">
            <!--                Email-->
            <div class="col-md-2 col-xs-6"><p><strong>Email:</strong></p></div>
            <div class="col-md-4 col-xs-6">
                <p><?= isset($khach_hang->email) && $khach_hang->email != '' ? $khach_hang->email : '<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>' ?></p>
            </div>

            <!--                Ngày sinh-->
            <div class="col-md-2 col-xs-6"><p><strong>Ngày sinh:</strong></p></div>
            <div class="col-md-4 col-xs-6">
                <p><?= isset($khach_hang->ngay_sinh) && $khach_hang->ngay_sinh != '' ? $khach_hang->ngay_sinh : '<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>' ?></p>
            </div>
        </div>
        <div class="row">
            <!--                Email-->
            <div class="col-md-2 col-xs-6"><p><strong>Nguồn khách:</strong></p></div>
            <div class="col-md-4 col-xs-6">
                <p><?= isset($khach_hang->name) && $khach_hang->name != '' ? $khach_hang->name : '<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>' ?></p>
            </div>

            <!--                Ngày sinh-->
            <div class="col-md-2 col-xs-6"><p><strong>Nhân viên sale:</strong></p></div>
            <div class="col-md-4 col-xs-6">
                <p><?= isset($khach_hang->nhan_vien_sale) && $khach_hang->nhan_vien_sale != '' ? $khach_hang->nhan_vien_sale : '<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>' ?></p>
            </div>
        </div>
        <div class="row">
            <!--                Địa chỉ-->
            <div class="col-md-2 col-xs-6"><p><strong>Địa chỉ:</strong></p></div>
            <div class="col-md-4 col-xs-6">
                <p><?= isset($khach_hang->dia_chi) && $khach_hang->dia_chi != '' ? $khach_hang->dia_chi : '<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>' ?></p>
            </div>
        </div>
    </div>
</div>
