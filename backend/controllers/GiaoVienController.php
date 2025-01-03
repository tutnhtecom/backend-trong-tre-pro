<?php namespace backend\controllers;

use backend\models\CauHinh;
use backend\models\ChungChi;
use backend\models\DanhMuc;
use backend\models\GiaoDich;
use backend\models\KetQuaDaoTao;
use backend\models\LichSuViecLamGiaoVien;
use backend\models\QuanLyKetQuaDaoTao;
use backend\models\QuanLyUserVaiTro;
use common\models\myAPI;
use common\models\User;
use Yii;
use yii\bootstrap\Html;
use yii\helpers\FileHelper;
use yii\web\HttpException;
use yii\web\UploadedFile;

class GiaoVienController extends CoreApiController
{
    public function actionDanhSach()
    {
        $trinhDo = $this->getDanhMuc(DanhMuc::TRINH_DO);
        $this->checkGetInput(['tuKhoa', 'trinh_do']);
        $users = QuanLyUserVaiTro::find()
            ->select(['hoten', 'id', 'anh_nguoi_dung', 'danh_gia', 'trinh_do', 'khoa_tai_khoan'])
            ->andFilterWhere(['active' => 1, 'is_admin' => 0, 'status' => 10, 'vai_tro' => User::GIAO_VIEN]);
        if ($this->dataGet['tuKhoa'] != "") {
            $users->andFilterWhere(['or',
                ['like', 'hoten', $this->dataGet['tuKhoa']],
                ['like', 'dien_thoai', $this->dataGet['tuKhoa']]
            ]);
        }
        if ($this->dataGet['trinh_do'] != "") {
            $users->andFilterWhere(['trinh_do' => $this->dataGet['trinh_do']]);
        }
        $count = count($users->all());
        $users = $users->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy(['created_at' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        $data = [];
        if (count($users) > 0) {
            foreach ($users as $item) {
                $trinhDoName = DanhMuc::findOne($item->trinh_do);
                $item->trinh_do = is_null($trinhDoName) ? "" : $trinhDoName->name;
                $item->anh_nguoi_dung = CauHinh::getServer() . '/upload-file/' . ($item->anh_nguoi_dung == null ? "user-nomal.jpg" : $item->anh_nguoi_dung);
                $data[] = $item;
            }
        }
        return $this->outputListSuccess2([
            'users' => $data,
            'trinhDo' => $trinhDo
        ], $count);
    }

    public function actionChiTiet()
    {
        $this->checkGetInput(['id']);
        if ($this->dataGet['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        /** @var QuanLyUserVaiTro $user */
        $user = QuanLyUserVaiTro::find()->select([
            'anh_nguoi_dung', 'id', 'danh_gia', 'hoten', 'trinh_do_name', 'trinh_do', 'gioi_tinh', 'vi_dien_tu', 'dien_thoai', 'ngay_sinh', 'cmnd_cccd', 'bang_cap', 'email', 'dia_chi',
            'ghi_chu', 'khoa_tai_khoan', 'dich_vu'
        ])->andFilterWhere(['id' => $this->dataGet['id'], 'active' => 1, 'status' => 10, 'vai_tro' => User::GIAO_VIEN])->one();
        if (is_null($user)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $chungNhan = ChungChi::find()->select(['id', 'file_path'])->andFilterWhere(['user_id' => $this->dataGet['id'], 'active' => 1])->all();
        foreach ($chungNhan as $item) {
            $item->file_path = CauHinh::getServer() . '/upload-file/' . ($item->file_path == null ? "user-nomal.jpg" : $item->file_path);
        }
        $user->dich_vu = explode(',', $user->dich_vu);
        $dichVu = [];
        foreach ($user->dich_vu as $item) {
            $dichVu[] = intval($item);
        }
        $user->dich_vu = $dichVu;
        $user->anh_nguoi_dung = $user->getImage();
        $arrBangCap = json_decode($user->bang_cap);
        $user->bang_cap = $user->getBangCap();
        $user->ngay_sinh = $user->ngay_sinh == null ? "" : date('d/m/Y', strtotime($user->ngay_sinh));
        return $this->outputSuccess([
            'user' => $user,
            'chungNhan' => $chungNhan,
            'bangCap' => $arrBangCap
        ]);
    }

    public function actionKhoaTaiKhoan()
    {
        $this->checkField(['id']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $user = User::findOne($this->dataPost['id']);
        if (is_null($user)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $user->updateAttributes(['khoa_tai_khoan' => 1]);
        return $this->outputSuccess('', 'Khóa tài khoản thành công');
    }

    public function actionKichHoatTaiKhoan()
    {
        $this->checkField(['id']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $user = User::findOne($this->dataPost['id']);
        if (is_null($user)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $user->updateAttributes(['khoa_tai_khoan' => 0]);
        return $this->outputSuccess('', 'Kích hoạt tài khoản thành công');
    }

    public function actionSua()
    {
        $this->checkField(['id']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $user = User::findOne($this->dataPost['id']);
        if (is_null($user)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if (isset($this->dataPost['hoten'])) {
            if ($this->dataPost['hoten'] != "") {
                $fields['hoten'] = $this->dataPost['hoten'];
            }
        }
        if (isset($this->dataPost['ngay_sinh'])) {
            if ($this->dataPost['ngay_sinh'] != "") {
                $fields['ngay_sinh'] = $this->dataPost['ngay_sinh'];
            }
        }
        if (isset($this->dataPost['cmnd_cccd'])) {
            if ($this->dataPost['cmnd_cccd'] != "") {
                $fields['cmnd_cccd'] = $this->dataPost['cmnd_cccd'];
            }
        }
        if (isset($this->dataPost['email'])) {
            if ($this->dataPost['email'] != "") {
                if (!$this->validateEmail($this->dataPost['email'])) {
                    throw new HttpException(500, 'Định dạnh email không hợp lệ');
                }
                $userEmail = QuanLyUserVaiTro::find()->andFilterWhere(['email' => $this->dataPost['email'], 'status' => 10, 'vai_tro' => User::GIAO_VIEN])->andFilterWhere(['<>', 'id', $user->id])->one();
                if (!is_null($userEmail)) {
                    throw new HttpException(500, 'Email đã tồn tại');
                }
                $fields['email'] = $this->dataPost['email'];
            }
        }
        if (isset($this->dataPost['bang_cap'])) {
            if (!isset($this->dataPost['bang_cap']['trinh_do']) ||
                !isset($this->dataPost['bang_cap']['chuyen_nganh']) ||
                !isset($this->dataPost['bang_cap']['truong_dao_tao'])
            ) {
                throw new HttpException(500, 'Định dạng trường bằng cấp không hợp lệ');
            }
            $fields['bang_cap'] = json_encode($this->dataPost['bang_cap']);
        }
        if (isset($this->dataPost['password'])) {
            $this->checkField(['password_confirm']);
            if ($this->dataPost['password'] != "") {
                if ($this->dataPost['password_confirm'] == $this->dataPost['password']) {
                    $fields['password_hash'] = Yii::$app->security->generatePasswordHash($this->dataPost['password']);
                } else {
                    throw new HttpException(500, "Xác nhận mật khẩu không chính xác");

                }
            }
        }
        $file = UploadedFile::getInstanceByName('anh_nguoi_dung');
        if (!empty($file)) {
            $path = (dirname(dirname(__DIR__))) . '/upload-file';
            $link = date('Y/m/d') . '/' . '_' . \Yii::$app->security->generateRandomString() . $this->get_extension_image($file->type);
            $fields['anh_nguoi_dung'] = $link;
            if (FileHelper::createDirectory($path . '/' . date('Y/m/d') . '/', $mode = 0775, $recursive = true)) {
                $file->saveAs($path . '/' . $link);
            }
        }
        $files = UploadedFile::getInstancesByName('chung_chi');
        if (count($files) > 0) {
            foreach ($files as $file) {
                $path = (dirname(dirname(__DIR__))) . '/upload-file';
                $link = date('Y/m/d') . '/' . '_' . \Yii::$app->security->generateRandomString() . $this->get_extension_image($file->type);
                if (FileHelper::createDirectory($path . '/' . date('Y/m/d') . '/', $mode = 0775, $recursive = true)) {
                    $file->saveAs($path . '/' . $link);
                }
                //Lưu tên file anh chứng chỉ
                $chungChi = new ChungChi();
                $chungChi->user_id = $user->id;
                $chungChi->file_path = $link;
                if (!$chungChi->save()) {
                    throw new HttpException(500, Html::errorSummary($chungChi));
                }
            }
        }

        $user->updateAttributes($fields);
        return $this->outputSuccess('', 'Sửa thông tin thành công');
    }

    public function actionCapNhatDichVu()
    {
        $this->checkField(['id', 'dich_vu']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $user = User::findOne($this->dataPost['id']);
        if (is_null($user)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($this->dataPost['dich_vu'] == "") {
            throw new HttpException(500, "Vui lòng chọn dịch vụ");
        }
        $user->updateAttributes(['dich_vu' => $this->dataPost['dich_vu']]);
        return $this->outputSuccess('', 'Cập nhật dịch vụ thành công');
    }

    public function actionCapNhatTrinhDo()
    {
        $this->checkField(['id', 'trinh_do']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $user = User::findOne($this->dataPost['id']);
        if (is_null($user)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($this->dataPost['trinh_do'] == "") {
            throw new HttpException(500, "Vui lòng chọn trình độ");
        }
        $user->updateAttributes(['trinh_do' => $this->dataPost['trinh_do']]);
        return $this->outputSuccess('', 'Cập nhật trình độ thành công');
    }

    public function actionGetTrinhDo()
    {
        $trinhDo = $this->getDanhMuc(DanhMuc::TRINH_DO);
        return $this->outputSuccess($trinhDo);
    }

    public function actionLichSuDonDay()
    {
        $this->checkGetInput(['giao_vien_id']);
        if ($this->dataGet['giao_vien_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền giao_vien_id");
        }
        $giaoVien = User::findOne($this->dataGet['giao_vien_id']);
        if (is_null($giaoVien)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $donDichVu = LichSuViecLamGiaoVien::find()
            ->andFilterWhere(['giao_vien_id' => $this->dataGet['giao_vien_id'], 'active' => 1])
            ->orderBy(['created' => $this->sort == 1 ? SORT_DESC : SORT_ASC]);
        $count = $donDichVu->count();
        $donDichVu = $donDichVu->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy(['created' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        $data = [];
        if (count($donDichVu) > 0) {
            foreach ($donDichVu as $item) {
                /** @var $item LichSuViecLamGiaoVien */
                $value = $item->donDichVu;
                $phuHuynh = $value->phuHuynh;
                $giaoVien = $item->giaoVien;
                $data[] = [
                    'id' => $value->id,
                    'ma_don_hang' => $value->ma_don_hang,
                    'created' => date("d/m/Y • H:i", strtotime($value->created)),
                    'trang_thai' => $value->trang_thai,
                    'phuHuynh' => [
                        'id' => $phuHuynh->id,
                        'anh_nguoi_dung' => $phuHuynh->getImage(),
                        'hoten' => $phuHuynh->hoten,
                        'dien_thoai' => $phuHuynh->dien_thoai,
                        'vai_tro' => "Phụ huynh",
                    ],
                    'ten_dich_vu' => $value->dichVu->ten_dich_vu,
                    'chonCa' => $value->getCaDayName(),
                    'dia_chi' => $value->dia_chi,
                    'giaoVien' => is_null($giaoVien) ? null : [
                        'id' => $giaoVien->id,
                        'hoten' => $giaoVien->hoten,
                        'dien_thoai' => $giaoVien->dien_thoai,
                        'anh_nguoi_dung' => $giaoVien->getImage(),
                        'trinh_do' => $giaoVien->getTrinhDo()
                    ],
                ];
            }
        }
        return $this->outputListSuccess2($data, $count);
    }

    public function actionNapTien()
    {
        $this->checkField(['id', 'so_tien', 'ghi_chu', 'type_id']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $user = User::findOne($this->dataPost['id']);
        if (is_null($user)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $giaoDich = new GiaoDich();
        $giaoDich->so_tien = $this->dataPost['so_tien'];
        $giaoDich->ghi_chu = $this->dataPost['ghi_chu'];
        $giaoDich->type = GiaoDich::NAP_TIEN;
        $giaoDich->user_id = $this->dataPost['id'];
        $giaoDich->type_id = $this->dataPost['type_id'];
        $giaoDich->created = isset($this->dataPost['ngay_nhap'])?$this->dataPost['ngay_nhap']:null;
        $giaoDich->tieu_de = "Nạp tiền vào tài khoản";
        $giaoDich->no_deposit_withdrawal = "NO_DEPOSIT_WITHDRAWAL";
        if (!$giaoDich->save()) {
            throw new HttpException(500, Html::errorSummary($giaoDich));
        }
        return $this->outputSuccess("", "Nạp tiền thành công");
    }

    public function actionRutTien()
    {
        $this->checkField(['id', 'so_tien', 'ghi_chu', 'type_id']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $user = User::findOne($this->dataPost['id']);
        if (is_null($user)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $giaoDich = new GiaoDich();
        $giaoDich->so_tien = $this->dataPost['so_tien'];
        $giaoDich->ghi_chu = $this->dataPost['ghi_chu'];
        $giaoDich->type = GiaoDich::RUT_TIEN;
        $giaoDich->type_id = $this->dataPost['type_id'];
        $giaoDich->user_id = $this->dataPost['id'];
        $giaoDich->tieu_de = "Trừ tiền vào tài khoản";
        $giaoDich->created = isset($this->dataPost['ngay_nhap'])?$this->dataPost['ngay_nhap']:null;
        $giaoDich->no_deposit_withdrawal = "NO_DEPOSIT_WITHDRAWAL";
        if (!$giaoDich->save()) {
            throw new HttpException(500, Html::errorSummary($giaoDich));
        }
        return $this->outputSuccess("", "Rút tiền thành công");
    }

    public function actionDanhSachKetQuaDaoTao()
    {
        $this->checkGetInput(['giao_vien_id']);
        if ($this->dataGet['giao_vien_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền giao_vien_id");
        }
        $giaoVien = User::findOne($this->dataGet['giao_vien_id']);
        if (is_null($giaoVien)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $ketQua = QuanLyKetQuaDaoTao::find()
            ->andFilterWhere(['active' => 1, 'giao_vien_id' => $giaoVien->id]);
        if ($this->tuKhoa != "") {
            $ketQua->andFilterWhere(['or',
                ['like', 'hocPhan', $this->tuKhoa],
                ['like', 'baiHoc', $this->tuKhoa],
            ]);
        }
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

    public function actionXoaChungChi()
    {
        $this->checkField(['giao_vien_id']);
        if ($this->dataPost['giao_vien_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền giao_vien_id");
        }
        $giaoVien = User::findOne($this->dataPost['giao_vien_id']);
        if (is_null($giaoVien)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        ChungChi::deleteAll(['user_id' => $this->dataPost['giao_vien_id']]);
        return $this->outputSuccess('', 'Xóa chứng chỉ giáo viên thành công');
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

    public function actionLoaiGiaoDichNapTien()
    {
        return $this->outputSuccess($this->getDanhMuc(DanhMuc::NAP_TIEN));
    }

    public function actionLoaiGiaoDichTruTien()
    {
        return $this->outputSuccess($this->getDanhMuc(DanhMuc::TRU_TIEN));
    }

    public function actionGetVi()
    {
        $this->checkGetInput(['giao_vien_id']);
        $user = User::findOne($this->dataGet['giao_vien_id']);
        return $this->outputSuccess([
            'id' => $user->id,
            'anh_nguoi_dung' => $user->getImage(),
            'hoten' => $user->hoten,
            'vi_dien_tu' => $user->vi_dien_tu,
            'chu_tai_khoan' => $user->chu_tai_khoan,
            'so_tai_khoan' => $user->so_tai_khoan,
            'ten_ngan_hang' => $user->ten_ngan_hang,
        ]);
    }
    public function actionLichSuTaiChinh()
    {
        $this->checkGetInput(['id']);
        $giaoDich = GiaoDich::find()
            ->andFilterWhere(['active' => 1, 'user_id' => $this->dataGet['id']]);
        if ($this->tuKhoa != "") {
            $giaoDich->andFilterWhere(['like', 'tieu_de', $this->tuKhoa]);
        }
        $count = count($giaoDich->all());
        $giaoDich = $giaoDich->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy(['created' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        $data = [];
        if (count($giaoDich) > 0) {
            foreach ($giaoDich as $item) {
                /* @var $item GiaoDich */
                $taiKhoan = '*******' . substr($item->user->dien_thoai, strlen($item->user->dien_thoai) - 3, strlen($item->user->dien_thoai));
                $data[] = [
                    'id' => $item->id,
                    'created' => date('d/m/Y • H:i', strtotime($item->created)),
                    'type' => $item->type,
                    'tieu_de' => $item->tieu_de,
                    'so_tien' => $item->so_tien,
                    'noi_dung' => !is_null($item->donDichVu) ? $item->donDichVu->ma_don_hang : $taiKhoan,
                    'vi_dien_tu' => $item->vi_dien_tu,
                    'ghi_chu' => $item->ghi_chu,
                ];
            }
        }
        $user = User::findOne($this->dataGet['id']);
        return $this->outputListSuccess2([
            'giaoDich' => $data,
            'id' => $user->id,
            'hoten' => $user->hoten,
            'anh_nguoi_dung' => $user->getImage(),
            'vi_dien_tu' => $user->vi_dien_tu,
        ], $count);
    }

}
