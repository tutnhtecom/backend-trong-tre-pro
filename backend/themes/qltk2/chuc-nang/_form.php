<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ChucNang */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="chuc-nang-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nhom')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'ghi_chu')->textarea(['rows' => 3]) ?>

    <?= $form->field($model, 'controller_action')->textInput(['maxlength' => true, 'placeholder' => 'Controller;Action. Viết hoa chữ cái đầu mỗi từ']) ?>

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
