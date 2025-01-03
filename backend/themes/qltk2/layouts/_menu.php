<?php

use common\models\User;
use common\models\VietHungAPI;
use yii\helpers\Html;
use common\models\myAPI;
use yii\helpers\Url;

?>
<ul class="nav navbar-nav">


        <li class="classic-menu-dropdown">
            <a data-toggle="dropdown" href="javascript:;" data-hover="megamenu-dropdown" data-close-others="true">
                <i class="fa fa-cog"></i> Hệ thống <i class="fa fa-angle-down"></i></a>
            <ul class="dropdown-menu pull-left">
                <?php if (myAPI::isAccess2('User', 'quan-ly-nguoi-dung')): ?>
                    <li>
                        <?= Html::a('<i class="fa fa-users"></i> Thành viên', Url::to(['user/quan-ly-nguoi-dung'])) ?>
                    </li>
                <?php endif; ?>
                <?php if (myAPI::isAccess2('VaiTro', 'index')): ?>
                    <li>
                        <?= Html::a('<i class="fa fa-users"></i> Vai trò', Yii::$app->urlManager->createUrl(['vai-tro'])) ?>
                    </li>
                <?php endif; ?>
                <?php if (myAPI::isAccess2('DanhMuc', 'Index')): ?>
                    <li>
                        <?= Html::a('<i class="fa fa-file"></i> Danh mục', Url::toRoute('danh-muc/index')) ?>
                    </li>
                <?php endif; ?>
                <?php if (myAPI::isAccess2('ChucNang', 'index')): ?>
                    <li>
                        <?= Html::a('<i class="fa fa-bars"></i> Chức năng', Yii::$app->urlManager->createUrl(['chuc-nang'])) ?>
                    </li>
                <?php endif; ?>
                <?php if (myAPI::isAccess2('PhanQuyen', 'index')): ?>
                    <li>
                        <?= Html::a('<i class="fa fa-users"></i> Phân quyền', Yii::$app->urlManager->createUrl(['phan-quyen'])) ?>
                    </li>
                <?php endif; ?>
            </ul>
        </li>
</ul>
