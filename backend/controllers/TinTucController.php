<?php namespace backend\controllers;

use backend\models\CauHinh;
use backend\models\TinTuc;
use backend\models\DanhMuc;
use yii\bootstrap\Html;
use yii\web\HttpException;

class TinTucController extends CoreApiController
{
    public function actionDanhSach(){
        $this->checkGetInput(['tuKhoa','type']);
        $tinTuc = TinTuc::find()
            ->select(['tieu_de','noi_dung','id','link','created','anh_dai_dien'])
            ->andFilterWhere(['active'=>1])
        ;
        $types = $this->getDanhMuc(DanhMuc::LOAI_TIN_TUC);
        if ($this->dataGet['tuKhoa']!=""){
            $tinTuc->andFilterWhere(['like','tieu_de',$this->dataGet['tuKhoa']]);
        }
        if ($this->dataGet['type']!=""){
            $tinTuc->andFilterWhere(['type'=>$this->dataGet['type']]);
        }
        $count =$tinTuc->count();
        $tinTuc = $tinTuc->limit($this->limit)->offset(($this->page-1)*$this->limit)->orderBy(['created'=>$this->sort==1?SORT_DESC:SORT_ASC])->all();
        if (count($tinTuc)>0){
            /** @var TinTuc $item */
            foreach ($tinTuc as $item){
                $item->created = date('d/m/Y',strtotime($item->created));
                $item->anh_dai_dien = CauHinh::getImage($item->anh_dai_dien);
            }
        }
        return $this->outputListSuccess2([
            'tinTuc'=>$tinTuc,
            'types'=>$types
        ],$count);
    }

    public function actionTaoMoi()
    {
        $this->checkField([
            'status',
            'tieu_de',
            'type',
            'noi_dung',
            'link',
        ]);
        if ($this->dataPost['status'] == "") {
            throw new HttpException(500, "Vui lòng truyền Ẩn/Hiện bằng 1 (Hiện) hoặc 0 (Ẩn) ");
        }
        if ($this->dataPost['tieu_de'] == "") {
            throw new HttpException(500, 'Vui lòng nhập tiêu đề');
        }
        if ($this->dataPost['type'] == "") {
            throw new HttpException(500, "Vui lòng chọn phân loại");
        }
        if ($this->dataPost['noi_dung'] == "") {
            throw new HttpException(500, 'Vui lòng nhập mô tả bài viết');
        }
        if ($this->dataPost['link'] == "") {
            throw new HttpException(500, 'Vui lòng gán link bài viết');
        }
        $tinTuc = new TinTuc();
        $tinTuc->status = $this->dataPost['status'];
        $tinTuc->tieu_de = $this->dataPost['tieu_de'];
        $tinTuc->type = $this->dataPost['type'];
        $tinTuc->noi_dung = $this->dataPost['noi_dung'];
        $tinTuc->link = $this->dataPost['link'];
        $tinTuc->user_id = $this->uid;
        $image = $this->saveImage();
        if ($image!=""){
            $tinTuc->anh_dai_dien = $image;
        }
        if (!$tinTuc->save()) {
            throw new HttpException(500, Html::errorSummary($tinTuc));
        }
        return $this->outputSuccess("", 'Lưu tin tức thành công');
    }
    public function actionGetLoaiTinTuc(){
        $type = $this->getDanhMuc(DanhMuc::LOAI_TIN_TUC);
        return $this->outputSuccess($type);
    }
    public function actionChiTiet()
    {
        $this->checkGetInput(['id']);
        if ($this->dataGet['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $tinTuc = TinTuc::find()->select([
            'status',
            'tieu_de',
            'type',
            'noi_dung',
            'link',
            'anh_dai_dien',
        ])->andFilterWhere(['id'=>$this->dataGet['id']])->one();
        $tinTuc->anh_dai_dien = CauHinh::getImage($tinTuc->anh_dai_dien);
        if (is_null($tinTuc)) {
            throw new HttpException(403,"Không xác định dữ liệu");
        }
        $types = $this->getDanhMuc(DanhMuc::LOAI_TIN_TUC);
        return $this->outputSuccess([
            'tinTuc' => $tinTuc,
            'types' => $types,
        ]);
    }
    public function actionSua(){
        $this->checkField([
            'id',
            'status',
            'tieu_de',
            'type',
            'noi_dung',
            'link',
        ]);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $tinTuc = TinTuc::findOne($this->dataPost['id']);
        if (is_null($tinTuc)) {
            throw new HttpException(403,"Không xác định dữ liệu");
        }
        if ($this->dataPost['status'] == "") {
            throw new HttpException(500, "Vui lòng truyền Ẩn/Hiện bằng 1 (Hiện) hoặc 0 (Ẩn) ");
        }
        if ($this->dataPost['tieu_de'] == "") {
            throw new HttpException(500, 'Vui lòng nhập tiêu đề');
        }
        if ($this->dataPost['type'] == "") {
            throw new HttpException(500, "Vui lòng chọn phân loại");
        }
        if ($this->dataPost['noi_dung'] == "") {
            throw new HttpException(500, 'Vui lòng nhập mô tả bài viết');
        }
        if ($this->dataPost['link'] == "") {
            throw new HttpException(500, 'Vui lòng gán link bài viết');
        }

        $tinTuc->status = $this->dataPost['status'];
        $tinTuc->tieu_de = $this->dataPost['tieu_de'];
        $tinTuc->type = $this->dataPost['type'];
        $tinTuc->noi_dung = $this->dataPost['noi_dung'];
        $tinTuc->link = $this->dataPost['link'];
        $tinTuc->user_id = $this->uid;
        $image = $this->saveImage();
        if ($image!=""){
            $tinTuc->anh_dai_dien = $image;
        }
        if (!$tinTuc->save()) {
            throw new HttpException(500, Html::errorSummary($tinTuc));
        }
        return $this->outputSuccess("", 'Lưu tin tức thành công');
    }
    public function actionXoa(){
        $this->checkField(['id']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $tinTuc = TinTuc::findOne($this->dataPost['id']);
        if (is_null($tinTuc)) {
            throw new HttpException(403,"Không xác định dữ liệu");
        }
        $tinTuc->updateAttributes(['active'=>0,'updated'=>date('Y-m-d H:i:s')]);
        return $this->outputSuccess("", 'Xóa tin tức thành công');
    }
}
