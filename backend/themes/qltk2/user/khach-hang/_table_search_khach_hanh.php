<?php
/**@var $khach_hang \backend\models\QuanLyKhachHang[]*/
/** @var $metaPage */
/** @var $perPage */
use backend\models\ChamSocKhachHang;
use backend\models\VaiTro;
use common\models\User;
use yii\bootstrap\Html;

?>
<div class="table-responsive">
    <table class="table table-bordered table-striped khach-hang ">
        <thead>
        <tr>
            <th width="1%" class="text-nowrap">
                STT
            </th>
            <th>
                Họ tên
            </th>
            <th class="text-nowrap" width="1%">
                Điện thoại
            </th>
            <th class="text-nowrap" width="1%">
                Nhân viên phụ trách
            </th>
            <th width="1%" class="text-nowrap">
                Chi nhánh
            </th>
            <th width="1%" class="text-nowrap">
                NC Quận huyện
            </th>
            <th width="1%" class="text-nowrap">
                NC Phường xã
            </th>
            <th width="1%" class="text-nowrap">
                NC Đường phố
            </th>
            <th width="1%" class="text-nowrap">
                NC Hướng
            </th>

        </tr>
        </thead>
        <tbody>
        <?php $index = ($perPage-1)*10 ?>
        <?php if (count($khach_hang) > 0): ?>
            <?php foreach ($khach_hang as $item): ?>
                <?php $index++ ?>
                <tr>
                    <td class="text-center">
                        <?= $index ?>
                    </td>
                    <td class="text-nowrap">
                        <strong class="badge badge-primary">#<?= $item->id?></strong> <?=\yii\bootstrap\Html::a($item->hoten,'',['class'=>'btn-xem-chi-tiet-tim-kiem','data-value'=>$item->id])?>
                    </td>
                    <td class="text-nowrap">
                        <?php
                        if(User::hasVaiTro(VaiTro::TRUONG_PHONG)||User::hasVaiTro(VaiTro::GIAM_DOC) || $item->nhan_vien_sale_id==Yii::$app->user->id){
                            echo $item->dien_thoai;
                        }else{
                            echo  '******'.substr($item->dien_thoai, strlen($item->dien_thoai) - 4, strlen($item->dien_thoai));
                        }

                        ?>
                    </td>
                    <td class="text-nowrap">
                        <?= $item->nhan_vien_sale?>
                    </td>
                    <td class="text-nowrap">
                        <?= $item->ten_chi_nhanh?>
                    </td>
                    <td class="text-nowrap">
                        <?= $item->nhu_cau_quan_huyen?>
                    </td>
                    <td class="text-nowrap">
                        <?= $item->nhu_cau_phuong_xa?>
                    </td>
                    <td class="text-nowrap">
                        <?= $item->nhu_cau_duong_pho?>
                    </td>
                    <td class="text-nowrap">
                        <?= $item->nhu_cau_huong?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if($metaPage>1):?>
            <tr >
                <td colspan="11">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <li class="page-item"><a class="page-link btn-pagination-search-khach-hang" data-value="1"  href="#">&laquo;</a></li>
                            <?php for ($i=1;$i<=$metaPage;$i++):?>
                                <li class="page-item"><a class="page-link btn-pagination-search-khach-hang"   href="" data-value="<?=$i?>"><?=$i?></a></li>
                            <?php endfor;?>
                            <li class="page-item"><a class="page-link btn-pagination-search-khach-hang"  data-value ="<?=$metaPage?>" href="#">&raquo;</a></li>
                        </ul>
                    </nav>
                </td>
            </tr>
        <?php endif;?>
        </tbody>
    </table>
</div>
