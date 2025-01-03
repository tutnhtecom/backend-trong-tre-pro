<?php
/**
 * Created by PhpStorm.
 * User: pnevn
 * Date: 1/10/2019
 * Time: 8:31 AM
 */
use yii\bootstrap\Html;

?>
<ul class="nav navbar-nav pull-right">
    <!--                THÔNG BÁO CV QUÁ HẠN-->
    <li class="dropdown dropdown-extended dropdown-notification" id="notification-thuocsaphet">
        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
            <i class="icon-bell"></i>
                <?php $sl_qua_han = (new \backend\models\CongVanDen())->getSLCV(\backend\models\CongVanDen::QUA_HAN) ?>
                <?php $data = (new \backend\models\CongVanDen())->getDsCV(\backend\models\CongVanDen::QUA_HAN); ?>
                <span class="badge badge-danger"><?=$sl_qua_han?></span>
        </a>
        <ul class="dropdown-menu">
            <li class="external">
                <h3><span class="bold"><?=$sl_qua_han?> CV Quá hạn</h3>
                <a href="<?=\yii\helpers\Url::toRoute('cong-van-den/qua-han')?>">Xem thêm</a>
            </li>
            <li>
                <ul class="dropdown-menu-list scroller" style="height: 250px;" data-handle-color="#637283">
                    <?php /** @var \backend\models\QuanLyVanBanDen $item */ ?>
                    <?php foreach ($data as $item): ?>
                        <li>
                            <a href="javascript:;" class="thongtincongvan-on-menu" data-value="<?=$item->ID?>">
                            <span class="details">
                                <?="[{$item->soDen}] $item->trichYeuNoiDung"?>
                                <span class="pull-right"><strong class="text-danger">(Còn <?=$item->so_ngay_con_lai?> ngày)</strong></span>
                            </span>ss
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>
        </ul>
    </li>
    <!--                END CV QÚA HẠN-->

    <!--                THÔNG BÁO CV SẮP ĐẾN HẠN-->
    <li class="dropdown dropdown-extended dropdown-notification" id="notification-thuocsaphet">
        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
            <i class="icon-bell"></i>
            <?php $sl_toihan = (new \backend\models\CongVanDen())->getSLCV(\backend\models\CongVanDen::SAP_TOI_HAN); ?>
            <i class="icon-bell"></i>
            <span class="badge badge-warning"><?=$sl_toihan?></span>
        </a>
        <ul class="dropdown-menu">
            <li class="external">
                <h3><span class="bold"><?=$sl_toihan?> Công văn tới hạn</h3>
                <?php $data = (new \backend\models\CongVanDen())->getDsCV(\backend\models\CongVanDen::SAP_TOI_HAN); ?>
                <a href="<?=\yii\helpers\Url::toRoute('cong-van-den/toi-han')?>">Xem thêm</a>
            </li>
            <li>
                <ul class="dropdown-menu-list scroller" style="height: 250px;" data-handle-color="#637283">
                    <?php foreach ($data as $item): ?>
                        <li>
                            <a href="javascript:;" class="thongtincongvan-on-menu" data-value="<?=$item->ID?>">
                            <span class="details">
                                <?="[{$item->soDen}] $item->trichYeuNoiDung"?>
                                <span class="pull-right"><strong class="text-danger">(Còn <?=$item->so_ngay_con_lai?> ngày)</strong></span>
                            </span>
                            </a>
                                                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>
        </ul>
    </li>
    <!--                END CV ĐẾN HẠN-->


    <li class="dropdown dropdown-user">
        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
            <img alt="" class="img-circle" src="<?=Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/admin/layout/img/avatar3_small.jpg"/>
            <span class="username username-hide-on-mobile"><?=Yii::$app->user->isGuest?"":Yii::$app->user->identity->username?> </span>
            <i class="fa fa-angle-down"></i>
        </a>
        <?php if(!Yii::$app->user->isGuest):?>
            <ul class="dropdown-menu dropdown-menu-default">

                <li>
                    <?=Html::a('<i class="icon-key"></i> Đổi mật khẩu', '#', ['class' => 'btn-doimatkhau'])?>
                </li>
                <li>
                    <?=Html::a('<i class="glyphicon glyphicon-log-out"></i> Đăng xuất', Yii::$app->urlManager->createUrl('site/logout'))?>
                </li>
            </ul>
        <?php endif; ?>
    </li>

    <li class="dropdown dropdown-quick-sidebar-toggler">
        <a href="<?=Yii::$app->urlManager->createUrl('site/logout')?>" class="dropdown-toggle">
            <i class="icon-logout"></i>
        </a>
    </li>
</ul>
