<?php
/** @var $khach_hang*/

use backend\models\SanPhamTheoNhuCau;
use common\models\myAPI;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
?>
<div class="thong-tin-khach-hang">
    <?=isset($view_thong_tin_khach_hang)?$view_thong_tin_khach_hang:""?>
</div>
<h4>Phân loại khách hàng</h4>
<?php ActiveForm::begin([
    'options' => ['autocomplete' => 'off','id'=>'form']
]); ?>
<?= Html::dropDownList("dau_tu","",$dau_tu,['prompt'=>"--Chon--",'class'=>'form-control','id'=>'dau_tu'])?>
<?php ActiveForm::end()?>
