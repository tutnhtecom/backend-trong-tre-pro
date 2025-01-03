<div class="table-responsive margin-top-20">
    <table class="table table-bordered table-striped text-nowrap">
        <thead>
        <tr>
            <th class="text-center">Mã công việc</th>
            <th class="text-center">Mã phân quyền</th>
            <th class="text-center">Công việc/Nhiệm vụ</th>
            <th class="text-center">Tần suất thực hiện</th>
            <th class="text-center">Yêu cầu kết quả</th>
            <th class="text-center">Quy trình công việc liên quan</th>
            <th class="text-right" width="3%">Điểm số</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($congviec as $item):?>
            <tr class="bg bg-primary">
                <td><?=$item->ma_cong_viec_chinh?></td>
                <td></td>
                <td class="td-cong-viec" colspan="5">
                    <?=$item->cong_viec; ?>
                </td>
            </tr>
            <?php if(isset($nhiemVu[$item->id])): ?>
                <?php /** @var $nhiem_vu \backend\models\CongViecNhiemVu */?>
                <?php foreach ($nhiemVu[$item->id] as $nhiem_vu):?>
                    <tr>
                        <td><?=$nhiem_vu->ma_cong_viec?></td>
                        <td><?=$nhiem_vu->ma_phan_quyen?></td>
                        <td><?=wordwrap($nhiem_vu->nhiem_vu,50, '<br/>');?></td>
                        <td><?=wordwrap($nhiem_vu->tan_suat_thuc_hien_cong_viec, 50, '<br/>')?></td>
                        <td><?=wordwrap($nhiem_vu->yeu_cau_ket_qua_id != '' ? $nhiem_vu->yeuCauKetQua->name: "", 50, '<br/>'); ?></td>
                        <td class="text-center"><?=$nhiem_vu->quy_trinh_cong_viec_lien_quan?></td>
                        <td class="text-right"><?=$nhiem_vu->diem_so?></td>
                    </tr>
                <?php endforeach;?>
            <?php endif;?>
        <?php endforeach;  ?>
        </tbody>
    </table>
</div>