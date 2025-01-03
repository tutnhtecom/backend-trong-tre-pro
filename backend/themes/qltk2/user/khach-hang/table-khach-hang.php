<?php
/** @var $khach_hang [] */
/** @var $soTuanTrongThang */
/** @var $thoiGianTrongTuan [] */
/** @var $nhu_cau \backend\models\NhuCauKhachHang */

/** @var $this \yii\web\View */

use yii\bootstrap\Html;

$this->title = 'Khách hàng';

use common\models\User;

?>
<div id="modal-khach-hang"></div>
<div class="modal-form-di-xem-san-pham"></div>
<div class="modal-tiem-nang"></div>
<div class="modal-giao-dich"></div>
<div class="modal-sua-nhu-cau"></div>
<div class="modal-tim-san-pham"></div>
<div class="modal-cham-soc-khach-hang"></div>
<div class="modal-xem-chi-tiet"></div>
<div class="modal-search-khach-hang"></div>
<?php if($last_date>=3 && !(User::hasVaiTro(\backend\models\VaiTro::TRUONG_PHONG) || User::hasVaiTro(\backend\models\VaiTro::GIAM_DOC))):?>
<div class="alert alert-warning" role="alert">
    Ôi bạn ơi! Bạn bỏ quên tôi rồi! Đã bao lâu bạn chưa nhập dữ liệu?
</div>
<?php endif;?>
<div class="margin-bottom-25">
    <?php $form = \yii\widgets\ActiveForm::begin([
        'options' => [
            'id' => 'form-filter-khach-hang'
        ]
    ]) ?>
    <div class="portlet-footer flex-end">
        <div class="col-md-4">
            <?= Html::a('<i class="fa fa-search"></i> Tìm kiếm', '#', ['class' => 'btn-search-khach-hang btn btn-primary badge-custom', 'data-target' => '#modal-search-san-pham', 'data-toggle' => 'modal']); ?>
            <?= Html::a('<i class="fa fa-plus"></i> Thêm khách hàng', '#', ['class' => 'btn-them-khach-hang btn btn-primary badge-custom']); ?>
            <?= Html::a('<i class="fa fa-search"></i> Lọc', '#', ['class' => 'filter-table btn btn-primary badge-custom']); ?>
        </div>

        <div class="col-md-8">
            <div class="row">
                <div class="col-md-4">
                    <label>Chi nhánh</label>
                    <?= Html::dropDownList('chi_nhanh', '', $chi_nhanh, ['multiple' => 'multiple', 'class' => 'form-control select2 ', 'prompt' => '--Chọn--', 'id' => 'chi_nhanh']) ?>
                </div>
                <div class="col-md-4">
                    <label>Nhân viên</label>
                    <?= Html::dropDownList('nhan_vien', '', $nhan_vien, ['multiple' => 'multiple', 'class' => 'form-control select2   ', 'prompt' => '--Chọn--', 'id' => 'nhan_vien']) ?>
                </div>
                <div class="col-md-2">
                    <label>Tháng</label>
                    <?= Html::dropDownList('thang', date('m'), User::getSoThang(12), ['class' => 'form-control ']) ?>
                </div>
                <div class="col-md-2">
                    <label>Năm</label>
                    <?= Html::input('number', 'nam', date('Y'), ['class' => 'form-control ']) ?>
                </div>
            </div>
        </div>
    </div>
    <?php \yii\widgets\ActiveForm::end() ?>
</div>
<div class="table-khach-hang  ">
    <table class="table table-bordered">
        <thead>
        <tr class="text-primary text-center">
            <th class="text-center col-md-1 width-12p" rowspan="2"> Tuần</th>
            <th class="text-center col-md-3 " colspan="2"> Có nhu cầu</th>
            <th class="text-center col-md-2 width-12p" rowspan="2"> Đã xem</th>
            <th class="text-center col-md-2 width-12p" rowspan="2"> Tiềm năng</th>
            <th class="text-center col-md-3 " colspan="2"> Giao dịch</th>
            <th class="text-center col-md-1 width-12p" rowspan="2"> Chung</th>
        </tr>
        <tr class="text-center">
            <th class="text-center col-md-1 width-12p">
                Giỏ 1
            </th>
            <th class="text-center col-md-1 width-12p">
                Giỏ 2
            </th>
            <th class="text-center col-md-1  width-12p">
                Đặt cọc
            </th>
            <th class="text-center col-md-1 width-12p">
                Thành công
            </th>
        </tr>
        </thead>
    </table>
    <div class="minus-tuan">

    </div>
    <div class="table-responsive table-container sc2" style="height: 550px">
        <table class="table table-bordered ">
            <tbody>
            <?php for ($i = 1; $i <= $soTuanTrongThang; $i++): ?>
                <tr class="parent collapse in" id="san-pham-<?= $i?>" >
                    <td class="width-12p">
                        <h4 class="text-center">
                            <strong>Tuần <?= $i; ?></strong>
                        </h4>

                        <p class="text-center">
                            <i>
                                Từ ngày <span
                                        class="text-success"><?= date("d/m/Y", strtotime($thoiGianTrongTuan[$i - 1]['start'])) ?></span><br/>
                                đến ngày <span
                                        class="text-success"><?= date("d/m/Y", strtotime($thoiGianTrongTuan[$i - 1]['end'])) ?></span>
                            </i>
                        </p>
                        <div class="child">
                            <a type="button" class="btn-collapse" data-toggle="collapse" data-target="#san-pham-<?= $i ?>" data-value="<?=$i?>">
                                <i class="fa fa-minus-square-o text-muted"></i>
                            </a>
                        </div>
                    </td>
                    <td data-phan-nhom="1" data-phan-tuan="<?= $i ?>" class="column col-md-1 width-12p"
                        id="tuan-<?= $i ?>-gio-1" data-value="Khách hàng có nhu cầu"></td>
                    <td data-phan-nhom="2" data-phan-tuan="<?= $i ?>" class="column col-md-1 width-12p"
                        id="tuan-<?= $i ?>-gio-2" data-value="Khách hàng có nhu cầu"></td>
                    <td data-phan-nhom="1" data-phan-tuan="<?= $i ?>" class="column col-md-1 width-12p"
                        id="tuan-<?= $i ?>-da-xem" data-value="Khách hàng đã xem"></td>
                    <td data-phan-nhom="1" data-phan-tuan="<?= $i ?>" class="column col-md-1 width-12p"
                        id="tuan-<?= $i ?>-tiem-nang" data-value="Khách hàng tiềm năng"></td>
                    <td data-phan-nhom="1" data-phan-tuan="<?= $i ?>" class="column col-md-1 width-12p"
                        id="tuan-<?= $i ?>-dat-coc" data-value="Khách hàng giao dịch"></td>
                    <td data-phan-nhom="2" data-phan-tuan="<?= $i ?>" class="column col-md-1 width-12p"
                        id="tuan-<?= $i ?>-thanh-cong" data-value="Khách hàng giao dịch"></td>
                    <td data-phan-nhom="2" data-phan-tuan="1" class="column col-md-1 width-12p" id="khach-hang-chung"
                        data-value="Khách hàng chung"></td>
                </tr>
            <?php endfor; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $this->registerJsFile(Yii::$app->request->baseUrl . '/backend/assets/js-view/khach-hang.js', ['depends' => ['backend\assets\Qltk2Asset'], 'position' => \yii\web\View::POS_END]); ?>
<?php $this->registerCssFile(Yii::$app->request->baseUrl . '/backend/assets/css/smoothness/jquery-ui.css'); ?>

