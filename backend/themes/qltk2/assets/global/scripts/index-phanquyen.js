/**
 * Created by hungluong on 6/28/18.
 */
$(document).ready(function () {
    $(document).on('change', "#nhom-chuc-nang", function () {
        $.ajax({
            url: '/phan-quyen/getphanquyen',
            type: 'post',
            data: $("#form-phanquyen").serializeArray(),
            dataType: 'json',
            beforeSend: function () {
                Metronic.blockUI();
            },
            success: function (data) {
                $("#table-phan-quyen").html(data.table_phanquyen);
            },
            complete: function () {
                Metronic.unblockUI();
            }
        })
    });

    $(document).on('click', '.btn-luu-phan-quyen', function (e) {
        $.ajax({
            url: '/phan-quyen/save',
            type: 'post',
            data: $("#form-phanquyen").serializeArray(),
            dataType: 'json',
            beforeSend: function () {
                Metronic.blockUI();
            },
            success: function (data) {
                $('.thongbao').html(data.message);
            },
            complete: function () {
                Metronic.unblockUI();
            }
        })
    })
});