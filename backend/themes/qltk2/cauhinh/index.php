<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset; 
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\Cấu hìnhearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cấu hình';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

?>
<div class="cauhinh-index">
    <div id="ajaxCrudDatatable">
        <?=GridView::widget([
            'id'=>'crud-datatable',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pjax'=>true,
            'columns' => require(__DIR__.'/_columns.php'),
            'toolbar'=> [
                ['content'=>
                    Html::a('<i class="glyphicon glyphicon-repeat"></i> Khôi phục lưới', [''],
                    ['data-pjax'=>1, 'class'=>'btn btn-default btn-sm', 'title'=>'Khôi phục lưới']).
                    Html::a('<i class="glyphicon glyphicon-plus"></i> Thêm Danh mục', ['create'],
                        ['role'=>'modal-remote','title'=> 'Thêm mới chức năng','class'=>'btn btn-primary'])
                ],
            ],
            'tableOptions' => ['class' => 'table table-bordered  text-nowrap'],
            'striped' => true,
            'condensed' => true,
            'responsive' => true,          
            'panel' => [
                'type' => 'primary', 
                'heading' => '<i class="glyphicon glyphicon-list"></i> Cấu hình',

            ]
        ])?>
    </div>
</div>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>
