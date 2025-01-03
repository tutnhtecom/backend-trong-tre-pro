<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cây Chi nhánh';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

?>
<div class="row">
    <div class="col-md-4">
        <div class="portlet red-pink box">
            <div class="portlet-title ">
                <div class="caption  col-md-12">
                    <div class="portlet-footer ">
                        <span><i class="fa fa-cogs"></i> Cây chi nhánh</span>
                        <?=   Html::a('<span><i class="fa fa-plus-circle"></i> Thêm</span>', ['chi-nhanh/create'],
                            ['role'=>'modal-remote','title'=> 'Thêm mới chi nhánh','class'=>'btn btn-danger'])?>
                    </div>

                </div>
            </div>
            <div class="portlet-body">
                <div id="tree_nhomtaisan" class="tree-demo"></div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div id="block-thong-tin-chung">

        </div>
    </div>
</div>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>
<?php $this->registerCssFile(Yii::$app->request->baseUrl.'/backend/themes/qltk2/assets/global/plugins/jstree/dist/themes/default/style.min.css'); ?>
<?php $this->registerJsFile(Yii::$app->request->baseUrl.'/backend/themes/qltk2/assets/global/plugins/jstree/dist/jstree.min.js',[ 'depends' => ['backend\assets\Qltk2Asset'], 'position' => \yii\web\View::POS_END ]); ?>
<?php $this->registerJsFile(Yii::$app->request->baseUrl.'/backend/assets/js-view/indexdaily.js',[ 'depends' => ['backend\assets\Qltk2Asset'], 'position' => \yii\web\View::POS_END ]); ?>
<?php $this->registerJsFile(Yii::$app->request->baseUrl . '/backend/assets/js-view/chi-nhanh.js', ['depends' => ['backend\assets\Qltk2Asset'], 'position' => \yii\web\View::POS_END]); ?>
