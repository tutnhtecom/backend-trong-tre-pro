<?php
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
/* @var $nhan_vien**/
?>
<div class="thong-tin-khach-hang">
    <?=isset($view_thong_tin_khach_hang)?$view_thong_tin_khach_hang:""?>
</div>

<?php ActiveForm::begin(['id' => 'form-cham-soc-khach-hang']) ?>
<div class="row">
    <div class="col-md-4">
        <label>Chọn nhân viên</label>
        <?=Html::dropDownList('nhan_vien','',$nhan_vien,['class'=>'form-control select2'])?>
    </div>
    <div class="col-md-4">
        <label>Ngày</label>
        <?=Html::input('text','ngay','',['class'=>'form-control date'])?>
    </div>
    <div class="col-md-2">
        <label>Giờ</label>
        <?=Html::dropDownList('gio','',\common\models\myAPI::getGio(),['class'=>'form-control '])?>
    </div>
    <div class="col-md-2">
        <label>Phút</label>
        <?=Html::dropDownList('phut','',\common\models\myAPI::getPhut(),['class'=>'form-control '])?>
    </div>
    <div class="col-md-12">
        <label>Ghi chú</label>
        <?=Html::textarea('ghi_chu','',['class'=>'form-control ','rows'=>3])?>
    </div>
</div>
<?php ActiveForm::end() ?>

<script>
    $(document).ready(function(){
        $(".date").datepicker();
        $(".select2").select2();
    })
</script>