<?php

use common\models\User;
use yii\helpers\Html;
use common\models\myAPI;
use yii\helpers\Url;
?>
<ul class="page-sidebar-menu" data-slide-speed="200" data-auto-scroll="true">
    <?php if(
        myAPI::isAccess2('Cauhinh', 'index') ||
        myAPI::isAccess2('VaiTro', 'index') ||
        myAPI::isAccess2('ChucNang', 'index') ||
        myAPI::isAccess2('User', 'index') ||
        myAPI::isAccess2('PhanQuyen', 'index')
    ): ?>
        <li>
            <a href="javascript:;">
                <i class="fa fa-cog"></i> Hệ thống <span class="arrow">
            </a>
            <ul class="sub-menu">
                <?php if(myAPI::isAccess2('Cauhinh', 'index')): ?>
                    <li>
                        <?=Html::a('<i class="fa fa-cogs"></i> Cấu hình', Yii::$app->urlManager->createUrl(['cauhinh']))?>
                    </li>
                <?php endif; ?>
                <?php if(myAPI::isAccess2('User', 'index')): ?>
                    <li>
                        <?=Html::a('<i class="fa fa-users"></i> Thành viên', Url::to(['user/index']))?>
                    </li>
                <?php endif; ?>
                <?php if(myAPI::isAccess2('VaiTro', 'index')): ?>
                    <li>
                        <?=Html::a('<i class="fa fa-users"></i> Vai trò', Yii::$app->urlManager->createUrl(['vai-tro']))?>
                    </li>
                <?php endif; ?>

                <?php if(myAPI::isAccess2('ChucNang', 'index')): ?>
                    <li>
                        <?=Html::a('<i class="fa fa-bars"></i> Chức năng', Yii::$app->urlManager->createUrl(['chuc-nang']))?>
                    </li>
                <?php endif; ?>

                <?php if(myAPI::isAccess2('PhanQuyen', 'index')): ?>
                    <li>
                        <?=Html::a('<i class="fa fa-users"></i> Phân quyền', Yii::$app->urlManager->createUrl(['phan-quyen']))?>
                    </li>
                <?php endif; ?>
            </ul>
        </li>
    <?php endif; ?>
</ul>
