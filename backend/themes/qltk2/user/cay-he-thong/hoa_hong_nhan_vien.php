<?php
/**
 * @var $giaoDich \backend\models\GiaoDich[]
 */
?>
<table class="table table-bordered table-striped text-nowrap">
    <thead>
    <tr>
        <th width="1%">STT</th>
        <th>Ngày</th>
        <th>Người thực hiện</th>
        <th>Sản phẩm</th>
        <th>Số tiền</th>
        <th>Tỉ lệ</th>
    </tr>
    </thead>
    <tbody>
    <?php if(count($giaoDich) == 0): ?>
    <tr>
        <td colspan="6">
            <p class="alert alert-warning">Vui lòng chọn 1 thành viên để xem thông tin chi tiết</p>
        </td>
    </tr>
    <?php else: ?>
    <?php foreach ($giaoDich as $index => $item): ?>
        <tr>
            <td><?= $index + 1 ?></td>
            <td><?= date('Y-m-d', strtotime($item->created)) ?></td>
            <td><?= $item->nguoiThucHienGiaoDich->hoten ?></td>
            <td><?= $item->sanPham->tieu_de ?></td>
            <td><?= $item->so_tien ?></td>
            <td><?= $item->ti_le_phan_cap ?></td>
        </tr>
    <?php endforeach; ?>
    <?php endif; ?>
    <tbody>
</table>
