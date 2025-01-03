<?php
/**
 * Created by PhpStorm.
 * User: hungluong
 * Date: 5/21/18
 * Time: 09:26
 */?>
<?=\yii\bootstrap\Html::beginForm('','', ['enctype' => 'multipart/form-data'])?>
<?=\yii\bootstrap\Html::fileInput('files[]', null, ['multiple' => 'multiple'])?>
<?=\yii\bootstrap\Html::submitButton('<i class="fa fa-upload"></i> Tải lên và lưu lại');?>
<?=\yii\bootstrap\Html::endForm()?>

