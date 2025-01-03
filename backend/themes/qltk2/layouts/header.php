qs<?php
/** @var $this View */

use backend\models\ThongBao;
use yii\helpers\Html;
use yii\web\View;
use common\models\User;

//\yii\helpers\VarDumper::dump($slYeuCauChoChapNhanChiaSe,10,true); exit();
?>

<!-- BEGIN HEADER -->
<div class="page-header -i navbar navbar-fixed-top">
    <!-- BEGIN HEADER INNER -->
    <div class="page-header-inner">
        <!-- BEGIN LOGO -->
        <div class="page-logo">
            <a href="<?=Yii::$app->urlManager->createUrl('site/index')?>" class="text-default">
            </a>
        </div>
        <!-- END LOGO -->

        <!-- BEGIN HORIZANTAL MENU -->
        <div class="hor-menu hidden-sm hidden-xs">
            <?=$this->render('_menu'); ?>
        </div>
        <!-- END HORIZANTAL MENU -->

        <!-- BEGIN RESPONSIVE MENU TOGGLER -->
        <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse"></a>
        <!-- END RESPONSIVE MENU TOGGLER -->
        <!-- BEGIN TOP NAVIGATION MENU -->
        <div class="top-menu">
            <ul class="nav navbar-nav">

            </ul>
            <ul class="nav navbar-nav pull-right">
                <!-- BEGIN USER LOGIN DROPDOWN -->
                <li class="dropdown dropdown-user">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <img alt="" class="img-circle" src="<?=Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/admin/layout/img/avatar3_small.jpg"/>
                        <span class="username username-hide-on-mobile">
                            <?=Yii::$app->user->isGuest ? '' : Yii::$app->user->identity->hoten; ?>
                        </span>
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-default">
                        <li>
                            <?=Html::a('<i class="fa faz badge-success"></i> '.(Yii::$app->user->isGuest ? '' : Yii::$app->user->identity->hoten), '',['class'=>'btn-cap-nhat-ho-so'])?>
                        </li>
                        <li>
                            <?=Html::a('<i class="glyphicon glyphicon-log-out"></i> Đăng xuất', Yii::$app->urlManager->createUrl('site/logout'))?>
                        </li>
                        <li>
                            <?=Html::a('<i class="icon-key"></i> Đổi mật khẩu', '#', ['class' => 'btn-doimatkhau'])?>
                        </li>

                    </ul>
                </li>
                <!-- END USER LOGIN DROPDOWN -->
            </ul>
        </div>
        <!-- END TOP NAVIGATION MENU -->
    </div>
    <!-- END HEADER INNER -->
</div>
<!-- END HEADER -->
<div class="clearfix"></div>
