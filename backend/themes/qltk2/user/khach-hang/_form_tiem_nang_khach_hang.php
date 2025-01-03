<?php
/** @var $khach_hang*/
/** @var $san_pham[]*/

use backend\models\SanPhamTheoNhuCau;
use common\models\myAPI;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
?>
    <div class="thong-tin-khach-hang">
        <?=isset($view_thong_tin_khach_hang)?$view_thong_tin_khach_hang:""?>
    </div>
    <h4>Chọn mức độ tiềm năng</h4>
<?php ActiveForm::begin([
    'options' => ['autocomplete' => 'off','id'=>'form-tiem-nang']
]); ?>
    <?= Html::dropDownList("muc_do_tiem_nang","",[
            1=>"Mức 1",
            2=>"Mức 2",
            3=>"Mức 3",
            4=>"Mức 4",
            5=>"Mức 5",
    ],['prompt'=>"--Chon TN--",'class'=>'form-control'])?>
<?php ActiveForm::end()?>
