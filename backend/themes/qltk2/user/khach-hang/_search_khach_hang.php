<?php
/** @var $khoang_gia[]*/
/** @var $quan_huyen[]*/
/** @var $huong[]*/
/** @var $phuong_xa[]*/
/** @var $duong_pho[]*/
/** @var $model \backend\models\search\QuanLyKhachHangSearch */
use yii\bootstrap\Html;

$form = \yii\widgets\ActiveForm::begin([
    'options' => [
        'id' => 'form-search',
    ]
])
?>
    <div class="row">
        <div class="col-md-3">
            <?=$form->field($model, 'id')->textInput()->label('Mã KH')?>
        </div>
        <div class="col-md-3">
            <?=$form->field($model, 'hoten')->textInput()->label('Họ tên')?>
        </div>
        <div class="col-md-3">
            <?=$form->field($model, 'dien_thoai')->label('Điện thoại')?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'khoang_gia')->dropDownList($khoang_gia, ['prompt'=>'--Chọn khoảng giá--'])->label("Khoảng giá") ?>
        </div>

    </div>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'nhu_cau_huong')->dropDownList( $huong, ['prompt' => '--Chọn--', 'multiple' => 'multiple'])->label("Hướng") ?>
        </div>
        <div class="col-md-3">
            <?=  $form->field($model, 'nhu_cau_quan_huyen')->dropDownList($quan_huyen, ['prompt' => '--Chọn--', 'multiple' => 'multiple'])->label("Quận huyện") ?>
        </div>
        <div class="col-md-3">
            <?=$form->field($model, 'nhu_cau_phuong_xa')->dropDownList( $phuong_xa, ['prompt' => '--Chọn--', 'multiple' => 'multiple'])->label("Phường xã") ?>
        </div>
        <div class="col-md-3">
            <?=$form->field($model, 'nhu_cau_duong_pho')->dropDownList( $duong_pho, ['prompt' => '--Chọn--', 'multiple' => 'multiple'])->label("Đường phố") ?>
        </div>
        <div class="col-md-12">
            <p>
                <?= $form->field($model, 'nhu_cau_ghi_chu')->textarea(['rows' => 2])->label("Ghi chú") ?>
            </p>
        </div>
        <div class="col-md-12">
            <p>
                <?= Html::a('<i class="fa fa-search"></i> Tìm khách hàng', '', ['class' => 'btn btn-primary btn-save-search-khach-hang']) ?>
                <?= Html::a('<i class="fa fa-cloud-download"></i> Tải danh sách', '', ['class' => 'btn btn-success btn-tai-danh-sach-tim-kiem']) ?>
            </p>
        </div>
    </div>
    <div class="table-khach-hang-tim-kiem">

    </div>
<?php \yii\widgets\ActiveForm::end() ?>
<script>
    $(document).ready(function (){
        $('#quanlykhachhangsearch-nhu_cau_huong,#quanlykhachhangsearch-nhu_cau_quan_huyen,#quanlykhachhangsearch-nhu_cau_phuong_xa,#quanlykhachhangsearch-nhu_cau_duong_pho').select2()
    })
</script>
