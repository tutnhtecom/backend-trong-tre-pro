<?php
/**
 * Created by PhpStorm.
 * User: HungLuongHien
 * Date: 6/23/2016
 * Time: 1:54 PM
 */
use yii\helpers\Html;
?>
<!-- BEGIN CONTAINER -->
<div class="page-container">
    <!-- BEGIN SIDEBAR -->
    <div class="page-sidebar-wrapper">
        <div class="page-sidebar navbar-collapse collapse">
            <?=$this->render('_menu_mobile', []); ?>
        </div>
        <!-- END HORIZONTAL RESPONSIVE MENU -->
    </div>
    <!-- END SIDEBAR -->

    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE HEADER-->
            <h3 class="page-title">
                <?=$this->title?>
            </h3>
            <hr/>
            <!-- END PAGE HEADER-->
            <div id="print-block"></div>
            <div class="thongbao"></div>

            <div class="modal-thong-bao"></div>
            <?= \bariew\yii2Pusher\Widget::widget(['events' => [
                'notification' => new \yii\web\JsExpression("function(data){showAlert(data)}")
            ]]) ?>
            <?= $content ?>
        </div>
    </div>
    <!-- END CONTENT -->

    <!-- BEGIN FOOTER -->
    <div class="page-footer">
        <div class="row">
            <div class="col-xs-4 col-xs-offset-4">
                <div class="text-white text-center">
                    <?=date("Y")?> &copy; TRÔNG TRẺ PRO
                </div>
            </div>
            <div class="col-xs-4">
                <div class="text-right">
                    <?=Html::a('Phát triển bởi Công ty Cổ phần Thương mại Andin', 'https://www.andin.io/', ['target' => '_blank'])?>
                </div>
            </div>
        </div>

        <div class="scroll-to-top">
            <i class="icon-arrow-up"></i>
        </div>
    </div>
</div>
<style>

</style>
<!-- END CONTAINER -->


