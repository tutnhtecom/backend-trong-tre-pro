<?php
/** @var $khach_hang */
use yii\bootstrap\Html; ?>
<div class="portlet" id="data<?=$khach_hang->id?>" data-value="<?= $khach_hang->id ?>" style="background:#F0EBE3;" data-san-pham="<?=$khach_hang->so_san_pham_theo_nhu_cau?>" data-da-xem="<?=$khach_hang->so_san_pham_da_xem?>">
    <div class="portlet-header"
         style=" <?= $khach_hang->so_san_pham_theo_nhu_cau != 0 ? 'background:#ededed;color:black' : 'background:#411530;color:white' ?>;">
        <div class="header">
            <a class="dropdown-toggle portlet-footer" data-toggle="dropdown"
               data-hover="dropdown"
               data-close-others="true">
                                        <span>
                                              <?= explode(" ", $khach_hang->hoten)[count(explode(" ", $khach_hang->hoten)) - 1] ?>
                                        <i class="fa fa-caret-down"></i>
                                        </span>
                <div class="logo ">
                    <img data-toggle="tooltip" data-placement="top" title="<?= $khach_hang->name ?>"
                         width="15px" alt="" class="img-circle"
                         src="<?= Yii::$app->request->baseUrl ?>/images/<?= $khach_hang->icon ?>"/>
                    <img data-toggle="tooltip" data-placement="top"
                         title="<?= $khach_hang->nhan_vien_sale ?>" width="15px" alt=""
                         class="img-circle"
                         src="<?= Yii::$app->request->baseUrl ?>/backend/themes/qltk2/assets/admin/layout/img/avatar3_small.jpg"/>
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-default">
                <li>
                    <?= Html::a('<i class="fa fa-eye"></i> Xem chi tiết', '', ['class' => 'btn-xem-chi-tiet', 'data-value' => $khach_hang->id]) ?>
                </li>
                <li>
                    <?= Html::a('<i class="fa fa-search"></i> Tìm sản phẩm', '', ['class' => 'btn-them-nhu-cau', 'data-value' => $khach_hang->id]) ?>
                </li>
                <li>
                    <?= Html::a('<i class="fa fa-user"></i> Chăm sóc khách hàng', '', ['class' => 'btn-cham-soc-khach-hang', 'data-value' => $khach_hang->id]) ?>
                </li>

                <?php if($khach_hang->trang_thai_khach_hang=="Khách hàng giao dịch" && $khach_hang->phan_nhom==1):?>
                    <li>
                        <?= Html::a('<i class="fa fa-level-down"></i> Bỏ cọc', '', ['class' => 'btn-bo-coc', 'data-value' => $khach_hang->id]) ?>
                    </li>

                <?php endif;?>
                <li>
                    <?= Html::a('<i class="fa fa-pencil"></i> Sửa', '', ['class' => 'btn-sua-khach-hang', 'data-value' => $khach_hang->id]) ?>
                </li>
                <li>
                    <?= Html::a('<i class="fa fa-cloud-download"></i> Lưu trữ', '#', ['class' => 'btn-close', 'data-value' => $khach_hang->id]) ?>
                </li>
                <li>
                    <?= Html::a('<i class="fa fa-trash"></i> Xóa ', '#', ['class' => 'btn-xoa', 'data-value' => $khach_hang->id]) ?>
                </li>
            </ul>
        </div>
        <div class="portlet-content">
            <i class="fa fa-phone"></i><span> <?= $khach_hang->dien_thoai ?></span><br/>
            <?php if ($khach_hang->so_san_pham_theo_nhu_cau > 0): ?>
                <i class="fa fa-check-circle text-success"></i>
            <?php elseif ($khach_hang->so_san_pham_theo_nhu_cau == 0): ?>
                <i class=" fa fa-close "></i>
            <?php endif; ?>
            <?= $khach_hang->so_san_pham_theo_nhu_cau ?> SP
        </div>
    </div>

</div>
