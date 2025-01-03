<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\PhuPhi */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="phu-phi-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'tieu_de')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tong_tien')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ghi_chu')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'type_id')->dropDownList(['43'=>'Ăn trưa','44'=>'thêm giờ']) ?>

    <?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
