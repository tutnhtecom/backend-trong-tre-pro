<?php
/** @var $khach_hang */
/** @var $san_pham [] */
/** @var $lich_su_giao_dich \backend\models\ThongTinBanHang[] */
/** @var $model */

/** @var $user */

use backend\models\SanPham;
use backend\models\SanPhamTheoNhuCau;
use common\models\myAPI;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

?>
    <div class="tabbale-line">
        <ul class="nav nav-tabs ">
            <li class="active">
                <a href="#tab_15_1" data-toggle="tab">THÔNG TIN CHUNG</a>
            </li>
            <li>
                <a href="#tab_15_2" data-toggle="tab">LỊCH SỬ GIAO DỊCH</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab_15_1">
                <div class="thong-tin-khach-hang">
                    <?= isset($view_thong_tin_khach_hang) ? $view_thong_tin_khach_hang : "" ?>
                </div>
                <div class="thong-tin-san-pham">

                </div>
                <?php $form = ActiveForm::begin([
                    'options' => ['autocomplete' => 'off', 'id' => 'form-ban-hang']
                ]); ?>
                <div class="hidden">
                    <?= $form->field($model, 'khach_hang_id')->hiddenInput() ?>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <?= $form->field($model, 'sale')->dropDownList([
                            SanPham::SALE_NGOAI => SanPham::SALE_NGOAI,
                            SanPham::SALE_CONG_TY => SanPham::SALE_CONG_TY
                        ], ['prompt' => '--Chọn--'])->label('Sale(<i class="text-danger">*</i> )') ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($model, 'type_giao_dich')->dropDownList([
                            SanPham::DAT_COC => SanPham::DAT_COC,
                            SanPham::THANH_CONG => SanPham::THANH_CONG
                        ], ['prompt' => '--Chọn--'])->label('Loại giao dịch(<i class="text-danger">*</i> )') ?>
                    </div>
                    <div class="col-md-3 hidden">
                        <?= $form->field($model, 'chi_nhanh')->dropDownList($chi_nhanh, ['prompt' => '--Chọn CN--'])->label('Chi nhánh (<i class="text-danger">*</i> )') ?>
                    </div>
                    <div class="col-md-3 hidden">
                        <?= $form->field($model, 'nguoi_ban_id')->dropDownList([], ['prompt' => '--Chọn--'])->label('Nhân viên (<i class="text-danger">*</i> )') ?>
                    </div>
                    <div class="col-md-3 hidden">
                        <?= $form->field($user, 'hoten')->textInput()->label('Họ tên ') ?>
                    </div>
                    <div class="col-md-3 hidden">
                        <?= $form->field($user, 'dien_thoai')->textInput()->label('Điện thoại ') ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <?= $form->field($model, 'san_pham_id')->dropDownList($san_pham, ['prompt' => '--Chọn SP--'])->label('Sản phẩm(<i class="text-danger">*</i> )') ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($model, 'so_tien')->textInput(['type'=>'number'])->label("Giá bán (Tỷ)(<i class='text-danger'>*</i> )") ?>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">
                                Ngày xem
                            </label>
                            <?= myAPI::dateField2('ThongTinBanHang[ngay_xem]', $model->ngay_xem, (date("Y") - 10) . ':' . (date("Y") + 2), ['class' => 'date form-control']) ?>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">
                                Ngày công chứng
                            </label>
                            <?= myAPI::dateField2('ThongTinBanHang[ngay_cong_chung]', $model->ngay_cong_chung, (date("Y") - 10) . ':' . (date("Y") + 2), ['class' => 'date form-control']) ?>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($model, 'hoa_hong')->textInput(['number'])->label('Hoa hồng (Triệu)') ?>
                    </div>
                    <div class="col-md-12">
                        <?= $form->field($model, 'ghi_chu')->textarea(['rows' => 3])->label("Ghi chú") ?>
                    </div>
                </div>

                <?php ActiveForm::end() ?>
            </div>
            <div class="tab-pane " id="tab_15_2">
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
                                   <strong class="badge badge-primary">#<?=$item->sanPham->id?></strong> <?= $item->sanPham->title ?>
                                </td>
                                <td class="text-right">
                                    <?= $item->so_tien ?>
                                </td>
                                <td class="text-right text-nowrap">
                                    <?= $item->sale ?>
                                </td>
                                <td class="text-right text-nowrap">
                                    <?= $item->ngay_ban!=''?date('d/m/Y',strtotime($item->ngay_ban)):'' ?>
                                </td>
                                <td class="text-right text-nowrap">
                                    <?= $item->ghi_chu ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
