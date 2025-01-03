<?php
/**
 * @var $this View
 */

use backend\models\ChucNang;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\web\View;

$this->title = 'Phân quyền'
?>
<?php $form = ActiveForm::begin([
        'options' => [
                'id' => 'form-phanquyen'
        ]
]) ?>
<div class="row">
    <div class="col-md-6">
        <label>Nhóm chức năng</label>
        <?= Html::dropDownList('nhom_chuc_nang', null, ArrayHelper::map(
            ChucNang::find()->groupBy('nhom')->all(), 'nhom', 'nhom'
        ), ['class' => 'form-control', 'prompt' => '', 'id' => 'nhom-chuc-nang'])?>
    </div>

</div>

<div id="table-phan-quyen">

</div>
<?php ActiveForm::end(); ?>

<style>
    table {
        text-align: left;
        position: relative;
        border-collapse: collapse;
    }

    th {
        background: white;
        position: sticky;
        top: 10%;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    }
</style>

<?php $this->registerCssFile(Yii::$app->request->baseUrl.'/backend/themes/qltk2/assets/global/plugins/bootstrap-select/bootstrap-select.min.css');?>
<?php $this->registerCssFile(Yii::$app->request->baseUrl.'/backend/themes/qltk2/assets/global/plugins/select2/select2.css');?>

<?php $this->registerJsFile(Yii::$app->request->baseUrl.'/backend/themes/qltk2/assets/global/plugins/bootstrap-select/bootstrap-select.min.js',[ 'depends' => ['backend\assets\Qltk2Asset'], 'position' => View::POS_END ]); ?>
<?php $this->registerJsFile(Yii::$app->request->baseUrl.'/backend/themes/qltk2/assets/global/plugins/select2/select2.min.js',[ 'depends' => ['backend\assets\Qltk2Asset'], 'position' => View::POS_END ]); ?>
<?php $this->registerJsFile(Yii::$app->request->baseUrl.'/backend/themes/qltk2/assets/global/scripts/index-phanquyen.js',[ 'depends' => ['backend\assets\Qltk2Asset'], 'position' => View::POS_END ]); ?>

