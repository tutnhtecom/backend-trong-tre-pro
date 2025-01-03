<?php
    /**@var $cham_soc_khach_hang ChamSocKhachHang[]*/

use backend\models\ChamSocKhachHang;
use yii\bootstrap\Html;

?>
<table class="table table-bordered table-striped cham-soc">
    <thead>
    <tr>
        <th width="1%" class="text-nowrap">
            STT
        </th>
        <th>
            Nội dung hẹn
        </th>
        <th width="1%" class="text-nowrap">
            Lịch hẹn
        </th>
        <th>
            Nội dung chăm sóc
        </th>

        <th width="1%" class="text-nowrap">
            Nhân viên chăm sóc
        </th>

        <th width="1%" class="text-nowrap">
            Lịch chăm sóc
        </th>
    </tr>
    </thead>
    <tbody>
    <?php $index = 0 ?>
    <?php if (count($cham_soc_khach_hang) > 0): ?>
        <?php foreach ($cham_soc_khach_hang as $item): ?>
            <?php $index++ ?>
            <tr>
                <td class="text-center">
                    <?= $index ?>
                </td>
                <td class="">
                    <?= $item->noi_dung_hen ?>
                </td>
                <td class="text-nowrap">
                    <?= isset($item->hen_gio)?date('H:i d/m/Y', strtotime($item->hen_gio)):'' ?>
                </td>
                <td class="text-nowrap">
                    <?= $item->noi_dung_cham_soc ?>
                </td>

                <td class="text-nowrap">
                    <?= isset($item->nhanVienChamSoc->hoten)?$item->nhanVienChamSoc->hoten:'' ?>
                </td>

                <td class="text-nowrap">
                    <?= isset($item->thoi_gian_cham_soc)?date('H:i d/m/Y',strtotime($item->thoi_gian_cham_soc)):'' ?>
                </td>

            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
