<?php

use backend\models\ChiaSeKhachHang;
use backend\models\LichSuDoanhThu;
use backend\models\SanPham;
use backend\models\ThongTinBanHang;
use backend\models\TrangThaiChiaSeKhachHang;
use backend\models\SanPhamTheoNhuCau;
use backend\models\TrangThaiSanPham;
use backend\models\VaiTro;
use common\models\User;
use yii\helpers\Html;
use yii\web\View;
use yii\helpers\VarDumper;

/** @var $khach_hang */
/** @var $view_san_pham_da_xem  */
/** @var $lich_su_giao_dich ThongTinBanHang[] */
/** @var $metaPage */
/** @var $trang_thai_khach_hang \backend\models\TrangThaiKhachHang[] */
/** @var $nhu_cau_khach_hang [] */
/** @var $view_nhu_cau_khach_hang */
/** @var $view_thong_tin_khach_hang */
/** @var $view_cham_soc_khach_hang */
/** @var $san_pham_da_xem \backend\models\SanPhamDaXem [] */
/** @var $san_pham \backend\models\QuanLySanPhamTheoNhuCau [] */

?>
<div class="tabbale-line">
    <ul class="nav nav-tabs ">
        <li class="active">
            <a href="#tab_15_1" data-toggle="tab">THÔNG TIN CHUNG</a>
        </li>
        <li>
            <a href="#tab_15_2" data-toggle="tab">LỊCH SỬ TRẠNG THÁI</a>
        </li>
        <li>
            <a href="#tab_15_3" data-toggle="tab">SẢN PHẨM ĐÃ XEM</a>
        </li>
        <li>
            <a href="#tab_15_4" data-toggle="tab">LỊCH SỬ GIAO DỊCH</a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="tab_15_1">
            <div class="row">
                <div class="col-md-6">
                    <div class="view-khach-hang">
                        <a type="button" class="" data-toggle="collapse" data-target="#khach-hang-<?= $khach_hang->id ?>">
                            <h4 class="text-primary">THÔNG TIN KHÁCH HÀNG #<?= $khach_hang->id ?>:<?= $khach_hang->hoten ?></h4>
                        </a>
                        <div id="khach-hang-<?= $khach_hang->id ?>" class="collapse in">
                            <div class="row">
                                <!--                Họ tên-->
                                <div class="col-md-6 col-xs-6"><p><strong>Họ tên:</strong></p></div>
                                <div class="col-md-6 col-xs-6">
                                    <p><?= isset($khach_hang->hoten) && $khach_hang->hoten != '' ? $khach_hang->hoten : '<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>' ?></p>
                                </div>
                            </div>
                            <div class="row">

                                <!--                Số điện thoại-->
                                <div class="col-md-6 col-xs-6"><p><strong>Điện thoại:</strong></p></div>
                                <div class="col-md-6 col-xs-6">
                                    <i>
                                        <?php
                                        if(User::hasVaiTro(VaiTro::TRUONG_PHONG)||User::hasVaiTro(VaiTro::GIAM_DOC) || $khach_hang->nhan_vien_sale_id==Yii::$app->user->id){
                                            echo $khach_hang->dien_thoai;
                                        }else{
                                            echo  '******'.substr($khach_hang->dien_thoai, strlen($khach_hang->dien_thoai) - 4, strlen($khach_hang->dien_thoai));
                                        }

                                        ?>
                                    </i>
                                </div>
                            </div>
                            <div class="row">
                                <!--                Email-->
                                <div class="col-md-6 col-xs-6"><p><strong>Email:</strong></p></div>
                                <div class="col-md-6 col-xs-6">
                                    <p><?= isset($khach_hang->email) && $khach_hang->email != '' ? $khach_hang->email : '<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>' ?></p>
                                </div>
                            </div>
                            <div class="row">
                                <!--                Ngày sinh-->
                                <div class="col-md-6 col-xs-6"><p><strong>Ngày sinh:</strong></p></div>
                                <div class="col-md-6 col-xs-6">
                                    <p><?= isset($khach_hang->ngay_sinh) && $khach_hang->ngay_sinh != '' ? $khach_hang->ngay_sinh : '<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>' ?></p>
                                </div>
                            </div>
                            <div class="row">
                                <!--                Email-->
                                <div class="col-md-6 col-xs-6"><p><strong>Nguồn khách:</strong></p></div>
                                <div class="col-md-6 col-xs-6">
                                    <p><?= isset($khach_hang->name) && $khach_hang->name != '' ? $khach_hang->name : '<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>' ?></p>
                                </div>
                            </div>
                            <div class="row">
                                <!--                Ngày sinh-->
                                <div class="col-md-6 col-xs-6"><p><strong>Nhân viên sale:</strong></p></div>
                                <div class="col-md-6 col-xs-6">
                                    <p><?= isset($khach_hang->nhan_vien_sale) && $khach_hang->nhan_vien_sale != '' ? $khach_hang->nhan_vien_sale : '<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>' ?></p>
                                </div>
                            </div>
                            <div class="row">
                                <!--                Địa chỉ-->
                                <div class="col-md-6 col-xs-6"><p><strong>Địa chỉ:</strong></p></div>
                                <div class="col-md-6 col-xs-6">
                                    <p><?= isset($khach_hang->dia_chi) && $khach_hang->dia_chi != '' ? $khach_hang->dia_chi : '<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>' ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h4 class="text-primary">NHU CẦU KHÁCH HÀNG</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Loại hình: </strong></p>
                        </div>
                        <div class="col-md-6">
                            <p><?=!empty($nhu_cau_khach_hang[0]->nhu_cau_loai_hinh)?$nhu_cau_khach_hang[0]->nhu_cau_loai_hinh:'<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>'?></p>
                        </div>
                        <div class="col-md-6">
                            <p> <strong>
                                    Diện tích
                                </strong></p>
                        </div>
                        <div class="col-md-6">
                            <p><?=!empty($nhu_cau_khach_hang[0]->dien_tich)&&$nhu_cau_khach_hang[0]->dien_tich!='Khác'?$nhu_cau_khach_hang[0]->dien_tich.' m<sup>2</sup>':'<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>'?></p>
                        </div>
                        <div class="col-md-6">
                            <p> <strong>
                                    Giá:
                                </strong></p>
                        </div>
                        <div class="col-md-6">
                            <p><?=!empty($nhu_cau_khach_hang[0]->gia)&&$nhu_cau_khach_hang[0]->gia!='Khác'?$nhu_cau_khach_hang[0]->gia.' tỷ':'<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>'?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Hướng:</strong></p>
                        </div>
                        <div class="col-md-6">
                            <p><?=!empty($nhu_cau_khach_hang[0]->nhu_cau_ve_huong)?$nhu_cau_khach_hang[0]->nhu_cau_ve_huong:'<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>'?></p>
                        </div>
                        <div class="col-md-6">
                            <p> <strong>
                                    Quận huyện:
                                </strong></p>
                        </div>
                        <div class="col-md-6">
                            <p><?=!empty($nhu_cau_khach_hang[0]->quan_huyen)?$nhu_cau_khach_hang[0]->quan_huyen:'<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>'?></p>
                        </div>
                        <div class="col-md-6">
                            <p> <strong>
                                    Phường xã:
                                </strong></p>
                        </div>
                        <div class="col-md-6">
                            <p><?=!empty($nhu_cau_khach_hang[0]->phuong_xa)?$nhu_cau_khach_hang[0]->phuong_xa:'<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>'?></p>
                        </div>
                        <div class="col-md-6">
                            <p> <strong>
                                    Đường phố:
                                </strong></p>
                        </div>
                        <div class="col-md-6">
                            <p><?=!empty($nhu_cau_khach_hang[0]->duong_pho)?$nhu_cau_khach_hang[0]->duong_pho:'<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>'?></p>
                        </div>
                        <div class="col-md-6">
                            <p> <strong>
                                    Ghi chú:
                                </strong></p>
                        </div>
                        <div class="col-md-6">
                            <p><?=!empty($nhu_cau_khach_hang[0]->ghi_chu)?$nhu_cau_khach_hang[0]->ghi_chu:'<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>'?></p>
                        </div>
                    </div>
                </div>
            </div>
            <h4 class="text-primary">Danh sách sản phẩm theo nhu cầu</h4>
            <?php if(count($san_pham)==0):?>
                <div class="alert alert-warning">
                    KHÁCH HÀNG KHÔNG CÓ SẢN PHẨM THEO NHU CẦU
                </div>
            <?php else:?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped view-san-pham">
                    <thead>

                    <tr>
                        <th width="1%" class="text-nowrap">
                            STT
                        </th>
                        <th  class="">
                            Tiêu đề
                        </th>
                        <th width="1%" class="text-nowrap ">
                            Sale
                        </th>
                        <th width="1%" class="text-nowrap text-right">
                            Giá (Tỷ)
                        </th>

                        <th width="1%" class="text-nowrap text-right">
                            Diện tích (m<sup>2</sup>)
                        </th>
                        <th width="1%" class="text-nowrap">
                            Hướng
                        </th>
                        <th width="1%" class="text-nowrap">
                            Quận huyện
                        </th>
                        <th width="1%" class="text-nowrap">
                            Đường phố
                        </th>
                        <th width="1%" class="text-nowrap">
                            Trạng thái
                        </th>
                        <th width="1%" class="text-nowrap">
                            Xem
                        </th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php $index = 0 ?>
                    <?php if (count($san_pham) > 0): ?>
                        <?php foreach ($san_pham as $item): ?>
                            <?php $index++ ?>
                            <tr  >
                                <td class="text-center">
                                    <?= $index ?>
                                </td>
                                <td class="text-left text-nowrap">
                                    <strong class="badge badge-primary">#<?= $item->san_pham_id?></strong> <?=\yii\bootstrap\Html::a($item->title,'',['class'=>'btn-view-chi-tiet','data-value'=>$item->san_pham_id])?>  <br/>
                                </td>
                                <td class="text-nowrap">
                                    <?=$item->hoten?>
                                </td>
                                <td class="text-nowrap text-right">
                                    <?=$item->gia_tu?>
                                </td>
                                <td class="text-nowrap text-right">
                                    <?=$item->dien_tich?>
                                </td>
                                <td class="text-nowrap text-left">
                                    <?=$item->huong?>
                                </td>
                                <td class="text-nowrap text-left">
                                    <?=$item->quan_huyen?>
                                </td>

                                <td class="text-nowrap text-left">
                                    <?=$item->phuong_xa?>
                                </td>
                                <td class="text-left text-nowrap">
                                    <?= $item->trang_thai_di_xem==SanPhamTheoNhuCau::DA_XEM?'<span class="text-success"><i class="fa fa-check-circle "></i> '.$item->trang_thai_di_xem.'</span>':'<span class="text-primary"><i class="fa fa-spinner "></i> '.$item->trang_thai_di_xem.'</span>' ?><br/>
                                </td>
                                <td class="text-center">
                                    <?= Html::a('<i class="fa fa-eye"></i>','',['class'=>'btn-lich-su-di-xem','data-value'=>$item->id,'data-khach-hang'=>$khach_hang->id])?>
                                </td>

                            </tr>

                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php endif;?>
        </div>
        <div class="tab-pane " id="tab_15_2">
            <?php if(count($trang_thai_khach_hang)==0):?>
                <div class="alert alert-warning">
                    KHÁCH HÀNG KHÔNG CHƯA CÓ TRẠNG THÁI KHÁCH HÀNG NÀO
                </div>
            <?php else:?>
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th width="1%" class="text-nowrap">
                        STT
                    </th>
                    <th width="1% " class="text-nowrap">
                        Ngày cập nhật
                    </th>
                    <th>
                        Trạng thái
                    </th>
                    <th width="1%" class="text-nowrap">
                        Tuần
                    </th>
                    <th  width="1% " class="text-nowrap">
                        Tháng
                    </th>

                    <th  width="1% " class="text-nowrap">
                        Năm
                    </th>
                    <th class="text-nowrap" width="1%">
                        Phân loại
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php $index = 0 ?>
                <?php if (count($trang_thai_khach_hang) > 0): ?>
                    <?php foreach ($trang_thai_khach_hang as $item): ?>
                        <?php $index++ ?>
                        <tr>
                            <td class="text-center">
                                <?= $index ?>
                            </td>
                            <td class=" text-nowrap">
                                <?= $item->created ?>
                            </td>
                            <td>
                                <?= $item->trang_thai ?>
                            </td>
                            <td class="text-right" >
                                <?= $item->tuan ?>
                            </td>
                            <td class="text-right">
                                <?= $item->thang ?>
                            </td>
                            <td class="text-right">
                                <?= $item->nam ?>
                            </td>
                            <?php if($item->trang_thai==User::KHACH_HANG_CO_NHU_CAU):?>
                            <td class="text-right">
                               <strong>Giỏ:</strong> <?= $item->gio?>
                            </td>
                            <?php elseif ($item->trang_thai==User::KHACH_HANG_DA_XEM):?>
                            <td class="text-right">
                               <strong>Lần:</strong> <?= $item->lan_xem?>
                            </td>
                            <?php elseif ($item->trang_thai==User::KHACH_HANG_GIAO_DICH):?>
                            <td>
                              <strong> Loại:</strong> <?= $item->trang_thai_giao_dich?>
                            </td>
                            <?php elseif ($item->trang_thai==User::KHACH_HANG_TIEM_NANG):?>
                            <td class="text-right">
                             <strong>  Mức:</strong> <?= $item->muc_do_tiem_nang?>
                            </td>
                            <?php endif;?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
            <?php endif;?>
        </div>
        <div class="tab-pane " id="tab_15_3">
            <div class="table-san-pham-da-xem">
                <?=$view_san_pham_da_xem?>
            </div>
        </div>
        <div class="tab-pane " id="tab_15_4">
            <div class="table-lich-su-giao-dich">
                <?php if(count($lich_su_giao_dich)==0):?>
                    <div class="alert alert-warning">
                        KHÁCH HÀNG CHƯA CÓ GIAO DỊCH NÀO
                    </div>
                <?php else:?>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th width="1%" class="text-nowrap">
                            STT
                        </th>
                        <th >
                            Sản phẩm
                        </th >
                        <th width="1% " class="text-nowrap">
                            số tiền
                        </th>
                        <th  width="1% " class="text-nowrap">
                            Sale
                        </th>

                        <th  width="1% " class="text-nowrap">
                            Ngày bán
                        </th>
                        <th class="text-nowrap" width="1%">
                            Ghi chú
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $index = 0 ?>
                    <?php if (count($lich_su_giao_dich) > 0): ?>
                        <?php foreach ($lich_su_giao_dich as $item): ?>
                            <?php $index++ ?>
                            <tr>
                                <td class="text-center">
                                    <?= $index ?>
                                </td>
                                <td class="">
                                    <strong class="badge badge-primary">#<?=$item->sanPham->id?></strong> <?=\yii\bootstrap\Html::a($item->sanPham->title,'',['class'=>'btn-view-chi-tiet','data-value'=>$item->sanPham->id])  ?>
                                </td>
                                <td class="text-right">
                                    <?= $item->so_tien ?>
                                </td>
                                <td class="text-right text-nowrap">
                                    <?= $item->sale ?>
                                </td>
                                <td class="text-right text-nowrap">
                                    <?= $item->ngay_cong_chung!=''?date('d/m/Y',strtotime($item->ngay_cong_chung)):'' ?>
                                </td>
                                <td class="text-right text-nowrap">
                                    <?= $item->ghi_chu ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
                <?php endif;?>
            </div>
        </div>

    </div>

</div>

<?php $this->registerCssFile(Yii::$app->request->baseUrl . '/backend/assets/plugins/lightbox/src/css/lightbox.css',
    ['depends' => ['backend\assets\Qltk2Asset'], 'position' => View::POS_END]); ?>
<?php $this->registerJsFile(Yii::$app->request->baseUrl . '/backend/assets/plugins/lightbox/src/js/lightbox.js',
    ['depends' => ['backend\assets\Qltk2Asset'], 'position' => View::POS_END]); ?>
<?php $this->registerJsFile(Yii::$app->request->baseUrl . '/backend/assets/js-view/xem-chi-tiet-san-pham1.js',
    ['depends' => ['backend\assets\Qltk2Asset'], 'position' => View::POS_END]); ?>
