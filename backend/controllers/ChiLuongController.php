<?php namespace backend\controllers;

use backend\controllers\CoreApiController;
use backend\models\CauHinh;
use backend\models\ChiLuong;
use backend\models\TienDoKhoaHoc;
use backend\models\DanhMuc;
use backend\models\DonDichVu;
use backend\models\DichVu;
use backend\models\GiaoDich;
use backend\models\KhieuNai;
use backend\models\LichSuViecLamGiaoVien;
use backend\models\PhieuLuong;
use backend\models\PhuPhi;
use backend\models\QuanLyUserVaiTro;
use backend\models\LichSuTrangThaiPhieuLuong;
use backend\models\ThuongPhat;
use backend\models\VaiTro;
use backend\models\Vaitrouser;
use common\models\exportExcelBaoCaoKhachHang;
use common\models\exportExcelBaoCaoUser;
use common\models\exportExcelDanhSachPhieuLuong;
use common\models\exportExcelPhieuLuong;
use common\models\User;
use Yii;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\HttpException;
use yii\web\Response;
use function GuzzleHttp\Psr7\str;

class ChiLuongController extends CoreApiController
{
    public function actionDanhSach()
    {
        $giaoVien =User::find()
            ->leftJoin(GiaoDich::tableName(), GiaoDich::tableName() . '.user_id=' . User::tableName() . '.id')
            ->leftJoin(Vaitrouser::tableName(), Vaitrouser::tableName() . '.user_id=' . User::tableName() . '.id')
            ->andFilterWhere([User::tableName() . '.active' => 1, User::tableName() . '.is_admin' => 0, User::tableName() . '.status' => 10, Vaitrouser::tableName() . '.vaitro_id' => User::GIAO_VIEN]);
        if ($this->tuKhoa != "") {
            $giaoVien->andFilterWhere(['like', User::tableName() . '.hoten', $this->tuKhoa]);
        }
        if (isset($this->dataGet['dien_thoai'])) {
            if ($this->dataGet['dien_thoai'] != "") {
                $giaoVien->andFilterWhere(['like', User::tableName() . '.dien_thoai', $this->dataGet['dien_thoai']]);
            }
        }
        if (isset($this->dataGet['leader_id'])) {
            if ($this->dataGet['leader_id'] != "") {
                $giaoVien->andFilterWhere(['=', User::tableName() . '.user_id', $this->dataGet['leader_id']]);
            }
        }
        if (isset($this->dataGet['dich_vu_id'])) {
            if ($this->dataGet['dich_vu_id'] != "") {
                $giaoVien->andFilterWhere(['like', User::tableName() . '.dich_vu', $this->dataGet['dich_vu_id']]);
            }
        }
        if (isset($this->dataGet['dia_chi'])) {
            if ($this->dataGet['dia_chi'] != "") {
                $giaoVien->andFilterWhere(['like', User::tableName() . '.dia_chi', $this->dataGet['dia_chi']]);
            }
        }
        $thang = date("m");
        $nam = date("Y");
        if (isset($this->dataGet['thang'])) {
            if ($this->dataGet['thang'] != "") {
                $arr = explode('/', $this->dataGet['thang']);
                if (count($arr) != 2) {
                    throw new HttpException(500, "Định dạng tháng không hợp lệ.");
                }
                $thang = $arr[0];
                $nam = $arr[1];
            }
        }
        $giaoVien->andFilterWhere(['=', 'month(' . GiaoDich::tableName() . '.created)', $thang]);
        $giaoVien->andFilterWhere(['=', 'year(' . GiaoDich::tableName() . '.created)',$nam]);

        $count = count($giaoVien->all());
       
        $giaoVien = $giaoVien
            ->limit($this->limit)
            ->offset(($this->page - 1) * $this->limit)
            ->orderBy(['created_at' => $this->sort == 1 ? SORT_DESC : SORT_ASC])
            ->groupBy([User::tableName() . '.id'])
            ->all();
        $data = [];
		$tong_tien_cac_giao_vien = 0;
        if (count($giaoVien) > 0) {
            /** @var User $item */
            foreach ($giaoVien as $item) {
                $tongTien =PhieuLuong::geLuongTheoThangbyGiaoVien($item->id,$thang,$nam);
                $tong_tien_cac_giao_vien += $tongTien;
                $data[] = [
                    'id'=>$item->id,
                    'hoten'=>$item->hoten,
					'dienthoai' =>$item->dien_thoai,
                    'anh_nguoi_dung'=>$item->getImage(),
                    'tong_tien'=>$tongTien,
                    'link'=>CauHinh::getServer()."/chi-luong/tao-phieu-luong?id=$item->id&thang=$thang/$nam",
                ];
            }
        }
        return $this->outputListSuccess2($data, $count, $tong_tien_cac_giao_vien);
    }
    public function actionExcelDanhSachPhieuLuong()
    {
        $giaoVien =User::find()
            ->leftJoin(GiaoDich::tableName(), GiaoDich::tableName() . '.user_id=' . User::tableName() . '.id')
            ->leftJoin(Vaitrouser::tableName(), Vaitrouser::tableName() . '.user_id=' . User::tableName() . '.id')
            ->andFilterWhere([User::tableName() . '.active' => 1, User::tableName() . '.is_admin' => 0, User::tableName() . '.status' => 10, Vaitrouser::tableName() . '.vaitro_id' => User::GIAO_VIEN]);
        if ($this->tuKhoa != "") {
            $giaoVien->andFilterWhere(['like', User::tableName() . '.hoten', $this->tuKhoa]);
        }
        if (isset($this->dataGet['dien_thoai'])) {
            if ($this->dataGet['dien_thoai'] != "") {
                $giaoVien->andFilterWhere(['like', User::tableName() . '.dien_thoai', $this->dataGet['dien_thoai']]);
            }
        }
        if (isset($this->dataGet['leader_id'])) {
            if ($this->dataGet['leader_id'] != "") {
                $giaoVien->andFilterWhere(['=', User::tableName() . '.user_id', $this->dataGet['leader_id']]);
            }
        }
        if (isset($this->dataGet['dich_vu_id'])) {
            if ($this->dataGet['dich_vu_id'] != "") {
                $giaoVien->andFilterWhere(['like', User::tableName() . '.dich_vu', $this->dataGet['dich_vu_id']]);
            }
        }
        if (isset($this->dataGet['dia_chi'])) {
            if ($this->dataGet['dia_chi'] != "") {
                $giaoVien->andFilterWhere(['like', User::tableName() . '.dia_chi', $this->dataGet['dia_chi']]);
            }
        }
        $thang = date("m");
        $nam = date("Y");
        if (isset($this->dataGet['thang'])) {
            if ($this->dataGet['thang'] != "") {
                $arr = explode('/', $this->dataGet['thang']);
                if (count($arr) != 2) {
                    throw new HttpException(500, "Định dạng tháng không hợp lệ.");
                }
                $thang = $arr[0];
                $nam = $arr[1];
            }
        }
        $giaoVien->andFilterWhere(['=', 'month(' . GiaoDich::tableName() . '.created)', $thang]);
        $giaoVien->andFilterWhere(['=', 'year(' . GiaoDich::tableName() . '.created)',$nam]);

        $giaoVien = $giaoVien->all();
        $data = [];
        $tong_tien_cac_giao_vien = 0;
        if (count($giaoVien) > 0) {
            /** @var User $item */
             foreach ($giaoVien as $item) {
				$giaoDich = GiaoDich::getGiaoDichTheoThang($item->id,$thang,$nam);
                $tongTien = PhieuLuong::geLuongTheoThangbyGiaoVien($item->id,$thang,$nam);
                $tong_tien_cac_giao_vien += $tongTien;
                $bangluong = PhieuLuong::getBangLuongTheoThangbyGiaoVien($item->id,$thang,$nam);
				$array_bangluong = [];
                if(count($bangluong)>0){
                    foreach ($bangluong as $key => $value){
                    //Mã đơn, dịch vụ, gói, số buổi, ngày dạy, giá buổi học
                        $don_dich_vu = DonDichVu::findOne($value['don_dich_vu_id']);
                        $buoi_hoc = TienDoKhoaHoc::findOne($value['buoi_hoc_id']);
                        $dich_vu_id = $don_dich_vu->dich_vu_id;
                        $dich_vu = DichVu::findOne($don_dich_vu->dich_vu_id);
                        $array_bangluong[$key]['ma_don'] = 'DH000'.$value['don_dich_vu_id'];
                        $array_bangluong[$key]['dich_vu'] = $dich_vu->ten_dich_vu;
                        $array_bangluong[$key]['goi'] = $don_dich_vu->tong_tien;
                        $array_bangluong[$key]['so_buoi'] = 'Buổi số '.$buoi_hoc->buoi."/".$buoi_hoc->tong_buoi;
                        $array_bangluong[$key]['ngay_day'] = $buoi_hoc->ngay_day;
                        $array_bangluong[$key]['gia_buoi_hoc'] = $value['tong_tien'];
                    }
                }

                $data[] = [
                    'id'=>$item->id,
                    'hoten'=>$item->hoten,
                    'dien_thoai'=>$item->dien_thoai,
                    'tong_tien'=>$tongTien,
                    'bangluong' => $array_bangluong,
					'nap_tien' => $giaoDich['nap_tien'],
					'rut_tien' => $giaoDich['rut_tien']
                ]; 
                
            }
        }
        $export = new exportExcelDanhSachPhieuLuong();
        $export->data = [
            'tuNgay' => "1/$thang/$nam",
            'denNgay' => date("t/m/Y", strtotime("$nam-$thang")),
            'tong_tien_cac_giao_vien' => $tong_tien_cac_giao_vien,
            'data'=>$data,
        ];
        $export->stream = false;
        $export->path_file = dirname(dirname(__DIR__)) . '/files_excel/';
        $file_name = $export->run();
        $file = CauHinh::getServer() . '/files_excel/' . $file_name;;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->outputSuccess($file);
    }

    public function actionChiTiet()
    {
        $thang = date("m");
        $nam = date("Y");
        $this->checkGetInput(['id']);
        if ($this->dataGet['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        /** @var QuanLyUserVaiTro $user */
        $user = QuanLyUserVaiTro::find()->andFilterWhere(['id' => $this->dataGet['id'], 'active' => 1, 'status' => 10, 'vai_tro' => User::GIAO_VIEN, 'is_admin' => 0])->one();
        if (is_null($user)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }

        $lichSuLuong = ChiLuong::find()->andFilterWhere([ChiLuong::tableName() . '.active' => 1, ChiLuong::tableName() . '.giao_vien_id' => $user->id])
            ->leftJoin(DonDichVu::tableName(), DonDichVu::tableName() . '.id=' . ChiLuong::tableName() . '.don_dich_vu_id');
        if (isset($this->dataGet['thang'])) {
            if ($this->dataGet['thang'] != "") {
                $thang = explode('/', $this->dataGet['thang']);
                if (count($thang) != 2) {
                    throw new HttpException(500, "Định dạng tháng không hợp lệ.");
                }
                $lichSuLuong->andFilterWhere(['=', 'month(' . ChiLuong::tableName() . '.created)', $thang[0]]);
                $lichSuLuong->andFilterWhere(['=', 'year(' . ChiLuong::tableName() . '.created)', $thang[1]]);
            } else {
                $thang = date("m");
                $nam = date("Y");
                $lichSuLuong->andFilterWhere(['=', 'month(' . ChiLuong::tableName() . '.created)', $thang]);
                $lichSuLuong->andFilterWhere(['=', 'year(' . ChiLuong::tableName() . '.created)', $nam]);
            }
        } else {
            $thang = date("m");
            $nam = date("Y");
            $lichSuLuong->andFilterWhere(['=', 'month(' . ChiLuong::tableName() . '.created)', $thang]);
            $lichSuLuong->andFilterWhere(['=', 'year(' . ChiLuong::tableName() . '.created)', $nam]);
        }
        if ($this->tuKhoa != "") {
            $lichSuLuong->andFilterWhere(['like', DonDichVu::tableName() . '.ma_don_hang', $this->tuKhoa]);
        }
        $count = count($lichSuLuong->all());
        $tongLuongThucTe = $lichSuLuong->sum(ChiLuong::tableName() .'.tong_tien+them_gio');
        // Lay du lieu an trua phu phi
        $anTrua = $lichSuLuong->sum('an_trua');
        $themGio = $lichSuLuong->sum('them_gio');
        $lichSuLuong = $lichSuLuong->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy([ChiLuong::tableName() . '.created' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        $ppKhac = CauHinh::getContent(28);
        $data = [];
        //Lay danh sach don
        // Tinh tong tien phu phi
        // Danh sach cac loai phu phi
        $phuPhiKhac = GiaoDich::find()
            ->andFilterWhere([GiaoDich::tableName() . '.user_id' => $user->id, GiaoDich::tableName() . '.type' => GiaoDich::NAP_TIEN])
            ->andFilterWhere(['=', 'month(' . GiaoDich::tableName() . '.created)', $thang])
            ->andFilterWhere(['=', 'year(' . GiaoDich::tableName() . '.created)', $nam])
            ->andWhere('type_id is not null');
        $totalPhuPhiKhac = $phuPhiKhac->sum('so_tien');
        $arrPhuPhiKhac = $phuPhiKhac->groupBy('type_id')
            ->leftJoin(DanhMuc::tableName(), DanhMuc::tableName() . '.id=' . GiaoDich::tableName() . '.type_id')
            ->select(['sum(so_tien) as so_tien', DanhMuc::tableName() . '.name'])->createCommand()->queryAll();

        /** @var ChiLuong $luong */
        //Cac phi khau tru
        $khauTru = GiaoDich::find()->andFilterWhere([GiaoDich::tableName() . '.user_id' => $user->id, GiaoDich::tableName() . '.type' => GiaoDich::RUT_TIEN])
            ->andFilterWhere(['=', 'month(' . GiaoDich::tableName() . '.created)', $thang])
            ->andFilterWhere(['=', 'year(' . GiaoDich::tableName() . '.created)', $nam])
            ->andWhere('type_id is not null');
        $totalKhauTru = $khauTru->sum('so_tien');
        $arrKhauTru = $khauTru->groupBy('type_id')
            ->leftJoin(DanhMuc::tableName(), DanhMuc::tableName() . '.id=' . GiaoDich::tableName() . '.type_id')
            ->select(['sum(so_tien) as so_tien', DanhMuc::tableName() . '.name'])->createCommand()->queryAll();
        /** @var ChiLuong $luong */
        foreach ($lichSuLuong as $luong) {

            $data [] = [
                'id' => $luong->donDichVu->id,
                'ma_don_hang' => $luong->donDichVu->ma_don_hang,
                'created' => date("d/m/Y", strtotime($luong->created)),
                'tong_tien' => $luong->tong_tien,
                'phuCap' => strval($luong->an_trua + $luong->them_gio),
                'thanh_tien' => $luong->thanh_tien,
                'buoi'=>$luong->buoiHoc->buoi
            ];
        }
        return $this->outputListSuccess2([
            'id' => $user->id,
            'hoten' => $user->hoten,
            'anh_nguoi_dung' => $user->getImage(),
            'trinh_do' => $user->trinh_do_name,
            'dien_thoai' => $user->dien_thoai,
            'tongLuong' => $tongLuongThucTe + $anTrua + $totalPhuPhiKhac - $totalKhauTru,
            'phuCap' => strval($totalPhuPhiKhac + $anTrua+$themGio),
            'anTrua' => $anTrua,
            'themGio' => $themGio,
            'phuPhiKhac' => $arrPhuPhiKhac,
            'arrKhauTru' => $arrKhauTru,
            'luong' => $data
        ], $count);
    }

    public function actionTaoPhieuLuong()
    {
        //Kiêm tra dau vao
        if ($this->dataGet['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        /** @var QuanLyUserVaiTro $user */
        $user = QuanLyUserVaiTro::find()->andFilterWhere(['id' => $this->dataGet['id'], 'active' => 1, 'status' => 10, 'vai_tro' => User::GIAO_VIEN, 'is_admin' => 0])->one();
        if (is_null($user)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        // Lọc theo ngay
        $lichSuLuong = ChiLuong::find()
            ->andFilterWhere([ChiLuong::tableName() . '.active' => 1, ChiLuong::tableName() . '.giao_vien_id' => $user->id]);
        $thoiGian = date("j/n/Y", strtotime("first day of this month")) . " - " . date("j/n/Y", strtotime("last day of this month"));
        $tuNgay = date("Y-n-j", strtotime("first day of this month"));
        $denNgay = date("Y-n-j", strtotime("last day of this month"));
        $thang = date("m");
        $nam = date("Y");
        if (isset($this->dataGet['thang'])) {
            if ($this->dataGet['thang'] != "") {
                $arr = explode('/', $this->dataGet['thang']);
                if (count($arr) != 2) {
                    throw new HttpException(500, "Định dạng tháng không hợp lệ.");
                }
                $thang = $arr[0];
                $nam = $arr[1];
                $tuNgay = date("$nam-$thang-j", strtotime("first day of this month"));
                $denNgay = date("$nam-$thang-t", strtotime($nam . "-" . $thang));
                $thoiGian = date("j/$thang/$nam", strtotime("first day of this month")) . " - " . date("t/$thang/$nam", strtotime($nam . "-" . $thang));
            }
        }
        //Query Dữ liệu theo tháng
        $lichSuLuong->andFilterWhere(['=', 'month(' . ChiLuong::tableName() . '.created)', $thang]);
        $lichSuLuong->andFilterWhere(['=', 'year(' . ChiLuong::tableName() . '.created)', $nam]);

        $tongLuongThucTe = $lichSuLuong->sum('tong_tien+them_gio');
        // Lay du lieu an trua phu phi
        $anTrua = $lichSuLuong->sum('an_trua');
        $themGio = $lichSuLuong->sum('them_gio');
        $lichSuLuong = $lichSuLuong->groupBy(['don_dich_vu_id'])->select(['id', 'don_dich_vu_id', 'tong_tien', 'count(id) as total_date']);
        // Lấy dữ liệu
        $lichSuLuong = $lichSuLuong->all();
        $data = [];
        //Lay danh sach don
        // Tinh tong tien phu phi
        // Danh sach cac loai phu phi
        $phuPhiKhac = GiaoDich::find()
            ->andFilterWhere([GiaoDich::tableName() . '.user_id' => $user->id, GiaoDich::tableName() . '.type' => GiaoDich::NAP_TIEN])
            ->andFilterWhere(['=', 'month(' . GiaoDich::tableName() . '.created)', $thang])
            ->andFilterWhere(['=', 'year(' . GiaoDich::tableName() . '.created)', $nam])
            ->andWhere('type_id is not null');
        $totalPhuPhiKhac = $phuPhiKhac->sum('so_tien');
        $arrPhuPhiKhac = $phuPhiKhac->groupBy('type_id')
            ->leftJoin(DanhMuc::tableName(), DanhMuc::tableName() . '.id=' . GiaoDich::tableName() . '.type_id')
            ->select(['sum(so_tien) as so_tien', DanhMuc::tableName() . '.name'])->createCommand()->queryAll();

        /** @var ChiLuong $luong */
        //Cac phi khau tru
        $khauTru = GiaoDich::find()->andFilterWhere([GiaoDich::tableName() . '.user_id' => $user->id, GiaoDich::tableName() . '.type' => GiaoDich::RUT_TIEN])
            ->andFilterWhere(['=', 'month(' . GiaoDich::tableName() . '.created)', $thang])
            ->andFilterWhere(['=', 'year(' . GiaoDich::tableName() . '.created)', $nam])
            ->andWhere('type_id is not null');
        $totalKhauTru = $khauTru->sum('so_tien');
        $arrKhauTru = $khauTru->groupBy('type_id')
            ->leftJoin(DanhMuc::tableName(), DanhMuc::tableName() . '.id=' . GiaoDich::tableName() . '.type_id')
            ->select(['sum(so_tien) as so_tien', DanhMuc::tableName() . '.name'])->createCommand()->queryAll();
        foreach ($lichSuLuong as $luong) {
            $data [] = [
                'id' => $luong->donDichVu->id,
                'ma_don_hang' => $luong->donDichVu->ma_don_hang,
                'so_buoi' => $luong->total_date,
                'tong_tien' => $luong->tong_tien,
            ];
        }
        // Lưu phiếu lương
        $phieuLuong = PhieuLuong::find()->andFilterWhere(['active' => 1])->andFilterWhere(['giao_vien_id' => $user->id])
            ->andFilterWhere(['=', 'month(tu_ngay)', $thang])
            ->andFilterWhere(['=', 'year(tu_ngay)', $nam])->one();
        if (is_null($phieuLuong)) {
            $phieuLuong = new PhieuLuong();
            $phieuLuong->trang_thai = PhieuLuong::CHUA_XAC_NHAN;
        }
        $phieuLuong->user_id = $this->uid;
        $phieuLuong->giao_vien_id = $user->id;
        $phieuLuong->tu_ngay = $tuNgay;
        $phieuLuong->den_ngay = $denNgay;
        $phieuLuong->chi_tiet_luong = json_encode($data);
        $phieuLuong->them_gio = $themGio;
        $phieuLuong->tong_luong_thuc_te = $tongLuongThucTe;
        $phieuLuong->an_trua = $anTrua;
        $phieuLuong->phu_phi_khac = json_encode($arrPhuPhiKhac);
        $phieuLuong->giam_tru = json_encode($arrKhauTru);
        $phieuLuong->tong_phu_phi = $totalPhuPhiKhac + $anTrua;
        $phieuLuong->tong_giam_tru = $totalKhauTru;
        $phieuLuong->thanh_tien = $tongLuongThucTe + $anTrua + $totalPhuPhiKhac - $totalKhauTru;
        $phieuLuong->tieu_de = "Phiếu lương tháng $thang/$nam";
        if (!$phieuLuong->save()) {
            throw new HttpException(500, Html::errorSummary($phieuLuong));
        }

        return $this->outputSuccess([
            'thoi_gian' => $thoiGian,
            'giaoVien'=>[
                'hoten' => $user->hoten,
                'dien_thoai' => $user->dien_thoai,
                'anh_nguoi_dung' => $user->getImage(),
                'id' => $user->id,
            ],
            'donDichVu' => $data,
            'themGio' => $themGio,
            'tongThucTe' => $tongLuongThucTe,
            'anTrua' => $anTrua,
            'tongPhuPhi' => strval(floatval($phieuLuong->tong_phu_phi)),
            'tongGiamTru' => strval(floatval($phieuLuong->tong_giam_tru)),
            'phuPhiKhac' => $arrPhuPhiKhac,
            'giamTru' => $arrKhauTru,
            'thanhTien' => strval($phieuLuong->thanh_tien),
            'phieu_luong_id' => $phieuLuong->id,
            'so_tien_da_thanh_toan' => $phieuLuong->so_tien_thanh_toan,
            'so_tien_con_lai' => strval($phieuLuong->thanh_tien - floatval($phieuLuong->so_tien_thanh_toan)),
        ]);
    }
    public function actionThanhToan()
    {
       
        $this->checkField(['phieu_luong_id', 'tong_tien', 'ghi_chu']);
        if ($this->dataPost['phieu_luong_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền phieu_luong_id");
            return false;
        }
        /** @var PhieuLuong $phieuLuong */
        $phieuLuong = PhieuLuong::findOne($this->dataPost['phieu_luong_id']);
        if (is_null($phieuLuong)) {
            throw new HttpException(403, "Không xác định dữ liệu");
            return false;
        }
        if ($phieuLuong->trang_thai == PhieuLuong::CHUA_XAC_NHAN) {
            throw new HttpException(500, "Phiếu lương chưa xác nhận");
            return false;
        }
        if ($phieuLuong->trang_thai == PhieuLuong::DA_THANH_TOAN) {
            throw new HttpException(500, "Phiếu lương đã thanh toán toàn bộ");
            return false;
        }
        if (floatval($this->dataPost['tong_tien']) < 100000) {
            throw new HttpException(500, "Số tiền tối thiểu 100.000");
            return false;
        }
        if (floatval($this->dataPost['tong_tien']) > ($phieuLuong->thanh_tien - floatval($phieuLuong->so_tien_thanh_toan))) {
            throw new HttpException(500, "Số tiền tối đa là " . number_format($phieuLuong->thanh_tien - floatval($phieuLuong->so_tien_thanh_toan), 0, ',', '.'));
            return false;
        }
       
        $transaction = Yii::$app->db->beginTransaction();
        try
        {
            if (!is_null($this->dataPost['tong_tien'])){
                if ($this->dataPost['tong_tien']==($phieuLuong->thanh_tien - floatval($phieuLuong->so_tien_thanh_toan))){
                    $phieuLuong->trang_thai = PhieuLuong::DA_THANH_TOAN;
                    $so_tien_da_thanh_toan = $phieuLuong->so_tien_thanh_toan;
                    $phieuLuong->so_tien_thanh_toan = $this->dataPost['tong_tien']+$so_tien_da_thanh_toan;
                }else{
                    $phieuLuong->trang_thai = PhieuLuong::THANH_TOAN_MOT_PHAN;
                    $so_tien_da_thanh_toan = $phieuLuong->so_tien_thanh_toan;
                    $phieuLuong->so_tien_thanh_toan = $this->dataPost['tong_tien']+$so_tien_da_thanh_toan;
                }
                $phieuLuong->ghi_chu = $this->dataPost['ghi_chu'] ?? '';
                $phieuLuong->updated = date('Y-m-d H:i:s');
                $phieuLuong->save();
                $trangThai = new LichSuTrangThaiPhieuLuong();
                $trangThai->user_id = $phieuLuong->user_id;
                $trangThai->phieu_luong_id = $phieuLuong->id;
                $trangThai->trang_thai = $phieuLuong->trang_thai;
                $trangThai->tong_tien = $this->dataPost['tong_tien'];
                $trangThai->ghi_chu = $this->dataPost['ghi_chu']  ?? '';
                $trangThai->save();
                $giaoDich = new GiaoDich();
                $giaoDich->so_tien = $this->dataPost['tong_tien'];
                $giaoDich->ghi_chu = $this->dataPost['ghi_chu']  ?? '';
                $giaoDich->type = GiaoDich::RUT_TIEN;
                $giaoDich->user_id = $phieuLuong->giao_vien_id;
                $giaoDich->tieu_de = "Trừ tiền vào tài khoản";
                $giaoDich->save();
            }
            $transaction->commit();
        }
        catch(Exception $e)
        {
            $transaction->rollBack();
        }
        return $this->outputSuccess("", "Thanh toán thành công");
    }
    public function actionExportPhieuLuong()
    {
        //Kiêm tra dau vao
        if ($this->dataGet['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        /** @var QuanLyUserVaiTro $user */
        $user = QuanLyUserVaiTro::find()->andFilterWhere(['id' => $this->dataGet['id'], 'active' => 1, 'status' => 10, 'vai_tro' => User::GIAO_VIEN, 'is_admin' => 0])->one();
        if (is_null($user)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        // Lọc theo ngay
        $lichSuLuong = ChiLuong::find()
            ->andFilterWhere([ChiLuong::tableName() . '.active' => 1, ChiLuong::tableName() . '.giao_vien_id' => $user->id]);
        $thoiGian = date("j/n/Y", strtotime("first day of this month")) . " - " . date("j/n/Y", strtotime("last day of this month"));
        $tuNgay = date("Y-n-j", strtotime("first day of this month"));
        $denNgay = date("Y-n-j", strtotime("last day of this month"));
        $thang = date("m");
        $nam = date("Y");
        if (isset($this->dataGet['thang'])) {
            if ($this->dataGet['thang'] != "") {
                $arr = explode('/', $this->dataGet['thang']);
                if (count($arr) != 2) {
                    throw new HttpException(500, "Định dạng tháng không hợp lệ.");
                }
                $thang = $arr[0];
                $nam = $arr[1];
                $tuNgay = date("$nam-$thang-j", strtotime("first day of this month"));
                $denNgay = date("$nam-$thang-t", strtotime($nam . "-" . $thang));
                $thoiGian = date("j/$thang/$nam", strtotime("first day of this month")) . " - " . date("t/$thang/$nam", strtotime($nam . "-" . $thang));
            }
        }
        //Query Dữ liệu theo tháng
        $lichSuLuong->andFilterWhere(['=', 'month(' . ChiLuong::tableName() . '.created)', $thang]);
        $lichSuLuong->andFilterWhere(['=', 'year(' . ChiLuong::tableName() . '.created)', $nam]);

        $tongLuongThucTe = $lichSuLuong->sum('tong_tien+them_gio');
        // Lay du lieu an trua phu phi
        $anTrua = $lichSuLuong->sum('an_trua');
        $themGio = $lichSuLuong->sum('them_gio');
        $lichSuLuong = $lichSuLuong->groupBy(['don_dich_vu_id'])->select(['id', 'don_dich_vu_id', 'tong_tien', 'count(id) as total_date']);
        // Lấy dữ liệu
        $lichSuLuong = $lichSuLuong->all();
        $data = [];
        //Lay danh sach don
        // Tinh tong tien phu phi
        // Danh sach cac loai phu phi
        $phuPhiKhac = GiaoDich::find()
            ->andFilterWhere([GiaoDich::tableName() . '.user_id' => $user->id, GiaoDich::tableName() . '.type' => GiaoDich::NAP_TIEN])
            ->andFilterWhere(['=', 'month(' . GiaoDich::tableName() . '.created)', $thang])
            ->andFilterWhere(['=', 'year(' . GiaoDich::tableName() . '.created)', $nam])
            ->andWhere('type_id is not null');
        $totalPhuPhiKhac = $phuPhiKhac->sum('so_tien');
        $arrPhuPhiKhac = $phuPhiKhac->groupBy('type_id')
            ->leftJoin(DanhMuc::tableName(), DanhMuc::tableName() . '.id=' . GiaoDich::tableName() . '.type_id')
            ->select(['sum(so_tien) as so_tien', DanhMuc::tableName() . '.name'])->createCommand()->queryAll();

        /** @var ChiLuong $luong */
        //Cac phi khau tru
        $khauTru = GiaoDich::find()->andFilterWhere([GiaoDich::tableName() . '.user_id' => $user->id, GiaoDich::tableName() . '.type' => GiaoDich::RUT_TIEN])
            ->andFilterWhere(['=', 'month(' . GiaoDich::tableName() . '.created)', $thang])
            ->andFilterWhere(['=', 'year(' . GiaoDich::tableName() . '.created)', $nam])
            ->andWhere('type_id is not null');
        $totalKhauTru = $khauTru->sum('so_tien');
        $arrKhauTru = $khauTru->groupBy('type_id')
            ->leftJoin(DanhMuc::tableName(), DanhMuc::tableName() . '.id=' . GiaoDich::tableName() . '.type_id')
            ->select(['sum(so_tien) as so_tien', DanhMuc::tableName() . '.name'])->createCommand()->queryAll();
        foreach ($lichSuLuong as $luong) {
            $data [] = [
                'id' => $luong->id,
                'ma_don_hang' => $luong->donDichVu->ma_don_hang,
                'so_buoi' => $luong->total_date,
                'tong_tien' => $luong->tong_tien,
            ];
        }
        // Lưu phiếu lương
        $phieuLuong = PhieuLuong::find()->andFilterWhere(['active' => 1])->andFilterWhere(['giao_vien_id' => $user->id])
            ->andFilterWhere(['=', 'month(tu_ngay)', $thang])
            ->andFilterWhere(['=', 'year(tu_ngay)', $nam])->one();
        if (is_null($phieuLuong)) {
            $phieuLuong = new PhieuLuong();
            $phieuLuong->trang_thai = PhieuLuong::CHUA_XAC_NHAN;
        }
        $phieuLuong->user_id = $this->uid;
        $phieuLuong->giao_vien_id = $user->id;
        $phieuLuong->tu_ngay = $tuNgay;
        $phieuLuong->den_ngay = $denNgay;
        $phieuLuong->chi_tiet_luong = json_encode($data);
        $phieuLuong->them_gio = $themGio;
        $phieuLuong->tong_luong_thuc_te = $tongLuongThucTe;
        $phieuLuong->an_trua = $anTrua;
        $phieuLuong->phu_phi_khac = json_encode($arrPhuPhiKhac);
        $phieuLuong->giam_tru = json_encode($arrKhauTru);
        $phieuLuong->tong_phu_phi = $totalPhuPhiKhac + $anTrua;
        $phieuLuong->tong_giam_tru = $totalKhauTru;
        $phieuLuong->thanh_tien = $tongLuongThucTe + $anTrua + $totalPhuPhiKhac - $totalKhauTru;
        $phieuLuong->tieu_de = "Phiếu lương tháng $thang/$nam";
        if (!$phieuLuong->save()) {
            throw new HttpException(500, Html::errorSummary($phieuLuong));
        }
        $export = new exportExcelPhieuLuong();
        $export->data = [
            'thoi_gian' => $thoiGian,
            'hoten' => $user->hoten,
            'id' => $user->id,
            'leader'=>$user->getLeader(),
            'donDichVu' => $data,
            'themGio' => $themGio,
            'tongThucTe' => $tongLuongThucTe,
            'anTrua' => $anTrua,
            'tongPhuPhi' => strval(floatval($phieuLuong->tong_phu_phi)),
            'tongGiamTru' => strval(floatval($phieuLuong->tong_giam_tru)),
            'phuPhiKhac' => $arrPhuPhiKhac,
            'giamTru' => $arrKhauTru,
            'thanhTien' => strval($phieuLuong->thanh_tien),
            'phieu_luong_id' => $phieuLuong->id,
            'so_tien_da_thanh_toan' => $phieuLuong->so_tien_thanh_toan,
            'so_tien_con_lai' => strval($phieuLuong->thanh_tien - floatval($phieuLuong->so_tien_thanh_toan)),
            'tuNgay' => isset($this->dataGet['tuNgay']) ?? "",
            'denNgay' => isset($this->dataGet['denNgay']) ?? "",
        ];
        $export->stream = false;
        $export->path_file = dirname(dirname(__DIR__)) . '/files_excel/';
        $file_name = $export->run();
        $file = CauHinh::getServer() . '/files_excel/' . $file_name;;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->outputSuccess($file);
    }
    public function actionPdfPhieuLuong()
    {
        //Kiêm tra dau vao
        if ($this->dataGet['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        /** @var QuanLyUserVaiTro $user */
        $user = QuanLyUserVaiTro::find()->andFilterWhere(['id' => $this->dataGet['id'], 'active' => 1, 'status' => 10, 'vai_tro' => User::GIAO_VIEN, 'is_admin' => 0])->one();
        if (is_null($user)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        // Lọc theo ngay
        $lichSuLuong = ChiLuong::find()
            ->andFilterWhere([ChiLuong::tableName() . '.active' => 1, ChiLuong::tableName() . '.giao_vien_id' => $user->id]);
        $thoiGian = date("j/n/Y", strtotime("first day of this month")) . " - " . date("j/n/Y", strtotime("last day of this month"));
        $tuNgay = date("Y-n-j", strtotime("first day of this month"));
        $denNgay = date("Y-n-j", strtotime("last day of this month"));
        $thang = date("m");
        $nam = date("Y");
        if (isset($this->dataGet['thang'])) {
            if ($this->dataGet['thang'] != "") {
                $arr = explode('/', $this->dataGet['thang']);
                if (count($arr) != 2) {
                    throw new HttpException(500, "Định dạng tháng không hợp lệ.");
                }
                $thang = $arr[0];
                $nam = $arr[1];
                $tuNgay = date("$nam-$thang-j", strtotime("first day of this month"));
                $denNgay = date("$nam-$thang-t", strtotime($nam . "-" . $thang));
                $thoiGian = date("j/$thang/$nam", strtotime("first day of this month")) . " - " . date("t/$thang/$nam", strtotime($nam . "-" . $thang));
            }
        }
        //Query Dữ liệu theo tháng
        $lichSuLuong->andFilterWhere(['=', 'month(' . ChiLuong::tableName() . '.created)', $thang]);
        $lichSuLuong->andFilterWhere(['=', 'year(' . ChiLuong::tableName() . '.created)', $nam]);

        $tongLuongThucTe = $lichSuLuong->sum('tong_tien+them_gio');
        // Lay du lieu an trua phu phi
        $anTrua = $lichSuLuong->sum('an_trua');
        $themGio = $lichSuLuong->sum('them_gio');
        $lichSuLuong = $lichSuLuong->groupBy(['don_dich_vu_id'])->select(['id', 'don_dich_vu_id', 'tong_tien', 'count(id) as total_date']);
        // Lấy dữ liệu
        $lichSuLuong = $lichSuLuong->all();
        $data = [];
        //Lay danh sach don
        // Tinh tong tien phu phi
        // Danh sach cac loai phu phi
        $phuPhiKhac = GiaoDich::find()
            ->andFilterWhere([GiaoDich::tableName() . '.user_id' => $user->id, GiaoDich::tableName() . '.type' => GiaoDich::NAP_TIEN])
            ->andFilterWhere(['=', 'month(' . GiaoDich::tableName() . '.created)', $thang])
            ->andFilterWhere(['=', 'year(' . GiaoDich::tableName() . '.created)', $nam])
            ->andWhere('type_id is not null');
        $totalPhuPhiKhac = $phuPhiKhac->sum('so_tien');
        $arrPhuPhiKhac = $phuPhiKhac->groupBy('type_id')
            ->leftJoin(DanhMuc::tableName(), DanhMuc::tableName() . '.id=' . GiaoDich::tableName() . '.type_id')
            ->select(['sum(so_tien) as so_tien', DanhMuc::tableName() . '.name'])->createCommand()->queryAll();

        /** @var ChiLuong $luong */
        //Cac phi khau tru
        $khauTru = GiaoDich::find()->andFilterWhere([GiaoDich::tableName() . '.user_id' => $user->id, GiaoDich::tableName() . '.type' => GiaoDich::RUT_TIEN])
            ->andFilterWhere(['=', 'month(' . GiaoDich::tableName() . '.created)', $thang])
            ->andFilterWhere(['=', 'year(' . GiaoDich::tableName() . '.created)', $nam])
            ->andWhere('type_id is not null');
        $totalKhauTru = $khauTru->sum('so_tien');
        $arrKhauTru = $khauTru->groupBy('type_id')
            ->leftJoin(DanhMuc::tableName(), DanhMuc::tableName() . '.id=' . GiaoDich::tableName() . '.type_id')
            ->select(['sum(so_tien) as so_tien', DanhMuc::tableName() . '.name'])->createCommand()->queryAll();
        foreach ($lichSuLuong as $luong) {
            $data [] = [
                'id' => $luong->id,
                'ma_don_hang' => $luong->donDichVu->ma_don_hang,
                'so_buoi' => $luong->total_date,
                'tong_tien' => $luong->tong_tien,
            ];
        }
        // Lưu phiếu lương
        $phieuLuong = PhieuLuong::find()->andFilterWhere(['active' => 1])->andFilterWhere(['giao_vien_id' => $user->id])
            ->andFilterWhere(['=', 'month(tu_ngay)', $thang])
            ->andFilterWhere(['=', 'year(tu_ngay)', $nam])->one();
        if (is_null($phieuLuong)) {
            $phieuLuong = new PhieuLuong();
            $phieuLuong->trang_thai = PhieuLuong::CHUA_XAC_NHAN;
        }
        $phieuLuong->user_id = $this->uid;
        $phieuLuong->giao_vien_id = $user->id;
        $phieuLuong->tu_ngay = $tuNgay;
        $phieuLuong->den_ngay = $denNgay;
        $phieuLuong->chi_tiet_luong = json_encode($data);
        $phieuLuong->them_gio = $themGio;
        $phieuLuong->tong_luong_thuc_te = $tongLuongThucTe;
        $phieuLuong->an_trua = $anTrua;
        $phieuLuong->phu_phi_khac = json_encode($arrPhuPhiKhac);
        $phieuLuong->giam_tru = json_encode($arrKhauTru);
        $phieuLuong->tong_phu_phi = $totalPhuPhiKhac + $anTrua;
        $phieuLuong->tong_giam_tru = $totalKhauTru;
        $phieuLuong->thanh_tien = $tongLuongThucTe + $anTrua + $totalPhuPhiKhac - $totalKhauTru;
        $phieuLuong->tieu_de = "Phiếu lương tháng $thang/$nam";
        if (!$phieuLuong->save()) {
            throw new HttpException(500, Html::errorSummary($phieuLuong));
        }
        $dataPDF = [
            'thoi_gian' => $thoiGian,
            'hoten' => $user->hoten,
            'id' => $user->id,
            'leader'=>$user->getLeader(),
            'donDichVu' => $data,
            'themGio' => $themGio,
            'tongThucTe' => $tongLuongThucTe,
            'anTrua' => $anTrua,
            'tongPhuPhi' => strval(floatval($phieuLuong->tong_phu_phi)),
            'tongGiamTru' => strval(floatval($phieuLuong->tong_giam_tru)),
            'phuPhiKhac' => $arrPhuPhiKhac,
            'giamTru' => $arrKhauTru,
            'thanhTien' => strval($phieuLuong->thanh_tien),
            'phieu_luong_id' => $phieuLuong->id,
            'so_tien_da_thanh_toan' => $phieuLuong->so_tien_thanh_toan,
            'so_tien_con_lai' => strval($phieuLuong->thanh_tien - floatval($phieuLuong->so_tien_thanh_toan)),
            'tuNgay' => isset($this->dataGet['tuNgay']) ?? "",
            'denNgay' => isset($this->dataGet['denNgay']) ?? "",
        ];
        $count = 0;
        $chiTietDon="";
        foreach ($dataPDF['donDichVu'] as $item) {
            $count++;
            $item['tong_tien']= number_format($item['tong_tien']);
            $chiTietDon.="<tr>
                <td  width='10%' style='text-align: center'>1. $count</td>
                <td> {$item['ma_don_hang']}</td>
                <td width='25%'> {$item['so_buoi']}</td>
                <td  width='20%' style='text-align: right'> {$item['tong_tien']}</td>
            </tr>";
        }
        $dataPDF['themGio'] = number_format($dataPDF['themGio']);
        $tbLamThemGio="<tr>
                <td  width='10%' style='text-align: center'>1. ".($count+1)."</td>
                <td> Làm thêm giờ</td>
                <td width='25%'></td>
                <td  width='20%' style='text-align: right'> {$dataPDF['themGio']}</td>
            </tr>";
        $dataPDF['tongThucTe'] = number_format($dataPDF['tongThucTe']);
        $tbTongLuongThucTe="<tr>
                <td  width='10%' style='text-align: center'>2</td>
                <td>Tổng lương theo ngày công thực tế</td>
                <td width='25%'></td>
                <td  width='20%' style='text-align: right'> {$dataPDF['tongThucTe']}</td>
            </tr>";
        $dataPDF['tongPhuPhi'] = number_format($dataPDF['tongPhuPhi']);
        $tbThuNhapBoSung="<tr>
                <td  width='10%' style='text-align: center'>3</td>
                <td>Thu nhập bổ sung</td>
                <td width='25%'></td>
                <td  width='20%' style='text-align: right'> {$dataPDF['tongPhuPhi']}</td>
            </tr>";
        $dataPDF['anTrua'] = number_format($dataPDF['anTrua']);
        $tbAnTrua="<tr>
                <td  width='10%' style='text-align: center'>3.1</td>
                <td>Phụ cấp ăn trưa</td>
                <td width='25%'></td>
                <td  width='20%' style='text-align: right'> {$dataPDF['anTrua']}</td>
            </tr>";
        $tbPhuPhi="";
        $count = 1;
        foreach ($dataPDF['phuPhiKhac'] as $item) {
            $count++;
            $item['so_tien']= number_format($item['so_tien']);
            $tbPhuPhi.="<tr>
                <td  width='10%' style='text-align: center'>3. $count</td>
                <td> {$item['name']}</td>
                <td width='25%'> </td>
                <td  width='20%'  style='text-align: right'> {$item['so_tien']}</td>
            </tr>";
        }
        $dataPDF['tongGiamTru'] = number_format($dataPDF['tongGiamTru']);
        $tbTongGiamTru="<tr>
                <td  width='10%' style='text-align: center'>4</td>
                <td>Các khoản giảm trừ</td>
                <td width='25%'></td>
                <td  width='20%'  style='text-align: right'> {$dataPDF['tongGiamTru']}</td>
            </tr>";
        $tbGiamTru="";
        $count = 1;
        foreach ($dataPDF['giamTru'] as $item) {
            $count++;
            $item['so_tien']= number_format($item['so_tien']);
            $tbGiamTru.="<tr>
                <td  width='10%' style='text-align: center'>4. $count</td>
                <td> {$item['name']}</td>
                <td width='25%'> </td>
                <td  width='20%'  style='text-align: right'> {$item['so_tien']}</td>
            </tr>";
        }
        $dataPDF['thanhTien'] = number_format($dataPDF['thanhTien']);
        $tbThangTien="<tr>
                <td  width='10%' style='text-align: center'>5</td>
                <td>Thành tiền</td>
                <td width='25%'></td>
                <td  width='20%'  style='text-align: right'> {$dataPDF['thanhTien']}</td>
            </tr>";
        $chiTietPhieu = "
            <table border='1' style='width: 100%;white-space: nowrap'>
                <thead>
                    <tr>
                        <th width='1%'></th>
                        <th>Chi tiết khoản mục</th>
                        <th width='25%'>Ngày công thực tế </th>
                        <th width='20%'>Đơn giá </th>
                    </tr>    
                </thead>
                <tbody>
                    <tr>
                        <td  width='10%' style='text-align: center'>1</td>
                        <td>Thông tin tính lương (Ngày công x Số tiền)</td>
                        <td  width='25%'></td>
                        <td  width='20%' style='text-align: right'></td>
                    </tr>
                    $chiTietDon    
                    $tbLamThemGio
                    $tbTongLuongThucTe
                    $tbThuNhapBoSung
                    $tbAnTrua
                    $tbPhuPhi
                    $tbTongGiamTru
                    $tbGiamTru
                    $tbThangTien
                </tbody>
            </table>
        ";
        $form = (new CauHinh())->getNoiDung(38);
        $form = str_replace("{{thoi_gian}}",$dataPDF['thoi_gian'],$form);
        $form = str_replace("{{ma_doi_tac}}",$dataPDF['id'],$form);
        $form = str_replace("{{ten_khach_hang}}",$dataPDF['hoten'],$form);
        $form = str_replace("{{leader}}",$dataPDF['leader'],$form);
        $form = str_replace("{{chi_tiet_phieu}}",$chiTietPhieu,$form);
        return $this->outputSuccess($this->formPDF('Phieu_Luong_'.date('d_m_Y__H_i_s')."(".$dataPDF['hoten'].')',$form));
    }

}
