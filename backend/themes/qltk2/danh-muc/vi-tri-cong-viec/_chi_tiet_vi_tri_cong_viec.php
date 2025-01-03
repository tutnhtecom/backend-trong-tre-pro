<?php
/**
 * @var $model \backend\models\DanhMuc
 * @var $congViec \backend\models\CongViecNhiemVu[]
 * @var $nhiemVu \backend\models\CongViecNhiemVu[]
 * @var $this \yii\web\View
 * @var $viTriCongViec \backend\models\DanhMuc
 * @var $phongBan []
 */
$this->title = 'Thông tin vị trí công việc '.$model->name;
?>
<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <?=\yii\helpers\Html::label('Phòng ban');?>
            <?=\yii\bootstrap\Html::dropDownList('phong_ban', null, $phongBan, ['class' => 'form-control', 'id' => 'phong-ban', 'prompt' => ''])?>
            <?=\yii\bootstrap\Html::activeHiddenInput($viTriCongViec, 'id')?>
        </div>
    </div>
    <div class="col-md-2">
        <p class="margin-top-25">
            <a class="btn btn-primary btn-sm" href="<?=\yii\helpers\Url::toRoute(['danh-muc/vi-tri-cong-viec'])?>">
                <i class="fa fa-step-backward"></i> Quay lại
            </a>
        </p>

    </div>
</div>
<div id="block-congviec-nhiem-vu">

</div>

<?php $this->registerJsFile(Yii::$app->request->baseUrl.'/backend/assets/js-view/view-cong-viec-vi-tri.js',[ 'depends' => ['backend\assets\Qltk2Asset'], 'position' => \yii\web\View::POS_END ]); ?>
