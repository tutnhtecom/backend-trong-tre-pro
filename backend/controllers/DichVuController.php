<?php namespace backend\controllers;

use backend\models\CauHinh;
use backend\models\DanhMuc;
use backend\models\DichVu;
use backend\models\DonDichVu;
use backend\models\GiaDichVu;
use backend\models\GiaHanDon;
use backend\models\KhungThoiGian;
use backend\models\QuyenLoi;
use Yii;
use yii\bootstrap\Html;
use yii\helpers\FileHelper;
use yii\helpers\VarDumper;
use yii\web\HttpException;
use yii\web\UploadedFile;

class DichVuController extends CoreApiController
{
    public $limit = 10;

    public function actionDanhSach()
    {
        $dichVu = DichVu::find()->andFilterWhere(['active' => 1])->select(['id', 'ten_dich_vu', 'image', 'khoa_dich_vu'])->orderBy(['seq'=>SORT_ASC]);
        if ($this->tuKhoa != "") {
            $dichVu->andFilterWhere(['like', 'ten_dich_vu', $this->tuKhoa]);
        }
        return $this->outputListSuccess($dichVu);
    }
    public function actionDanhSachDichVu()
    {
        $dichVu = DichVu::find()->andFilterWhere(['active' => 1])->select(['id', 'ten_dich_vu', 'image', 'khoa_dich_vu'])->orderBy(['seq'=>SORT_ASC]);
        if ($this->tuKhoa != "") {
            $dichVu->andFilterWhere(['like', 'ten_dich_vu', $this->tuKhoa]);
        }
        return $this->outputListSuccess($dichVu);
    }
    public function actionTaoMoi()
    {
        $this->checkField([
            'khoa_dich_vu',
            'ten_dich_vu',
            'do_tuoi_id',
            'quyen_loi',
            'gia_tri',
            'cam_ket',
            'hop_dong_dich_vu',
            'link',
            'loai_dich_vu_id',
        ]);
        if ($this->dataPost['khoa_dich_vu'] == "") {
            throw new HttpException(500, "Vui lòng truyền khóa dịch vụ bằng 1 (Bật) hoặc 0 (Tắt) ");
        }
        if ($this->dataPost['ten_dich_vu'] == "") {
            throw new HttpException(500, "Vui lòng nhập tên dịch vụ");
        }
        if ($this->dataPost['do_tuoi_id'] == "") {
            throw new HttpException(500, 'Vui lòng chọn độ tuổi');
        }
      if ($this->dataPost['loai_dich_vu_id'] == "") {
        throw new HttpException(500, 'Vui lòng chọn loại dịch vụ');
      }
        $dichVu = new DichVu();
        $dichVu->khoa_dich_vu = $this->dataPost['khoa_dich_vu'];
        $files = UploadedFile::getInstanceByName('image');
        if (!empty($files)) {
            $path = (dirname(dirname(__DIR__))) . '/upload-file';
            $link = date('Y/m/d') . '/' . '_' . \Yii::$app->security->generateRandomString() . $this->get_extension_image($files->type);
            $dichVu->image = $link;
            if (FileHelper::createDirectory($path . '/' . date('Y/m/d') . '/', $mode = 0775, $recursive = true)) {
                $files->saveAs($path . '/' . $link);
            }
        }
        $dichVu->ten_dich_vu = $this->dataPost['ten_dich_vu'];
        $dichVu->do_tuoi_id = $this->dataPost['do_tuoi_id'];
        $dichVu->gia_tri = $this->dataPost['gia_tri'];
        $dichVu->cam_ket = $this->dataPost['cam_ket'];
        $dichVu->loai_dich_vu_id = $this->dataPost['loai_dich_vu_id'];
        $dichVu->link = $this->dataPost['link'];
        $dichVu->hop_dong_dich_vu = $this->dataPost['hop_dong_dich_vu'];
        $dichVu->user_id = $this->uid;

        if (!$dichVu->save()) {
            throw new HttpException(500, Html::errorSummary($dichVu));
        } else {
            $quyenLois = ($this->dataPost['quyen_loi']);
            if (is_array($quyenLois)) {
                if (count($quyenLois) > 0) {
                    foreach ($quyenLois as $item) {
                        $item = (object)$item;
                        $quyenLoi = new QuyenLoi();
                        $quyenLoi->user_id = $this->uid;
                        $quyenLoi->dich_vu_id = $dichVu->id;
                        $quyenLoi->name = $item->name;
                        $quyenLoi->link = $item->link;
                        if (!$quyenLoi->save()) {
                            throw new HttpException(500, Html::errorSummary($quyenLoi));
                        }
                    }
                }
            }

        };
        return $this->outputSuccess("", 'Lưu thông tin dịch vụ thành công');
    }

    public function actionGetDoTuoi()
    {
        $doTuoi = $this->getDanhMuc(DanhMuc::Do_TUOI);
        return $this->outputSuccess($doTuoi);
    }

    public function actionChiTiet()
    {
        $this->checkGetInput(['id']);
        if ($this->dataGet['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $dichVu = DichVu::find()->select(['khoa_dich_vu','loai_dich_vu_id', 'image', 'ten_dich_vu', 'do_tuoi_id', 'id', 'gia_tri', 'cam_ket', 'hop_dong_dich_vu','link'])->andFilterWhere(['id' => $this->dataGet['id']])->one();
        if (is_null($dichVu)) {
            throw new HttpException(403,"Không xác định dữ liệu");
        }
        $dichVu->image = CauHinh::getImage($dichVu->image);
        $doTuoi = $this->getDanhMuc(DanhMuc::Do_TUOI);
        $QuyenLoi = QuyenLoi::find()->andFilterWhere(['active' => 1, 'dich_vu_id' => $dichVu->id])->select(['id','name', 'link'])->all();
        return $this->outputSuccess([
            'dichVu' => $dichVu,
            'doTuoi' => $doTuoi,
            'quyenLoi' => $QuyenLoi,
        ]);
    }

    public function actionSua()
    {
        $this->checkField([
            'id',
            'khoa_dich_vu',
            'ten_dich_vu',
            'do_tuoi_id',
            'quyen_loi',
            'gia_tri',
            'cam_ket',
            'hop_dong_dich_vu',
            'link',
            'loai_dich_vu_id'
        ]);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $dichVu = DichVu::findOne($this->dataPost['id']);
        if (is_null($dichVu)) {
            throw new HttpException(403,"Không xác định dữ liệu");
        }
        if ($this->dataPost['khoa_dich_vu'] == "") {
            throw new HttpException(500, "Vui lòng truyền khóa dịch vụ bằng 1 (Bật) hoặc 0 (Tắt) ");
        }
        if ($this->dataPost['ten_dich_vu'] == "") {
            throw new HttpException(500, "Vui lòng nhập tên dịch vụ");
        }
        if ($this->dataPost['do_tuoi_id'] == "") {
            throw new HttpException(500, 'Vui lòng chọn độ tuổi');
        }
      if ($this->dataPost['loai_dich_vu_id'] == "") {
        throw new HttpException(500, 'Vui lòng chọn loại dịch vụ');
      }
        $dichVu->khoa_dich_vu = $this->dataPost['khoa_dich_vu'];
        $files = UploadedFile::getInstanceByName('image');
        if (!empty($files)) {
            $path = (dirname(dirname(__DIR__))) . '/upload-file';
            $link = date('Y/m/d') . '/' . '_' . \Yii::$app->security->generateRandomString() . $this->get_extension_image($files->type);
            $dichVu->image = $link;
            if (FileHelper::createDirectory($path . '/' . date('Y/m/d') . '/', $mode = 0775, $recursive = true)) {
                $files->saveAs($path . '/' . $link);
            }
        }
        $dichVu->ten_dich_vu = $this->dataPost['ten_dich_vu'];
        $dichVu->do_tuoi_id = $this->dataPost['do_tuoi_id'];
        $dichVu->gia_tri = $this->dataPost['gia_tri'];
        $dichVu->link = $this->dataPost['link'];
        $dichVu->loai_dich_vu_id = $this->dataPost['loai_dich_vu_id'];
        $dichVu->cam_ket = $this->dataPost['cam_ket'];
        $dichVu->hop_dong_dich_vu = $this->dataPost['hop_dong_dich_vu'];
        $dichVu->user_id = $this->uid;
        QuyenLoi::deleteAll(['dich_vu_id' => $dichVu->id]);
        if (!$dichVu->save()) {
            throw new HttpException(500, Html::errorSummary($dichVu));
        } else {
            $quyenLois = ($this->dataPost['quyen_loi']);
            if (is_array($quyenLois)) {
                if (count($quyenLois) > 0) {
                    foreach ($quyenLois as $item) {
                        $item = (object)$item;
                        $quyenLoi = new QuyenLoi();
                        $quyenLoi->user_id = $this->uid;
                        $quyenLoi->dich_vu_id = $dichVu->id;
                        $quyenLoi->name = $item->name;
                        $quyenLoi->link = $item->link;
                        if (!$quyenLoi->save()) {
                            throw new HttpException(500, Html::errorSummary($quyenLoi));
                        }
                    }
                }
            }

        };
        return $this->outputSuccess("", 'Lưu thông tin dịch vụ thành công');
    }
    public function actionThemGiaBuoiHoc(){

        $this->checkField(['dich_vu_id', 'trinh_do','so_buoi','tong_tien', 'khuyen_mai','khung_gio_id']);
        if ($this->dataPost['dich_vu_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền dich_vu_id");
        }
        $dichVu = DichVu::findOne($this->dataPost['dich_vu_id']);
        if (is_null($dichVu)) {
            throw new HttpException(403,"Không xác định dữ liệu");
        }
        if ($this->dataPost['trinh_do'] == "") {
            throw new HttpException(500, 'Vui lòng chọn trình độ');
        }
        if (intval($this->dataPost['so_buoi']) ==0) {
            throw new HttpException(500, 'Số buổi tối thiểu là 1');
        }
        if ($this->dataPost['tong_tien']=="") {
            throw new HttpException(500, 'Vui lòng nhập tổng tiền');
        }
        $giaDichVu = new  GiaDichVu();
        $giaDichVu->dich_vu_id = $this->dataPost['dich_vu_id'];
        $giaDichVu->user_id = $this->uid;
        $giaDichVu->trinh_do = $this->dataPost['trinh_do'];
        $giaDichVu->so_buoi = $this->dataPost['so_buoi'];
        $giaDichVu->tong_tien = $this->dataPost['tong_tien'];
        $giaDichVu->khung_gio_id = $this->dataPost['khung_gio_id'];
        $giaDichVu->khuyen_mai = intval($this->dataPost['khuyen_mai']);
        $giaDichVu->thanh_tien = $giaDichVu->khuyen_mai > 0 ? ($giaDichVu->tong_tien - ($giaDichVu->khuyen_mai * $giaDichVu->tong_tien / 100)) : $giaDichVu->tong_tien;
        if (!$giaDichVu->save()) {
            throw new HttpException(500, Html::errorSummary($giaDichVu));
        }
        return $this->outputSuccess("", "Thêm giá buổi học thành công");
    }
    public function actionCapNhatGiaBuoiHocV2(){

        $this->checkField(['id', 'trinh_do','so_buoi','tong_tien', 'khuyen_mai','khung_gio_id']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $giaDichVu = GiaDichVu::findOne($this->dataPost['id']);
        if (is_null($giaDichVu)) {
            throw new HttpException(403,"Không xác định dữ liệu");
        }
        if ($this->dataPost['trinh_do'] == "") {
            throw new HttpException(500, 'Vui lòng chọn trình độ');
        }
        if (intval($this->dataPost['so_buoi']) ==0) {
            throw new HttpException(500, 'Số buổi tối thiểu là 1');
        }
        if ($this->dataPost['tong_tien']=="") {
            throw new HttpException(500, 'Vui lòng nhập tổng tiền');
        }
        $giaDichVu->user_id = $this->uid;
        $giaDichVu->trinh_do = $this->dataPost['trinh_do'];
        $giaDichVu->so_buoi = $this->dataPost['so_buoi'];
        $giaDichVu->tong_tien = $this->dataPost['tong_tien'];
        $giaDichVu->khuyen_mai = intval($this->dataPost['khuyen_mai']);
        $giaDichVu->khung_gio_id = $this->dataPost['khung_gio_id'];
        $giaDichVu->thanh_tien = $giaDichVu->khuyen_mai > 0 ? ($giaDichVu->tong_tien - ($giaDichVu->khuyen_mai * $giaDichVu->tong_tien / 100)) : $giaDichVu->tong_tien;
        if (!$giaDichVu->save()) {
            throw new HttpException(500, Html::errorSummary($giaDichVu));
        }
        return $this->outputSuccess("", "Cập nhập giá buổi học thành công");
    }

    public function actionXoaGiaBuoiHoc(){

        $this->checkField(['id']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $giaDichVu = GiaDichVu::findOne(['id'=>$this->dataPost['id'],'active'=>1]);
        if (is_null($giaDichVu)) {
            throw new HttpException(403,"Không xác định dữ liệu");
        }
        $giaDichVu->updateAttributes(['active'=>0]);

        return $this->outputSuccess("", "Xóa giá buổi học thành công");
    }
    public function actionCapNhatGiaBuoiHoc()
    {
        $this->checkField(['dich_vu_id', 'trinh_do', 'giaDichVu']);
        if ($this->dataPost['dich_vu_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền dich_vu_id");
        }
        $dichVu = DichVu::findOne($this->dataPost['dich_vu_id']);
        if (is_null($dichVu)) {
            throw new HttpException(403,"Không xác định dữ liệu");
        }
        if ($this->dataPost['trinh_do'] == "") {
            throw new HttpException(500, 'Vui lòng chọn trình độ');
        }
        $giaDichVus = $this->dataPost['giaDichVu'];
        if (is_array($giaDichVus)) {
            if (count($giaDichVus) > 0) {
                foreach ($giaDichVus as $item) {
                    $item = (object)$item;
                    if (isset($item->id)) {
                        $giaDichVu = GiaDichVu::findOne($item->id);
                        if (is_null($giaDichVu)) {
                            $giaDichVu = new  GiaDichVu();
                        }
                        $giaDichVu->id = $item->id;
                    } else
                        $giaDichVu = new  GiaDichVu();
                    $giaDichVu->dich_vu_id = $this->dataPost['dich_vu_id'];
                    $giaDichVu->user_id = $this->uid;
                    $giaDichVu->trinh_do = $this->dataPost['trinh_do'];
                    $giaDichVu->so_buoi = $item->so_buoi;
                    $giaDichVu->tong_tien = $item->tong_tien;
                    $giaDichVu->khuyen_mai = intval($item->khuyen_mai);
                    $giaDichVu->thanh_tien = $giaDichVu->khuyen_mai > 0 ? ($giaDichVu->tong_tien - ($giaDichVu->khuyen_mai * $giaDichVu->tong_tien / 100)) : $giaDichVu->tong_tien;
                    if (!$giaDichVu->save()) {
                        throw new HttpException(500, Html::errorSummary($giaDichVu));
                    }
                }
            }
        }
        return $this->outputSuccess("", "Cập nhật giá buổi học thành công");
    }

    public function actionDanhSachGiaBuoiHoc()
    {
        $this->checkGetInput(['trinh_do', 'dich_vu_id']);
        $trinhDo = $this->getDanhMuc(DanhMuc::TRINH_DO);
        $giaBuoiHoc = GiaDichVu::find()
            ->select(['id', 'so_buoi', 'khuyen_mai', 'tong_tien','khung_gio_id'])
            ->andFilterWhere(['active' => 1, 'dich_vu_id' => $this->dataGet['dich_vu_id']]);
        if ($this->dataGet['trinh_do'] != "") {
            $giaBuoiHoc->andFilterWhere(['trinh_do' => $this->dataGet['trinh_do']]);
        }
        $count = count($giaBuoiHoc->all());
        $giaBuoiHoc = $giaBuoiHoc->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy(['ID' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        $data = [];
        /** @var GiaDichVu $item */
        foreach ($giaBuoiHoc as $item){
            $khungGio = $item->khungGio;
            $data[] = [
                'id'=>$item->id,
                'so_buoi'=>$item->so_buoi,
                'khuyen_mai'=>$item->khuyen_mai,
                'tong_tien'=>$item->tong_tien,
                'thanh_tien'=>$item->thanh_tien,
                'khungGio'=>is_null($item->khungGio)?null:[
                    'id'=>$khungGio->id,
                    'khung_gio'=>$khungGio->khungGio->name,
                    'type'=>$khungGio->type0->name,
                ]
            ];
        }
        return $this->outputListSuccess2([
            'giaBuoiHoc' => $data,
            'trinhDo' => $trinhDo
        ], $count);
    }
    public function actionGetCa()
    {
        $type = $this->getDanhMuc(DanhMuc::CHON_CA);
        return $this->outputSuccess($type);
    }

    public function actionThemKhungGio(){
        $this->checkField(['dich_vu_id', 'type','khung_gio','noi_dung']);
        if ($this->dataPost['dich_vu_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền dich_vu_id");
        }
        $dichVu = DichVu::findOne($this->dataPost['dich_vu_id']);
        if (is_null($dichVu)) {
            throw new HttpException(403,"Không xác định dữ liệu");
        }
        if ($this->dataPost['type'] == "") {
            throw new HttpException(500, 'Vui lòng chọn ca');
        }
        if ($this->dataPost['khung_gio'] == "") {
            throw new HttpException(500, 'Vui lòng chọn khung giờ');
        }
        if ($this->dataPost['noi_dung'] == "") {
            throw new HttpException(500, 'Vui lòng nhập nội dung');
        }
        $khungGio = new  KhungThoiGian();
        $khungGio->dich_vu_id = $this->dataPost['dich_vu_id'];
        $khungGio->user_id = $this->uid;
        $khungGio->type = $this->dataPost['type'];
        $khungGio->khung_gio = $this->dataPost['khung_gio'];
        $khungGio->noi_dung = $this->dataPost['noi_dung'];
        if (!$khungGio->save()) {
            throw new HttpException(500, Html::errorSummary($khungGio));
        }
        return $this->outputSuccess("", "Thêm khung giờ thành công");
    }
    public function actionCapNhatKhungGioV2(){
        $this->checkField(['id', 'type','khung_gio','noi_dung']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $khungGio = KhungThoiGian::findOne($this->dataPost['id']);
        if (is_null($khungGio)) {
            throw new HttpException(403,"Không xác định dữ liệu");
        }
        if ($this->dataPost['type'] == "") {
            throw new HttpException(500, 'Vui lòng chọn ca');
        }
        if ($this->dataPost['khung_gio'] == "") {
            throw new HttpException(500, 'Vui lòng chọn khung giờ');
        }
        if ($this->dataPost['noi_dung'] == "") {
            throw new HttpException(500, 'Vui lòng nhập nội dung');
        }
        $khungGio->user_id = $this->uid;
        $khungGio->type = $this->dataPost['type'];
        $khungGio->khung_gio = $this->dataPost['khung_gio'];
        $khungGio->noi_dung = $this->dataPost['noi_dung'];
        if (!$khungGio->save()) {
            throw new HttpException(500, Html::errorSummary($khungGio));
        }
        return $this->outputSuccess("", "Cập nhật khung giờ thành công");
    }
    public function actionChiTietKhungGioV2()
    {
        $this->checkGetInput(['id']);
        if ($this->dataGet['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $giaDichVu = KhungThoiGian::findOne($this->dataGet['id']);
        if (is_null($giaDichVu)) {
            throw new HttpException(403,"Không xác định dữ liệu");
        }
        return $this->outputSuccess($giaDichVu);
    }
    public function actionXoaKhungGio(){

        $this->checkField(['id']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $giaDichVu = KhungThoiGian::findOne($this->dataPost['id']);
        if (is_null($giaDichVu)) {
            throw new HttpException(403,"Không xác định dữ liệu");
        }
        $giaDichVu->updateAttributes(['active'=>0]);
        return $this->outputSuccess("", "Xóa khung giờ thành công");
    }
    public function actionCapNhatKhungGio()
    {
        $this->checkField(['dich_vu_id', 'type', 'khungGio']);
        if ($this->dataPost['dich_vu_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền dich_vu_id");
        }
        $dichVu = DichVu::findOne($this->dataPost['dich_vu_id']);
        if (is_null($dichVu)) {
            throw new HttpException(403,"Không xác định dữ liệu");
        }
        if ($this->dataPost['type'] == "") {
            throw new HttpException(500, 'Vui lòng chọn ca');
        }
        $khungGios = ($this->dataPost['khungGio']);
        if (is_array($khungGios)) {
            if (count($khungGios) > 0) {
                foreach ($khungGios as $item) {
                    $item = (object)$item;
                    if (isset($item->id)) {
                        $khungGio = KhungThoiGian::findOne($item->id);
                        if (is_null($khungGio)) {
                            $khungGio = new  KhungThoiGian();
                        }
                        $khungGio->id = $item->id;
                    } else
                        $khungGio = new  KhungThoiGian();
                    $khungGio->dich_vu_id = $this->dataPost['dich_vu_id'];
                    $khungGio->user_id = $this->uid;
                    $khungGio->type = $this->dataPost['type'];
                    $khungGio->khung_gio = $item->khung_gio;
                    $khungGio->noi_dung = $item->noi_dung;
                    if (!$khungGio->save()) {
                        throw new HttpException(500, Html::errorSummary($khungGio));
                    }
                }
            }
        }
        return $this->outputSuccess("", "Cập nhật khung giờ thành công");
    }

    public function actionDanhSachKhungGio()
    {
        $this->checkGetInput(['type', 'dich_vu_id']);
        $type = $this->getDanhMuc(DanhMuc::CHON_CA);
        $perPage = $this->getPerPage();
        $khungGio = KhungThoiGian::find()
            ->select(['id', 'khung_gio', 'noi_dung'])
            ->andFilterWhere(['active' => 1, 'dich_vu_id' => $this->dataGet['dich_vu_id']]);
        if ($this->dataGet['type'] != "") {
            $khungGio->andFilterWhere(['type' => $this->dataGet['type']]);
        }
        $count = ($khungGio->count());
        $khungGio = $khungGio->limit($this->limit)->offset(($perPage - 1) * $this->limit)->orderBy(['ID' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        foreach ($khungGio as $item) {
            $item->khung_gio = $item->khungGio->name;
        }
        return $this->outputListSuccess2([
            'khungGio' => $khungGio,
            'chonCa' => $type
        ], $count);
    }
    public function actionDanhSachKhungGioFull()
    {
        $this->checkGetInput(['type', 'dich_vu_id']);
        $type = $this->getDanhMuc(DanhMuc::CHON_CA);
        $khungGio = KhungThoiGian::find()
            ->select(['id', 'khung_gio', 'noi_dung'])
            ->andFilterWhere(['active' => 1, 'dich_vu_id' => $this->dataGet['dich_vu_id']]);
        if ($this->dataGet['type'] != "") {
            $khungGio->andFilterWhere(['type' => $this->dataGet['type']]);
        }
        $khungGio = $khungGio->all();
        foreach ($khungGio as $item) {
            $item->khung_gio = $item->khungGio->name;
        }
        return $this->outputSuccess([
            'khungGio' => $khungGio,
            'chonCa' => $type
        ]);
    }
    public function actionChiTietKhungGio(){
        $this->checkGetInput(['khung_gio_id']);
        if ($this->dataGet['khung_gio_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền khung_gio_id");
        }
        $khungGio = KhungThoiGian::findOne($this->dataGet['khung_gio_id']);
        if (is_null($khungGio)) {
            throw new HttpException(403,"Không xác định dữ liệu");
        }
        return $this->outputSuccess([
            'id'=>$khungGio->id,
            'khung_gio'=>$khungGio->khungGio->name,
            'type'=>$khungGio->type0->name,
        ]);
    }
    public function actionChiTietGiaBuoiHoc(){
        $this->checkGetInput(['goi_hoc_phi_id']);
        if ($this->dataGet['goi_hoc_phi_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền goi_hoc_phi_id");
        }
        $giaBuoiHoc = GiaDichVu::findOne($this->dataGet['goi_hoc_phi_id']);
        if (is_null($giaBuoiHoc)) {
            throw new HttpException(403,"Không xác định dữ liệu");
        }
        $khungGio = $giaBuoiHoc->khungGio;
        return $this->outputSuccess([
            'id'=>$giaBuoiHoc->id,
            'so_buoi'=>$giaBuoiHoc->so_buoi,
            'khuyen_mai'=>$giaBuoiHoc->khuyen_mai,
            'tong_tien'=>$giaBuoiHoc->tong_tien,
            'thanh_tien'=>$giaBuoiHoc->thanh_tien,
            'trinh_do'=>$giaBuoiHoc->trinh_do,
            'khungGio'=>is_null($giaBuoiHoc->khungGio)?null:[
                'id'=>$khungGio->id,
                'khung_gio'=>$khungGio->khungGio->name,
                'ca'=>[
                    'id'=>$khungGio->type0->id,
                    'name'=>$khungGio->type0->name,
                ],
            ]
        ]);
    }
    public function actionGetKhungThoiGian()
    {
        if (!isset($_GET['type'])) {
            throw new HttpException(400, "Vui lòng truyền tham số type");
        }
        if ($_GET['type'] == "") {
            throw new HttpException(400, "Vui lòng truyền tham số type");
        }
        return $this->outputSuccess(DanhMuc::find()->select(['id', 'name'])->andFilterWhere(['type' => DanhMuc::GIO_LINH_HOAT, 'parent_id' => $_GET['type']])->orderBy(['name'=>SORT_ASC])->createCommand()->queryAll());
    }
    public function actionDanhSachDichVuFull()
    {
        $dichVu = DichVu::find()->andFilterWhere(['active' => 1,'khoa_dich_vu'=>0])
            ->select(['id', 'ten_dich_vu', 'image'])->orderBy(['seq'=>SORT_ASC]);
        $dichVu = $dichVu->all();
        /** @var DichVu $item */
        foreach ($dichVu as $item) {
            $item->image = CauHinh::getImage($item->image);
        }
        return $this->outputSuccess($dichVu);
    }
  public function actionXoaDichVu()
  {
    $this->checkField([
      'id',
    ]);
    if ($this->dataPost['id'] == "") {
      throw new HttpException(500, "Vui lòng truyền id");
    }
    $dichVu = DichVu::findOne(['id' => $this->dataPost['id'], 'active' => 1]);
    if (is_null($dichVu)) {
      throw new HttpException(403, "Không xác định dữ liệu");
    }
//    $email = CauHinh::findOne(2)->ghi_chu;
//    $emailAdmin = CauHinh::findOne(39)->content;
//    $tokenComfirm = CauHinh::findOne(40);
//    $token  = Yii::$app->security->generateRandomString();
//    $tokenComfirm->updateAttributes(['ghi_chu'=>$token]);
//    $linkDeleteDichVu =CauHinh::getServer()."/dich-vu/xac-nhan-xoa-dich-vu?id=$dichVu->id&&token=".$token;
//    $this->sendEMail('Trông trẻ Pro', $email, $emailAdmin, 'Admin', 'Yêu cầu xóa dịch vụ', "
//     Xin chào $emailAdmin,
//
//Chúng tôi đã nhận được yêu cầu xóa dịch vụ: $dichVu->ten_dich_vu.
//
//Để xóa dịch vụ vui lòng nhấn vào liên kết dưới đây, hoặc sao chép và dán lại địa chỉ vào trình duyệt của mình:
//
//$linkDeleteDichVu
//
//Liên kết này chỉ có hiệu lực 1 lần duy nhất. Trong vòng 1 ngày nếu liên kết này không được sử dụng, nó sẽ hết hạn và không sử dụng được.
//
//Sau khi nhấn vào link sẽ xóa dịch vụ và chuyển hướng đến trang chủ.
//
//--  Ban quản trị Trông trẻ Pro
//    ");
            $dichVu->active = 0;
    if (!$dichVu->save()) {
      throw new HttpException(500, Html::errorSummary($dichVu));
    }
    return $this->outputSuccess("", 'Xóa đơn dịch vụ thành công');
  }
  public function actionXacNhanXoaDichVu()
  {
    $tokenComfirm = CauHinh::findOne(40);
    if (is_null($tokenComfirm->ghi_chu)){
      header("location: https://admin-app-test.netlify.app/");
      return false;
    }
    if (!isset($this->dataGet['id'])||!isset($this->dataGet['token'])){
      header("location: https://admin-app-test.netlify.app");
      return false;
    }
    if ($tokenComfirm->ghi_chu!==$this->dataGet['token']){
      header("location: https://admin-app-test.netlify.app");
      return false;
    }
    $tokenComfirm->updateAttributes(['ghi_chu'=>null]);
    if ($this->dataGet['id'] == "") {
      header("location: https://admin-app-test.netlify.app");
      return false;
    }
    $dichVu = DonDichVu::findOne(['id' => $this->dataGet['id'], 'active' => 1]);
    if (is_null($dichVu)) {
      header("location: https://admin-app-test.netlify.app");
      return false;
    }
    $dichVu->active = 0;
    if (!$dichVu->save()) {
      header("location: https://admin-app-test.netlify.app");
      return false;
    }
    header("location: https://admin-app-test.netlify.app");
    return false;
  }
    public function actionXoaDonDichVu(){
        $this->checkField([
            'id',
        ]);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $dichVu = DonDichVu::findOne(['id'=>$this->dataPost['id'],'active'=>1]);
        if (is_null($dichVu)) {
            throw new HttpException(403,"Không xác định dữ liệu");
        }
        $dichVu->active = 0;
        if (!$dichVu->save()) {
            throw new HttpException(500, Html::errorSummary($dichVu));
        }
        return $this->outputSuccess("", 'Xóa đơn dịch vụ thành công');
    }
    public function actionLoaiDichVu(){
      return $this->outputSuccess($this->getDanhMuc(DanhMuc::LOAI_DICH_VU));
    }
}
