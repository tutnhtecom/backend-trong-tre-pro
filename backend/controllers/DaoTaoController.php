<?php namespace backend\controllers;

use backend\models\BaiHoc;
use backend\models\BaiKiemTra;
use backend\models\CauHinh;
use backend\models\CauHoi;
use backend\models\DanhMuc;
use backend\models\DonDichVu;
use backend\models\HoanThanhDaoTao;
use backend\models\HocPhan;
use backend\models\KetQuaDaoTao;
use backend\models\KhoaHoc;
use backend\models\QuanLyKetQuaDaoTao;
use backend\models\QuanLyUserVaiTro;
use backend\models\ThongBao;
use common\models\exportExcelBaoCaoDoanhThu;
use common\models\exportExcelKetQuaDaoTao;
use common\models\User;
use Yii;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\web\HttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use function GuzzleHttp\Psr7\str;

class DaoTaoController extends CoreApiController
{
    public function actionTaoMoi()
    {
        $this->checkField(['tieu_de']);
        if ($this->dataPost['tieu_de'] == "") {
            throw new HttpException(500, "Vui lòng nhập tiêu đề");
        }
        $khoaHoc = new KhoaHoc();
        $khoaHoc->tieu_de = $this->dataPost['tieu_de'];
        $khoaHoc->type = KhoaHoc::GIAO_VIEN;
        $file = UploadedFile::getInstanceByName('image');
        if (!empty($file)) {
            $path = (dirname(dirname(__DIR__))) . '/upload-file';
            $link = date('Y/m/d') . '/' . '_' . \Yii::$app->security->generateRandomString() . $this->get_extension_image($file->type);
            $khoaHoc->image = $link;
            if (FileHelper::createDirectory($path . '/' . date('Y/m/d') . '/', $mode = 0775, $recursive = true)) {
                $file->saveAs($path . '/' . $link);
            }
        }
        if (!$khoaHoc->save()) {
            throw new HttpException(500, Html::errorSummary($khoaHoc));
        }
        return $this->outputSuccess("", "Thêm khóa học thành công");
    }
    public function actionSua()
    {
        $this->checkField(['id','tieu_de']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $khoaHoc = KhoaHoc::findOne($this->dataPost['id']);
        if (is_null($khoaHoc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($this->dataPost['tieu_de'] == "") {
            throw new HttpException(500, "Vui lòng nhập tiêu đề");
        }
        $khoaHoc->tieu_de = $this->dataPost['tieu_de'];
        $khoaHoc->type = KhoaHoc::GIAO_VIEN;
        if (!$khoaHoc->save()) {
            throw new HttpException(500, Html::errorSummary($khoaHoc));
        }
        return $this->outputSuccess("", "Sửa khóa học thành công");
    }
    public function actionXoaKhoaHoc()
    {
      $this->checkField(['id']);
      if ($this->dataPost['id'] == "") {
        throw new HttpException(500, "Vui lòng truyền id");
      }
      $khoaHoc = KhoaHoc::findOne($this->dataPost['id']);
      if (is_null($khoaHoc)) {
        throw new HttpException(403, "Không xác định dữ liệu");
      }
      $khoaHoc->active = 0;
      if (!$khoaHoc->save()) {
        throw new HttpException(500, Html::errorSummary($khoaHoc));
      }
      return $this->outputSuccess("", "Xóa khóa học thành công");
    }
    public function actionCapNhatImage()
    {
        $cauHinh = CauHinh::findOne(21);
        $file = UploadedFile::getInstanceByName('image');
        if (!empty($file)) {
            $path = (dirname(dirname(__DIR__))) . '/upload-file';
            $link = date('Y/m/d') . '/' . '_' . \Yii::$app->security->generateRandomString() . $this->get_extension_image($file->type);
            $cauHinh->image = $link;
            if (FileHelper::createDirectory($path . '/' . date('Y/m/d') . '/', $mode = 0775, $recursive = true)) {
                $file->saveAs($path . '/' . $link);
            }
        } else {
            throw new HttpException(400, "Vui lòng chọn ảnh");
        }
        if (!$cauHinh->save()) {
            throw new HttpException(500, Html::errorSummary($cauHinh));
        }
        return $this->outputSuccess("", "Cập nhật ảnh đại diện thành công");
    }

    public function actionXoaImage()
    {
        $cauHinh = CauHinh::findOne(21);
        $cauHinh->image = null;
        if (!$cauHinh->save()) {
            throw new HttpException(500, Html::errorSummary($cauHinh));
        }
        return $this->outputSuccess("", "Xóa ảnh đại diện thành công");
    }

    public function actionDanhSach()
    {
        $this->checkGetInput(['tuKhoa']);
        $khoaHoc = KhoaHoc::find()
            ->select(['tieu_de', 'image', 'id'])
            ->andFilterWhere(['active' => 1, 'type' => KhoaHoc::GIAO_VIEN]);
        if ($this->dataGet['tuKhoa'] != "") {
            $khoaHoc->andFilterWhere(['like', 'tieu_de', $this->dataGet['tuKhoa']]);
        }
        $count = $khoaHoc->count();
        $khoaHoc = $khoaHoc->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy(['created' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        if (count($khoaHoc) > 0) {
            foreach ($khoaHoc as $item) {
                $item->image = CauHinh::getImage($item->image);
            }
        }
        $cauHinh = CauHinh::findOne(21);
        return $this->outputListSuccess2([
            'khoaHoc' => $khoaHoc,
            'image' => CauHinh::getImage($cauHinh->image)
        ], $count);
    }

    public function actionChiTietKhoaHoc()
    {
        $this->checkGetInput(['id']);
        if ($this->dataGet['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $khoaHoc = KhoaHoc::findOne($this->dataGet['id']);
        if (is_null($khoaHoc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $khoaHocCoBan = HocPhan::find()
            ->select(['tieu_de', 'image', 'id'])
            ->andFilterWhere(['active' => 1, 'cap_do_id' => 52, 'khoa_hoc_id' => $this->dataGet['id']]);;
        $khoaHocCoBan = $khoaHocCoBan->all();
        /** @var HocPhan $item */
        foreach ($khoaHocCoBan as $item) {
            $item->image = CauHinh::getImage($item->image);
        }
        $khoaHocNangCao = HocPhan::find()
            ->select(['tieu_de', 'image', 'id'])
            ->andFilterWhere(['active' => 1, 'cap_do_id' => 53,'khoa_hoc_id' => $this->dataGet['id']]);;
        $khoaHocNangCao = $khoaHocNangCao->all();
        /** @var HocPhan $item */
        foreach ($khoaHocNangCao as $item) {
            $item->image = CauHinh::getImage($item->image);
        }
        return $this->outputSuccess([
            'id' => $khoaHoc->id,
            'tieu_de' => $khoaHoc->tieu_de,
            'image' => CauHinh::getImage($khoaHoc->image),
            'coBan' => $khoaHocCoBan,
            'nangCao' => $khoaHocNangCao,
        ]);
    }

    public function actionUpdateImageKhoaHoc()
    {
        $this->checkField(['id']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $khoaHoc = KhoaHoc::findOne($this->dataPost['id']);
        if (is_null($khoaHoc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $file = UploadedFile::getInstanceByName('image');
        if (!empty($file)) {
            $path = (dirname(dirname(__DIR__))) . '/upload-file';
            $link = date('Y/m/d') . '/' . '_' . \Yii::$app->security->generateRandomString() . $this->get_extension_image($file->type);
            $khoaHoc->image = $link;
            if (FileHelper::createDirectory($path . '/' . date('Y/m/d') . '/', $mode = 0775, $recursive = true)) {
                $file->saveAs($path . '/' . $link);
            }
        } else {
            throw new HttpException(400, "Vui lòng chọn ảnh");
        }
        if (!$khoaHoc->save()) {
            throw new HttpException(500, Html::errorSummary($khoaHoc));
        }
        return $this->outputSuccess("", "Cập nhật ảnh đại diện khóa học thành công");
    }

    public function actionDeleteImageKhoaHoc()
    {
        $this->checkField(['id']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $khoaHoc = KhoaHoc::findOne($this->dataPost['id']);
        if (is_null($khoaHoc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $khoaHoc->image = null;
        if (!$khoaHoc->save()) {
            throw new HttpException(500, Html::errorSummary($khoaHoc));
        }
        return $this->outputSuccess("", "Xóa ảnh đại diện khóa học thành công");
    }

    public function actionTaoMoiHocPhan()
    {
        $this->checkField(['id', 'bat_khoa_hoc', 'tieu_de', 'cap_do_id', 'type_id']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $khoaHoc = KhoaHoc::findOne($this->dataPost['id']);
        if (is_null($khoaHoc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($this->dataPost['tieu_de'] == "") {
            throw new HttpException(500, "Vui lòng nhập tiêu đề");
        }
        if ($this->dataPost['cap_do_id'] == "") {
            throw new HttpException(500, "Vui lòng chọn cấp độ");
        }
        $hocPhan = new HocPhan();
        $hocPhan->khoa_hoc_id = $this->dataPost['id'];
        $hocPhan->bat_khoa_hoc = $this->dataPost['bat_khoa_hoc'];
        $hocPhan->tieu_de = $this->dataPost['tieu_de'];
        $hocPhan->cap_do_id = $this->dataPost['cap_do_id'];
        $hocPhan->type_id = $this->dataPost['type_id'];
        $hocPhan->user_id = $this->uid;
        $file = UploadedFile::getInstanceByName('image');
        if (!empty($file)) {
            $path = (dirname(dirname(__DIR__))) . '/upload-file';
            $link = date('Y/m/d') . '/' . '_' . \Yii::$app->security->generateRandomString() . $this->get_extension_image($file->type);
            $hocPhan->image = $link;
            if (FileHelper::createDirectory($path . '/' . date('Y/m/d') . '/', $mode = 0775, $recursive = true)) {
                $file->saveAs($path . '/' . $link);
            }
        }
        if (!$hocPhan->save()) {
            throw new HttpException(500, Html::errorSummary($hocPhan));
        }
        return $this->outputSuccess("", "Thêm học phần thành công");
    }

    public function actionDeleteImageHocPhan()
    {
        $this->checkField(['hoc_phan_id']);
        if ($this->dataPost['hoc_phan_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền hoc_phan_id");
        }
        $hocPhan = HocPhan::findOne($this->dataPost['hoc_phan_id']);
        if (is_null($hocPhan)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $hocPhan->image = null;
        if (!$hocPhan->save()) {
            throw new HttpException(500, Html::errorSummary($hocPhan));
        }
        return $this->outputSuccess("", "Xóa ảnh đại diện học phần thành công");
    }

    public function actionDeleteHocPhan()
    {
        $this->checkField(['hoc_phan_id']);
        if ($this->dataPost['hoc_phan_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền hoc_phan_id");
        }
        $hocPhan = HocPhan::findOne($this->dataPost['hoc_phan_id']);
        if (is_null($hocPhan)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $hocPhan->active = 0;
        if (!$hocPhan->save()) {
            throw new HttpException(500, Html::errorSummary($hocPhan));
        }
        return $this->outputSuccess("", "Xóa học phần thành công");
    }

    public function actionChiTietHocPhan()
    {
        $this->checkGetInput(['hoc_phan_id']);
        if ($this->dataGet['hoc_phan_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền hoc_phan_id");
        }
        $hocPhan = HocPhan::findOne($this->dataGet['hoc_phan_id']);
        if (is_null($hocPhan)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        return $this->outputSuccess([
            'id' => $hocPhan->id,
            'khoaHoc' => $hocPhan->khoaHoc->tieu_de,
            'tieu_de' => $hocPhan->tieu_de,
            'image' => CauHinh::getImage($hocPhan->image),
            'capDo' => $hocPhan->capDo->name,
            'cap_do_id' => $hocPhan->cap_do_id,
            'type' => $hocPhan->type->name,
            'type_id' => $hocPhan->type_id,
            'bat_khoa_hoc' => $hocPhan->bat_khoa_hoc,
        ]);
    }

    public function actionSuaHocPhan()
    {
        $this->checkField(['hoc_phan_id', 'bat_khoa_hoc', 'tieu_de', 'cap_do_id', 'type_id']);
        if ($this->dataPost['hoc_phan_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền hoc_phan_id");
        }
        $hocPhan = HocPhan::findOne($this->dataPost['hoc_phan_id']);
        if (is_null($hocPhan)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($this->dataPost['tieu_de'] == "") {
            throw new HttpException(500, "Vui lòng nhập tiêu đề");
        }
        if ($this->dataPost['cap_do_id'] == "") {
            throw new HttpException(500, "Vui lòng chọn cấp độ");
        }
        $hocPhan->bat_khoa_hoc = $this->dataPost['bat_khoa_hoc'];
        $hocPhan->tieu_de = $this->dataPost['tieu_de'];
        $hocPhan->cap_do_id = $this->dataPost['cap_do_id'];
        $hocPhan->type_id = $this->dataPost['type_id'];
        $hocPhan->user_id = $this->uid;
        $file = UploadedFile::getInstanceByName('image');
        if (!empty($file)) {
            $path = (dirname(dirname(__DIR__))) . '/upload-file';
            $link = date('Y/m/d') . '/' . '_' . \Yii::$app->security->generateRandomString() . $this->get_extension_image($file->type);
            $hocPhan->image = $link;
            if (FileHelper::createDirectory($path . '/' . date('Y/m/d') . '/', $mode = 0775, $recursive = true)) {
                $file->saveAs($path . '/' . $link);
            }
        }
        if (!$hocPhan->save()) {
            throw new HttpException(500, Html::errorSummary($hocPhan));
        }
        return $this->outputSuccess("", "Cập nhật phần thành công");
    }

    public function actionDanhSachBaiHoc()
    {
        $this->checkGetInput(['hoc_phan_id', 'tuKhoa']);
        if ($this->dataGet['hoc_phan_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền hoc_phan_id");
        }
        $hocPhan = HocPhan::findOne($this->dataGet['hoc_phan_id']);
        if (is_null($hocPhan)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $baiHoc = BaiHoc::find()
            ->select(['tieu_de', 'id'])
            ->andFilterWhere(['active' => 1, 'hoc_phan_id' => $this->dataGet['hoc_phan_id']]);
        if ($this->dataGet['tuKhoa'] != "") {
            $baiHoc->andFilterWhere(['like', 'tieu_de', $this->dataGet['tuKhoa']]);
        }
        $count = $baiHoc->count();
        $baiHoc = $baiHoc->limit($this->limit)->offset(($this->page - 1) * $this->limit)
            ->orderBy(['thu_tu'=>SORT_ASC,'created' => $this->sort == 1 ? SORT_DESC : SORT_ASC])
            ->all();
        return $this->outputListSuccess2($baiHoc, $count);
    }

    public function actionTaoMoiBaiHoc()
    {
        $this->checkField(['hoc_phan_id', 'tieu_de', 'cauHoi', 'baiTest']);
        if ($this->dataPost['hoc_phan_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền hoc_phan_id");
        }
        $hocPhan = HocPhan::findOne($this->dataPost['hoc_phan_id']);
        if (is_null($hocPhan)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($this->dataPost['tieu_de'] == "") {
            throw new HttpException(500, "Vui lòng nhập tiêu đề");
        }
        $baiHoc = new BaiHoc();
        $baiHoc->tieu_de = $this->dataPost['tieu_de'];
        $baiHoc->hoc_phan_id = $this->dataPost['hoc_phan_id'];
        $baiHoc->user_id = $this->uid;
        if (isset($this->dataPost['thu_tu'])){
            if (intval($this->dataPost['thu_tu'])>0){
                $baiHoc->thu_tu = intval($this->dataPost['thu_tu']);
            }
        }
        if (!$baiHoc->save()) {
            throw new HttpException(500, Html::errorSummary($baiHoc));
        } else {
            $cauHois = $this->dataPost['cauHoi'];
            if (!is_array($cauHois)) {
                throw new HttpException(500, "Tham số câu hỏi phải là danh sách");
            }
            if (count($cauHois) > 0) {
                foreach ($cauHois as $item) {
                    $cauHoi = new CauHoi();
                    $cauHoi->tieu_de = $item['tieu_de'];
                    $cauHoi->gioi_thieu = $item['gioi_thieu'];
                    $cauHoi->link = $item['link'];
                    $cauHoi->user_id = $this->uid;
                    $cauHoi->bai_hoc_id = $baiHoc->id;
                    if (!$cauHoi->save()) {
                        throw new HttpException(500, Html::errorSummary($cauHoi));
                    }
                }
            }

            $baiTests = $this->dataPost['baiTest'];
            if (!is_array($baiTests)) {
                throw new HttpException(500, "Tham số bài test phải là danh sách");
            }
            if (count($baiTests) > 0) {
                foreach ($baiTests as $item) {
                    $baiTest = new BaiKiemTra();
                    $baiTest->link = $item;
                    $baiTest->bai_hoc_id = $baiHoc->id;
                    $baiTest->user_id = $this->uid;
                    if (!$baiTest->save()) {
                        throw new HttpException(500, Html::errorSummary($baiTest));
                    }
                }
            }
        }
        return $this->outputSuccess("", "Thêm bài học thành công");
    }

    public function actionChiTietBaiHoc()
    {
        $this->checkGetInput(['bai_hoc_id']);
        if ($this->dataGet['bai_hoc_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền bai_hoc_id");
        }
        $baiHoc = BaiHoc::findOne($this->dataGet['bai_hoc_id']);
        if (is_null($baiHoc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }

        return $this->outputSuccess([
            'id' => $baiHoc->id,
            'tieu_de' => $baiHoc->tieu_de,
            'thu_tu' => $baiHoc->thu_tu==9999999?null:$baiHoc->thu_tu,
            'cauHoi' => $baiHoc->cauHoi(),
            'baiTest' => $baiHoc->kiemTra()
        ]);
    }

    public function actionDeleteBaiHoc()
    {
        $this->checkField(['bai_hoc_id']);
        if ($this->dataPost['bai_hoc_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền bai_hoc_id");
        }
        $baiHoc = BaiHoc::findOne($this->dataPost['bai_hoc_id']);
        if (is_null($baiHoc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $baiHoc->active = 0;
        if (!$baiHoc->save()) {
            throw new HttpException(500, Html::errorSummary($baiHoc));
        }
        return $this->outputSuccess("", "Xóa bài học thành công");
    }
    public function actionSuaBaiHoc()
    {
        $this->checkField(['bai_hoc_id','tieu_de']);
        if ($this->dataPost['bai_hoc_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền bai_hoc_id");
        }
        $baiHoc = BaiHoc::findOne($this->dataPost['bai_hoc_id']);
        if (is_null($baiHoc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $baiHoc->tieu_de = $this->dataPost['tieu_de'];
        if (isset($this->dataPost['thu_tu'])){
            if (intval($this->dataPost['thu_tu'])>0){
                $baiHoc->thu_tu = intval($this->dataPost['thu_tu']);
            }
        }
        if (!$baiHoc->save()) {
            throw new HttpException(500, Html::errorSummary($baiHoc));
        }
        return $this->outputSuccess("", "Sửa bài học thành công");
    }
    public function actionThemCauHoiBaiHoc()
    {
        $this->checkField(['bai_hoc_id','tieu_de','gioi_thieu','link']);
        if ($this->dataPost['bai_hoc_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền bai_hoc_id");
        }
        $baiHoc = BaiHoc::findOne($this->dataPost['bai_hoc_id']);
        if (is_null($baiHoc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
      if ($this->dataPost['gioi_thieu'] == "") {
        throw new HttpException(500, "Vui lòng nhập giới thiệu câu hỏi");
      }
        $cauHoi = new CauHoi();
        $cauHoi->tieu_de = $this->dataPost['tieu_de'];
        $cauHoi->gioi_thieu = $this->dataPost['gioi_thieu'];
        $cauHoi->link = $this->dataPost['link'];
        $cauHoi->user_id = $this->uid;
        $cauHoi->bai_hoc_id = $baiHoc->id;
        if (!$cauHoi->save()) {
            throw new HttpException(500, Html::errorSummary($cauHoi));
        }
        return $this->outputSuccess("", "Thêm câu hỏi thành công");
    }
    public function actionDeleteCauHoiBaiHoc()
    {
        $this->checkField(['cau_hoi_id']);
        if ($this->dataPost['cau_hoi_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền cau_hoi_id");
        }
        $cauHoi = CauHoi::findOne($this->dataPost['cau_hoi_id']);
        if (is_null($cauHoi)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $cauHoi->active = 0;
        if (!$cauHoi->save()) {
            throw new HttpException(500, Html::errorSummary($cauHoi));
        }
        return $this->outputSuccess("", "Xóa câu hỏi thành công");
    }
    public function actionSuaCauHoiBaiHoc()
    {
        $this->checkField(['cau_hoi_id','tieu_de','gioi_thieu','link']);
        if ($this->dataPost['cau_hoi_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền cau_hoi_id");
        }
        $cauHoi = CauHoi::findOne($this->dataPost['cau_hoi_id']);
        if (is_null($cauHoi)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $cauHoi->tieu_de = $this->dataPost['tieu_de'];
        $cauHoi->gioi_thieu = $this->dataPost['gioi_thieu'];
        $cauHoi->link = $this->dataPost['link'];
        $cauHoi->user_id = $this->uid;
        if (!$cauHoi->save()) {
            throw new HttpException(500, Html::errorSummary($cauHoi));
        }
        return $this->outputSuccess("", "Sửa câu hỏi thành công");
    }
    public function actionThemKiemTraBaiHoc()
    {
        $this->checkField(['bai_hoc_id','link']);
        if ($this->dataPost['bai_hoc_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền bai_hoc_id");
        }
        $baiHoc = BaiHoc::findOne($this->dataPost['bai_hoc_id']);
        if (is_null($baiHoc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        BaiKiemTra::deleteAll(['bai_hoc_id'=>$baiHoc->id]);
        $baiTest = new BaiKiemTra();
        $baiTest->link = $this->dataPost['link'];
        $baiTest->bai_hoc_id = $baiHoc->id;
        $baiTest->user_id = $this->uid;
        if (!$baiTest->save()) {
            throw new HttpException(500, Html::errorSummary($baiTest));
        }
        return $this->outputSuccess("", "Thêm bài kiểm tra thành công");
    }
    public function actionDeleteKiemTraBaiHoc()
    {
        $this->checkField(['kiem_tra_id']);
        if ($this->dataPost['kiem_tra_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền kiem_tra_id");
        }
        $kiemTra = BaiKiemTra::findOne($this->dataPost['kiem_tra_id']);
        if (is_null($kiemTra)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $kiemTra->active = 0;
        if (!$kiemTra->save()) {
            throw new HttpException(500, Html::errorSummary($kiemTra));
        }
        return $this->outputSuccess("", "Xóa bài kiểm tra thành công");
    }
    public function actionSuaKiemTraBaiHoc()
    {
        $this->checkField(['kiem_tra_id','link']);
        if ($this->dataPost['kiem_tra_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền kiem_tra_id");
        }
        $kiemTra = BaiKiemTra::findOne($this->dataPost['kiem_tra_id']);
        if (is_null($kiemTra)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $kiemTra->link = $this->dataPost['link'];
        $kiemTra->user_id = $this->uid;
        if (!$kiemTra->save()) {
            throw new HttpException(500, Html::errorSummary($kiemTra));
        }
        return $this->outputSuccess("", "Sửa kiểm tra thành công");
    }
    public function actionDanhSachGiaoVien()
    {
        $this->checkGetInput(['tuKhoa', 'hoc_phan_id']);
        if ($this->dataGet['hoc_phan_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền hoc_phan_id");
        }
        $hocPhan = HocPhan::findOne($this->dataGet['hoc_phan_id']);
        if (is_null($hocPhan)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $ganGiaoVien = $hocPhan->getListGiaoVienDaGan();
        $users = QuanLyUserVaiTro::find()
            ->select(['hoten', 'id', 'anh_nguoi_dung'])
            ->andFilterWhere(['active' => 1, 'is_admin' => 0, 'status' => 10, 'vai_tro' => User::GIAO_VIEN]);
        if (count($ganGiaoVien) > 0) {
            $users->andFilterWhere(['not in', 'id', $ganGiaoVien]);
        }
        if ($this->dataGet['tuKhoa'] != "") {
            $users->andFilterWhere(['like', 'hoten', $this->dataGet['tuKhoa']]);
        }
        $count = count($users->all());
        $users = $users->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy(['created_at' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        if (count($users) > 0) {
            foreach ($users as $item) {
                $item->anh_nguoi_dung = CauHinh::getServer() . '/upload-file/' . ($item->anh_nguoi_dung == null ? "user-nomal.jpg" : $item->anh_nguoi_dung);
            }
        }
        return $this->outputListSuccess2($users, $count);
    }

    public function actionGanGiaoVien()
    {
        $this->checkField(['hoc_phan_id', 'giao_vien_id']);
        if ($this->dataPost['hoc_phan_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền hoc_phan_id");
        }
        $hocPhan = HocPhan::findOne($this->dataPost['hoc_phan_id']);
        if (is_null($hocPhan)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $ganGiaoVien = $hocPhan->getListGiaoVienDaGan();
        if (in_array($this->dataPost['giao_vien_id'], $ganGiaoVien)) {
            $hocPhan->boGanGiaoVien($this->dataPost['giao_vien_id']);
            return $this->outputSuccess('', "Bỏ gán giáo viên thành công");
        }
        $hocPhan->ganGiaoVien($this->dataPost['giao_vien_id']);
        return $this->outputSuccess('', "Gán giáo viên thành công");
    }

    public function actionDanhSachGiaoVienDaGan()
    {
        $this->checkGetInput(['tuKhoa', 'hoc_phan_id']);
        if ($this->dataGet['hoc_phan_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền hoc_phan_id");
        }
        $hocPhan = HocPhan::findOne($this->dataGet['hoc_phan_id']);
        if (is_null($hocPhan)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $ganGiaoVien = $hocPhan->getListGiaoVienDaGan();
        $users = QuanLyUserVaiTro::find()
            ->select(['hoten', 'id', 'anh_nguoi_dung'])
            ->andFilterWhere(['active' => 1, 'is_admin' => 0, 'status' => 10, 'vai_tro' => User::GIAO_VIEN]);
        if (count($ganGiaoVien) > 0) {
            $users->andFilterWhere(['in', 'id', $ganGiaoVien]);
        }
        if ($this->dataGet['tuKhoa'] != "") {
            $users->andFilterWhere(['like', 'hoten', $this->dataGet['tuKhoa']]);
        }
        $count = count($users->all());
        $users = $users->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy(['created_at' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        if (count($users) > 0) {
            foreach ($users as $item) {
                $item->anh_nguoi_dung = CauHinh::getServer() . '/upload-file/' . ($item->anh_nguoi_dung == null ? "user-nomal.jpg" : $item->anh_nguoi_dung);
            }
        }
        return $this->outputListSuccess2($users, $count);
    }

    public function actionDanhGiaKetQua()
    {
        $this->checkField(['ket_qua_id', 'trang_thai', 'ghi_chu']);
        if ($this->dataPost['ket_qua_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền ket_qua_id");
        }
        $ketQua = KetQuaDaoTao::findOne($this->dataPost['ket_qua_id']);
        if (is_null($ketQua)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $ketQua->trang_thai = $this->dataPost['trang_thai'];
        $ketQua->ghi_chu = $this->dataPost['ghi_chu'];
        if (!$ketQua->save()) {
            throw new HttpException(500, Html::errorSummary($ketQua));
        }
        return $this->outputSuccess("", "Đánh giá kết quả đào tạo thành công");
    }

    public function actionDanhSachKetQuaDaoTao()
    {
        $this->checkGetInput(['thang']);
        $ketQua = QuanLyKetQuaDaoTao::find()
            ->andFilterWhere(['active' => 1]);
        if ($this->tuKhoa != "") {
            $ketQua->andFilterWhere(['or',
                ['like', 'hocPhan', $this->tuKhoa],
                ['like', 'hoten', $this->tuKhoa],
                ['like', 'baiHoc', $this->tuKhoa],
            ]);
        }
        $thang = date("m");
        $nam = date("Y");
        if (isset($this->dataGet['thang'])) {
            if ($this->dataGet['thang'] != "") {
                $strThang = explode('/', $this->dataGet['thang']);
                if (count($strThang) != 2) {
                    throw new HttpException(500, "Định dạng tháng không hợp lệ.");
                }
                $thang = $strThang[0];
                $nam = $strThang[1];
            }
        }
        $ketQua = $ketQua->andFilterWhere(['=', 'month(' . QuanLyKetQuaDaoTao::tableName() . '.created)', $thang]);
        $ketQua = $ketQua->andFilterWhere(['=', 'year(' . QuanLyKetQuaDaoTao::tableName() . '.created)', $nam]);
        $count = $ketQua->count();
        $ketQua = $ketQua->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy(['created' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        $data = [];
        /** @var QuanLyKetQuaDaoTao $item */
        foreach ($ketQua as $item) {
            $data [] = [
                'id' => $item->id,
                'giaoVien' => is_null($item->giao_vien_id) ? null : [
                    'id' => $item->giao_vien_id,
                    'hoten' => $item->hoten,
                    'trinh_do' => $item->trinh_do,
                    'dien_thoai' => $item->dien_thoai,
                    'anh_nguoi_dung' => $item->getImage()
                ],
                'hocPhan' => $item->hocPhan,
                'created' => date('d/m/Y', strtotime($item->created)),
                'capDo' => $item->capDo,
                'baiHoc' => $item->baiHoc,
                'trang_thai' => $item->getTrangThai()
            ];
        }
        return $this->outputListSuccess2($data, $count);
    }

    public function actionExportExcelKetQuaDaoTao()
    {
        $ketQua = QuanLyKetQuaDaoTao::find()
            ->andFilterWhere(['active' => 1]);
        if ($this->tuKhoa != "") {
            $ketQua->andFilterWhere(['or',
                ['like', 'hocPhan', $this->tuKhoa],
                ['like', 'hoten', $this->tuKhoa],
                ['like', 'baiHoc', $this->tuKhoa],
            ]);
        }
        $thang = date("m");
        $nam = date("Y");
        if (isset($this->dataGet['thang'])) {
            if ($this->dataGet['thang'] != "") {
                $strThang = explode('/', $this->dataGet['thang']);
                if (count($strThang) != 2) {
                    throw new HttpException(500, "Định dạng tháng không hợp lệ.");
                }
                $thang = $strThang[0];
                $nam = $strThang[1];
            }
        }
        $ketQua = $ketQua->andFilterWhere(['=', 'month(' . QuanLyKetQuaDaoTao::tableName() . '.created)', $thang]);
        $ketQua = $ketQua->andFilterWhere(['=', 'year(' . QuanLyKetQuaDaoTao::tableName() . '.created)', $nam]);
        $export = new exportExcelKetQuaDaoTao();
        $export->data = [
            'data' => $ketQua->all(),
            'tuNgay' => "1/$thang/$nam",
            'denNgay' => date("t/m/Y", strtotime("$nam-$thang")),
        ];
        $export->stream = false;
        $export->path_file = dirname(dirname(__DIR__)) . '/files_excel/';
        $file_name = $export->run();
        $file = CauHinh::getServer() . '/files_excel/' . $file_name;;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->outputSuccess($file);
    }

    public function actionKetQuaDaoTao()
    {
        $this->checkGetInput(['ket_qua_id']);
        if ($this->dataGet['ket_qua_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền ket_qua_id");
        }
        $ketQua = KetQuaDaoTao::findOne($this->dataGet['ket_qua_id']);
        if (is_null($ketQua)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $giaoVien = $ketQua->giaoVien;
        $baiHoc = $ketQua->baiHoc;
        $hocPhan = $baiHoc->hocPhan;
        $khoaHoc = $hocPhan->khoaHoc;
        return $this->outputSuccess([
            'giaoVien' => is_null($giaoVien) ? null : [
                'id' => $giaoVien->id,
                'hoten' => $giaoVien->hoten,
                'trinh_do' => $giaoVien->getTrinhDo(),
                'danh_gia' => $giaoVien->danh_gia,
                'dien_thoai' => $giaoVien->dien_thoai,
                'anh_nguoi_dung' => $giaoVien->getImage()
            ],
            'id' => $ketQua->id,
            'chuongTrinh' => $khoaHoc->tieu_de,
            'created' => date('d/m/Y', strtotime($ketQua->created)),
            'capDo' => $hocPhan->capDo->name,
            'hocPhan' => $hocPhan->tieu_de,
            'baiHoc' => $baiHoc->tieu_de,
            'link' => $ketQua->link,
            'ghi_chu' => $ketQua->ghi_chu,
            'trang_thai' => $ketQua->getTrangThai()
        ]);
    }

    public function actionYeuCauHocLai()
    {
        $this->checkField(['ket_qua_id', 'ghi_chu']);
        if ($this->dataPost['ket_qua_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền ket_qua_id");
        }
        $ketQua = KetQuaDaoTao::findOne($this->dataPost['ket_qua_id']);
        if (is_null($ketQua)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $baiHoc = $ketQua->baiHoc;
        $hocPhan = $baiHoc->hocPhan;
        $ketQua->trang_thai = KetQuaDaoTao::HOC_LAI;
        $ketQua->ghi_chu = $this->dataPost['ghi_chu'];
        if (!$ketQua->save()) {
            throw new HttpException(500, Html::errorSummary($ketQua));
        } else {
            $thongBao = new ThongBao();
            $thongBao->to_id = 60;
            $thongBao->type_id = 66;
            $thongBao->giao_vien_id = strval($ketQua->giao_vien_id);
            $thongBao->noi_dung = "Kết quả đào tạo " . $hocPhan->tieu_de . " chưa đạt, vui lòng học lại!.<\br>Giáo viên: " . $ketQua->giaoVien->hoten . " • " . $ketQua->giaoVien->getTrinhDo();
            $thongBao->tieu_de = "Yêu cầu học lại";
            $this->saveThongBao($thongBao);
        }
        return $this->outputSuccess('', 'Yêu cầu học lại thành công');
    }

    public function actionDanhSachCapDo()
    {
        return $this->outputSuccess($this->getDanhMuc(DanhMuc::CAP_DO));
    }

    public function actionDanhSachPhanLoaiHocPhan()
    {
        return $this->outputSuccess($this->getDanhMuc(DanhMuc::PHAN_LOAI_HOC_PHAN));
    }

    public function actionBaoCaoKetQuaDaoTao()
    {
        $thang = date("m");
        $nam = date("Y");
        if (isset($this->dataGet['thang'])) {
            if ($this->dataGet['thang'] != "") {
                $strThang = explode('/', $this->dataGet['thang']);
                if (count($strThang) != 2) {
                    throw new HttpException(500, "Định dạng tháng không hợp lệ.");
                }
                $thang = $strThang[0];
                $nam = $strThang[1];
            }
        }
        $dangHoc = KetQuaDaoTao::find()
            ->andFilterWhere(['month(created)' => $thang])
            ->andFilterWhere(['year(created)' => $nam])
            ->groupBy('giao_vien_id')->count();
        $dat = KetQuaDaoTao::find()
            ->andFilterWhere(['month(created)' => $thang])
            ->andFilterWhere(['year(created)' => $nam])
            ->andFilterWhere(['trang_thai' => KetQuaDaoTao::DAT])
            ->groupBy('giao_vien_id')->count();
        $hocLai = KetQuaDaoTao::find()
            ->andFilterWhere(['month(created)' => $thang])
            ->andFilterWhere(['year(created)' => $nam])
            ->andFilterWhere(['trang_thai' => KetQuaDaoTao::HOC_LAI])
            ->groupBy('giao_vien_id')->count();
        $hoanThanh = HoanThanhDaoTao::find()
            ->andFilterWhere(['month(created)' => $thang])
            ->andFilterWhere(['year(created)' => $nam])
            ->andFilterWhere(['trang_thai' => HoanThanhDaoTao::DA_HOAN_THANH])
            ->count();
        return $this->outputSuccess([
            'dangHoc' => $dangHoc,
            'dat' => $dat,
            'hocLai' => $hocLai,
            'hoanThanh' => $hoanThanh,
        ]);
    }
}
