<?php
/**
 * @var $congviec_nhiemvu \backend\models\CongViecNhiemVu[]
 * @var $nhiemVu \backend\models\CongViecNhiemVu[]
 */
?>
    <h4 class="text-primary">CÔNG VIỆC & NHIỆM VỤ</h4>

<table class="table table-bordered table-striped text-nowrap" id="table-bang-ke-hoac-muc-tieu">

    <thead>
    <tr>
        <th width="3%">Mã công việc</th>
        <th width="3%">Mã phân quyền</th>
        <th class="text-center">Công việc/Nhiệm vụ</th>
        <th class="text-center">Tần suất thực hiện</th>
        <th class="text-center">Yêu cầu kết quả</th>
        <th class="text-center">Quy trình công việc liên quan</th>
        <th class="text-center">Điểm số</th>
        <th width="3%">Thêm</th>
        <th width="3%">Hủy</th>
    </tr>
    </thead>
    <tbody>
    <?php if(count($congviec_nhiemvu) > 0): ?>
        <?php foreach ($congviec_nhiemvu as $item):?>
            <tr class="nhom-cong-viec" data-value="<?=$item->id?>" id="rows-nhom-<?=$item->id?>" data-cong-viec="<?=$item->id;?>">
                <td class="td-ma-cong-viec">
                    <input type="text" value="<?=$item->ma_cong_viec_chinh ?>" class="form-control" name="OldMaCongViecChinh[<?=$item->id?>]">
                </td>
                <td class="td-ma-phan-quyen">
                    <input type="text" value="<?=$item->ma_phan_quyen ?>" class="form-control" name="OldMaPhanQuyen[<?=$item->id?>]">
                </td>
                <td class="td-cong-viec" colspan="5">
                    <input type="hidden" name="index_ke_hoach_nhom[<?=$item->id?>]" class="index_ke_hoach_nhom" value="<?=$item->id?>">
                    <input type="text" value="<?=$item->cong_viec ?>" class="form-control" name="OldCongViec[<?=$item->id?>]">
                </td>
                <td class="text-center">
                </td>
                <td class="text-center">
                    <a href="#" data-value="<?=$item->id ?>" class="btn-delete-kehoach btn-delete-nhom text-danger"><i class="fa fa-minus"></i></a>
                </td>
            </tr>
            <?php /** @var $item_nhiemvu \backend\models\CongViecNhiemVu */ ?>
            <?php foreach ($nhiemVu[$item->id] as $item_nhiemvu): ?>
                <tr class="oldnhom-<?=$item_nhiemvu->id; ?>" data-value="<?=$item_nhiemvu->id ?>" data-index-kehoach="<?=$item_nhiemvu->id?>" data-cong-viec="<?=$item->id?>">
                    <td class="td-ma-cong-viec">
                        <input type="text" value="<?=$item_nhiemvu->ma_cong_viec ?>" class="form-control" name="OldMaCongViec[<?=$item->id?>][<?=$item_nhiemvu->id?>]">
                    </td>
                    <td class="td-ma-phan-quyen">
                        <input type="text" value="<?=$item_nhiemvu->ma_phan_quyen ?>" class="form-control" name="OldMaPhanQuyen[<?=$item->id?>][<?=$item_nhiemvu->id?>]">
                    </td>
                    <td><input type="text" class="form-control" value="<?=$item_nhiemvu->nhiem_vu; ?>" name="OldNhiemVu[<?=$item->id?>][<?=$item_nhiemvu->id?>]"></td>
                    <td>
                        <input type="text" class="form-control" value="<?=$item_nhiemvu->tan_suat_thuc_hien_cong_viec; ?>" name="OldTanSuatThucHien[<?=$item->id?>][<?=$item_nhiemvu->id ?>]">
                    </td>
                    <td><input type="text" class="form-control" value="<?=$item_nhiemvu->yeu_cau_ket_qua_id != '' ? $item_nhiemvu->yeuCauKetQua->name: ''; ?>" name="OldYeuCauKetQua[<?=$item->id?>][<?=$item_nhiemvu->id ?>]"></td>
                    <td><input type="text" class="form-control" value="<?=$item_nhiemvu->quy_trinh_cong_viec_lien_quan; ?>" name="OldQuyTrinhCongViecLienQuan[<?=$item->id?>][<?=$item_nhiemvu->id ?>]"></td>
                    <td><input type="number" class="form-control" value="<?=$item_nhiemvu->diem_so; ?>" name="OldDiemSo[<?=$item->id?>][<?=$item_nhiemvu->id ?>]"></td>
                    <td class="text-center">
                        <a href="#" class="btn-add-kehoach text-primary"><i class="fa fa-plus"></i></a>
                    </td>
                    <td class="text-center">
                        <a href="#" data-value="<?=$item_nhiemvu->id ?>" class="btn-delete-kehoach text-danger"><i class="fa fa-minus"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endforeach;  ?>
    <?php endif; ?>
    </tbody>
    <tfoot>
    <tr>
       <td colspan="9">
           <?=\yii\bootstrap\Html::a('<i class="fa fa-plus-circle"></i> Thêm công việc', '#', ['class' => 'btn btn-them-cong-viec btn-primary btn-sm btn'])?>
           <?=\yii\bootstrap\Html::a('<i class="fa fa-plus-circle"></i> Thêm nhiệm vụ', '#', ['class' => 'btn green btn-sm btn btn-them-nhiem-vu'])?>
       </td>
    </tr>
    </tfoot>
</table>

<?=\yii\bootstrap\Html::a('<i class="fa fa-step-backward"></i> Quay lại danh sách', \yii\helpers\Url::toRoute(['danh-muc/vi-tri-cong-viec']), ['class' => 'btn btn-success pull-right'])?>
<?=\yii\bootstrap\Html::a('<i class="fa fa-save"></i> Lưu lại', '#', ['class' => 'btn btn-primary btn-save-cong-viec'])?>