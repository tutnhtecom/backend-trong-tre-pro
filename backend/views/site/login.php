<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Đăng nhập';
?>

<!DOCTYPE html>

<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8"/>
    <title>TRÔNG TRẺ PRO</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
    <link href="<?=Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?=Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?=Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?=Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link href="<?=Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/admin/pages/css/login2.css" rel="stylesheet" type="text/css"/>
    <!-- END PAGE LEVEL SCRIPTS -->
    <!-- BEGIN THEME STYLES -->
    <link href="<?=Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/global/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>
    <link href="<?=Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>
    <link href="<?=Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
    <link href="<?=Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/admin/layout/css/themes/darkblue.css" rel="stylesheet" type="text/css" id="style_color"/>
    <link href="<?=Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
    <!-- END THEME STYLES -->
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="login">
<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
<div class="menu-toggler sidebar-toggler">
</div>
<!-- END SIDEBAR TOGGLER BUTTON -->
<!-- BEGIN LOGO -->
<div class="logo">
    <a href="#" style="font-size: 18pt; text-decoration: none">
        TRÔNG TRẺ PRO
    </a>
</div>
<!-- END LOGO -->
<!-- BEGIN LOGIN -->
<div class="content">
    <!-- BEGIN LOGIN FORM -->
    <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false, 'class' => 'login-form']); ?>
    <div class="form-title">
        <span class="form-title">Xin chào!.</span>
        <span class="form-subtitle">Vui lòng đăng nhập.</span>
    </div>

    <div class="alert alert-danger display-hide">
        <button class="close" data-close="alert"></button>
        <span>
            Điền thông tin tên đăng nhập và mật khẩu
        </span>
    </div>
    <div class="form-group">
        <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
        <label class="control-label visible-ie8 visible-ie9">Tên đăng nhập</label>
        <?=Html::activeTextInput($model, 'username',['autofocus' => true, 'class' => 'form-control form-control-solid placeholder-no-fix', 'autocomplete' => 'off', 'placeholder' => 'Tên đăng nhập'])?>
        <?=Html::error($model, 'username',['class' => 'form-subtitle'])?>

    </div>
    <div class="form-group">
        <label class="control-label visible-ie8 visible-ie9">Mật khẩu</label>
        <?=Html::activePasswordInput($model, 'password',['class' => 'form-control form-control-solid placeholder-no-fix', 'autocomplete' => 'new-password', 'placeholder' => 'Mật khẩu'])?>
        <?=Html::error($model, 'password',['class' => 'form-subtitle'])?>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary btn-block uppercase">Đăng nhập</button>
    </div>
    <?php \yii\bootstrap\ActiveForm::end(); ?>
    <!-- END LOGIN FORM -->
</div>
<div class="copyright">
    <?=date("Y"); ?> TRÔNG TRẺ PRO<sup>™</sup>
</div>
<!-- END LOGIN -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="<?=Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/global/plugins/respond.min.js"></script>
<script src="<?=Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/global/plugins/excanvas.min.js"></script>
<![endif]-->
<script src="<?=Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="<?=Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
<script src="<?=Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
</body>
<!-- END BODY -->
</html>
