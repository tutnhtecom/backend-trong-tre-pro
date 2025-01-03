<?php namespace backend\controllers;

use backend\models\BaiHoc;
use backend\models\BanGiao;
use backend\models\CauHinh;
use backend\models\ChuongTrinhHoc;
use backend\models\DanhMuc;
use backend\models\DichVu;
use backend\models\DonDichVu;
use backend\models\GiaDichVu;
use backend\models\GiaHanDon;
use backend\models\GiaoDich;
use backend\models\GoiHoc;
use backend\models\HoaDon;
use backend\models\LichSuTrangThaiDon;
use backend\models\LichSuTrangThaiThanhToan;
use backend\models\NhanLich;
use backend\models\PhuPhi;
use backend\models\PhuPhiDichVu;
use backend\models\QuanLyDonDichVu;
use backend\models\QuanLyKetQuaDaoTao;
use backend\models\QuanLyUserVaiTro;
use backend\models\ThongBao;
use backend\models\TienDoKhoaHoc;
use backend\models\VaiTro;
use common\models\exportExcelDonDichVu;
use common\models\myAPI;
use common\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\web\HttpException;
use yii\web\Response;

class DonDichVuController extends CoreApiController
{
    public $limit = 10;
    public $leader_kd = 12;

    public function actionDanhSach()
    {
        $this->checkGetInput(['tuKhoa', 'giaoVien', 'thang']);
        $donDichVu = QuanLyDonDichVu::find();
        if ($this->dataGet['tuKhoa'] != "") {
            $donDichVu->andFilterWhere(['or',['like', 'ma_don_hang', $this->dataGet['tuKhoa']],['like', 'hoten_phu_huynh', $this->dataGet['tuKhoa']]]);
        }
        if ($this->dataGet['giaoVien'] != "") {
            $donDichVu->andFilterWhere(['like', 'ho_ten_giao_vien', $this->dataGet['giaoVien']]);
        }
        if (isset($this->dataGet['dich_vu_id'])) {
            if ($this->dataGet['dich_vu_id'] != "") {
                $donDichVu->andFilterWhere(['dich_vu_id' => $this->dataGet['dich_vu_id']]);
            }
        }
        if (isset($this->dataGet['trang_thai'])) {
            if ($this->dataGet['trang_thai'] != "") {
                $donDichVu->andFilterWhere(['trang_thai' => $this->dataGet['trang_thai']]);
            }
        }
        if (isset($this->dataGet['phu_huynh_id'])) {
            if ($this->dataGet['phu_huynh_id'] != "") {
                $donDichVu->andFilterWhere(['phu_huynh_id' => $this->dataGet['phu_huynh_id']]);
            }
        }

        if ($this->dataGet['thang'] != "") {
            $thang = explode('/', $this->dataGet['thang']);
            if (count($thang) != 2) {
                throw new HttpException(500, "Định dạng tháng không hợp lệ.");
            }
            $donDichVu->andFilterWhere(['=', 'month(' . QuanLyDonDichVu::tableName() . '.created)', $thang[0]]);
            $donDichVu->andFilterWhere(['=', 'year(' . QuanLyDonDichVu::tableName() . '.created)', $thang[1]]);
        }
        $user = QuanLyUserVaiTro::findOne(['id' => $this->uid]);
        if ($user->vai_tro == $this->leader_kd) {
            $donDichVu->andFilterWhere(['leader_kd_id' => $this->uid]);
        }
        $count = $donDichVu->count();
        $donDichVu = $donDichVu->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy(['created' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        $data = [];
        if (count($donDichVu) > 0) {
            foreach ($donDichVu as $item) {
                /**@var $item QuanLyDonDichVu */
                $donDV = DonDichVu::findOne($item->id);
                $buoiHienTai = $donDV->getBuoiHienTai();

                $data [] = [
                    'id' => $item->id,
                    'ma_hoa_don' => $item->ma_don_hang,
                    'gio_bat_dau' => $item->gio_bat_dau,
                    'çreated' => date('d/m/Y • H:i', strtotime($item->created)),
                    'trang_thai' => $item->trang_thai,
                    'soBuoiHoanThanh' => $donDV->soBuoiGiaoVienHoanThanh(),
                    'so_buoi' => $item->so_buoi,
                    'buoi_hien_tai' => $buoiHienTai >= $donDV->so_buoi ? $donDV->so_buoi - 1 : $buoiHienTai,
                    'phuHuynh' => is_null($item->phu_huynh_id) ? null : [
                        'id' => $item->phu_huynh_id,
                        'ho_ten' => $item->hoten_phu_huynh,
                        'anh_nguoi_dung' => CauHinh::getImage($item->anh_dai_dien_phu_huynh),
                        'vai_tro' => (new User())->getVaiTro(),
                        'dien_thoai' => $item->dien_thoai_phu_huynh,
                    ],
                    'dichVu' => $item->ten_dich_vu,
                    'chonCa' => $item->chon_ca,
                    'dia_chi' => $item->dia_chi,
                    'giaoVien' => is_null($item->giao_vien_id) ? null : [
                        'id' => $item->giao_vien_id,
                        'ho_ten' => $item->ho_ten_giao_vien,
                        'anh_nguoi_dung' => CauHinh::getImage($item->anh_nguoi_dung_giao_vien),
                        'trinh_do' => $item->trinh_do_giao_vien,
                        'dien_thoai' => $item->dien_thoai_giao_vien,
                    ],

                ];
            }
        }
        return $this->outputListSuccess2($data, $count);
    }

    public function actionXuatExcel()
    {
        $this->checkGetInput(['tuKhoa', 'giaoVien', 'thang']);
        $donDichVu = QuanLyDonDichVu::find();
        if ($this->dataGet['tuKhoa'] != "") {
            $donDichVu->andFilterWhere(['like', 'ma_don_hang', $this->dataGet['tuKhoa']]);
        }
        if ($this->dataGet['giaoVien'] != "") {
            $donDichVu->andFilterWhere(['like', 'ho_ten_giao_vien', $this->dataGet['giaoVien']]);
        }
        if (isset($this->dataPost['dich_vu_id'])) {
            if ($this->dataGet['dich_vu_id'] != "") {
                $donDichVu->andFilterWhere(['dich_vu_id' => $this->dataGet['dich_vu_id']]);
            }
        }
        if (isset($this->dataPost['trang_thai'])) {
            if ($this->dataGet['trang_thai'] != "") {
                $donDichVu->andFilterWhere(['trang_thai' => $this->dataGet['trang_thai']]);
            }
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
        $user = QuanLyUserVaiTro::findOne(['id' => $this->uid]);
        if ($user->vai_tro !== User::ADMIN) {
            $donDichVu->andFilterWhere(['leader_kd_id' => $this->uid]);
        }
        $donDichVu = $donDichVu->andFilterWhere(['=', 'month(' . QuanLyDonDichVu::tableName() . '.created)', $thang]);
        $donDichVu = $donDichVu->andFilterWhere(['=', 'year(' . QuanLyDonDichVu::tableName() . '.created)', $nam]);
        $donDichVu = $donDichVu->all();
        $export = new exportExcelDonDichVu();
        $export->data = [
            'data' => $donDichVu,
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

    public function actionTrangThaiDon()
    {
        $trangThai = $this->getDanhMuc(DanhMuc::TRANG_THAI_DON, ['id', 'name']);
        return $this->outputSuccess($trangThai);
    }

    public function actionChiTiet()
    {
        $this->checkGetInput(['id']);
        if ($this->dataGet['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $donDichVu = DonDichVu::findOne($this->dataGet['id']);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $don_dich_vu_id = $donDichVu->id;
        $tienDoKhoaHocChuaDay = TienDoKhoaHoc::find()->andFilterWhere([TienDoKhoaHoc::tableName() . '.don_dich_vu_id' => $don_dich_vu_id])
                    ->andFilterWhere([TienDoKhoaHoc::tableName() . '.trang_thai' => TienDoKhoaHoc::CHUA_DAY])
                    ->orderBy([
                        'buoi' => SORT_ASC,
                      ])->all();
        $anTrua = PhuPhi::findOne(['don_dich_vu_id' => $donDichVu->id, 'type_id' => DanhMuc::AN_TRUA]);
        $themGio = PhuPhi::findOne(['don_dich_vu_id' => $donDichVu->id, 'type_id' => DanhMuc::THEM_GIO]);
        $leaderKd = QuanLyUserVaiTro::findOne(['id' => $donDichVu->leader_kd_id]);
        $giaoVien = $donDichVu->giaoVien;
        $banGiaoGiaoCu = BanGiao::find()
            ->andFilterWhere(['giao_vien_id' => $this->uid])
            ->andFilterWhere(['don_dich_vu_id' => $donDichVu->id])
            ->all();
        $dataBanGiao = [];
        if (count($banGiaoGiaoCu) > 0) {
            /** @var BanGiao $value */
            foreach ($banGiaoGiaoCu as $value) {
                $giaoVienBanGiao = $value->giaoVien;
                $dataBanGiao [] = [
                    'id' => $value->id,
                    'giaoVien' => is_null($giaoVienBanGiao) ? null : [
                        'id' => $giaoVienBanGiao->id,
                        'hoten' => $giaoVienBanGiao->hoten,
                        'anh_nguoi_dung' => $giaoVienBanGiao->getImage(),
                        'trinh_do' => $giaoVienBanGiao->getTrinhDo(),
                        'dien_thoai' => $giaoVienBanGiao->dien_thoai,
                    ],
                    'ngay_nhan' => date('d/m/Y', strtotime($value->ngay_nhan)),
                    'codeGiaoCu' => $value->getCodeGiaoCu(),
                    'ngay_tra' => is_null($value->ngay_tra) ? null : date("d/m/Y", strtotime($value->ngay_tra)),
                    'ghi_chu' => $value->ghi_chu,
                ];
            }
        }
        $buoiHienTai = $donDichVu->getBuoiHienTai();
        $data = [
            'id' => $donDichVu->id,
            'ma_don_hang' => $donDichVu->ma_don_hang,
            'created' => date('d/m/Y • H:i', strtotime($donDichVu->created)),
            'trang_thai' => $donDichVu->trang_thai,
            'loai_giao_vien' => $donDichVu->loai_giao_vien,
            'loai_giao_vien_name' => $donDichVu->loai_giao_vien == 26 ? 'Chuyên viên' : 'Nhân viên',
            'phuHuynh' => [
				'id' => $donDichVu->phuHuynh->id,
                'hoten' => $donDichVu->phuHuynh->hoten,
                'dien_thoai' => $donDichVu->phuHuynh->dien_thoai,
                'vai_tro' => $donDichVu->phuHuynh->getVaiTro(),
                'anh_nguoi_dung' => $donDichVu->phuHuynh->getImage(),
            ],
            'dia_chi' => $donDichVu->dia_chi,
            'ghi_chu' => $donDichVu->ghi_chu,
            'noi_dung_khao_sat' => $donDichVu->noi_dung_khao_sat,
            'dichVu' => $donDichVu->dichVu->ten_dich_vu,
            'so_luong_be' => $donDichVu->so_luong_be,
            'so_tien_hoan' => $donDichVu->so_tien_hoan,
            'so_buoi_hoan' => $donDichVu->so_buoi_hoan,
            'anTrua' => is_null($anTrua) ? null : [
                'ghi_chu' => $anTrua->ghi_chu,
                'tong_tien' => $anTrua->tong_tien,
                'tieu_de' => $anTrua->tieu_de
            ],
            'themGio' => is_null($themGio) ? null : [
                'ghi_chu' => $themGio->ghi_chu,
                'tong_tien' => $themGio->tong_tien,
                'tieu_de' => $themGio->tieu_de
            ],
            'lich_hoc' => $donDichVu->getNamebyThu(),
            'thoi_gian' => $donDichVu->getThoiGian(),
            'chonCa' => $donDichVu->chonCa->type0->name,
            'khungGio' => $donDichVu->chonCa->khungGio->name . ' - ' . date('H:i', strtotime($donDichVu->gio_bat_dau)),
            'gio_bat_dau' => $donDichVu->gio_bat_dau,
            'soBuoiHoanThanh' => $donDichVu->getTrangThaiTienDo(),
            'hinh_thuc_thanh_toan' => isset($donDichVu->hinhThucThanhToan->name) ? $donDichVu->hinhThucThanhToan->name : null,
            'ghi_chu_thanh_toan' => CauHinh::getImage($donDichVu->ghi_chu_thanh_toan),
            'trang_thai_thanh_toan' => $donDichVu->trang_thai_thanh_toan,
            'hoc_phi' => $donDichVu->hoc_phi,
            'phu_cap' => $donDichVu->phu_cap,
            'tongTien' => $donDichVu->tong_tien,
            'leaderKD' => is_null($leaderKd) ? null : [
                'id' => $leaderKd->id,
                'hoten' => $leaderKd->hoten,
                'dien_thoai' => $leaderKd->dien_thoai,
                'vai_tro' => $leaderKd->vai_tro_name,
                'anh_nguoi_dung' => CauHinh::getImage($leaderKd->anh_nguoi_dung),
            ],
            'giaoVien' => is_null($giaoVien) ? null : [
                'hoten' => $giaoVien->hoten,
                'dien_thoai' => $giaoVien->dien_thoai,
                'trinh_do' => $giaoVien->trinh_do,
                'danh_gia' => $giaoVien->danh_gia,
                'anh_nguoi_dung' => $giaoVien->getImage(),
            ],
            'phuPhi' => PhuPhi::find()->select(['id', 'ghi_chu', 'tieu_de', 'tong_tien'])
                ->andFilterWhere(['active' => 1, 'don_dich_vu_id' => $donDichVu->id])
                ->andFilterWhere(['not in', 'type_id', [DanhMuc::AN_TRUA, DanhMuc::THEM_GIO]])
                ->all(),
            'li_do_huy' => $donDichVu->li_do_huy,
            'ke_hoach_day' => $donDichVu->getChuongTrinhDay(),
            'chuong_trinh_hoc_id' => $donDichVu->chuong_trinh_hoc_id,
            'dich_vu_id' => $donDichVu->dich_vu_id,
            'banGiao' => $dataBanGiao,
            'gia_han_don' => $donDichVu->giaHanDon(),
            'lich_hoc_arr' => explode(',', $donDichVu->thu),
            'buoi_hien_tai' => $buoiHienTai >= $donDichVu->so_buoi ? $donDichVu->so_buoi - 1 : $buoiHienTai,
            'tienDoKhoaHocChuaDay' => $tienDoKhoaHocChuaDay

        ];
        return $this->outputSuccess($data);

    }

    public function actionDuyetDon()
    {
        $this->checkField(['id', 'leader_kd_id']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $donDichVu = DonDichVu::findOne(['id' => $this->dataPost['id']]);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($donDichVu->trang_thai != LichSuTrangThaiDon::CHUA_CO_GIAO_VIEN) {
            throw new HttpException(500, "Đơn hàng đã được duyệt, vui lòng kiểm tra lại!");
        }
        $user = QuanLyUserVaiTro::findOne(['active' => 1, 'status' => 10, 'is_admin' => 1, 'vai_tro' => $this->leader_kd, 'id' => $this->dataPost['leader_kd_id']]);
        if (is_null($user)) {
            throw new HttpException(400, "Không xác định leader kinh doanh");
        }
        if (isset($this->dataPost['noi_dung_khao_sat'])) {
            if ($this->dataPost['noi_dung_khao_sat'] != "") {
                $donDichVu->noi_dung_khao_sat = $this->dataPost['noi_dung_khao_sat'];
            }
        }
        $donDichVu->leader_kd_id = $this->dataPost['leader_kd_id'];
        if (!$donDichVu->save()) {
            throw new HttpException(500, Html::errorSummary($donDichVu));
        }
        $email = CauHinh::findOne(2)->ghi_chu;
        $emailAdmin = CauHinh::findOne(39)->content;
        $this->sendEMail('Trông trẻ Pro', $email, $emailAdmin, 'Leader KD', 'Duyệt đơn thành công', "Mã đơn hàng $donDichVu->ma_don_hang duyệt thành công và được gán bởi learder kinh doanh: {$donDichVu->leaderKd->hoten}");
        $this->sendEMail('Trông trẻ Pro', $email, $donDichVu->leaderKd->email, 'Leader KD', 'Duyệt đơn thành công', "Mã đơn hàng $donDichVu->ma_don_hang duyệt thành công và đã được gán thành công cho learder kinh doanh: {$donDichVu->leaderKd->hoten}");
        return $this->outputSuccess('', 'Duyệt đơn hàng thành công');
    }

    public function actionCapNhatNoiDungKhaoSat()
    {
        $this->checkField(['id', 'noi_dung_khao_sat']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $donDichVu = DonDichVu::findOne(['id' => $this->dataPost['id']]);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($donDichVu->trang_thai != LichSuTrangThaiDon::CHUA_CO_GIAO_VIEN && $donDichVu->trang_thai != LichSuTrangThaiDon::DANG_KHAO_SAT) {
            throw new HttpException(500, "Đơn hàng đã được duyệt, vui lòng kiểm tra lại!");
        }
        if ($this->dataPost['noi_dung_khao_sat'] == "") {
            throw new HttpException(400, "Vui lòng nhập nội dung đánh giá");
        }
        $donDichVu->noi_dung_khao_sat = $this->dataPost['noi_dung_khao_sat'];
        if (!$donDichVu->save()) {
            throw new HttpException(500, Html::errorSummary($donDichVu));
        }
        return $this->outputSuccess('', 'Cập nhật nội dung khảo sát thành công');
    }

    public function actionLeaderKinhDoanh()
    {
        $users = QuanLyUserVaiTro::find()
            ->select(['hoten', 'id'])
            ->andFilterWhere(['active' => 1, 'status' => 10, 'is_admin' => 1, 'vai_tro' => $this->leader_kd])->all();
        return $this->outputSuccess($users);
    }

    public function actionHuyDon()
    {
        $this->checkField(['id', 'li_do_huy']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $donDichVu = DonDichVu::findOne(['id' => $this->dataPost['id']]);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }

        $donDichVu->trang_thai = LichSuTrangThaiDon::DA_HUY;
        $donDichVu->li_do_huy = $this->dataPost['li_do_huy'];
        if (!$donDichVu->save()) {
            throw new HttpException(500, Html::errorSummary($donDichVu));
        }
        return $this->outputSuccess('', 'Hủy đơn hàng thành công');
    }

    public function actionHoanDon()
    {
        $this->checkField(['id', 'so_buoi_hoan', 'so_tien_hoan']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $donDichVu = DonDichVu::findOne(['id' => $this->dataPost['id']]);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $soBuoiHoanThanh = $donDichVu->soBuoiHoanThanh();
        $soBuoiConLai = $donDichVu->so_buoi - $soBuoiHoanThanh;
        if (intval($this->dataPost['so_buoi_hoan']) > $soBuoiConLai || intval($this->dataPost['so_buoi_hoan']) == 0) {
            throw new HttpException(500, "Số buổi hoàn không hợp lệ (lớn hơn 0 và nhỏ hơn {$soBuoiConLai})");
        }
        if ($this->dataPost['so_tien_hoan'] == "") {
            throw new HttpException(400, "Vui lòng nhập số tiền hoàn");
        }
        $donDichVu->trang_thai = LichSuTrangThaiDon::DON_HOAN;
        $donDichVu->so_buoi_hoan = $this->dataPost['so_buoi_hoan'];
        $donDichVu->so_tien_hoan = $this->dataPost['so_tien_hoan'];
        if (!$donDichVu->save()) {
            throw new HttpException(500, Html::errorSummary($donDichVu));
        }
        return $this->outputSuccess('', 'Hoàn đơn hàng thành công');
    }

    public function actionThemPhuPhi()
    {
        $this->checkField(['id', 'tong_tien', 'ghi_chu', 'type_id']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $donDichVu = DonDichVu::findOne(['id' => $this->dataPost['id']]);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $phuPhi = new PhuPhi();
        $phuPhi->user_id = $this->uid;
        $phuPhi->don_dich_vu_id = $donDichVu->id;
        $phuPhi->tong_tien = $this->dataPost['tong_tien'];
        $phuPhi->ghi_chu = $this->dataPost['ghi_chu'];
        $phuPhi->type_id = $this->dataPost['type_id'];
        $danhMuc = DanhMuc::findOne(['type' => DanhMuc::NAP_TIEN, 'id' => $this->dataPost['type_id']]);
        if (is_null($danhMuc)) {
            throw new HttpException(403, "Không xác định loại phụ phí");
        }
        $phuPhi->tieu_de = $danhMuc->name;
        if (!$phuPhi->save()) {
            throw new HttpException(500, Html::errorSummary($phuPhi));
        } else {
            $donDichVu->updateTongTien();
        }

        return $this->outputSuccess("", "Thêm phụ phí thành công");
    }

    public function actionXoaPhuPhi()
    {
        $this->checkField(['phu_phi_id']);
        if ($this->dataPost['phu_phi_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền phu_phi_id");
        }
        $phuPhi = PhuPhi::findOne(['id' => $this->dataPost['phu_phi_id'], 'active' => 1]);
        if (is_null($phuPhi)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $phuPhi->active = 0;
        if (!$phuPhi->save()) {
            throw new HttpException(500, Html::errorSummary($phuPhi));
        } else {
            $phuPhi->donDichVu->updateTongTien();;
        }
        return $this->outputSuccess("", "Xóa phụ phí thành công");
    }

    public function actionSuaPhuPhi()
    {
        $this->checkField(['phu_phi_id', 'tong_tien', 'ghi_chu', 'type_id']);
        if ($this->dataPost['phu_phi_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền phu_phi_id");
        }
        $phuPhi = PhuPhi::findOne(['id' => $this->dataPost['phu_phi_id'], 'active' => 1]);
        if (is_null($phuPhi)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $phuPhi->tong_tien = $this->dataPost['tong_tien'];
        $phuPhi->ghi_chu = $this->dataPost['ghi_chu'];
        $phuPhi->type_id = $this->dataPost['type_id'];
        if (!$phuPhi->save()) {
            throw new HttpException(500, Html::errorSummary($phuPhi));
        } else {
            $phuPhi->donDichVu->updateTongTien();
        }
        return $this->outputSuccess("", "Sửa phụ phí thành công");
    }

    public function actionDanhSachGiaoVien()
    {
        $this->checkGetInput(['tuKhoa', 'id']);
        if ($this->dataGet['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $donDichVu = DonDichVu::findOne($this->dataGet['id']);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $nhanLich = ArrayHelper::map(NhanLich::findAll(['active' => 1, 'don_dich_vu_id' => $this->dataGet['id'], 'trang_thai' => NhanLich::DANG_CHO_DUYET]), 'giao_vien_id', 'giao_vien_id');
        if (count($nhanLich) == 0) {
            return $this->outputListSuccess2([], 0);
        }
        $users = QuanLyUserVaiTro::find()
            ->select(['hoten', 'id', 'anh_nguoi_dung', 'trinh_do'])->andFilterWhere(['in', 'id', $nhanLich])
            ->andFilterWhere(['active' => 1, 'is_admin' => 0, 'status' => 10, 'khoa_tai_khoan' => 0, 'vai_tro' => User::GIAO_VIEN]);
        if ($this->dataGet['tuKhoa'] != "") {
            $users->andFilterWhere(['like', 'hoten', $this->dataGet['tuKhoa']]);
        }
        $count = count($users->all());
        $users = $users->limit($this->limit)->offset(($this->page - 1) * $this->limit)->all();
        $data = [];
        if (count($users) > 0) {
            /** @var QuanLyUserVaiTro $item */
            foreach ($users as $item) {
                $trinhDoName = DanhMuc::findOne($item->trinh_do);
                $item->trinh_do = is_null($trinhDoName) ? "" : $trinhDoName->name;
                $item->anh_nguoi_dung = CauHinh::getImage($item->anh_nguoi_dung);
                $data[] = $item;
            }
        }
        return $this->outputListSuccess2($data, $count);
    }

    public function actionDieuGiaoVien()
    {
        $this->checkField(['id', 'giao_vien_id']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $donDichVu = DonDichVu::findOne($this->dataPost['id']);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($this->dataPost['giao_vien_id'] == "") {
            throw new HttpException(500, "Vui lòng chọn giáo viên");
        }
        if (isset($this->dataPost['buoi_chua_day_id'])) {
            $buoi_chua_day_id = $this->dataPost['buoi_chua_day_id'];
            $nhanLich = NhanLich::findOne([
                'giao_vien_id' => $this->dataPost['giao_vien_id'],
                'don_dich_vu_id' => $this->dataPost['id'],
                'active' => 1,
            ]);
            if (is_null($nhanLich)) {
                $nhanLich = new NhanLich();
                $nhanLich->giao_vien_id = $this->dataPost['giao_vien_id'];
                $nhanLich->don_dich_vu_id = $this->dataPost['id'];
                $nhanLich->user_id = $this->uid;
                $nhanLich->trang_thai = NhanLich::DANG_DAY;
            } else {
                $nhanLich->trang_thai = NhanLich::DANG_DAY;
            }
            if (!$nhanLich->save()) {
                throw new HttpException(500, Html::errorSummary($nhanLich));
            }
            $tienDoKhoaHoc = TienDoKhoaHoc::findOne($buoi_chua_day_id);
            $tienDoKhoaHoc->giao_vien_id = $this->dataPost['giao_vien_id'];
            if (!$tienDoKhoaHoc->save()) {
                throw new HttpException(500, Html::errorSummary($tienDoKhoaHoc));
            }
            return $this->outputSuccess("", "Đổi giáo viên thành công");
        }
        $nhanLich = NhanLich::findOne([
            'giao_vien_id' => $this->dataPost['giao_vien_id'],
            'don_dich_vu_id' => $this->dataPost['id'],
            'active' => 1,
        ]);
        if (is_null($nhanLich)) {
            $nhanLich = new NhanLich();
            $nhanLich->giao_vien_id = $this->dataPost['giao_vien_id'];
            $nhanLich->don_dich_vu_id = $this->dataPost['id'];
            $nhanLich->user_id = $this->uid;
            $nhanLich->trang_thai = NhanLich::DA_DUYET;
        } else {
            $nhanLich->trang_thai = NhanLich::DA_DUYET;
        }

        if ($donDichVu->giao_vien_id != $this->dataPost['giao_vien_id']) {
            $donDichVu->giao_vien_id = $this->dataPost['giao_vien_id'];
            $donDichVu->trang_thai = LichSuTrangThaiDon::DANG_KHAO_SAT;
            $donDichVu->giao_vien_dong_thuan = 0;
            $donDichVu->phu_huynh_dong_thuan = 0;
            if (!$donDichVu->save()) {
                throw new HttpException(500, Html::errorSummary($donDichVu));
            } else {
                if (!$nhanLich->save()) {
                    throw new HttpException(500, Html::errorSummary($nhanLich));
                }
            }
        } else {
            throw new HttpException(500, "Giáo viên " . $nhanLich->giaoVien->hoten . " đã được điều từ trước đó!");
        }
        $email = CauHinh::findOne(2)->ghi_chu;
        $emailAdmin = CauHinh::findOne(39)->content;
        $this->sendEMail('Trông trẻ Pro', $email, $emailAdmin, 'Leader KD', 'Điều giáo viên thành công', "Mã đơn hàng $donDichVu->ma_don_hang đã được điều cho giáo viên: {$donDichVu->giaoVien->hoten}");
        $this->sendEMail('Trông trẻ Pro', $email, $donDichVu->leaderKd->email, 'Leader KD', 'Điều giáo viên thành công', "Mã đơn hàng $donDichVu->ma_don_hang đã được điều cho giáo viên: {$donDichVu->giaoVien->hoten}");
        return $this->outputSuccess("", "Điều giáo viên thành công");
    }

    public function actionTienDoKhoaHoc()
    {
        $this->checkGetInput(['id', 'buoi']);
        if ($this->dataGet['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $donDichVu = DonDichVu::findOne($this->dataGet['id']);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
//        if ($donDichVu->trang_thai !== LichSuTrangThaiDon::DANG_DAY&&$donDichVu->trang_thai !== LichSuTrangThaiDon::HOAN_THANH) {
//            throw new HttpException(500, "Đơn dịch vụ đang không được phép truy cập");
//
//        }
        if (intval($this->dataGet['buoi']) == 0 || intval($this->dataGet['buoi']) > $donDichVu->so_buoi) {
            throw new HttpException(500, "Thông tin buổi học không hợp lệ");
        }
        $tienDo = $donDichVu->tienDoKhoaHoc($this->dataGet['buoi']);

        $giao_vien_id = isset($tienDo['giao_vien_id']) ? $tienDo['giao_vien_id'] : '';
        $giaoVien = User::findOne($giao_vien_id);
        return [
            'tienDo' => $tienDo,
            'giaoVien' => is_null($giaoVien) ? null : [
                'id' => $giaoVien->id,
                'anh_nguoi_dung' => $giaoVien->getImage(),
                'hoten' => $giaoVien->hoten,
                'trinh_do' => $giaoVien->getTrinhDo(),
                'dien_thoai' => $giaoVien->dien_thoai,
            ]
        ];
    }

    public function actionDoiGioDay()
    {
        $this->checkField(['ca_day_id', 'gio_day']);
        if ($this->dataPost['ca_day_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền ca_day_id");
        }
        if ($this->dataPost['gio_day'] == "") {
            throw new HttpException(500, "Vui lòng chọn giờ");
        }
        $caDay = TienDoKhoaHoc::findOne(['id' => $this->dataPost['ca_day_id'], 'active' => 1]);
        if (is_null($caDay)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
//        if ($caDay->trang_thai !== TienDoKhoaHoc::CHUA_DAY) {
//            throw new HttpException(500, "Đổi lịch chỉ áp dụng cho các buổi học chưa dạy");
//        }
        $caDay->gio_day = $this->dataPost['gio_day'];
        if (!$caDay->save()) {
            throw new HttpException(500, \yii\bootstrap\Html::errorSummary($caDay));
        }
        return $this->outputSuccess("", "Đổi giờ dạy thành công");
    }

    public function actionDoiNgayDay()
    {

        $this->checkField(['ca_day_id', 'ngay_day']);
        if ($this->dataPost['ca_day_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền ca_day_id");
        }
        if ($this->dataPost['ngay_day'] == "") {
            throw new HttpException(500, "Vui lòng chọn ngày");
        }
        $caDay = TienDoKhoaHoc::findOne(['id' => $this->dataPost['ca_day_id'], 'active' => 1]);
        if (is_null($caDay)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
//        if ($caDay->trang_thai !== TienDoKhoaHoc::CHUA_DAY) {
//            throw new HttpException(500, "Đổi lịch chỉ áp dụng cho các buổi học chưa dạy");
//        }
        /** @var TienDoKhoaHoc $checkDate */
        $checkDate = TienDoKhoaHoc::find()->andFilterWhere(['don_dich_vu_id' => $caDay->don_dich_vu_id, 'active' => 1])
            ->andFilterWhere(['date(ngay_day)' => myAPI::convertDMY2YMD($this->dataPost['ngay_day'])])
            ->andFilterWhere(['<>', 'trang_thai', TienDoKhoaHoc::DA_HUY])->one();
        if (!is_null($checkDate)) {
            throw new HttpException(500, "Ngày dạy đã trùng với buổi số {$checkDate->buoi}");
        }
        $caDay->ngay_day = $this->dataPost['ngay_day'];
        if (!$caDay->save()) {
            throw new HttpException(500, Html::errorSummary($caDay));
        }
        return $this->outputSuccess("", "Đổi ngày dạy thành công");
    }

    public function actionDoiGiaoVienDay()
    {
        $this->checkField(['ca_day_id', 'giao_vien_id']);
        if ($this->dataPost['ca_day_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền ca_day_id");
        }
        if ($this->dataPost['giao_vien_id'] == "") {
            throw new HttpException(500, "Vui lòng chọn giáo viên");
        }
        $caDay = TienDoKhoaHoc::findOne(['id' => $this->dataPost['ca_day_id'], 'active' => 1]);
        if (is_null($caDay)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($caDay->trang_thai !== TienDoKhoaHoc::CHUA_DAY) {
            throw new HttpException(500, "Đổi lịch chỉ áp dụng cho các buổi học chưa dạy");
        }
        $user = QuanLyUserVaiTro::find()->andFilterWhere(['id' => $this->dataPost['giao_vien_id'], 'active' => 1, 'status' => 10, 'vai_tro' => User::GIAO_VIEN])->one();
        if (is_null($user)) {
            throw new HttpException(403, "Không tìm thấy giáo viên");
        }
        if ($caDay->giao_vien_id == $this->dataPost['giao_vien_id']) {
            throw new HttpException(500, "Giáo viên $user->hoten đang phụ trách đơn {$caDay->donDichVu->ma_don_hang} vui lòng chọn giáo viên khác ");
        }
        $caDay->giao_vien_id = $this->dataPost['giao_vien_id'];
        if (!$caDay->save()) {
            throw new HttpException(500, \yii\bootstrap\Html::errorSummary($caDay));
        }
        return $this->outputSuccess("", "Đổi giáo viên dạy thành công");
    }

    public function actionDanhSachGiaoVienDangRanh()
    {
        $this->checkGetInput(['trinh_do']);
        $users = QuanLyUserVaiTro::find()
            ->select(['hoten', 'id', 'anh_nguoi_dung', 'trinh_do'])->andFilterWhere(['in', 'trinh_do', [26, 27, 28]])
            ->andFilterWhere(['active' => 1, 'is_admin' => 0, 'status' => 10, 'khoa_tai_khoan' => 0, 'vai_tro' => User::GIAO_VIEN]);
        if ($this->tuKhoa != "") {
            $users->andFilterWhere(['like', 'hoten', $this->dataGet['tuKhoa']]);
        }
        if ($this->dataGet['trinh_do'] != "") {
            $users->andFilterWhere(['trinh_do' => $this->dataGet['trinh_do']]);
        }
        $data = [];
        $users = $users->all();
        if (count($users) > 0) {
            foreach ($users as $item) {
                $trinhDoName = DanhMuc::findOne($item->trinh_do);
                $item->trinh_do = is_null($trinhDoName) ? "" : $trinhDoName->name;
                $item->anh_nguoi_dung = CauHinh::getServer() . '/upload-file/' . ($item->anh_nguoi_dung == null ? "user-nomal.jpg" : $item->anh_nguoi_dung);
                $data[] = $item;
            }
        }
        return $this->outputSuccess($data);
    }

    public function actionDanhSachGiaBuoiHoc()
    {
        $this->checkGetInput(['id', 'khung_gio_id', 'trinh_do']);
        $giaBuoiHoc = GiaDichVu::find()
            ->select(['id', 'so_buoi', 'khuyen_mai', '(tong_tien*so_buoi) as tong_tien', '(tong_tien*so_buoi-tong_tien*so_buoi*khuyen_mai/100) as thanh_tien'])
            ->andFilterWhere(['active' => 1, 'dich_vu_id' => $this->dataGet['id'], 'khung_gio_id' => $this->dataGet['khung_gio_id']]);
        $giaBuoiHoc->andFilterWhere(['trinh_do' => $this->dataGet['trinh_do']]);
        $count = count($giaBuoiHoc->all());
        $giaBuoiHoc = $giaBuoiHoc->limit($this->limit)
            ->offset(($this->page - 1) * $this->limit)
            ->orderBy(['ID' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        return $this->outputListSuccess2($giaBuoiHoc, $count);
    }

    public function actionLoadFormTaoDon()
    {
        $tongTienAnTrua = 0;
        $tongTienThemGio = 0;
        $hocPhi = 0;
        $phuCap = 0;
        $tongTien = 0;
        $phuPhiThemTre = CauHinh::getContent(37);
        $soLuongTre = 1;
        if (isset($this->dataGet['so_luong_tre'])) {
            if (intval($this->dataGet['so_luong_tre']) == 0) {
                throw new HttpException(500, "Số lượng trẻ không hợp lệ");
            }
            $soLuongTre = intval($this->dataGet['so_luong_tre']);
        }
        if (isset($this->dataGet['an_trua_id'])) {
            $anTrua = DanhMuc::findOne(['id' => $this->dataGet['an_trua_id'], 'type' => 'Ăn trưa']);
            if (is_null($anTrua)) {
                throw new HttpException(500, "Không xác định dữ liệu ăn trưa");
            }
            $ghiChu = json_decode($anTrua->ghi_chu);
            if (isset($ghiChu->tong_tien)) {
                $tongTienAnTrua = intval($ghiChu->tong_tien);
            }
        }
        if (isset($this->dataGet['them_gio_id'])) {
            $themGio = DanhMuc::findOne(['id' => $this->dataGet['them_gio_id'], 'type' => 'Thêm giờ']);
            if (is_null($themGio)) {
                throw new HttpException(500, "Không xác định dữ liệu thêm giờ");
            }
            $ghiChu = json_decode($themGio->ghi_chu);
            if (isset($ghiChu->tong_tien)) {
                $tongTienThemGio = intval($ghiChu->tong_tien);
            }
        }
        if (isset($this->dataGet['goi_hoc_phi_id'])) {
            $goiDichVu = GiaDichVu::findOne($this->dataGet['goi_hoc_phi_id']);
            if (is_null($goiDichVu)) {
                throw new HttpException(500, "Gói dịch vụ không hợp lệ");
            }
            $thanhTien = $goiDichVu->tong_tien * $goiDichVu->so_buoi - $goiDichVu->tong_tien * $goiDichVu->khuyen_mai * $goiDichVu->so_buoi / 100;
            $hocPhi = $thanhTien + $thanhTien * ($soLuongTre - 1) * $phuPhiThemTre / 100;
            $tongTienAnTrua = $tongTienAnTrua * $goiDichVu->so_buoi;
            $tongTienThemGio = $tongTienThemGio * $goiDichVu->so_buoi;
        }
        $phuCap = ($tongTienAnTrua + $tongTienThemGio) * $soLuongTre;
        $tongTien = $hocPhi + $phuCap;
        $anTrua = DanhMuc::find()->andFilterWhere(['type' => 'Ăn trưa', 'active' => 1])->all();
        $dataAnTrua = [];
        /** @var DanhMuc $item */
        foreach ($anTrua as $item) {
            $ghiChu = json_decode($item->ghi_chu);
            $dataAnTrua[] = [
                'id' => $item->id,
                'tieu_De' => $item->name,
                'ghi_chu' => $ghiChu->ghi_chu,
                'tong_tien' => $ghiChu->tong_tien
            ];
        }
        $themGio = DanhMuc::find()->andFilterWhere(['type' => 'Thêm giờ', 'active' => 1])->all();
        $dataThemGio = [];
        /** @var DanhMuc $item */
        foreach ($themGio as $item) {
            $ghiChu = json_decode($item->ghi_chu);
            $dataThemGio[] = [
                'id' => $item->id,
                'tieu_De' => $item->name,
                'ghi_chu' => $ghiChu->ghi_chu,
                'tong_tien' => $ghiChu->tong_tien
            ];
        }
        return $this->outputSuccess([
            'loaiGiaoVien' => [
                ['id' => 26, 'name' => 'Bảo mẫu Pro'],
                ['id' => 27, 'name' => 'Bảo mẫu'],
            ],
            'anTrua' => $dataAnTrua,
            'themGio' => $dataThemGio,
            'hocPhi' => $hocPhi,
            'phuCap' => $phuCap,
            'tongTien' => $tongTien,

        ]);
    }

    public function actionTaoDon()
    {
        $this->checkField([
            'phu_huynh_id',
            'dich_vu_id',
            'dia_chi',
            'thu',
            'thoi_gian_bat_dau',
            'chon_ca_id',
            'so_luong_be',
            'an_trua_id',
            'them_gio_id',
            'goi_hoc_phi_id',
            'hoc_phi',
            'phu_cap',
            'tong_tien',
            'ghi_chu',
            'giao_vien_id',
            'leader_kd_id',

        ]);
        $fields = [
            'phu_huynh_id',
            'dich_vu_id',
            'dia_chi',
            'thu',
            'thoi_gian_bat_dau',
            'chon_ca_id',
            'so_luong_be',
            'goi_hoc_phi_id',
            'ghi_chu',
            'giao_vien_id',
            'an_trua_id',
            'them_gio_id',
            'leader_kd_id'
        ];
        if ($this->dataPost['phu_huynh_id'] == "") {
            throw new HttpException(500, "Vui lòng chọn phụ huynh");
        }
        if ($this->dataPost['dich_vu_id'] == "") {
            throw new HttpException(500, "Vui lòng chọn dịch vụ");
        }
        if ($this->dataPost['dia_chi'] == "" || $this->dataPost['dia_chi'] == "null") {
            throw new HttpException(500, "Vui lòng nhập địa chỉ");
        }
        if ($this->dataPost['thoi_gian_bat_dau'] == "") {
            throw new HttpException(500, "Vui lòng chọn ngày bắt đầu");
        }
        if ($this->dataPost['thu'] == "") {
            throw new HttpException(500, "Vui lòng chọn thứ");
        }
        if ($this->dataPost['chon_ca_id'] == "") {
            throw new HttpException(500, "Vui lòng chọn ca học");
        }
        if ($this->dataPost['so_luong_be'] == "") {
            throw new HttpException(500, "Vui lòng chọn số lượng bé");
        }
        if ($this->dataPost['an_trua_id'] == "") {
            throw new HttpException(500, "Vui lòng chọn ăn trưa");
        }
        if ($this->dataPost['them_gio_id'] == "") {
            throw new HttpException(500, "Vui lòng chọn thêm giờ");
        }
        if ($this->dataPost['goi_hoc_phi_id'] == "") {
            throw new HttpException(500, "Vui lòng chọn gói học");
        }
        $dangKi = new DonDichVu();
        foreach ($fields as $field) {
            $dangKi->{$field} = $this->dataPost[$field];
        }
        $total = $dangKi->totalDonDichVu();
        $dangKi->hoc_phi = $total['hocPhi'];
        $dangKi->phu_cap = $total['phuCap'];
        $dangKi->tong_tien = $total['tongTien'];
        $dangKi->user_id = $this->uid;
        if (isset($this->dataPost['loai_giao_vien'])) {
            $dangKi->loai_giao_vien = $this->dataPost['loai_giao_vien'];
        }
        if ($this->dataPost['giao_vien_id'] == "") {
            $dangKi->trang_thai = LichSuTrangThaiDon::CHUA_CO_GIAO_VIEN;
        } else {
            $dangKi->trang_thai = LichSuTrangThaiDon::DANG_KHAO_SAT;
            $nhanLich = new NhanLich();
            $nhanLich->giao_vien_id = $this->dataPost['giao_vien_id'];
            $nhanLich->user_id = $this->uid;
            $nhanLich->trang_thai = NhanLich::DA_DUYET;
        }
        $dangKi->trang_thai_thanh_toan = LichSuTrangThaiThanhToan::CHUA_THANH_TOAN;
        if (!$dangKi->save()) {
            throw new HttpException(500, \yii\bootstrap\Html::errorSummary($dangKi));
        } else {
            $anTrua = DanhMuc::findOne($this->dataPost['an_trua_id']);
            if (!is_null($anTrua)) {
                $ghiChuAnTrua = json_decode($anTrua->ghi_chu);
                $dangKi->updateAttributes(['ma_don_hang' => 'DH' . sprintf("%07d", $dangKi->id)]);
                $phuPhiAnTrua = new PhuPhi();
                $phuPhiAnTrua->don_dich_vu_id = $dangKi->id;
                $phuPhiAnTrua->user_id = $this->uid;
                $phuPhiAnTrua->type_id = DanhMuc::AN_TRUA;
                $phuPhiAnTrua->ghi_chu = $ghiChuAnTrua->ghi_chu;
                $phuPhiAnTrua->tong_tien = $ghiChuAnTrua->tong_tien;
                $phuPhiAnTrua->tieu_de = "Ăn trưa";
                if (!$phuPhiAnTrua->save()) {
                    throw new HttpException(500, \yii\bootstrap\Html::errorSummary($phuPhiAnTrua));
                }
            }
            $themGio = DanhMuc::findOne($this->dataPost['them_gio_id']);
            if (!is_null($themGio)) {
                $ghiChuThemGio = json_decode($themGio->ghi_chu);
                $phuPhiThemGio = new PhuPhi();
                $phuPhiThemGio->don_dich_vu_id = $dangKi->id;
                $phuPhiThemGio->user_id = $this->uid;
                $phuPhiThemGio->type_id = DanhMuc::THEM_GIO;
                $phuPhiThemGio->ghi_chu = $ghiChuThemGio->ghi_chu;
                $phuPhiThemGio->tong_tien = $ghiChuThemGio->tong_tien;
                $phuPhiThemGio->tieu_de = "Thêm giờ";
                if (!$phuPhiThemGio->save()) {
                    throw new HttpException(500, Html::errorSummary($phuPhiThemGio));
                }
            }
            if ($this->dataPost['giao_vien_id'] !== "") {
                $nhanLich->don_dich_vu_id = $dangKi->id;
                if (!$nhanLich->save()) {
                    throw new HttpException(500, Html::errorSummary($nhanLich));
                }
            }
            $thongBao = new ThongBao();
            $thongBao->to_id = 61;
            $thongBao->type_id = 65;
            $thongBao->phu_huynh_id = $dangKi->phu_huynh_id;
            $user = QuanLyUserVaiTro::findOne(['id' => $this->uid]);
            $thongBao->noi_dung = "Phụ huynh đăng kí khóa học thành công!. \nChương trình: "
                . $dangKi->dichVu->ten_dich_vu . ". \nBởi: " . $user->hoten .
                " • " . $user->vai_tro_name;
            $thongBao->tieu_de = "Đăng kí khóa học";
            $this->saveThongBao($thongBao);
            $email = CauHinh::findOne(2)->ghi_chu;
            $emailAdmin = CauHinh::findOne(39)->content;
            $this->sendEMail('Trông trẻ Pro', $email, $emailAdmin, $thongBao->tieu_de, $thongBao->tieu_de, str_replace('\n', '<br>', $thongBao->noi_dung));
        }
        return $this->outputSuccess("", "Đăng kí dịch vụ thành công");
    }

    public function actionDanhSachPhuHuynh()
    {
        $users = QuanLyUserVaiTro::find()
            ->select(['hoten', 'dien_thoai', 'id', 'anh_nguoi_dung'])
            ->andFilterWhere(['active' => 1, 'is_admin' => 0, 'status' => 10, 'vai_tro' => User::PHU_HUYNH]);
        if ($this->tuKhoa != "") {
            $users->andFilterWhere(['like', 'hoten', $this->dataGet['tuKhoa']]);
        }
        $data = [];
        $users = $users->all();
        if (count($users) > 0) {
            foreach ($users as $item) {
                $item->anh_nguoi_dung = CauHinh::getServer() . '/upload-file/' . ($item->anh_nguoi_dung == null ? "user-nomal.jpg" : $item->anh_nguoi_dung);
                $data[] = $item;
            }
        }
        return $this->outputSuccess($data);
    }

    public function actionChiTietNhanXet()
    {
        $this->checkGetInput(['ca_day_id']);
        if ($this->dataGet['ca_day_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền ca_day_id");
        }
        $tienDo = TienDoKhoaHoc::findOne($this->dataGet['ca_day_id']);
        if (is_null($tienDo)) {
            throw new HttpException(403, "Không xác định dữ liệu");
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
                'danh_gia' => $tienDo->danh_gia
            ],
            'formDanhGia' => $tienDo->getFormDanhGia()
        ]);
    }

    public function actionDanhSachChuongTrinhHoc()
    {
        $this->checkGetInput(['id']);
        if ($this->dataGet['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $donDichVu = DonDichVu::findOne($this->dataGet['id']);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $chuongTrinhHoc = ChuongTrinhHoc::find()
            ->select(['tieu_de', 'id'])
            ->andFilterWhere(['active' => 1, 'dich_vu_id' => $donDichVu->dich_vu_id, 'bat_chuong_trinh' => 1])->all();
        return $this->outputSuccess($chuongTrinhHoc);
    }

    public function actionDanhSachGoiHoc()
    {
        $this->checkGetInput(['chuong_trinh_hoc_id']);
        if ($this->dataGet['chuong_trinh_hoc_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền chuong_trinh_hoc_id");
        }
        $chuongTrinhHoc = ChuongTrinhHoc::findOne($this->dataGet['chuong_trinh_hoc_id']);
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
            ->andFilterWhere(['active' => 1, 'type' => DanhMuc::GOI_HOC])->andFilterWhere(['in', 'id', $goiHoc])->all();
        /** @var DanhMuc $item */
        foreach ($goiHocs as $item) {
            $data [] = [
                'id' => $item->id,
                'name' => $item->name,
                'baiHoc' => GoiHoc::find()->select(['id', 'tieu_de'])
                    ->andFilterWhere(['active' => 1, 'nhom_id' => $item->id, 'chuong_trinh_id' => $chuongTrinhHoc->id])->all()
            ];
        }
        return $this->outputSuccess($data);
    }

    public function actionThemChuongTrinhHoc()
    {
        $this->checkField(['id', 'chuong_trinh_hoc_id', 'baiHocs']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $donDichVu = DonDichVu::findOne($this->dataPost['id']);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định đơn dịch vụ");
        }
        if ($this->dataPost['chuong_trinh_hoc_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền chuong_trinh_hoc_id");
        }
        $chuongTrinhHoc = ChuongTrinhHoc::findOne(['id' => $this->dataPost['chuong_trinh_hoc_id'], 'active' => 1]);
        if (is_null($chuongTrinhHoc)) {
            throw new HttpException(403, "Không xác định chương trình học");
        }
        if (!is_array($this->dataPost['baiHocs'])) {
            throw new HttpException(500, "Danh sách bài học không hợp lệ");
        }
        if (count($this->dataPost['baiHocs']) == 0) {
            throw new HttpException(500, "Danh sách bài học không hợp lệ");
        }
        /** @var GoiHoc $item */
        foreach ($this->dataPost['baiHocs'] as $item) {
            $baiHoc = GoiHoc::findOne(['id' => $item, 'active' => 1]);
            if (is_null($baiHoc)) {
                throw new HttpException(500, "Danh sách bài học không hợp lệ");
            }
        }
        $donDichVu->chuong_trinh_hoc_id = $chuongTrinhHoc->id;
        $donDichVu->goi_hoc_id = json_encode($this->dataPost['baiHocs']);
        if (!$donDichVu->save()) {
            throw new HttpException(500, Html::errorSummary($donDichVu));
        }
        return $this->outputSuccess("", "Thêm chương trình học thành công");
    }

    public function actionXoaBaiHoc()
    {
        $this->checkField(['id', 'bai_hoc_id']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $donDichVu = DonDichVu::findOne($this->dataPost['id']);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định đơn dịch vụ");
        }
        $goiHocs = json_decode($donDichVu->goi_hoc_id);
        if (is_array($goiHocs))
            if (count($goiHocs) == 0) {
                throw new HttpException(403, "Không xác định dữ liệu");
            }
        $checkRemove = false;
        foreach ($goiHocs as $index => $item) {
            if ($item == $this->dataPost['bai_hoc_id']) {
                unset($goiHocs[$index]);
                $checkRemove = true;
            }
        }
        if (!$checkRemove) {
            throw new HttpException(403, "Gói học không tồn tại trong đơn");
        }
        $donDichVu->goi_hoc_id = json_encode($goiHocs);
        if (!$donDichVu->save()) {
            throw new HttpException(500, Html::errorSummary($donDichVu));
        }
        return $this->outputSuccess("", "Xóa bài học thành công");
    }

    public function actionDanhSachDonDichVuFull()
    {
        $donDichVu = DonDichVu::find()->select(['id', 'ma_don_hang']);
        if (isset($this->dataGet['trang_thai'])) {
            if ($this->dataGet['trang_thai'] != "") {
                $donDichVu->andFilterWhere(['trang_thai' => $this->dataGet['trang_thai']]);
            }
        }
        if (isset($this->dataGet['giao_vien_id'])) {
            if ($this->dataGet['giao_vien_id'] != "") {
                $donDichVu->andFilterWhere(['giao_vien_id' => $this->dataGet['giao_vien_id']]);
            }
        }
        return $this->outputSuccess($donDichVu->all());
    }

    public function actionXoaChuongTrinhHoc()
    {
        $this->checkField(['id']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $donDichVu = DonDichVu::findOne($this->dataPost['id']);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định đơn dịch vụ");
        }
        $donDichVu->goi_hoc_id = null;
        if (!$donDichVu->save()) {
            throw new HttpException(500, Html::errorSummary($donDichVu));
        }
        return $this->outputSuccess("", "Xóa chương trình học thành công");
    }

    public function actionXacNhanThanhToan()
    {
        $this->checkField(['id', 'xac_nhan_thanh_toan']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $donDichVu = DonDichVu::findOne(['id' => $this->dataPost['id']]);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($this->dataPost['xac_nhan_thanh_toan'] == 1) {
            $donDichVu->trang_thai_thanh_toan = LichSuTrangThaiThanhToan::DA_THANH_TOAN;
        }
        if (!$donDichVu->save()) {
            throw new HttpException(500, Html::errorSummary($donDichVu));
        }
        return $this->outputSuccess('', 'Lưu thông tin đơn dịch vụ thành công');
    }

    /**
     * @throws HttpException
     */
    public function actionGiaHanDon()
    {
        $this->checkField([
            'id',
            'so_buoi',
        ]);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $donDichVu = DonDichVu::findOne(['id' => $this->dataPost['id'], 'active' => 1]);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($this->dataPost['so_buoi'] == "") {
            throw new HttpException(403, "Vui lòng nhập số buổi");
        }
        $tongTien = 0;
        if ($donDichVu->so_buoi > 0) {
            $tongTien = $donDichVu->tong_tien * $this->dataPost['so_buoi'] / $donDichVu->so_buoi;
        }
        $donDichVu->tong_tien += $tongTien;
        $donDichVu->hoc_phi += $tongTien;
        $donDichVu->so_buoi = $donDichVu->so_buoi + $this->dataPost['so_buoi'];
        if ($donDichVu->trang_thai == LichSuTrangThaiDon::HOAN_THANH) {
            $donDichVu->trang_thai = LichSuTrangThaiDon::DANG_DAY;
            $nhanLich = NhanLich::findOne(['giao_vien_id' => $donDichVu->giao_vien_id, 'don_dich_vu_id' => $donDichVu->id, 'active' => 1]);
            $nhanLich->trang_thai = NhanLich::DANG_DAY;
            if (!$nhanLich->save()) {
                throw new HttpException(500, \yii\helpers\Html::errorSummary($nhanLich));
            }
        }
        if (!$donDichVu->save()) {
            throw new HttpException(500, \yii\bootstrap\Html::errorSummary($donDichVu));
        } else {
            $giaHanDon = new GiaHanDon();
            $giaHanDon->user_id = $this->uid;
            $giaHanDon->don_dich_vu_id = $donDichVu->id;
            $giaHanDon->so_buoi = $this->dataPost['so_buoi'];
            $giaHanDon->tong_tien = $tongTien;
            if (!$giaHanDon->save()) {
                throw new HttpException(500, Html::errorSummary($giaHanDon));
            }
        }
        return $this->outputSuccess("", "Gia hạn đơn thành công");
    }

    public function actionGetTongTienGiaHan()
    {
        $this->checkGetInput([
            'id',
            'so_buoi',
        ]);
        if ($this->dataGet['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $donDichVu = DonDichVu::findOne(['id' => $this->dataGet['id'], 'active' => 1]);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($this->dataGet['so_buoi'] == "") {
            return $this->outputSuccess(0);
        }
        if ($donDichVu->so_buoi == 0) {
            return $this->outputSuccess(0);
        }
        return $this->outputSuccess($donDichVu->tong_tien * $this->dataGet['so_buoi'] / $donDichVu->so_buoi);
    }

    public function actionHuyCa()
    {
        $this->checkField(['ca_day_id']);
        if ($this->dataPost['ca_day_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền ca_day_id");
        }
        $tienDo = TienDoKhoaHoc::findOne($this->dataPost['ca_day_id']);
        if (is_null($tienDo)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($tienDo->trang_thai !== TienDoKhoaHoc::CHUA_DAY) {
            throw new HttpException(500, "Ca dạy đang có trạng thái $tienDo->trang_thai");
        }
        $tienDo->trang_thai = TienDoKhoaHoc::DA_HUY;
        if (!$tienDo->save()) {
            throw new HttpException(500, \yii\bootstrap\Html::errorSummary($tienDo));
        }
        $donDichVu = $tienDo->donDichVu;
        $donDichVu->so_buoi = $donDichVu->so_buoi + 1;
        if (!$donDichVu->save()) {
            throw new HttpException(500, Html::errorSummary($tienDo));
        }
        $thongBao = new ThongBao();
        $thongBao->to_id = 60;
        $thongBao->type_id = 65;
        $thongBao->noi_dung =
            "Ca dạy của giáo viên đã hủy lúc " . date("H:i", strtotime($tienDo->gio_day)) . ". \nBạn vui lòng kiểm tra lại thông tin. \nChúc bạn may mắn!";
        $thongBao->tieu_de = "Hủy buổi học!";
        $this->saveThongBao($thongBao);
        $email = CauHinh::findOne(2)->ghi_chu;
        $emailAdmin = CauHinh::findOne(39)->content;
        $this->sendEMail('Trông trẻ Pro', $email, $emailAdmin, $thongBao->tieu_de, $thongBao->tieu_de, str_replace('\n', '<br/>', $thongBao->noi_dung));
        $this->sendEMail('Trông trẻ Pro', $email, $donDichVu->leaderKd->email, $thongBao->tieu_de, $thongBao->tieu_de, str_replace('\n', '<br/>', $thongBao->noi_dung));
        return $this->outputSuccess("", "Hủy buổi học thành công");
    }

    public function actionGetHoaDon()
    {
        $this->checkGetInput(['id']);
        if ($this->dataGet['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $donDichVu = DonDichVu::findOne($this->dataGet['id']);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $hoaDon = HoaDon::findOne(['don_dich_vu_id' => $donDichVu->id]);

        return $this->outputSuccess(is_null($hoaDon) ? null : [
            'ho_ten' => $hoaDon->ho_ten,
            'cmnd_cccd' => $hoaDon->cmnd_cccd,
            'dia_chi' => $hoaDon->dia_chi,
            'ma_so_thue' => $hoaDon->ma_so_thue,
            'email' => $hoaDon->email,
            'ho_ten_con' => $hoaDon->ho_ten_con,
            'nam_sinh_cua_con' => $hoaDon->nam_sinh_cua_con,
        ]);
    }

    public function actionSuaLeaderkd()
    {
        $this->checkField(['id', 'leader_kd_id']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $donDichVu = DonDichVu::findOne(['id' => $this->dataPost['id']]);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $user = QuanLyUserVaiTro::findOne(['active' => 1, 'status' => 10, 'is_admin' => 1, 'vai_tro' => $this->leader_kd, 'id' => $this->dataPost['leader_kd_id']]);
        if (is_null($user)) {
            throw new HttpException(400, "Không xác định leader kinh doanh");
        }
        $donDichVu->leader_kd_id = $this->dataPost['leader_kd_id'];
        if (!$donDichVu->save()) {
            throw new HttpException(500, Html::errorSummary($donDichVu));
        }
        return $this->outputSuccess('', 'Cập nhập leader kinh doanh thành công');
    }

    public function actionGetTrinhDoKhiDieuGiaoVien()
    {
        $danhMuc = DanhMuc::find()->andFilterWhere(['type' => DanhMuc::TRINH_DO, 'active' => 1])->andFilterWhere(['in', 'id', [26, 27]])->select(['id', 'name'])->all();
        return $this->outputSuccess($danhMuc);
    }

    public function actionSuaDon()
    {
        $this->checkField(['id']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $donDichVu = DonDichVu::findOne(['id' => $this->dataPost['id']]);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $donDichVu->thu = $this->dataPost['thu'];
        $donDichVu->thoi_gian_bat_dau = $this->dataPost['thoi_gian_bat_dau'] != 'null' && $this->dataPost['thoi_gian_bat_dau'] != '' ? $this->dataPost['thoi_gian_bat_dau'] : $donDichVu->thoi_gian_bat_dau;

        if (!$donDichVu->save()) {
            throw new HttpException(500, Html::errorSummary($donDichVu));
        }
        return $this->outputSuccess('', 'Cập nhập đơn dịch vụ thành công');
    }

    public function actionChiTietPhuPhi()
    {
        $this->checkGetInput(['phu_phi_id']);
        if ($this->dataGet['phu_phi_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền phu_phi_id");
        }
        $phuPhi = PhuPhi::findOne(['id' => $this->dataGet['phu_phi_id'], 'active' => 1]);
        if (is_null($phuPhi)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        return $this->outputSuccess([
            'id' => $phuPhi->id,
            'tong_tien' => $phuPhi->tong_tien,
            'ghi_chu' => $phuPhi->ghi_chu,
            'type_id' => $phuPhi->type_id,
        ]);
    }

    public function actionGetNhomNhanSu()
    {
        $this->checkGetInput(['dich_vu_id']);
        $loaiGiaoVien = [
            ['id' => 26, 'name' => 'Chuyên viên'],
        ];
        if (isset($this->dataGet['dich_vu_id'])) {
            $dichVu = DichVu::findOne($this->dataGet['dich_vu_id']);
            if (!is_null($dichVu)) {
                if ($dichVu->loai_dich_vu_id == DichVu::CHAM_SOC_TRE) {
                    $loaiGiaoVien = [
                        ['id' => 26, 'name' => 'Chuyên viên'],
                        ['id' => 27, 'name' => 'Nhân viên'],
                    ];
                }
            }
        }
        return $this->outputSuccess($loaiGiaoVien);
    }
}
