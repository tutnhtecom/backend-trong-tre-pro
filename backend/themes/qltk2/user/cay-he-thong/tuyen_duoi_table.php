<?php
/**
 * @var $thanhVien \common\models\User[]
 */

use backend\models\QuanLyKhachHangCuaToi;
use backend\models\SanPham;

?>
<table class="table table-bordered table-striped text-nowrap">
    <thead>
    <tr>
        <th width="1%">STT</th>
        <th>Họ tên</th>
        <th>Điện thoại</th>
        <th>SP mới</th>
        <th>SP bán</th>
        <th>SL Khách</th>
    </tr>
    </thead>
    <tbody>
    <?php if(count($thanhVien) == 0): ?>
    <tr>
        <td colspan="6">
            <p class="alert alert-warning">Vui lòng chọn 1 thành viên để xem thông tin chi tiết</p>
        </td>
    </tr>
    <?php else: ?>
    <?php foreach ($thanhVien as $index => $item): ?>
        <tr>
            <td class="text-center"><?= $index + 1 ?></td>
            <td><?= $item->hoten?></td>
            <td><?= $item->dien_thoai?></td>
            <td class="text-right"><?= SanPham::find()->andFilterWhere(['nhan_vien_phu_trach_id' => $item->id])
                    ->andFilterWhere(['trang_thai' => SanPham::DA_DUYET])->count(); ?></td>
            <td class="text-right"><?= SanPham::find()->andFilterWhere(['nhan_vien_ban_id' => $item->id])
                    ->andFilterWhere(['in', 'trang_thai', [SanPham::DA_BAN, SanPham::DA_BAN_MOT_PHAN]])->count(); ?></td>
            <td class="text-right"><?= QuanLyKhachHangCuaToi::find()->andFilterWhere(['user_id' => $item->id])->count(); ?></td>
        </tr>
    <?php endforeach; ?>
    <?php endif; ?>
    <tbody>
</table>
