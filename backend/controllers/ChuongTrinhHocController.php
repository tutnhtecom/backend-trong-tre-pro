<?php namespace backend\controllers;

use backend\models\BaiHoc;
use backend\models\CauHinh;
use backend\models\ChuongTrinhHoc;
use backend\models\DanhMuc;
use backend\models\DichVu;
use backend\models\GiaoCu;
use backend\models\GoiHoc;
use backend\models\KeHoachDay;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\web\HttpException;
use yii\web\UploadedFile;

class ChuongTrinhHocController extends CoreApiController
{
    public function actionDanhSachChuongTrinh()
    {
        $dichVu = DichVu::find()->andFilterWhere(['active' => 1])
            ->select(['id', 'ten_dich_vu', 'image', 'khoa_dich_vu'])
            ->limit($this->limit)->offset(($this->page - 1) * $this->limit)
            ->orderBy(['seq' => SORT_ASC]);
        $count = $dichVu->count();
        $dichVu = $dichVu->all();
        /** @var DichVu $item */
        foreach ($dichVu as $item) {
            $item->ten_dich_vu = "Chương trình " . $item->ten_dich_vu;
            $item->image = CauHinh::getImage($item->image);
        }
        return $this->outputListSuccess2($dichVu, $count);
    }

    public function actionTaoMoi()
    {
        $this->checkField(['bat_chuong_trinh', 'tieu_de', 'dich_vu_id']);
        if ($this->dataPost['dich_vu_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền dich_vu_id");
        }
        $dichVu = DichVu::findOne($this->dataPost['dich_vu_id']);
        if (is_null($dichVu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($this->dataPost['tieu_de'] == "") {
            throw new HttpException(500, "Vui lòng nhập tiêu đề");
        }
        $chuongTrinhHoc = new ChuongTrinhHoc();
        $chuongTrinhHoc->dich_vu_id = $this->dataPost['dich_vu_id'];
        $chuongTrinhHoc->bat_chuong_trinh = $this->dataPost['bat_chuong_trinh'];
        $chuongTrinhHoc->tieu_de = $this->dataPost['tieu_de'];
        $chuongTrinhHoc->user_id = $this->uid;
        $file = UploadedFile::getInstanceByName('image');
        if (!empty($file)) {
            $path = (dirname(dirname(__DIR__))) . '/upload-file';
            $link = date('Y/m/d') . '/' . '_' . \Yii::$app->security->generateRandomString() . $this->get_extension_image($file->type);
            $chuongTrinhHoc->image = $link;
            if (FileHelper::createDirectory($path . '/' . date('Y/m/d') . '/', $mode = 0775, $recursive = true)) {
                $file->saveAs($path . '/' . $link);
            }
        }
        if (!$chuongTrinhHoc->save()) {
            throw new HttpException(500, Html::errorSummary($chuongTrinhHoc));
        }
        return $this->outputSuccess("", "Thêm chương trình học thành công");
    }

    public function actionDanhSach()
    {
        $this->checkGetInput(['dich_vu_id']);
        if ($this->dataGet['dich_vu_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền dich_vu_id");
        }
        $dichVu = DichVu::findOne($this->dataGet['dich_vu_id']);
        if (is_null($dichVu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $chuongTrinhHoc = ChuongTrinhHoc::find()
            ->select(['tieu_de', 'image', 'id'])
            ->andFilterWhere(['active' => 1, 'dich_vu_id' => $this->dataGet['dich_vu_id']]);
        $count = $chuongTrinhHoc->count();
        $chuongTrinhHoc = $chuongTrinhHoc->orderBy(['created' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        if (count($chuongTrinhHoc) > 0) {
            foreach ($chuongTrinhHoc as $item) {
                $item->image = CauHinh::getImage($item->image);
            }
        }
        return $this->outputSuccess($chuongTrinhHoc);
    }

    public function actionXoa()
    {
        $this->checkField(['id']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $chuongTrinhHoc = ChuongTrinhHoc::findOne($this->dataPost['id']);
        if (is_null($chuongTrinhHoc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $chuongTrinhHoc->active = 0;
        if (!$chuongTrinhHoc->save()) {
            throw new HttpException(500, Html::errorSummary($chuongTrinhHoc));
        }
        return $this->outputSuccess("", "Xóa học phần thành công");
    }

    public function actionChiTietChinhSua()
    {
        $this->checkGetInput(['id']);
        if ($this->dataGet['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }

        $chuongTrinhHoc = ChuongTrinhHoc::findOne($this->dataGet['id']);
        if (is_null($chuongTrinhHoc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        return $this->outputSuccess([
            "id" => $chuongTrinhHoc->id,
            "bat_chuong_trinh" => $chuongTrinhHoc->bat_chuong_trinh,
            "tieu_de" => $chuongTrinhHoc->tieu_de,
            "dich_vu_id" => $chuongTrinhHoc->dich_vu_id,
            "dichVu" => $chuongTrinhHoc->dichVu->ten_dich_vu,
            "image" => CauHinh::getImage($chuongTrinhHoc->image)
        ]);
    }

    public function actionSua()
    {
        $this->checkField(['id', 'bat_chuong_trinh', 'tieu_de', 'dich_vu_id']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $chuongTrinhHoc = ChuongTrinhHoc::findOne($this->dataPost['id']);
        if (is_null($chuongTrinhHoc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($this->dataPost['tieu_de'] == "") {
            throw new HttpException(500, "Vui lòng nhập tiêu đề");
        }
        if ($this->dataPost['dich_vu_id'] == "") {
            throw new HttpException(500, "Vui lòng chọn dịch vụ");
        }

        $chuongTrinhHoc->dich_vu_id = $this->dataPost['dich_vu_id'];
        $chuongTrinhHoc->bat_chuong_trinh = $this->dataPost['bat_chuong_trinh'];
        $chuongTrinhHoc->tieu_de = $this->dataPost['tieu_de'];
        $chuongTrinhHoc->user_id = $this->uid;
        $file = UploadedFile::getInstanceByName('image');
        if (!empty($file)) {
            $path = (dirname(dirname(__DIR__))) . '/upload-file';
            $link = date('Y/m/d') . '/' . '_' . \Yii::$app->security->generateRandomString() . $this->get_extension_image($file->type);
            $chuongTrinhHoc->image = $link;
            if (FileHelper::createDirectory($path . '/' . date('Y/m/d') . '/', $mode = 0775, $recursive = true)) {
                $file->saveAs($path . '/' . $link);
            }
        }
        if (!$chuongTrinhHoc->save()) {
            throw new HttpException(500, Html::errorSummary($chuongTrinhHoc));
        }
        return $this->outputSuccess("", "Cập nhật chương trình học thành công");
    }

    public function actionThemGoiHoc()
    {
        $this->checkField(['id', 'goi_hoc']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $chuongTrinhHoc = ChuongTrinhHoc::findOne($this->dataPost['id']);
        if (is_null($chuongTrinhHoc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $danhMucGoiHoc = DanhMuc::findOne(['name' => $this->dataPost['goi_hoc'], 'type' => DanhMuc::GOI_HOC]);
        if (is_null($danhMucGoiHoc)) {
            $danhMucGoiHoc = new DanhMuc();
            $danhMucGoiHoc->name = $this->dataPost['goi_hoc'];
            $danhMucGoiHoc->type = DanhMuc::GOI_HOC;
            if (!$danhMucGoiHoc->save()) {
                throw new HttpException(500, Html::errorSummary($danhMucGoiHoc));
            }
        }
        $goiHoc = is_null($chuongTrinhHoc->goi_hoc) ? [] : json_decode($chuongTrinhHoc->goi_hoc);
        if (!in_array($danhMucGoiHoc->id, $goiHoc)) {
            $goiHoc[] = $danhMucGoiHoc->id;
        } else {
            throw new HttpException(500, $this->dataPost['goi_hoc'] . " đã tồn tại");
        }
        $chuongTrinhHoc->goi_hoc = json_encode($goiHoc);
        if (!$chuongTrinhHoc->save()) {
            throw new HttpException(500, Html::errorSummary($chuongTrinhHoc));
        }
        return $this->outputSuccess("", "Thêm gói học thành công");
    }

    public function actionSuaGoiHoc()
    {
        $this->checkField(['id', 'goi_hoc', 'goi_hoc_id']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $chuongTrinhHoc = ChuongTrinhHoc::findOne($this->dataPost['id']);
        if (is_null($chuongTrinhHoc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $danhMucGoiHoc = DanhMuc::findOne(['name' => $this->dataPost['goi_hoc'], 'type' => DanhMuc::GOI_HOC]);
        if (is_null($danhMucGoiHoc)) {
            $danhMucGoiHoc = new DanhMuc();
            $danhMucGoiHoc->name = $this->dataPost['goi_hoc'];
            $danhMucGoiHoc->type = DanhMuc::GOI_HOC;
            if (!$danhMucGoiHoc->save()) {
                throw new HttpException(500, Html::errorSummary($danhMucGoiHoc));
            }
        }
        $goiHoc = is_null($chuongTrinhHoc->goi_hoc) ? [] : json_decode($chuongTrinhHoc->goi_hoc);
        foreach ($goiHoc as $index => $item) {
            if ($item == $this->dataPost['goi_hoc_id']) {
                unset($goiHoc[$index]);
            }
        }
        if (!in_array($danhMucGoiHoc->id, $goiHoc)) {
            $goiHoc[] = $danhMucGoiHoc->id;
        } else {
            throw new HttpException(500, $this->dataPost['goi_hoc'] . " đã tồn tại");
        }
        sort($goiHoc, SORT_DESC);
        $chuongTrinhHoc->goi_hoc = json_encode($goiHoc);
        if (!$chuongTrinhHoc->save()) {
            throw new HttpException(500, Html::errorSummary($chuongTrinhHoc));
        } else {
            GoiHoc::updateAll(['nhom_id' => $danhMucGoiHoc->id], ['nhom_id' => $this->dataPost['goi_hoc_id'], 'chuong_trinh_id' => $chuongTrinhHoc->id]);
        }
        return $this->outputSuccess("", "Sửa gói học thành công");
    }

    public function actionXoaGoiHoc()
    {
        $this->checkField(['id', 'goi_hoc_id']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $chuongTrinhHoc = ChuongTrinhHoc::findOne($this->dataPost['id']);
        if (is_null($chuongTrinhHoc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $goiHoc = is_null($chuongTrinhHoc->goi_hoc) ? [] : json_decode($chuongTrinhHoc->goi_hoc);
        foreach ($goiHoc as $index => $item) {
            if ($item == $this->dataPost['goi_hoc_id']) {
                unset($goiHoc[$index]);
            }
        }
        sort($goiHoc, SORT_DESC);
        $chuongTrinhHoc->goi_hoc = json_encode($goiHoc);
        if (!$chuongTrinhHoc->save()) {
            throw new HttpException(500, Html::errorSummary($chuongTrinhHoc));
        }
        return $this->outputSuccess("", "Xóa gói học thành công");
    }

    public function actionXemGoiHoc()
    {
        $this->checkGetInput(['goi_hoc_id']);
        if ($this->dataGet['goi_hoc_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền goi_hoc_id");
        }
        $goiHoc = DanhMuc::findOne(['id' => $this->dataGet['goi_hoc_id'], 'type' => DanhMuc::GOI_HOC]);
        if (is_null($goiHoc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        return $this->outputSuccess([
            'id' => $goiHoc->id,
            'name' => $goiHoc->name,
        ]);
    }

    public function actionDanhSachGoiHoc()
    {
        $this->checkGetInput(['id']);
        if ($this->dataGet['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $chuongTrinhHoc = ChuongTrinhHoc::findOne($this->dataGet['id']);
        if (is_null($chuongTrinhHoc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if (is_null($chuongTrinhHoc->goi_hoc)) {
            return $this->outputListSuccess2([], 0);
        }
        $goiHoc = json_decode($chuongTrinhHoc->goi_hoc);
        if (is_array($goiHoc))
            if (count($goiHoc) == 0) {
                return $this->outputListSuccess2([], 0);
            }
        $goiHocs = DanhMuc::find()
            ->select(['name', 'id'])
            ->andFilterWhere(['active' => 1, 'type' => DanhMuc::GOI_HOC])->andFilterWhere(['in', 'id', $goiHoc]);
        $count = $goiHocs->count();
        $data = [];
        $goiHocs = $goiHocs->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy(['name' => SORT_ASC])->all();
        if (count($goiHocs) > 0) {
            foreach ($goiHocs as $item) {
                $data [] = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'baiHoc' => GoiHoc::find()->select(['id', 'tieu_de'])
                        ->andFilterWhere(['active' => 1, 'nhom_id' => $item->id, 'chuong_trinh_id' => $chuongTrinhHoc->id])->orderBy(['tieu_de' => SORT_ASC])->all()
                ];
            }
        }
        return $this->outputListSuccess2($data, $count);
    }


    public function actionThemBaiHoc()
    {
        $this->checkField(['id', 'nhom_id', 'tieu_de']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $chuongTrinhHoc = ChuongTrinhHoc::findOne($this->dataPost['id']);
        if (is_null($chuongTrinhHoc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($this->dataPost['nhom_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền nhom_id");
        }
        $nhom = DanhMuc::findOne($this->dataPost['nhom_id']);
        if (is_null($nhom)) {
            throw new HttpException(400, "Không xác định gói");
        }
        $goiHoc = GoiHoc::findOne(['tieu_de' => $this->dataPost['tieu_de'], 'nhom_id' => $nhom->id, 'active' => 1, 'chuong_trinh_id' => $chuongTrinhHoc->id]);
        if (!is_null($goiHoc)) {
            throw new HttpException(500, "Mã " . $this->dataPost['tieu_de'] . " đã tồn tại");
        }
        $goiHoc = new GoiHoc();
        $goiHoc->nhom_id = $nhom->id;
        $goiHoc->tieu_de = $this->dataPost['tieu_de'];
        $goiHoc->chuong_trinh_id = $this->dataPost['id'];
        $goiHoc->user_id = $this->uid;
        if (!$goiHoc->save()) {
            throw new HttpException(500, Html::errorSummary($goiHoc));
        }
        return $this->outputSuccess("", "Thêm bài học thành công");
    }

    public function actionSuaBaiHoc()
    {
        $this->checkField(['id', 'nhom_id', 'tieu_de', 'bai_hoc_id']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $chuongTrinhHoc = ChuongTrinhHoc::findOne($this->dataPost['id']);
        if (is_null($chuongTrinhHoc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($this->dataPost['nhom_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền nhom_id");
        }
        $nhom = DanhMuc::findOne($this->dataPost['nhom_id']);
        if (is_null($nhom)) {
            throw new HttpException(400, "Không xác định gói");
        }
        $goiHoc = GoiHoc::find()->andFilterWhere(['<>', 'id', $this->dataPost['bai_hoc_id']])->andFilterWhere(['tieu_de' => $this->dataPost['tieu_de'], 'nhom_id' => $nhom->id, 'active' => 1, 'chuong_trinh_id' => $chuongTrinhHoc->id])->one();
        if (!is_null($goiHoc)) {
            throw new HttpException(500, "Mã " . $this->dataPost['tieu_de'] . " đã tồn tại");
        }
        $goiHoc = GoiHoc::findOne($this->dataPost['bai_hoc_id']);
        $goiHoc->nhom_id = $nhom->id;
        $goiHoc->tieu_de = $this->dataPost['tieu_de'];
        $goiHoc->chuong_trinh_id = $this->dataPost['id'];
        $goiHoc->user_id = $this->uid;
        if (!$goiHoc->save()) {
            throw new HttpException(500, Html::errorSummary($goiHoc));
        }
        return $this->outputSuccess("", "Sửa bài học thành công");
    }

    public function actionChiTietBaiHoc()
    {
        $this->checkGetInput(['bai_hoc_id']);
        if ($this->dataGet['bai_hoc_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền bai_hoc_id");
        }
        $baiHoc = GoiHoc::findOne($this->dataGet['bai_hoc_id']);
        if (is_null($baiHoc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $giaoCu = $baiHoc->getGiaoCu();
        return $this->outputSuccess([
            'id' => $baiHoc->id,
            'tieu_de' => $baiHoc->tieu_de,
            'giaoCu' => $giaoCu,
            'nhom_id' => $baiHoc->nhom_id,
        ]);
    }

    public function actionThemBuoiHoc()
    {
        $this->checkField(['bai_hoc_id', 'buoi', 'noi_dung']);
        if ($this->dataPost['bai_hoc_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền bai_hoc_id");
        }
        $baiHoc = GoiHoc::findOne($this->dataPost['bai_hoc_id']);
        if (is_null($baiHoc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $keHoachDay = KeHoachDay::findOne(['active' => 1, 'buoi' => $this->dataPost['buoi'], 'goi_hoc_id' => $this->dataPost['bai_hoc_id']]);
        if (!is_null($keHoachDay)) {
            throw new HttpException(500, "Buổi học " . $this->dataPost['buoi'] . " đã tồn tại");
        }
        $keHoachDay = new KeHoachDay();
        $keHoachDay->buoi = $this->dataPost['buoi'];
        $keHoachDay->noi_dung = $this->dataPost['noi_dung'];
        $keHoachDay->goi_hoc_id = $this->dataPost['bai_hoc_id'];
        $keHoachDay->user_id = $this->uid;
        if (!$keHoachDay->save()) {
            throw new HttpException(500, Html::errorSummary($keHoachDay));
        }
        return $this->outputSuccess("", "Thêm buổi học thành công");
    }

    public function actionDanhSachBuoiHoc()
    {
        $this->checkGetInput(['bai_hoc_id']);
        if ($this->dataGet['bai_hoc_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền bai_hoc_id");
        }
        $baiHoc = GoiHoc::findOne($this->dataGet['bai_hoc_id']);
        if (is_null($baiHoc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $buoiHoc = KeHoachDay::find()
            ->select(['buoi', 'id', 'noi_dung'])
            ->andFilterWhere(['active' => 1, 'goi_hoc_id' => $this->dataGet['bai_hoc_id']]);
        $count = $buoiHoc->count();
        $buoiHoc = $buoiHoc->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy(['buoi' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();

        return $this->outputListSuccess2($buoiHoc, $count);
    }

    public function actionXoaBaiHoc()
    {
        $this->checkField(['bai_hoc_id']);
        if ($this->dataPost['bai_hoc_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền bai_hoc_id");
        }
        $baiHoc = GoiHoc::findOne($this->dataPost['bai_hoc_id']);
        if (is_null($baiHoc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $baiHoc->active = 0;
        if (!$baiHoc->save()) {
            throw new HttpException(500, Html::errorSummary($baiHoc));
        }
        return $this->outputSuccess("", "Xóa bài học thành công");
    }

    public function actionXoaBuoiHoc()
    {
        $this->checkField(['buoi_hoc_id']);
        if ($this->dataPost['buoi_hoc_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền buoi_hoc_id");
        }
        $buoiHoc = KeHoachDay::findOne($this->dataPost['buoi_hoc_id']);
        if (is_null($buoiHoc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $buoiHoc->active = 0;
        if (!$buoiHoc->save()) {
            throw new HttpException(500, Html::errorSummary($buoiHoc));
        }
        return $this->outputSuccess("", "Xóa buổi học thành công");
    }

    public function actionSuaBuoiHoc()
    {
        $this->checkField(['buoi_hoc_id', 'buoi', 'noi_dung']);
        if ($this->dataPost['buoi_hoc_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền buoi_hoc_id");
        }
        $buoiHoc = KeHoachDay::findOne($this->dataPost['buoi_hoc_id']);
        if (is_null($buoiHoc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $buoiHoc->buoi = $this->dataPost['buoi'];
        $buoiHoc->noi_dung = $this->dataPost['noi_dung'];
        if (!$buoiHoc->save()) {
            throw new HttpException(500, Html::errorSummary($buoiHoc));
        }
        return $this->outputSuccess("", "Sửa buổi học thành công");
    }

    public function actionXemBuoiHoc()
    {
        $this->checkGetInput(['buoi_hoc_id']);
        if ($this->dataGet['buoi_hoc_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền buoi_hoc_id");
        }
        $buoiHoc = KeHoachDay::findOne($this->dataGet['buoi_hoc_id']);
        if (is_null($buoiHoc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        return $this->outputSuccess([
            'id' => $buoiHoc->id,
            'buoi' => $buoiHoc->buoi,
            'noi_dung' => $buoiHoc->noi_dung,
        ]);
    }

    public function actionGanGiaoCu()
    {
        $this->checkField(['bai_hoc_id', 'giao_cu_id']);
        if ($this->dataPost['bai_hoc_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền bai_hoc_id");
        }
        $baiHoc = GoiHoc::findOne($this->dataPost['bai_hoc_id']);
        if (is_null($baiHoc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($this->dataPost['giao_cu_id'] == "") {
            throw new HttpException(500, "Vui lòng chọn giáo cụ");
        }
        $giaoCuIDs = explode(',', $this->dataPost['giao_cu_id']);
        if (count($giaoCuIDs) > 0) {
            foreach ($giaoCuIDs as $giaoCuID) {
                $giaoCu = GiaoCu::findOne($giaoCuID);
                if (is_null($giaoCu)) {
                    throw new HttpException(403, "Danh sách giáo cụ không hợp lệ");
                }
            }
        }
        $baiHoc->giao_cu_id = join(',', array_filter($giaoCuIDs));
        if (!$baiHoc->save()) {
            throw new HttpException(500, Html::errorSummary($baiHoc));
        }
        return $this->outputSuccess("", "Gán giáo cụ thành công");
    }

    public function actionXoaGiaoCu()
    {
        $this->checkField(['bai_hoc_id']);
        if ($this->dataPost['bai_hoc_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền bai_hoc_id");
        }
        $baiHoc = GoiHoc::findOne($this->dataPost['bai_hoc_id']);
        if (is_null($baiHoc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $baiHoc->giao_cu_id = null;
        if (!$baiHoc->save()) {
            throw new HttpException(500, Html::errorSummary($baiHoc));
        }
        return $this->outputSuccess("", "Xóa giáo cụ thành công");
    }

}
