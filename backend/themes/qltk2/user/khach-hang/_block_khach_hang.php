<?php
/** @var $model \backend\models\QuanLyKhachHang */

use backend\models\VaiTro;
use common\models\myAPI;
use common\models\User;
use yii\bootstrap\Html; ?>
<div class="portlet" data-value="<?= $model->id ?>" data-nhu-cau="<?= $model->so_san_pham_theo_nhu_cau ?>"
     data-da-xem="<?= $model->so_san_pham_da_xem ?>">
    <div class="panel panel-default portlet-header" id="block-san-pham-<?= $model->id ?>">
        <div class="panel-heading p-5 ">
            <div class="panel-title">
            <span class="move-block text-muted portlet-footer">
                 <a class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <span class="move-block text-muted">
                            <i class="fa fa-arrows-alt" aria-hidden="true"></i>
                        </span>
                    <span class="badge-success badge-custom badge">#<?= $model->id; ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-default">
                    <li>
                        <?= Html::a('<i class="fa fa-eye text-warning"></i> Xem chi tiết', '', ['class' => 'btn-xem-chi-tiet', 'data-value' => $model->id]) ?>
                    </li>
                       <?php if (User::hasVaiTro(VaiTro::TRUONG_PHONG)
                           ||User::hasVaiTro(VaiTro::GIAM_DOC)
                           ||$model->nhan_vien_sale_id==Yii::$app->user->id
                           ||User::hasVaiTro(VaiTro::QUAN_LY_CONG_TAC_VIEN)): ?>
                    <li>
                        <?= Html::a('<i class="fa fa-search "></i> Tìm sản phẩm', '', ['class' => 'btn-them-nhu-cau', 'data-value' => $model->id]) ?>
                    </li>
                    <li>
                        <?= Html::a('<i class="fa fa-user"></i> Chăm sóc khách hàng', '', ['class' => 'btn-cham-soc-khach-hang', 'data-value' => $model->id]) ?>
                    </li>

                    <li>
                        <?= Html::a('<i class="fa fa-eye"></i> Đi xem sản phẩm', '', ['class' => 'btn-di-xem-san-pham', 'data-value' => $model->id,'data-nhu-cau'=>$model->so_san_pham_theo_nhu_cau]) ?>
                    </li>

                    <?php if ($model->type_khach_hang == User::KHACH_HANG_GIAO_DICH && $model->type_giao_dich == User::DAT_COC): ?>
                        <li>
                            <?= Html::a('<i class="fa fa-level-down"></i> Bỏ cọc', '', ['class' => 'btn-bo-coc', 'data-value' => $model->id]) ?>
                        </li>

                    <?php endif; ?>
                    <li>
                        <?= Html::a('<i class="fa fa-pencil"></i> Sửa khách hàng', '', ['class' => 'btn-sua-khach-hang', 'data-value' => $model->id]) ?>
                    </li>
                     <li>
                        <?= Html::a('<i class="fa fa-pencil"></i> Sửa nhu cầu', '', ['class' => 'btn-sua-nhu-cau-khach-hang', 'data-value' => $model->id]) ?>
                    </li>
                    <li>
                        <?= Html::a('<i class="fa fa-cloud-download"></i> Ẩn', '#', ['class' => 'btn-close', 'data-value' => $model->id]) ?>
                    </li>
                    <li>
                        <?= Html::a('<i class="fa fa-trash"></i> Xóa ', '#', ['class' => 'btn-xoa', 'data-value' => $model->id]) ?>
                    </li>
                    <?php endif;?>
                </ul>
                    <div class="logo ">
                   <img data-toggle="tooltip" data-placement="top" title="<?= $model->name ?>"
                        width="15px" alt="" class="img-circle"
                        src="<?= Yii::$app->request->baseUrl ?>/images/<?= $model->icon ?>"/>
                   <img data-toggle="tooltip" data-placement="top"
                        title="<?= $model->nhan_vien_sale ?>" width="15px" alt=""
                        class="img-circle"
                        src="<?= Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/admin/layout/img/avatar3_small.jpg"/>
                </div>
            </span>
            </div>
        </div>
        <div class="panel-body p-5">
            <i class="fa fa-user-o text-warning"></i> <b><i><?=$model->hoten?></i></b><br/>
            <i class="fa fa-phone text-warning"></i>
                <i>
                    <?php
                    if(User::hasVaiTro(VaiTro::TRUONG_PHONG)
                        ||User::hasVaiTro(VaiTro::GIAM_DOC)
                        ||User::hasVaiTro(VaiTro::QUAN_LY_CONG_TAC_VIEN)
                        || $model->nhan_vien_sale_id==Yii::$app->user->id){
                        echo $model->dien_thoai;
                    }else{
                        echo  '******'.substr($model->dien_thoai, strlen($model->dien_thoai) - 4, strlen($model->dien_thoai));
                    }

                    ?>
                </i>
            <br/>
            <?php if (myAPI::isHasRole(User::GIAM_DOC)
                ||myAPI::isHasRole(VaiTro::QUAN_LY_CONG_TAC_VIEN)
            ||User::hasVaiTro(VaiTro::TRUONG_PHONG)
            ||User::hasVaiTro(VaiTro::QUAN_LY_CHI_NHANH)): ?>
                <i class="fa fa-user-o text-warning"></i>
                <i> <?= isset($model->nhan_vien_sale) ? $model->nhan_vien_sale : '<i class="text-muted font-9"> Đang cập nhật</i>' ?></i>
            <?php endif; ?>
            <br/>
            <?php if (myAPI::isHasRole(User::GIAM_DOC)): ?>
                <i class="fa fa-map-pin text-warning"></i>
                <i> <?= isset($model->ten_chi_nhanh) ? $model->ten_chi_nhanh : '<i class="text-muted font-9"> Đang cập nhật</i>' ?></i>
            <?php endif; ?>
        </div>
        <div class="panel-footer portlet-footer  p-5">
            <span>
                <?php if ($model->so_san_pham_theo_nhu_cau > 0): ?>
                    <i class="fa fa-check-circle text-success"></i>
                <?php elseif ($model->so_san_pham_theo_nhu_cau == 0): ?>
                    <i class=" fa fa-close text-danger"></i>
                <?php endif; ?>
                <?= ($model->type_khach_hang != User::KHACH_HANG_CO_NHU_CAU ? $model->so_san_pham_da_xem . '/' : '') . $model->so_san_pham_theo_nhu_cau ?> SP
            </span>
            <?php if ($model->type_khach_hang == User::KHACH_HANG_TIEM_NANG): ?>
                <span data-toggle="tooltip" title="Mức <?= $model->muc_do_tiem_nang ?>">
                    <i class="fa fa-check-circle"
                       style="color: <?= $model->muc_do_tiem_nang != '' ? User::arr_tiem_nang[$model->muc_do_tiem_nang] : 'black' ?>"></i>
                </span>
            <?php endif; ?>
            <?php if ($model->type_khach_hang == User::KHACH_HANG_DA_XEM): ?>
                <span class="text-success" data-toggle="tooltip" title="Đã xem lần <?= $model->lan_xem ?>">
                    <i class="fa fa-eye "></i> <?= $model->lan_xem ?>
                </span>
            <?php endif; ?>
        </div>
    </div>

</div>
