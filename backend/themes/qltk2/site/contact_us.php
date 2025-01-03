<?php
/**
 * Created by PhpStorm.
 * User: pnevn
 * Date: 4/9/2019
 * Time: 4:59 PM
 * @var $branches \backend\models\DanhMuc[]
 * @var $contact_us \backend\models\ContactUs
 */?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
<p><label>Contact us by phone</label></p>
<ul class="list-unstyled">
    <?php foreach ($branches as $branch): ?>
    <li class="branch-info row">
        <div class="col-md-6 name-branch"><?=$branch->name?></div>
        <div class="col-md-6">: <?=$branch->phone?></div>
    </li>
    <?php endforeach; ?>
</ul>
<?php $form = \yii\widgets\ActiveForm::begin([
    'options' => ['id' => 'form-contact-us']
]) ?>
<?=$form->field($contact_us, 'your_name')->label('Your name <strong class="text-danger">*</strong>');?>
<?=$form->field($contact_us, 'your_email')->textInput(['type' => 'email'])->label('Your email <strong class="text-danger">*</strong>');?>
<?=$form->field($contact_us, 'your_phone')->textInput()->label('Your phone <strong class="text-danger">*</strong>');?>
<?=$form->field($contact_us, 'branch_id')->dropDownList(
    \yii\helpers\ArrayHelper::map($branches, 'id', 'name'), ['prompt' => '--Choose a branch--']
)->label('Branch <strong class="text-danger">*</strong>');?>
<?=$form->field($contact_us, 'message')->textarea(['rows' => 3])->label('Messsage <strong class="text-danger">*</strong>')?>
<?=\yii\helpers\Html::a('<i class="fa fa-send-o"></i> Send', '#', ['class' => 'btn btn-primary btn-send-contact-us'])?>
<?php \yii\widgets\ActiveForm::end(); ?>

