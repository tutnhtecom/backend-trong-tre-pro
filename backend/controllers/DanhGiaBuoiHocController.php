<?php namespace backend\controllers;

use backend\models\CauHinh;
use backend\models\DanhGiaBuoiHoc;
use backend\models\DichVu;
use backend\models\DonDichVu;
use backend\models\QuanLyUserVaiTro;
use backend\models\TienDoKhoaHoc;
use common\models\User;
use yii\bootstrap\Html;
use yii\web\HttpException;

class DanhGiaBuoiHocController extends CoreApiController
{


    public function actionChiTiet()
    {
        $this->checkGetInput(['id']);
        if ($this->dataGet['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $user = QuanLyUserVaiTro::find()->select([
            'anh_nguoi_dung', 'id', 'danh_gia', 'hoten', 'vai_tro_name', 'dien_thoai', 'email', 'dia_chi',
            'ho_ten_con', 'ngay_sinh_cua_con', 'cmnd_cccd', 'ghi_chu'
        ])->andFilterWhere(['id' => $this->dataGet['id'], 'active' => 1, 'status' => 10, 'vai_tro' => User::PHU_HUYNH])->one();
        if (is_null($user)) {
            throw new HttpException(403,"Không xác định dữ liệu");
        }
        $user->anh_nguoi_dung = CauHinh::getServer() . '/upload-file/' . ($user->anh_nguoi_dung == null ? "user-nomal.jpg" : $user->anh_nguoi_dung);
        $user->ngay_sinh_cua_con = $user->ngay_sinh_cua_con == null ? "" : date('d/m/Y', strtotime($user->ngay_sinh_cua_con));
        return $this->outputSuccess($user);
    }

    public function actionGetFormDanhGia()
    {
        $this->checkGetInput(['dich_vu_id']);
        if ($this->dataGet['dich_vu_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền dich_vu_id");
        }
        $dichVu = DichVu::findOne($this->dataGet['dich_vu_id']);
        if (is_null($dichVu)) {
            throw new HttpException(403,"Không xác định dữ liệu");
        }
        $formDanhGia = DanhGiaBuoiHoc::find()->andFilterWhere(['active' => 1, 'dich_vu_id' => $dichVu->id,'danh_muc_id'=>isset($this->dataGet['danh_muc_id'])?$this->dataGet['danh_muc_id']:68])->all();
        $data = [];
        /** @var DanhGiaBuoiHoc $item */
        foreach ($formDanhGia as $item) {
            $data[] = [
                'tieu_de' => $item->tieu_de,
                'muc_do' => json_decode($item->muc_do),
                'nhan_xet' => $item->getNhanXet(),
                'goi_y' => json_decode($item->goi_y)
            ];
        }
        return $this->outputSuccess($data);
    }
    public function actionCapNhatForm()
    {
        $this->checkField(['dich_vu_id', 'cauHois']);
        if (!isset( $this->dataPost['danh_muc_id'])){
            $this->dataPost['danh_muc_id'] = 68;
        }
        if ($this->dataPost['dich_vu_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền dich_vu_id");
        }
        $dichVu = DichVu::findOne($this->dataPost['dich_vu_id']);
        if (is_null($dichVu)) {
            throw new HttpException(403,"Không xác định dữ liệu");
        }
        $cauHois = $this->dataPost['cauHois'];
        DanhGiaBuoiHoc::deleteAll(['dich_vu_id'=>$dichVu->id,'danh_muc_id'=>$this->dataPost['danh_muc_id']]);
        foreach ($cauHois as $item) {
            $item = (object)$item;
            $cauHoi = new DanhGiaBuoiHoc();
            $cauHoi->dich_vu_id = $this->dataPost['dich_vu_id'];
            $cauHoi->user_id = $this->uid;
            $cauHoi->tieu_de = $item->tieu_de;
            $cauHoi->muc_do =$item->muc_do!=""? json_encode($item->muc_do):null;
            $cauHoi->goi_y = json_encode($item->goi_y);
            $cauHoi->nhan_xet = $item->nhan_xet;
            $cauHoi->danh_muc_id = $this->dataPost['danh_muc_id'];
            if (!$cauHoi->save()) {
                throw new HttpException(500, Html::errorSummary($cauHoi));
            }
        }
        return $this->outputSuccess("", "Cập nhật form đánh giá");
    }
    public function actionCapNhatFormChamSocTre()
    {
        $this->checkField(['dich_vu_id', 'cauHois']);

        if ($this->dataPost['dich_vu_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền dich_vu_id");
        }
        $dichVu = DichVu::findOne($this->dataPost['dich_vu_id']);
        if (is_null($dichVu)) {
            throw new HttpException(403,"Không xác định dữ liệu");
        }
        $cauHois = $this->dataPost['cauHois'];
        DanhGiaBuoiHoc::deleteAll(['dich_vu_id'=>$dichVu->id,'danh_muc_id'=>69]);
        foreach ($cauHois as $item) {
            $item = (object)$item;
            $cauHoi = new DanhGiaBuoiHoc();
            $cauHoi->dich_vu_id = $this->dataPost['dich_vu_id'];
            $cauHoi->muc_do =$item->muc_do!=""? json_encode($item->muc_do):null;
            $cauHoi->user_id = $this->uid;
            $cauHoi->tieu_de = $item->tieu_de;
            $cauHoi->goi_y = json_encode($item->goi_y);
            $cauHoi->nhan_xet = $item->nhan_xet;
            $cauHoi->danh_muc_id = 69;
            if (!$cauHoi->save()) {
                throw new HttpException(500, Html::errorSummary($cauHoi));
            }else{
                if (isset($item->buoi)){
                    $cauHoi->updateAttributes(['cac_buoi'=>json_encode($item->buoi)]);
                    foreach ($item->buoi as $buoi){
                        $mucDoBuoi = new DanhGiaBuoiHoc();
                        $mucDoBuoi->tieu_de = $buoi;
                        $mucDoBuoi->muc_do = json_encode($item->muc_do);
                        $mucDoBuoi->parent_id = $cauHoi->id;
                        $mucDoBuoi->danh_muc_id = 69;
                        $mucDoBuoi->user_id = $this->uid;
                        $mucDoBuoi->dich_vu_id = $this->dataPost['dich_vu_id'];
                        if (!$mucDoBuoi->save()) {
                            throw new HttpException(500, Html::errorSummary($mucDoBuoi));
                        }
                    }
                }
            }
        }
        return $this->outputSuccess("", "Cập nhật form chăm sóc trẻ thành công");
    }
    public function actionXoaForm(){
        $this->checkField(['dich_vu_id']);
        if ($this->dataPost['dich_vu_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền dich_vu_id");
        }
        $dichVu = DichVu::findOne($this->dataPost['dich_vu_id']);
        if (is_null($dichVu)) {
            throw new HttpException(403,"Không xác định dữ liệu");
        }
        DanhGiaBuoiHoc::deleteAll(['dich_vu_id'=>$dichVu->id,'danh_muc_id'=>68]);
        return $this->outputSuccess('',"Xóa form thành công");
    }
    public function actionDanhSach(){
        $buoiHoc = TienDoKhoaHoc::find()
            ->select([
                TienDoKhoaHoc::tableName().'.id as id',
                TienDoKhoaHoc::tableName().'.don_dich_vu_id as don_dich_vu_id',
                TienDoKhoaHoc::tableName().'.created as created',
                TienDoKhoaHoc::tableName().'.trang_thai as trang_thai',
                TienDoKhoaHoc::tableName().'.giao_vien_id as giao_vien_id',
                TienDoKhoaHoc::tableName().'.buoi as buoi',
                TienDoKhoaHoc::tableName().'.tong_buoi as tong_buoi',
                DonDichVu::tableName().'.dich_vu_id as dich_vu_id',
                TienDoKhoaHoc::tableName().'.thu as thu',
                TienDoKhoaHoc::tableName().'.ca_day_id as ca_day_id',
            ])
            ->leftJoin(DonDichVu::tableName(), DonDichVu::tableName().'.id='.TienDoKhoaHoc::tableName().'.don_dich_vu_id')
            ->leftJoin(User::tableName(), User::tableName().'.id='.TienDoKhoaHoc::tableName().'.giao_vien_id')
            ->andFilterWhere([TienDoKhoaHoc::tableName().'.active' => 1, TienDoKhoaHoc::tableName().'.trang_thai'=>TienDoKhoaHoc::DA_HOAN_THANH]);
        if ($this->tuKhoa != "") {
            $buoiHoc->andFilterWhere(['or',
                ['like','ma_don_hang',$this->tuKhoa],
                ['like','hoten',$this->tuKhoa],
            ]);
        }
        $count = count($buoiHoc->all());
        $buoiHoc = $buoiHoc->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy([TienDoKhoaHoc::tableName().'.created' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        $data = [];
        if (count($buoiHoc) > 0) {
            /** @var TienDoKhoaHoc $item */
            foreach ($buoiHoc as $item) {
                $giaoVien = $item->giaoVien;
                $data[] = [
                    'id'=>$item->id,
                    'ma_don_hang'=>$item->donDichVu->ma_don_hang,
                    'created'=>date("d/m/Y • H:i",strtotime($item->created)),
                    'trang_thai'=>$item->getTrangThaiID(),
                    'giaoVien'=>is_null($giaoVien)?null:[
                        'id'=>$giaoVien->id,
                        'hoten'=>$giaoVien->hoten,
                        'anh_nguoi_dung'=>$giaoVien->getImage(),
                        'trinh_do'=>$giaoVien->getTrinhDo(),
                        'dien_thoai'=>$giaoVien->dien_thoai,
                    ],
                    'buoi'=>$item->buoi,
                    'tong_buoi'=>$item->tong_buoi,
                    'dichVu'=>$item->donDichVu->dichVu->ten_dich_vu,
                    'lich_hoc'=>$item->donDichVu->getNamebyThu(),
                    'chonCa'=>$item->getCaDayName()
                ];
            }
        }
        return $this->outputListSuccess2($data, $count);
    }
    public function actionChiTietNhanXet()
    {
        $this->checkGetInput(['ca_day_id']);
        if ($this->dataGet['ca_day_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền ca_day_id");
        }
        $tienDo = TienDoKhoaHoc::findOne($this->dataGet['ca_day_id']);
        if (is_null($tienDo)) {
            throw new HttpException(403,"Không xác định dữ liệu");
        }
        if ($tienDo->trang_thai !== TienDoKhoaHoc::DA_HOAN_THANH) {
            throw new HttpException(500, "Ca dạy chưa hoàn thành");
        }
        $donDichVu = $tienDo->donDichVu;
        $giaoVien = $tienDo->giaoVien;
        return $this->outputSuccess([
            'id' => $donDichVu->id,
            'ma_don_hang' => $donDichVu->ma_don_hang,
            'giaoVien' => is_null($giaoVien) ? null : [
                'id' => $giaoVien->id,
                'anh_nguoi_dung' => $giaoVien->getImage(),
                'hoten' => $giaoVien->hoten,
                'trinh_do' => $giaoVien->getTrinhDo(),
            ],
            'tienDo' => [
                'buoi' => $tienDo->buoi,
                'thoi_gian' => $tienDo->getTimeSuccess(),
                'nhan_xet_buoi_hoc' => $tienDo->nhan_xet_buoi_hoc,
                'image' => CauHinh::getImage($tienDo->image),
                'video' => $tienDo->video,
                'danh_gia' => $tienDo->danh_gia,
                'phu_huynh_danh_gia'=>$tienDo->phu_huynh_danh_gia,
                'phu_huynh_nhan_xet'=>$tienDo->phu_huynh_nhan_xet,
            ],
            'formDanhGia'=>$tienDo->getFormDanhGia()
        ]);
    }
}
