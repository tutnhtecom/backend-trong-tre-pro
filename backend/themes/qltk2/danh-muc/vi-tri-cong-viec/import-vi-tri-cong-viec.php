<?php
/** @var \yii\web\View $this */
$this->title = 'Nhập dữ liệu vị trí công việc';

echo $thongbao;
?>
<?php $form = \yii\bootstrap\ActiveForm::begin([
    'options' => ['id' => 'form-import', 'enctype' => 'multipart/form-data']
]) ?>
<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <?=\yii\helpers\Html::label('Phòng ban'); ?>
            <?=\yii\bootstrap\Html::dropDownList('phongban', null, \yii\helpers\ArrayHelper::map(\backend\models\DanhMuc::findAll(['type' => \backend\models\DanhMuc::PHONG_BAN]), 'id', 'name'), [
                'class' => 'form-control', 'prompt' => ''
            ]);?>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <?=\yii\helpers\Html::label('File đính kèm'); ?>
            <?=\yii\bootstrap\Html::fileInput('file_nhap_lieu', null, ['class' => 'form-control'])?>
        </div>
    </div>
    <div class="col-md-3">
        <blockquote>
            <?=\yii\helpers\Html::a('<i class="fa fa-link"></i> Tải mẫu file import', 'fileimports/MAU_IMPORT_QLCV_SAO_DO.xlsx', array('class' => 'text-primary'))?>
        </blockquote>
    </div>
</div>
<?=\yii\bootstrap\Html::submitButton('<i class="fa fa-cloud-upload"></i> Upload', ['class' => 'btn btn-primary'])?>


<?php \yii\bootstrap\ActiveForm::end() ?>
