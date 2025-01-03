<?php
/**
 * @var $this \yii\web\View
 * @var $congviec_nhiemvu \backend\models\CongViecNhiemVu[]
 * @var $index_congviec int
 * */?>
<?php
$this->title = 'Tạo vị trí công việc';

$form = \yii\widgets\ActiveForm::begin([
    'options' => ['id' => 'form-tao-vi-tri-cong-viec']
]); ?>
<div class="row">
    <?=\yii\bootstrap\Html::activeHiddenInput($model, 'id');?>
    <div class="col-md-3">
        <?=$form->field($model, 'name')->label('Tên'); ?>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <?=\yii\bootstrap\Html::label('Phòng ban');?>
            <?=\yii\bootstrap\Html::dropDownList('phong_ban', null, \yii\helpers\ArrayHelper::map(
                \backend\models\DanhMuc::findAll(['type' => \backend\models\DanhMuc::PHONG_BAN, 'active' => 1]),
                'id', 'name'
            ), ['prompt' => '', 'class' => 'form-control', 'id' => 'phong_bana']); ?>
        </div>

    </div>
    <?=\yii\bootstrap\Html::hiddenInput('index_nhom_lap_ke_hoach', $index_congviec, ['id' => 'index-nhom-lap-ke-hoach'])?>
    <?=\yii\helpers\Html::activeHiddenInput($model, 'type'); ?>
</div>
<div id="block-ke-hoach-cv">

</div>
<?php \yii\widgets\ActiveForm::end(); ?>

<?php $this->registerJsFile(Yii::$app->request->baseUrl.'/backend/themes/qltk2/assets/global/scripts/bootstrap3-typeahead.js',[ 'depends' => ['backend\assets\Qltk2Asset'], 'position' => \yii\web\View::POS_END ]); ?>
<?php $this->registerJsFile(Yii::$app->request->baseUrl.'/backend/assets/js-view/tao-vi-tri-cong-viec.js',[ 'depends' => ['backend\assets\Qltk2Asset'], 'position' => \yii\web\View::POS_END ]); ?>


