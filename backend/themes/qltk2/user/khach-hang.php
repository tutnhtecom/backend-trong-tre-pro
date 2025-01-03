<?php

use johnitvn\ajaxcrud\CrudAsset;
use yii\bootstrap\Html;
use yii\widgets\Pjax;

$this->title = 'Khách hàng';
$this->params['breadcrumbs'][] = $this->title;
/** @var $table_khach_hang */
/** @var $phan_nhom[] */
/** @var $trang_thai[] */
CrudAsset::register($this);
?>
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script>
    var count_click = 0;
    $(document).ready(function () {
        $(".date").datepicker({dateFormat: 'yy/mm/dd'});
        $(".column").sortable({
            connectWith: ".column",
            handle: ".portlet-header",
            cancel: ".portlet-toggle",
            placeholder: "portlet-placeholder ui-corner-all",
        });

        $(".portlet")
            .addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all")
            .find(".portlet-header")
            .addClass("ui-widget-header ui-corner-all")
            .prepend("<span class='ui-icon ui-icon-minusthick portlet-toggle'></span>");

        $(".portlet-toggle").on("click", function () {
            var icon = $(this);
            icon.toggleClass("ui-icon-minusthick ui-icon-plusthick");
            icon.closest(".portlet").find(".portlet-content").toggle();
        });
    });
</script>
<div class="thong-bao-khach-hang">

</div>
<?php Pjax::begin(['id' => 'id-pjax-khach-hang']); ?>
    <div class="row form-tim-kiem" >
        <?php $form = \yii\bootstrap\ActiveForm::begin(['options' => ['id' => 'form-search', 'status' => '1']]); ?>
        <div class="col-md-12">
           <p style="position: relative">
               <?=Html::input('text','tu_khoa','',['class'=>'form-control tu-khoa'])?>
               <?=Html::button('<i class="fa fa-search"></i> Tìm kiếm',['class'=>'btn btn-danger btn-search','placeholder'=>'Từ khóa'])?>
           </p>

        </div>
        <div class="col-md-3">
           <p>
               <?=Html::input('text','tu_ngay','',['class'=>'form-control date select','placeholder'=>'Từ ngày'])?>
           </p>
        </div>
        <div class="col-md-3">
            <p>
                <?=Html::input('text','den_ngay','',['class'=>'form-control date select','placeholder'=>'Đến ngày'])?>
            </p>
        </div>
        <div class="col-md-3">
            <p>
                <?=Html::dropDownList('tren_toan_quoc','',$trang_thai,['class'=>'form-control ','prompt' => 'Trạng thái','id'=>'trang_thai_khach_hang'])?>
            </p>
        </div>
        <div class="col-md-3">
            <p class="pos">
                <?=Html::dropDownList('tren_toan_quoc','',$phan_nhom,['class'=>'form-control select','prompt' => '--Chọn--','id'=>'trang_thai_nhom_khach_hang'])?>
            </p>
        </div>
        <?php \yii\bootstrap\ActiveForm::end() ?>

    </div>
    <div class="table-khach-hang" style="overflow: auto">
        <?= $table_khach_hang ?>
    </div>
<?php Pjax::end(); ?>
