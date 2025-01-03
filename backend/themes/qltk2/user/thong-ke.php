<?php
use backend\models\DanhMuc;
use backend\models\SanPham;
use common\models\myAPI;
use common\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$this->title = 'Thống kê khách hàng';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="col-md-6">
                <label>Từ ngày</label>
                <input type="text"  id="tuNgay" class="form-control">
        </div>
        <div class="col-md-6">
            <label>Đến ngày</label>
            <input type="text" id="denNgay" class="form-control">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
    <div class="col-md-12">
            <button class="btn btn-primary btn-kiem-tra" style="width: 100%; margin-top: 10px">Kiểm tra</button>
    </div>
    </div>
</div>

<div id="chartdiv" style="width: 100%"></div>
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script>
    var count_click = 0;
    $(document).ready(function () {
        $('#tuNgay').datepicker()
        $('#denNgay').datepicker()

    });
    $(document).on('click', '.btn-kiem-tra',function (e) {

        am4core.ready(function() {

// Themes begin
            am4core.useTheme(am4themes_animated);
// Themes end

// Create chart instance
            var chart = am4core.create("chartdiv", am4charts.XYChart);

// Add data
//         chart.data = cart;
            //
            chart.data = [{
                "country": "11/08/2022",
                "visits": 78
            },{
                "country": "12/08/2022",
                "visits": 90
            },
            {
                "country": "13/08/2022",
                "visits": 50
            }];

// Create axes

            var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "country";
            categoryAxis.renderer.grid.template.location = 0;
            categoryAxis.renderer.minGridDistance = 30;

            categoryAxis.renderer.labels.template.adapter.add("dy", function(dy, target) {
                if (target.dataItem && target.dataItem.index & 2 == 2) {
                    return dy + 25;
                }
                return dy;
            });

            var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

// Create series
            var series = chart.series.push(new am4charts.ColumnSeries());
            series.dataFields.valueY = "visits";
            series.dataFields.categoryX = "country";
            series.name = "Visits";
            series.columns.template.tooltipText = "{categoryX}: [bold]{valueY}[/]";

            var columnTemplate = series.columns.template;
            columnTemplate.strokeWidth = 2;
            columnTemplate.strokeOpacity = 1;

        });
    });
</script>

<div id="chartdiv" style="width: 100%"></div>


<?php $this->registerJsFile(Yii::$app->request->baseUrl . '/backend/assets/js-view/khach-hang.js', ['depends' => ['backend\assets\Qltk2Asset'], 'position' => \yii\web\View::POS_END]); ?>
<?php $this->registerJsFile(Yii::$app->request->baseUrl . '/backend/themes/qltk2/assets/global/plugins/amcharts4/core.js', ['depends' => ['backend\assets\Qltk2Asset'], 'position' => \yii\web\View::POS_END]); ?>
<?php $this->registerJsFile(Yii::$app->request->baseUrl . '/backend/themes/qltk2/assets/global/plugins/amcharts4/charts.js', ['depends' => ['backend\assets\Qltk2Asset'], 'position' => \yii\web\View::POS_END]); ?>
<?php $this->registerJsFile(Yii::$app->request->baseUrl . '/backend/themes/qltk2/assets/global/plugins/amcharts4/themes/animated.js', ['depends' => ['backend\assets\Qltk2Asset'], 'position' => \yii\web\View::POS_END]); ?>
