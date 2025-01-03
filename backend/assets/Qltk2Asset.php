<?php

namespace backend\assets;


use yii\web\AssetBundle;

class Qltk2Asset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'backend/themes/qltk2/assets/global/jquery-ui/jquery-ui.min.css',
        'backend/themes/qltk2/assets/global/jquery-ui/jquery-ui.css',
        'backend/themes/qltk2/assets/global/plugins/font-awesome/css/font-awesome.min.css',
        'backend/themes/qltk2/assets/global/plugins/simple-line-icons/simple-line-icons.min.css',
        'backend/themes/qltk2/assets/global/plugins/bootstrap/css/bootstrap.min.css',
        'backend/themes/qltk2/assets/global/plugins/uniform/css/uniform.default.css',
        'backend/themes/qltk2/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css',

        'backend/themes/qltk2/assets/global/plugins/bootstrap-select/bootstrap-select.min.css',
        'backend/themes/qltk2/assets/global/plugins/select2/select2.css',
        'backend/themes/qltk2/assets/global/plugins/jquery-multi-select/css/multi-select.css',
        'backend/themes/qltk2/assets/admin/pages/css/profile-old.css',
        'backend/themes/qltk2/assets/global/css/components.css',
        'backend/themes/qltk2/assets/global/css/plugins.css',
        'backend/themes/qltk2/assets/admin/layout/css/layout.css',
        'backend/themes/qltk2/assets/admin/layout/css/themes/darkblue.css',
        'backend/themes/qltk2/assets/global/plugins/bootstrap-toastr/toastr.min.css',
        'backend/assets/plugins/jquery-confirm-master/dist/jquery-confirm.min.css',
        'backend/assets/plugins/jQuery-contextMenu-master/dist/jquery.contextMenu.min.css',
        'backend/assets/plugins/font-awesome-4.7.0/css/font-awesome.min.css',
        'backend/themes/qltk2/assets/admin/layout/css/custom.css',
        'backend/assets/plugins/lightbox/dist/css/lightbox.min.css',
    ];
    public $js = [
        'backend/themes/qltk2/assets/global/scripts/chart.js',
        'backend/themes/qltk2/assets/global/scripts/core.js',
        'backend/themes/qltk2/assets/global/scripts/charts.js',
        'backend/themes/qltk2/assets/global/scripts/animated.js',
        'backend/themes/qltk2/assets/global/scripts/main.js',
        'backend/themes/qltk2/assets/global/scripts/index.js',
        'backend/themes/qltk2/assets/global/scripts/xy.js',
        'backend/themes/qltk2/assets/global/scripts/Animatedv2.js',
        'backend/themes/qltk2/assets/global/scripts/jquery-migrate-1.2.1.min.js',
        'backend/themes/qltk2/assets/global/plugins/jquery-ui/jquery-ui.min.js',
        'backend/themes/qltk2/assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js',
        'backend/themes/qltk2/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js',
        'backend/themes/qltk2/assets/global/plugins/jquery.blockui.min.js',
        'backend/themes/qltk2/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js',
        'backend/themes/qltk2/assets/global/plugins/bootstrap-toastr/toastr.min.js',
        'backend/themes/qltk2/assets/global/scripts/metronic.js',
        'backend/themes/qltk2/assets/admin/layout/scripts/layout.js',
        'backend/themes/qltk2/assets/admin/layout/scripts/quick-sidebar.js',
        'backend/themes/qltk2/assets/global/scripts/jquery.PrintArea.js',

        'backend/themes/qltk2/assets/global/plugins/bootstrap-select/bootstrap-select.min.js',
        'backend/themes/qltk2/assets/global/plugins/select2/select2.min.js',
        'backend/themes/qltk2/assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js',
        'backend/assets/plugins/jQuery-contextMenu-master/dist/jquery.contextMenu.min.js',
        'backend/assets/plugins/jquery-confirm-master/dist/jquery-confirm.min.js',
        'backend/assets/plugins/lightbox/dist/js/lightbox.min.js',
        'backend/assets/js-view/doimatkhau.js',
        'backend/assets/js-view/pusher.min.js',
        'backend/assets/js-view/thong-bao.js',
        'backend/assets/js-view/menu.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

}

