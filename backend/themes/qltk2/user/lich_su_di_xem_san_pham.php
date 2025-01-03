<?php
    /** @var $lich_su_di_xem_san_pham[] */
?>

<table class="table table-bordered table-striped view-san-pham">
    <thead>
    <tr>
        <th width="1%" class="text-nowrap">
            STT
        </th>
        <th >
            Tiêu đề
        </th>
        <th class="text-nowrap" width="1%">
            Giá (Tỷ)
        </th>
        <th width="1%" class="text-nowrap">
            Ngày xem
        </th>
        <th width="1%" class="text-nowrap">
            Ghi chú
        </th>
    </tr>
    </thead>
    <tbody>
    <?php $index = 0 ?>
    <?php if (count($lich_su_di_xem_san_pham) > 0): ?>
        <?php foreach ($lich_su_di_xem_san_pham as $item): ?>
            <?php $index++ ?>
            <tr>
                <td class="text-center">
                    <?= $index ?>
                </td>
                <td class="text-left">
                    <strong class="badge badge-primary"> #<?=$item->sanPham->id?></strong>  <?= $item->sanPham->title ?><br/>
                </td>
                <td class=" text-nowrap text-right">
                    <?= $item->sanPham->gia_tu ?>
                </td>
                <td class="text-left text-nowrap">
                  <?= isset($item->ngay_xem)?date('d/m/Y',strtotime($item->ngay_xem)):'' ?><br/>
                </td>
                <td class="text-left text-nowrap">
                    <?= $item->ghi_chu ?><br/>
                </td>

            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

