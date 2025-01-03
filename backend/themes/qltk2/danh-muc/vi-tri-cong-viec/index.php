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

$this->title = 'Quản lý vị trí công việc';
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
                    Html::a('<i class="glyphicon glyphicon-plus"></i> Thêm vị trí công việc',
                        Url::toRoute('danh-muc/tao-vi-tri-cong-viec'),
                        ['class'=>'btn btn-primary'])
                ],
            ],
            'striped' => true,
            'condensed' => true,
            'responsive' => true,
            'panel' => [
                'type' => 'primary',
                'heading' => '<i class="glyphicon glyphicon-list"></i> Danh sách vị trí công việc,',
                'after' => false,
                'showFooter' => false,
            ],
            'tableOptions' => ['class' => 'table table-borderd table-stripped text-nowrap'],

        ])?>
    </div>
</div>
<?php $this->registerJsFile(Yii::$app->request->baseUrl.'/backend/assets/js-view/tao-vi-tri-cong-viec.js',[ 'depends' => ['backend\assets\Qltk2Asset'], 'position' => \yii\web\View::POS_END ]); ?>
