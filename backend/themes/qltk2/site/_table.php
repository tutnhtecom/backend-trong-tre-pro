<?php
/** @var $san_pham_da_xem \backend\models\SanPhamDaXem[] */
/** @var $metaPage */
/** @var $perPage */
?>
<table class="table table-bordered da-xem">
    <thead>
    <tr>
        <th width="1%" class="text-nowrap">
            STT
        </th>
        <th width="1%" class="text-nowrap">
            Ngày tạo
        </th>
        <th>
            Tiêu đề
        </th>
        <th width="1%" class="text-nowrap">
            Người thực hiện
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
    <?php $index = ($perPage - 1) * 10 ?>
    <?php if (count($san_pham_da_xem) > 0): ?>
        <?php foreach ($san_pham_da_xem as $item): ?>
            <?php $index++ ?>
            <tr>
                <td class="text-center">
                    <?= $index ?>
                </td>
                <td class="text-left">
                    <?= isset($item->created) ? date('d/m/Y', strtotime($item->created)) : '' ?>
                </td>
                <td class="text-left">
                    <?= $item->sanPham->title ?>
                </td>
                <td class="text-left">
                    <?= $item->user->hoten ?>
                </td>
                <td class="text-center">
                    <?= isset($item->ngay_xem) ? date('d/m/Y', strtotime($item->ngay_xem)) : '' ?>
                </td>
                <td class="text-left">
                    <?= $item->ghi_chu ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if ($metaPage > 1): ?>
            <tr>
                <td colspan="6">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <li class="page-item"><a class="page-link btn-pagination" data-value="1"
                                                     data-khach-hang= <?= $san_pham_da_xem[0]->khach_hang_id ?> href="#">&laquo;</a>
                            </li>
                            <?php for ($i = 1; $i <= $metaPage; $i++): ?>
                                <li class="page-item"><a class="page-link btn-pagination"
                                                         data-khach-hang= <?= $san_pham_da_xem[0]->khach_hang_id ?>  href=""
                                                         data-value="<?= $i ?>"><?= $i ?></a></li>
                            <?php endfor; ?>
                            <li class="page-item"><a class="page-link btn-pagination"
                                                     data-khach-hang= <?= $san_pham_da_xem[0]->khach_hang_id ?>  data-value
                                ="<?= $metaPage ?>" href="#">&raquo;</a></li>
                        </ul>
                    </nav>
                </td>

            </tr>
        <?php endif; ?>
    <?php endif; ?>

    </tbody>
</table>
