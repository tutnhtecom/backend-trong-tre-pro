<?php
/** @var  $content */
/** @var  $header */


?>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-id">
    <div class="modal-dialog modal-full" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><?=$header?></h4>
            </div>
            <div class="modal-body">
                <div class="thongbao"></div>
                <div id="block-form">
                    <?= $content?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng lại</button>
                <button type="button" class="btn btn-primary btn-luu">Lưu lại</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
