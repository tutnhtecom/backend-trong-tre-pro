<?php

use yii\bootstrap\Html;

/** @var  $nhu_cau_khach_hang [] */
/** @var  $khach_hang */
?>
<div class="nhu-cau">
    <h4 class="text-primary">NHU CẦU KHÁCH HÀNG</h4>
    <table class="table table-bordered ">
        <thead>
        <tr>
            <th colspan="6">
                <?= Html::a('<i class="fa fa-plus"></i> Tìm sản phẩm', '', ['class' => 'btn btn-primary btn-them-nhu-cau', 'data-value' => $khach_hang->id]) ?>
            </th>
        </tr>
        <tr>
            <th width="1%" class="text-nowrap">
                STT
            </th>
            <th width="1%" class="text-nowrap">
                Giá
            </th>
            <th width="1%" class="text-nowrap">
                Diện tích
            </th>
            <th>
                Địa chỉ
            </th>
            <th width="1%" class="">
                Hướng
            </th>
            <th width="1%" class="text-nowrap">
                Ngày cập nhật
            </th>

        </tr>
        </thead>
        <tbody>
        <?php $index = 0 ?>
        <?php if (count($nhu_cau_khach_hang) > 0): ?>
            <?php foreach ($nhu_cau_khach_hang as $item): ?>
                <?php $index++ ?>
                <tr>
                    <td class="text-center">
                        <?= $index ?>
                    </td>
                    <td class="text-right">
                        <?= $item->gia ?>
                    </td>
                    <td class="text-right">
                        <?= $item->dien_tich ?>
                    </td>
                    <td>
                        <?= isset($item->duong_pho)?$item->duong_pho.',':'' ?> <?= isset($item->phuong_xa)?$item->phuong_xa.',':'' ?> <?= isset($item->quan_huyen)?$item->quan_huyen.',':'' ?>, <?= isset($item->thanh_pho)?$item->thanh_pho.',':'' ?>
                    </td>
                    <td class="text-right">
                        <?= $item->nhu_cau_ve_huong ?>
                    </td>
                    <td class="text-center">
                        <?= $item->created ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>