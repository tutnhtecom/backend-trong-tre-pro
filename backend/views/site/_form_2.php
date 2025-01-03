<!DOCTYPE html>

<!--[if IE 8]>
<html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8"/>
    <title>TRÔNG TRẺ PRO</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet"
          type="text/css"/>
    <link href="<?= Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/global/plugins/font-awesome/css/font-awesome.min.css"
          rel="stylesheet" type="text/css"/>
    <link href="<?= Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/global/plugins/simple-line-icons/simple-line-icons.min.css"
          rel="stylesheet" type="text/css"/>
    <link href="<?= Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/global/plugins/uniform/css/uniform.default.css"
          rel="stylesheet" type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link href="<?= Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/admin/pages/css/login2.css"
          rel="stylesheet" type="text/css"/>
    <!-- END PAGE LEVEL SCRIPTS -->
    <!-- BEGIN THEME STYLES -->
    <link href="<?= Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/global/css/components.css"
          id="style_components" rel="stylesheet" type="text/css"/>
    <link href="<?= Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/global/css/plugins.css" rel="stylesheet"
          type="text/css"/>
    <link href="<?= Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/admin/layout/css/layout.css"
          rel="stylesheet" type="text/css"/>
    <link href="<?= Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/admin/layout/css/themes/darkblue.css"
          rel="stylesheet" type="text/css" id="style_color"/>
    <link href="<?= Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/admin/layout/css/custom.css"
          rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

    <!-- END THEME STYLES -->
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<style>
    .d-flex {
        display: flex;
        margin-top: 10px;
        align-items: center;
        white-space: nowrap;
    }

    .input-text {
        border: none;
        border-bottom: 1px dashed;
        position: relative;
        /* top: -5px; */
        outline: none;
        padding-bottom: 0;
        width: 100%;
    }

    .area-input {
        border: none;
        width: 100%;
        outline: none;
    }

    .area {
        border: none;
        width: 100%;
        outline: none;
        background-image: -webkit-linear-gradient(left, white 10px, transparent 10px), -webkit-linear-gradient(right, white 10px, transparent 10px), -webkit-linear-gradient(white 30px, #ccc 30px, #ccc 31px, white 31px);
        background-size: 100% 100%, 100% 100%, 100% 31px;
        line-height: 10px;
        padding: 10px;
        padding-top: 5px;
        margin-bottom: 10px;
    }

    .checkbox {
        margin-right: 20px;
    }

    .width-100 {
        width: 100% !important;
    }
    label {
        font-weight: 400!important;
        font-size: 14px!important;
    }
</style>

<body style="background: #f5f5f5;">
<div class="col-md-12 text-center margin-top-20">
    <img src="/images/Banner.png">
</div>
<?php $block_1 = true ?>
<?php $block_2 = true ?>
<?php if (is_object($data)): ?>

    <div class="col-md-12 margin-top-25 block-1">
        <label><a href="#block1" style="color: unset;width: 100%" data-toggle="collapse"><strong style="font-size: 18px">Thông tin chung</strong></a></label>
        <div id="block1" class="panel collapse in" style="border-radius: 10px!important;">
            <div class="panel-body">
                <div class="row">
                    <?php if ($data->user_1 !== ""): ?>

                        <div class="col-xs-5">
                            <strong>Người thực hiện</strong>
                        </div>
                        <div class="col-xs-7">
                            <?= $data->user_1 ?>
                        </div>
                        <?php $block_1 = false ?>
                    <?php endif; ?>
                    <?php if ($data->address_user !== ""): ?>
                        <div class="col-xs-5 margin-top-10">
                            <strong>Địa chỉ ca làm</strong>
                        </div>
                        <div class="col-xs-7 margin-top-10">
                            <?= $data->address_user ?>
                        </div>
                        <?php $block_1 = false ?>
                    <?php endif; ?>
                    <?php if ($data->dai_dien_gia_dinh !== ""): ?>
                        <div class="col-xs-5 margin-top-10">
                            <strong>Đại diện gia đình</strong>
                        </div>
                        <div class="col-xs-7 margin-top-10">
                            <?= $data->dai_dien_gia_dinh ?>
                        </div>
                        <?php $block_1 = false ?>
                    <?php endif; ?>
                    <?php if ($data->dien_thoai !== ""): ?>
                        <div class="col-xs-5 margin-top-10">
                            <strong>Điện thoại</strong>
                        </div>
                        <div class="col-xs-7 margin-top-10">
                            <?= $data->dien_thoai ?>
                        </div>
                        <?php $block_1 = false ?>
                    <?php endif; ?>
                    <?php if ($data->nguoi_than_2 !== ""): ?>
                        <div class="col-xs-5 margin-top-10">
                            <strong>Người thân thứ 2</strong>
                        </div>
                        <div class="col-xs-7 margin-top-10">
                            <?= $data->nguoi_than_2 ?>
                        </div>
                        <?php $block_1 = false ?>
                    <?php endif; ?>
                    <?php if ($data->dien_thoai_2 !== ""): ?>
                        <div class="col-xs-5 margin-top-10">
                            <strong>Điện thoại </strong>
                        </div>
                        <div class="col-xs-7 margin-top-10">
                            <?= $data->dien_thoai_2 ?>
                        </div>
                        <?php $block_1 = false ?>
                    <?php endif; ?>
                    <?php if ($data->ho_va_ten_tre_em !== ""): ?>
                        <div class="col-xs-5 margin-top-10">
                            <strong>Họ và tên trẻ (nickname)</strong>
                        </div>
                        <div class="col-xs-7 margin-top-10">
                            <?= $data->ho_va_ten_tre_em ?>
                        </div>
                        <?php $block_1 = false ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 margin-top-25 block-2">
        <label ><a href="#block2" data-toggle="collapse" style="color: unset"> <strong style="font-size: 18px">Timeline hoạt động</strong></a></label>
        <div id="block2" class="collapse in">
        <?php foreach (range(1, 6) as $item): ?>
        <?php if (isset($data->{'time_' . $item})&&isset($data->{'hoat_dong_' . $item})&& isset($data->{'du_kien_' . $item})):?>
            <?php if ($data->{'time_' . $item} !== "" || $data->{'hoat_dong_' . $item} !== "" || $data->{'du_kien_' . $item} !== ""): ?>
                <?php $block_2 = false ?>
                <div class="panel" style="border-radius: 10px!important;">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-xs-5 margin-top-10">
                                <strong>Giờ giấc</strong>
                            </div>
                            <div class="col-xs-7 margin-top-10">
                                <?= $data->{'time_' . $item} ?>
                            </div>
                            <div class="col-xs-5 margin-top-10">
                                <strong>Hoạt động của bé</strong>
                            </div>
                            <div class="col-xs-7 margin-top-10">
                                <?= $data->{'hoat_dong_' . $item} ?>
                            </div>
                            <div class="col-xs-5 margin-top-10">
                                <strong>Dự kiến công việc của cô</strong>
                            </div>
                            <div class="col-xs-7 margin-top-10">
                                <?= $data->{'du_kien_' . $item} ?>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endif; ?>
        <?php endif;?>
        <?php endforeach; ?>
        </div>
    </div>
    <div class="col-md-12 margin-top-25">
        <label><a href="#block3" style="color: unset" data-toggle="collapse"><strong style="font-size: 18px">Câu hỏi khảo sát</strong></a></label>
        <div class="collapse in" id="block3">
            <div class="panel" style="border-radius: 10px!important; pointer-events: none;">
                <div class="panel-body">
                    <label style="margin-right: 10px; margin-bottom:10px;"><strong>1. Sức khỏe hiện nay của bé có gặp
                            vấn
                            đề gì cần người chăm sóc trẻ lưu ý đặc biệt không ?
                            VD: biểu hiện sốt, hen phế quản, dị ứng…Nếu không có ghi “Không”</strong>
                    </label>
                    <label class="width-100" style="display: flex;align-items: start;"><input
                            <?= in_array("Không gặp vấn đề", $data->cau_hoi_1) ? "checked" : "" ?>
                                type="checkbox" class="checkbox" value="Không gặp vấn đề" style="margin-right: 10px"
                                name="cau_hoi_1[]"> Không gặp vấn đề</label>
                    <label class="width-100" style="display: flex;align-items: start;"><input
                            <?= in_array("Ốm sốt, ho, cảm cúm…", $data->cau_hoi_1) ? "checked" : "" ?>
                                type="checkbox" class="checkbox" value="Ốm sốt, ho, cảm cúm…" style="margin-right: 10px"
                                name="cau_hoi_1[]"> Ốm sốt, ho, cảm cúm…</label>
                    <label class="width-100" style="display: flex;align-items: start;"><input
                            <?= in_array("Các bệnh tiêu hoá", $data->cau_hoi_1) ? "checked" : "" ?>
                                type="checkbox" class="checkbox" value="Các bệnh tiêu hoá" style="margin-right: 10px"
                                name="cau_hoi_1[]"> Các bệnh tiêu hoá</label>
                    <label class="width-100" style="display: flex;align-items: start;"><input
                            <?= in_array("Các bệnh da liễu", $data->cau_hoi_1) ? "checked" : "" ?>
                                type="checkbox" class="checkbox" value="Các bệnh da liễu" style="margin-right: 10px"
                                name="cau_hoi_1[]"> Các bệnh da liễu</label>
                </div>
            </div>
            <div class="panel" style="border-radius: 10px!important; pointer-events: none;">
                <div class="panel-body">
                    <label style="margin-right: 10px; margin-bottom: 10px;"><strong> 2. Loại sữa, cách pha hoặc hâm
                            nóng:</strong> </label>
                    <label class="width-100" style="display: flex;align-items: start;"><input
                            <?= in_array("Sữa mẹ trực tiếp", $data->cau_hoi_2) ? "checked" : "" ?>
                                type="checkbox" class="checkbox" value="Sữa mẹ trực tiếp" style="margin-right: 10px"
                                name="cau_hoi_2[]"> Sữa mẹ trực tiếp</label>
                    <label class="width-100" style="display: flex;align-items: start;"><input
                            <?= in_array("Sữa mẹ cấp đông", $data->cau_hoi_2) ? "checked" : "" ?>
                                type="checkbox" class="checkbox" value="Sữa mẹ cấp đông" style="margin-right: 10px"
                                name="cau_hoi_2[]"> Sữa mẹ cấp đông</label>
                    <label class="width-100" style="display: flex;align-items: start;"><input
                            <?= in_array("Sữa công thức", $data->cau_hoi_2) ? "checked" : "" ?> type="checkbox"
                                                                                                class="checkbox"
                                                                                                value="Sữa công thức"
                                                                                                style="margin-right: 10px"
                                                                                                name="cau_hoi_2[]"> Sữa
                        công thức</label>
                    <label><strong>Ghi chú:</strong></label>
                    <input class="form-control" style="border-radius: 5px!important;"
                           value="<?= $data->ghi_chu_2 ?>">
                </div>
            </div>
            <div class="panel" style="border-radius: 10px!important; pointer-events: none;">
                <div class="panel-body">
                    <label style="margin-right: 10px; margin-bottom: 10px;"><strong>3. Lưu ý về bữa ăn của
                            trẻ</strong></label>
                    <label class="width-100" style="display: flex;align-items: start;"><input
                            <?= in_array("Có quy tắc bàn ăn", $data->cau_hoi_3) ? "checked" : "" ?>
                                type="checkbox" class="checkbox" value="Có quy tắc bàn ăn" style="margin-right: 10px"
                                name="cau_hoi_3[]"> Có quy tắc bàn ăn</label>
                    <label class="width-100" style="display: flex;align-items: start;"><input
                            <?= in_array("Không", $data->cau_hoi_3) ? "checked" : "" ?> type="checkbox"
                                                                                        class="checkbox" value="Không"
                                                                                        style="margin-right: 10px"
                                                                                        name="cau_hoi_3[]"> Không</label>
                    <label class="width-100" style="display: flex;align-items: start;"><input
                            <?= in_array("Ăn dặm", $data->cau_hoi_3) ? "checked" : "" ?> type="checkbox"
                                                                                         class="checkbox" value="Ăn dặm"
                                                                                         style="margin-right: 10px"
                                                                                         name="cau_hoi_3[]"> Ăn
                        dặm</label>
                    <label><strong>Ghi chú:</strong></label>
                    <input class="form-control" style="border-radius: 5px!important;"
                           value="<?= $data->ghi_chu_3 ?>">
                </div>
            </div>
            <div class="panel" style="border-radius: 10px!important; pointer-events: none;">
                <div class="panel-body">
                    <label style="margin-right: 10px; margin-bottom: 10px;"><strong>4. Lưu ý về giờ ngủ của
                            trẻ</strong></label>
                    <label><strong>Ghi chú:</strong></label>
                    <input class="form-control" style="border-radius: 5px!important;"
                           value="<?= $data->ghi_chu_4 ?>">
                </div>
            </div>
            <div class="panel" style="border-radius: 10px!important; pointer-events: none;">
                <div class="panel-body">
                    <label style="margin-right: 10px; margin-bottom: 10px;"><strong>5. Dặn dò về hoạt động vui chơi
                            của trẻ</strong></label>
                    <label class="width-100" style="display: flex;align-items: start;"><input
                            <?= in_array("Cho bé ra ngoài trời chơi. Ghi rõ những khu vực gia đình cho phép", $data->cau_hoi_5) ? "checked" : "" ?>
                                type="checkbox" value="Cho bé ra ngoài trời chơi. Ghi rõ những khu vực gia đình cho phép"
                                class="checkbox" style="margin-right: 10px" name="cau_hoi_5[]"> Cho bé ra ngoài trời chơi.
                        Ghi rõ những khu vực gia đình cho phép</label>
                    <label class="width-100" style="display: flex;align-items: start;"><input
                            <?= in_array("Chỉ hoạt động trong khuôn viên nhà", $data->cau_hoi_5) ? "checked" : "" ?>
                                type="checkbox" value="Chỉ hoạt động trong khuôn viên nhà" class="checkbox"
                                style="margin-right: 10px" name="cau_hoi_5[]"> Chỉ hoạt động trong khuôn viên nhà</label>
                </div>
            </div>
            <div class="panel" style="border-radius: 10px!important; pointer-events: none;">
                <div class="panel-body">
                    <label style="margin-right: 10px; margin-bottom: 10px;"><strong>6. Dặn dò về toa thuốc cần cho bé
                            dùng</strong></label> <br>
                    <label><input <?= in_array("Có", [$data->cau_hoi_6]) ? "checked" : "" ?> type="radio"
                                                                                             value="Có" name="cau_hoi_6"> Có</label>
                    <label><input <?= in_array("Không", [$data->cau_hoi_6]) ? "checked" : "" ?> type="radio"
                                                                                                value="Không"
                                                                                                name="cau_hoi_6"
                                                                                                style="margin-left: 20px">
                        Không</label>
                    <br>
                    <label><strong>Ghi chú:</strong></label>
                    <input class="form-control" style="border-radius: 5px!important;"
                           value="<?= $data->ghi_chu_6 ?>">
                </div>
            </div>
            <div class="panel" style="border-radius: 10px!important; pointer-events: none;">
                <div class="panel-body">
                    <label style="margin-right: 10px; margin-bottom: 10px;"><strong>7. Chỉ áp dụng với ca 8h, suất ăn của
                            cô do
                            gia đình chuẩn bị hoặc gia đình phụ cấp cho cô tự chuẩn bị suất ăn
                            (30k/ngày).</strong></label>
                    <label><input <?= in_array("Gia đình chuẩn bị", [$data->cau_hoi_7]) ? "checked" : "" ?>
                                type="radio" name="cau_hoi_7" value="Gia đình chuẩn bị"> Gia đình chuẩn bị</label>
                    <label><input <?= in_array("Cô tự chuẩn bị", [$data->cau_hoi_7]) ? "checked" : "" ?>
                                type="radio" name="cau_hoi_7" value="Cô tự chuẩn bị" style="margin-left: 20px"> Cô tự
                        chuẩn bị</label>
                </div>
            </div>
        </div>

    </div>
<?php else: ?>


<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/global/plugins/respond.min.js"></script>
<script src="<?= Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/global/plugins/excanvas.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function () {
        <?php if ($block_1):?>
        $(".block-1").hide()
        <?php endif;?>
        <?php if ($block_2):?>
        $(".block-2").hide()
        <?php endif;?>
    })
</script>
<!-- END CORE PLUGINS -->
</body>

<!-- END BODY -->
</html>
