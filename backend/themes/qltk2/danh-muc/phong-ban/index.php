<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\DanhMucSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Quản lý phòng ban';
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
                    Html::a('<i class="glyphicon glyphicon-plus"></i> Thêm Quản lý phòng ban', ['create'],
                        ['role'=>'modal-remote','title'=> 'Thêm mới Chức năng','class'=>'btn btn-primary'])
                ],
            ],
            'striped' => true,
            'condensed' => true,
            'responsive' => true,
            'responsiveWrap' => false,
            'panel' => [
                'type' => 'primary',
                'heading' => '<i class="glyphicon glyphicon-list"></i> Danh sách Quản lý phòng ban',
                'after' => false,
                'showFooter' => false,
            ],
            'tableOptions' => ['class' => 'table table-borderd table-stripped text-nowrap'],
        ])?>
    </div>
</div>
