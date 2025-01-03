<?php
/** @var $khach_hang \common\models\User*/
/** @var $san_pham \backend\models\QuanLySanPhamTheoNhuCau[]*/

use backend\models\SanPhamTheoNhuCau;
use common\models\myAPI;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
?>

<?php ActiveForm::begin([
    'options' => ['autocomplete' => 'off','id'=>'form-them-san-pham-da-xem']
]); ?>
<div class="thong-tin-khach-hang">
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
                <div class="col-md-2 col-xs-6">
                    <p><?= !empty($khach_hang->hoten) && $khach_hang->hoten != '' ? $khach_hang->hoten : '<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>' ?></p>
                </div>

                <!--                Số điện thoại-->
                <div class="col-md-2 col-xs-6"><p><strong>Điện thoại:</strong></p></div>
                <div class="col-md-2 col-xs-6">
                    <p><?= !empty($khach_hang->dien_thoai) && $khach_hang->dien_thoai != '' ? $khach_hang->dien_thoai : '<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>' ?></p>
                </div>
                <!--                Email-->
                <div class="col-md-2 col-xs-6"><p><strong>Email:</strong></p></div>
                <div class="col-md-2 col-xs-6">
                    <p><?= !empty($khach_hang->email) && $khach_hang->email != '' ? $khach_hang->email : '<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>' ?></p>
                </div>

                <!--                Ngày sinh-->
                <div class="col-md-2 col-xs-6"><p><strong>Ngày sinh:</strong></p></div>
                <div class="col-md-2 col-xs-6">
                    <p><?= !empty($khach_hang->ngay_sinh) && $khach_hang->ngay_sinh != '' ? $khach_hang->ngay_sinh : '<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>' ?></p>
                </div>
                <div class="col-md-2 col-xs-6"><p><strong>Nguồn khách:</strong></p></div>
                <div class="col-md-2 col-xs-6">
                    <p><?= !empty($khach_hang->name) && $khach_hang->name != '' ? $khach_hang->name : '<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>' ?></p>
                </div>

                <!--                Ngày sinh-->
                <div class="col-md-2 col-xs-6"><p><strong>Nhân viên sale:</strong></p></div>
                <div class="col-md-2 col-xs-6">
                    <p><?= !empty($khach_hang->nhan_vien_sale) && $khach_hang->nhan_vien_sale != '' ? $khach_hang->nhan_vien_sale : '<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>' ?></p>
                </div>
                <!--                Địa chỉ-->
            </div>
            <div class="row">
                <div class="col-md-2 col-xs-6"><p><strong>Địa chỉ:</strong></p></div>
                <div class="col-md-4 col-xs-6">
                    <p><?= !empty($khach_hang->dia_chi) && $khach_hang->dia_chi != '' ? $khach_hang->dia_chi : '<i class="text-muted"><i class="fa fa-spinner"></i> Đang cập nhật</i>' ?></p>
                </div>
            </div>
        </div>
    </div>

</div>

<h4 class="text-primary"> DANH SÁCH SẢN PHẨM THEO NHU CẦU</h4>
<?php if (count($san_pham) == 0): ?>
    <div class="alert alert-warning">KHÔNG CÓ DỮ LIỆU SẢN PHẨM PHÙ HỢP VỚI NHU CẦU CỦA KHÁCH HÀNG.</div>
<?php else: ?>
    <div class="row">
        <div class="col-md-2">
            <label>Lần xem</label>
            <?=Html::input('number','phan_nhom',$khach_hang->lan_xem + 1,['class'=>'  form-control phan_tuan','readonly'=>'readonly'])?>
        </div>
        <div class="col-md-2">
            <label>Chọn Tuần</label>
            <?=Html::dropDownList('phan_tuan',$khach_hang->phan_tuan,\common\models\User::getSoTuanTrongThang($tuan),['class'=>' form-control phan_tuan'])?>
        </div>
    </div>
    <div class="table-responsive margin-top-10">
        <table class="table table-bordered view-san-pham table-striped">
            <thead>
            <tr>
                <th width="1%" class="text-nowrap">
                    STT
                </th>
                <th  class="text-nowrap">
                    Thông tin sản phẩm
                </th>
                <th width="1%" class="text-nowrap">
                    Trạng thái
                </th>
                <th width="12%" class="text-nowrap">
                    Ngày xem
                </th>
                <th width="40%" class="text-nowrap">
                    Nội dung đã xem
                </th>
                <th width="1%">Xem</th>
            </tr>
            </thead>
            <tbody>
            <?php $index = 0 ?>
            <?php if (count($san_pham) > 0): ?>
                <?php foreach ($san_pham as $item): ?>
                    <?php $index++ ?>
                    <tr  data-value="<?=$item->id?>">
                        <td class="text-center">
                            <?= $index ?>
                        </td>
                        <td class="text-left text-nowrap">
                            <strong class="badge badge-primary">#<?= $item->id?></strong> <?=\yii\bootstrap\Html::a($item->title,'',['class'=>'btn-view-chi-tiet','data-value'=>$item->id])?>
                        </td>
                        <td class="text-left text-nowrap">
                            <?= $item->trang_thai_di_xem==SanPhamTheoNhuCau::DA_XEM?'<span class="text-success"><i class="fa fa-check-circle "></i> '.$item->trang_thai_di_xem.'</span>':'<span class="text-primary"><i class="fa fa-spinner "></i> '.$item->trang_thai_di_xem.'</span>' ?><br/>
                        </td>
                        <td>
                            <?= myAPI::dateField2('ngay_xem['.$item->id.']','',  (date("Y") - 10) . ':' . (date("Y") + 2),['class'=>'date form-control']) ?>
                        </td>
                        <td class="text-left ">
                            <input type="text" name="ghi_chu[<?=$item->id?>]"  class="form-control" >
                        </td>
                        <td class="text-center">
                            <?= Html::a('<i class="fa fa-eye"></i>','',['class'=>'btn-lich-su-di-xem','data-value'=>$item->san_pham_id,'data-khach-hang'=>$khach_hang->id])?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">
                        <div class="alert alert-warning">KHÔNG CÓ DỮ LIỆU SẢN PHẨM PHÙ HỢP VỚI NHU CẦU CỦA KHÁCH HÀNG.</div>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php ActiveForm::end()?>

