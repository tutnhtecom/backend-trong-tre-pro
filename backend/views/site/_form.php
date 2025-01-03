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
    <link href="<?= Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/global/plugins/bootstrap/css/bootstrap.min.css"
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
        border-bottom: 1px dotted;
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
    .checkbox{
        margin-right: 20px;
    }
    .width-100{
        width: 100%!important;
    }
    html,body,h1,h2,h3,h4,h5,h6{
        font-family: "Times New Roman", Times, serif!important;
    }
</style>
<body style="background: white">
<form id="form-khao-sat">
    <input type="hidden" id="token_form" name="<?= Yii::$app->request->csrfParam; ?>"
           value="<?= Yii::$app->request->csrfToken; ?>"/>
    <input type="hidden" name="giao_vien_id" id="giao_vien_id" value="<?= $_GET['giao_vien_id'] ?>"/>
    <input type="hidden" name="don_dich_vu_id" id="don_dich_vu_id" value="<?= $_GET['don_dich_vu_id'] ?>"/>
    <div class="p-5">
        <div class="row">
            <div class="col-md-8 col-md-offset-2 col-sm-offset-0" style="padding: 10px; ">
                <div class="col-md-12 text-center" style="margin-bottom: 10px;margin-top: 50px">
                    <h2 class="text-uppercase">Biên bản khảo sát ca làm bảo mẫu pro</h2>
                </div>
                <div class="col-md-12 text-center" style="margin-bottom: 30px">
                    <input class="input-text" name="address" style="width: 100px">ngày
                    <input class="input-text" name="day" style="width: 40px">tháng
                    <input class="input-text" name="month" style="width: 40px">năm
                    <input class="input-text" name="years" style="width: 40px">
                </div>
                <div class="col-md-12 d-flex">
                    <label style="margin-right: 10px; margin-bottom: 0;"><i class="fa fa-circle" style="font-size: 6px"></i> Người thực hiện:
                    </label>
                    <input class="input-text" name="user_1">
                </div>
                <div class="col-md-12 d-flex">
                    <label style="margin-right: 10px; margin-bottom: 0;"><i class="fa fa-circle" style="font-size: 6px"></i> Địa chỉ ca làm:
                    </label>
                    <input class="input-text" name="address_user">
                </div>
                <div class="col-md-12 d-flex">
                    <label style="margin-right: 10px; margin-bottom: 0;"><i class="fa fa-circle" style="font-size: 6px"></i> Đại diện gia đình:
                    </label>
                    <input class="input-text" name="dai_dien_gia_dinh">
                </div>
                <div class="col-md-12 d-flex">
                    <label style="margin-right: 10px; margin-bottom: 0;"><i class="fa fa-circle" style="font-size: 6px"></i> Điện thoại :
                    </label>
                    <input class="input-text" name="dien_thoai">
                </div>
                <div class="col-md-12 d-flex">
                    <label style="margin-right: 10px; margin-bottom: 0;"><i class="fa fa-circle" style="font-size: 6px"></i> Người thân thứ 2:
                    </label>
                    <input class="input-text" name="nguoi_than_2">
                </div>
                <div class="col-md-12 d-flex">
                    <label style="margin-right: 10px; margin-bottom: 0;"><i class="fa fa-circle" style="font-size: 6px"></i> Điện thoại :
                    </label>
                    <input class="input-text" name="dien_thoai_2">
                </div>
                <div class="col-md-12 d-flex">
                    <label style="margin-right: 10px; margin-bottom: 0;"><i class="fa fa-circle" style="font-size: 6px"></i> Họ và tên trẻ
                        (nickname)
                        : </label>
                    <input class="input-text" name="ho_va_ten_tre_em">
                </div>
                <div class="col-md-12" style="margin-top: 20px">
                    <p>
                        1. Đặc điểm tính nết (nết ăn, nết ngủ, nết chơi và cảm nhận về mức độ dễ/khó làm
                        quen người chăm sóc mới) </p>
                    <p> 2. Lịch sinh hoạt bao gồm: giờ các bữa ăn của bé: VD: 8h - 150ml sữa, thời gian ngủ… </p>
                    <p>3. Dự kiến các công việc của Cô bảo mẫu </p>

                </div>
                <div class="col-md-12" style="margin-top: 20px">
                    <table class="table" border="1">
                        <thead>
                        <tr>
                            <td class="text-center " >
                                <strong> GIỜ GIẤC</strong>
                            </td>
                            <td class="text-center">
                                <strong>HOẠT ĐỘNG CỦA TRẺ</strong>
                            </td>
                            <td class="text-center">
                                <strong>DỰ KIẾN CÔNG VIỆC</strong>

                            </td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><textarea class="area-input" name="time_1"></textarea></td>
                            <td><textarea class="area-input" name="hoat_dong_1"></textarea></td>
                            <td><textarea class="area-input" name="du_kien_1"></textarea></td>
                        </tr>
                        <tr>
                            <td><textarea class="area-input" name="time_2"></textarea></td>
                            <td><textarea class="area-input" name="hoat_dong_2"></textarea></td>
                            <td><textarea class="area-input" name="du_kien_2"></textarea></td>
                        </tr>
                        <tr>
                            <td><textarea class="area-input" name="time_3"></textarea></td>
                            <td><textarea class="area-input" name="hoat_dong_3"></textarea></td>
                            <td><textarea class="area-input" name="du_kien_3"></textarea></td>
                        </tr>
                        <tr>
                            <td><textarea class="area-input" name="time_4"></textarea></td>
                            <td><textarea class="area-input" name="hoat_dong_4"></textarea></td>
                            <td><textarea class="area-input" name="du_kien_4"></textarea></td>
                        </tr>
                        <tr>
                            <td><textarea class="area-input" name="time_5"></textarea></td>
                            <td><textarea class="area-input" name="hoat_dong_5"></textarea></td>
                            <td><textarea class="area-input" name="du_kien_5"></textarea></td>
                        </tr>
                        <tr>
                            <td><textarea class="area-input" name="time_6"></textarea></td>
                            <td><textarea class="area-input" name="hoat_dong_6"></textarea></td>
                            <td><textarea class="area-input" name="du_kien_6"></textarea></td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="col-md-8 col-md-offset-2 col-sm-offset-0" style="padding: 10px; ">
                <div class="col-md-12 text-center" style="margin-bottom: 10px;margin-top: 50px">
                    <h2 class="text-uppercase">Phiếu dặn dò của Phụ huynh</h2>
                </div>
                <div class="col-md-12 margin-bottom-10">
                    <label style="margin-right: 10px; margin-bottom:10px;">1. Sức khỏe hiện nay của bé có gặp vấn
                        đề gì cần người chăm sóc trẻ lưu ý đặc biệt không ?
                        VD: biểu hiện sốt, hen phế quản, dị ứng…Nếu không có ghi “Không”
                    </label>
                    <label class="width-100" style="display: flex;align-items: start;"><input type="checkbox" class="checkbox" value="Không gặp vấn đề" style="margin-right: 10px" name="cau_hoi_1[]"> Không gặp vấn đề</label>
                    <label class="width-100" style="display: flex;align-items: start;"><input type="checkbox" class="checkbox" value="Ốm sốt, ho, cảm cúm…" style="margin-right: 10px" name="cau_hoi_1[]"> Ốm sốt, ho, cảm cúm…</label>
                    <label class="width-100" style="display: flex;align-items: start;"><input type="checkbox" class="checkbox" value="Các bệnh tiêu hoá" style="margin-right: 10px" name="cau_hoi_1[]"> Các bệnh tiêu hoá</label>
                    <label class="width-100" style="display: flex;align-items: start;"><input type="checkbox" class="checkbox" value="Các bệnh da liễu" style="margin-right: 10px" name="cau_hoi_1[]"> Các bệnh da liễu</label>
                </div>
                <div class="col-md-12 ">
                    <label style="margin-right: 10px; margin-bottom: 10px;"> 2. Loại sữa, cách pha hoặc hâm nóng: </label>
                    <label class="width-100" style="display: flex;align-items: start;"><input type="checkbox" class="checkbox" value="Sữa mẹ trực tiếp"  style="margin-right: 10px" name="cau_hoi_2[]"> Sữa mẹ trực tiếp</label>
                    <label class="width-100" style="display: flex;align-items: start;"><input type="checkbox" class="checkbox" value="Sữa mẹ cấp đông" style="margin-right: 10px" name="cau_hoi_2[]"> Sữa mẹ cấp đông</label>
                    <label class="width-100" style="display: flex;align-items: start;"><input type="checkbox" class="checkbox" value="Sữa công thức" style="margin-right: 10px" name="cau_hoi_2[]"> Sữa công thức</label>
                    <label>Ghi chú:</label>
                    <textarea class="area" name="ghi_chu_2" rows="2"></textarea>
                </div>
                <div class="col-md-12 ">
                    <label style="margin-right: 10px; margin-bottom: 10px;">3. Lưu ý về bữa ăn của trẻ</label>
                    <label class="width-100" style="display: flex;align-items: start;"><input type="checkbox" class="checkbox" value="Có quy tắc bàn ăn" style="margin-right: 10px" name="cau_hoi_3[]"> Có quy tắc bàn ăn</label>
                    <label class="width-100" style="display: flex;align-items: start;"><input type="checkbox" class="checkbox" value="Không" style="margin-right: 10px" name="cau_hoi_3[]"> Không</label>
                    <label class="width-100" style="display: flex;align-items: start;"><input type="checkbox" class="checkbox" value="Ăn dặm" style="margin-right: 10px" name="cau_hoi_3[]"> Ăn dặm</label>
                    <label>Ghi chú:</label>
                    <textarea class="area" name="ghi_chu_3" rows="2"></textarea>
                </div>
                <div class="col-md-12 ">
                    <label style="margin-right: 10px; margin-bottom: 10px;">4. Lưu ý về giờ ngủ của trẻ</label>
                    <label>Ghi chú:</label>
                    <textarea class="area" name="ghi_chu_4" rows="2"></textarea>
                </div>
                <div class="col-md-12 margin-bottom-10">
                    <label style="margin-right: 10px; margin-bottom: 10px;">5. Dặn dò về hoạt động vui chơi của trẻ</label>
                    <label class="width-100" style="display: flex;align-items: start;"><input type="checkbox" value="Cho bé ra ngoài trời chơi. Ghi rõ những khu vực gia đình cho phép" class="checkbox" style="margin-right: 10px" name="cau_hoi_5[]"> Cho bé ra ngoài trời chơi. Ghi rõ những khu vực gia đình cho phép</label>
                    <label class="width-100" style="display: flex;align-items: start;"><input type="checkbox" value="Chỉ hoạt động trong khuôn viên nhà" class="checkbox" style="margin-right: 10px" name="cau_hoi_5[]"> Chỉ hoạt động trong khuôn viên nhà</label>
                </div>
                <div class="col-md-12 ">
                    <label style="margin-right: 10px; margin-bottom: 10px;">6. Dặn dò về toa thuốc cần cho bé dùng</label>  <br>
                    <label><input type="radio" value="Có" name="cau_hoi_6" > Có</label>
                    <label><input type="radio" value="Không" name="cau_hoi_6" style="margin-left: 20px"> Không</label>
                    <br>
                    <label>Ghi chú:</label>
                    <textarea class="area" name="ghi_chu_6" rows="2"></textarea>

                </div>
                <div class="col-md-12 margin-bottom-10">
                    <label  style="margin-right: 10px; margin-bottom: 10px;">7. Chỉ áp dụng với ca 8h, suất ăn của cô do
                        gia đình chuẩn bị hoặc gia đình phụ cấp cho cô tự chuẩn bị suất ăn (30k/ngày).</label>
                    <label><input type="radio" name="cau_hoi_7" value="Gia đình chuẩn bị" > Gia đình chuẩn bị</label>
                    <label><input type="radio" name="cau_hoi_7" value="Cô tự chuẩn bị" style="margin-left: 20px"> Cô tự chuẩn bị</label>
                </div>
                <div class="col-md-12 text-center" style="margin-bottom: 20px">
                    <a class="btn btn-primary btn-submit" href="#"><i class="fa fa-send"></i> Gửi</a>
                </div>
            </div>
        </div>
    </div>
</form>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/global/plugins/respond.min.js"></script>
<script src="<?= Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/global/plugins/excanvas.min.js"></script>
<script src="<?= Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/global/plugins/jquery.min.js"
        type="text/javascript"></script>
<script src="<?= Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/global/plugins/jquery-migrate.min.js"
        type="text/javascript"></script>
<script src="<?= Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/global/plugins/bootstrap/js/bootstrap.min.js"
        type="text/javascript"></script>
<script>
    $(document).ready(function () {
        $(document).on('click', '.btn-submit', function (e) {
            e.preventDefault()
            $.ajax({
                url: '/site/save-khao-sat',
                dataType: 'json',
                type: 'POST',
                data: $("#form-khao-sat").serializeArray(),
                cache: false,
                success: function (data) {
                    if (data) {
                        Swal.fire({
                            title: "Gửi khảo sát thành công",
                            confirmButtonText: "Xác nhận",
                        }).then((result) => {
                            /* Read more about isConfirmed, isDenied below */
                            if (result.isConfirmed) {
                                window.location.href = '/services/thanh-cong';
                            }
                        });
                        setTimeout(function () {
                            window.location.href = '/services/thanh-cong';
                        }, 3000)
                    } else {
                        Swal.fire({
                            title: "Gửi khảo sát không thành công",
                            confirmButtonText: "Xác nhận",
                        }).then((result) => {
                            /* Read more about isConfirmed, isDenied below */
                            if (result.isConfirmed) {
                                window.location.href = '/services/that-bai';
                            }
                        });
                        setTimeout(function () {
                            window.location.href = '/services/that-bai';
                        }, 3000)
                    }
                },
            });
        })
    })
</script>
<!-- END CORE PLUGINS -->
</body>
<!-- END BODY -->
</html>
