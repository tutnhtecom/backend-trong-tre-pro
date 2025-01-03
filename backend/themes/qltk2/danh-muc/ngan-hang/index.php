<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\DonviSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Danh mục ngân hàng';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

?>
<div class="nhommau-index">
    <div id="ajaxCrudDatatable">
        <?=GridView::widget([
            'summary' => "Hiển thị {begin} - {end} Trên tổng số {totalCount}",
            'id'=>'crud-datatable',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pjax'=>true,
            'emptyText' => 'Chưa có dữ liệu',
            'columns' => require(__DIR__.'/_columns.php'),
            'toolbar'=> [
                ['content'=>
                    Html::a('<i class="glyphicon glyphicon-plus"></i> Thêm ngân hàng', ['danh-muc/tao-ngan-hang'],
                        ['role'=>'modal-remote','title'=> 'Thêm mới ngân hàng','class'=>'btn btn-primary'])
                ],
            ],
            'striped' => true,
            'condensed' => true,
            'responsive' => true,
            'responsiveWrap' => false,
            'tableOptions' => ['class' => 'table table-bordered table-striped text-nowrap'],
            'panel' => [
                'type' => 'primary',
                'heading' => '<i class="glyphicon glyphicon-list"></i> Danh sách ngân hàng',
                'after' => false,
                'showFooter' => false,
            ]
        ])?>
    </div>
</div>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>

<?php $this->registerJsFile(Yii::$app->request->baseUrl.'/backend/assets/js-view/index-hoivien.js',[ 'depends' => ['backend\assets\Qltk2Asset'], 'position' => \yii\web\View::POS_END ]); ?>

