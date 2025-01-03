<?php namespace backend\controllers;

use backend\models\CauHinh;
use backend\models\ChiLuong;
use backend\models\DanhMuc;
use backend\models\DichVu;
use backend\models\DonDichVu;
use backend\models\GiaDichVu;
use backend\models\GiaoDich;
use backend\models\NhanLich;
use backend\models\LichSuTrangThaiDon;
use backend\models\LichSuTrangThaiThanhToan;
use backend\models\PhuPhi;
use backend\models\QuanLyUserVaiTro;
use backend\models\TienDoKhoaHoc;
use backend\models\VaiTro;
use common\models\exportExcelBaoCaoDoanhThu;
use common\models\exportExcelBaoCaoDoanhThuTheoNgay;
use common\models\exportExcelBaoCaoGiaoDich;
use common\models\exportExcelBaoCaoKhachHang;
use common\models\exportExcelBaoCaoTinhTrangDon;
use common\models\exportExcelBaoCaoUser;
use common\models\User;
use DateTime;
use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\HttpException;
use yii\web\Response;
use function GuzzleHttp\Psr7\str;

class BaoCaoController extends CoreApiController
{
    public function actionTongQuan()
    {
        $thang = date("m");
        $nam = date("Y");
        //Kiểm tra dau vao
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
        //DoanhThu
        $tongTien = DonDichVu::find()->andFilterWhere(['active' => 1])
            ->andFilterWhere(['=', 'year(created)', $nam])
            ->andFilterWhere(['=', 'month(created)', $thang]);
        $tongTienThangTruoc = DonDichVu::find()->andFilterWhere(['active' => 1])
            ->andFilterWhere(['=', 'year(created)', $thang - 1 == 0 ? $nam - 1 : $nam])
            ->andFilterWhere(['=', 'month(created)', $thang - 1 == 0 ? 12 : $thang - 1]);
        $tongTienThucTe = DonDichVu::find()->andFilterWhere(['active' => 1])
            ->andFilterWhere(['trang_thai_thanh_toan' => LichSuTrangThaiThanhToan::DA_THANH_TOAN])
            ->andFilterWhere(['=', 'year(created)', $nam])
            ->andFilterWhere(['=', 'month(created)', $thang])
            ->sum('tong_tien');
        $tongDoanhThu = $tongTien->sum('tong_tien');//Tong doanh thu thang hien tai
        $tongDoanhThuHoan =  DonDichVu::find()->andFilterWhere(['active' => 1])
        ->andFilterWhere(['trang_thai' => LichSuTrangThaiDon::DON_HOAN])
        ->andFilterWhere(['=', 'year(created)', $nam])
        ->andFilterWhere(['=', 'month(created)', $thang])
        ->sum('so_tien_hoan');
        $tongDoanhThuHuy =  DonDichVu::find()->andFilterWhere(['active' => 1])
        ->andFilterWhere(['trang_thai' => LichSuTrangThaiDon::DA_HUY])
        ->andFilterWhere(['=', 'year(created)', $nam])
        ->andFilterWhere(['=', 'month(created)', $thang])
        ->sum('tong_tien');
        $tongDoanhThuMuonLay = $tongDoanhThu - $tongDoanhThuHoan - $tongDoanhThuHuy;
        $tongDoanhThuThucTe = $tongTienThucTe;// Tong doanh thu thuc thang hien tai
        $tongDoanhThuThangTruoc = $tongTienThangTruoc->sum('tong_tien'); // Tong doanh thu thang truoc
        $tongSoDon = intval($tongTien->count());
        $tongSoDonHoanThanh = $tongTien->andFilterWhere(['trang_thai' => LichSuTrangThaiDon::HOAN_THANH])->count();
        $tongSoDonThangTruoc = intval($tongTienThangTruoc->count());
        $soKhachHangDaDatDon = DonDichVu::find()->andFilterWhere(['active' => 1])
            ->andFilterWhere(['=', 'year(created)', $nam])
            ->andFilterWhere(['=', 'month(created)', $thang])
            ->groupBy("phu_huynh_id")->count();
        $tongSoKhachHang = QuanLyUserVaiTro::find()
            ->andFilterWhere(['=', 'year(created_at)', $nam])
            ->andFilterWhere(['=', 'month(created_at)', $thang])
            ->andFilterWhere(['active' => 1, 'is_admin' => 0, 'status' => 10, 'vai_tro' => User::PHU_HUYNH])
            ->count();
        $tongSoKhachHangThangTruoc = QuanLyUserVaiTro::find()
            ->andFilterWhere(['=', 'year(created_at)', $thang - 1 == 0 ? $nam - 1 : $nam])
            ->andFilterWhere(['=', 'month(created_at)', $thang - 1 == 0 ? 12 : $thang - 1])
            ->andFilterWhere(['active' => 1, 'is_admin' => 0, 'status' => 10, 'vai_tro' => User::PHU_HUYNH])
            ->count();
        // Loi nhuan
        $user = QuanLyUserVaiTro::findOne(['id' => \Yii::$app->controller->uid ?? 0]);


        $loiNhan = GiaoDich::find()
            ->andFilterWhere(['=', 'year(' . GiaoDich::tableName() . '.created)', $nam])
            ->andFilterWhere(['=', 'month(' . GiaoDich::tableName() . '.created)', $thang]);
        $loiNhuanThangTruoc = GiaoDich::find()
            ->andFilterWhere(['=', 'year(' . GiaoDich::tableName() . '.created)', $thang - 1 == 0 ? $nam - 1 : $nam])
            ->andFilterWhere(['=', 'month(' . GiaoDich::tableName() . '.created)', $thang - 1 == 0 ? 12 : $thang - 1]);
        if ($user->vai_tro !== VaiTro::LEADER_KD) {
            $loiNhan = $loiNhan->innerJoin(DonDichVu::tableName(), DonDichVu::tableName() . '.id=' . GiaoDich::tableName() . '.don_dich_vu_id');
            $loiNhuanThangTruoc = $loiNhuanThangTruoc->innerJoin(DonDichVu::tableName(), DonDichVu::tableName() . '.id=' . GiaoDich::tableName() . '.don_dich_vu_id');
            $loiNhan = $loiNhan->sum(DonDichVu::tableName() . '.tong_tien/' . DonDichVu::tableName() . '.so_buoi-' . GiaoDich::tableName() . '.so_tien');
            $loiNhuanThangTruoc = $loiNhuanThangTruoc->sum(DonDichVu::tableName() . '.tong_tien/' . DonDichVu::tableName() . '.so_buoi-' . GiaoDich::tableName() . '.so_tien');
        }else{
            $loiNhan = 0;
            $loiNhuanThangTruoc = 0;
        }
        return $this->outputSuccess([
            'tongDoanhThu' => [
                'tong' => $tongDoanhThuMuonLay,
                'thuc_te' => $tongDoanhThu > 0 ? round($tongDoanhThuThucTe * 100 / $tongDoanhThu) : 0,
                'tang_truong' => $tongDoanhThu > $tongDoanhThuThangTruoc && $tongDoanhThuThangTruoc > 0 ? round(($tongDoanhThu - $tongDoanhThuThangTruoc) * 100 / $tongDoanhThuThangTruoc) : 0,
            ],
            'loiNhuan' => [
                'tong' => $loiNhan,
                'thuc_te' => $tongDoanhThu > 0 ? round($loiNhan * 100 / $tongDoanhThu) : 0,
                'tang_truong' => $loiNhan > $loiNhuanThangTruoc && $loiNhuanThangTruoc > 0 ? round(($loiNhan - $loiNhuanThangTruoc) * 100 / $loiNhuanThangTruoc) : 0,
            ],
            'soDon' => [
                'tong' => $tongSoDon,
                'thuc_te' => $tongSoDon > 0 ? round($tongSoDonHoanThanh * 100 / $tongSoDon) : 0,
                'tang_truong' => $tongSoDon > $tongSoDonThangTruoc && $tongSoDonThangTruoc > 0 ? round(($tongSoDon - $tongSoDonThangTruoc) * 100 / $tongSoDonThangTruoc) : 0,
            ],
            'soKhachHang' => [
                'tong' => $tongSoKhachHang,
                'thuc_te' => $tongSoKhachHang > 0 ? round($soKhachHangDaDatDon * 100 / $tongSoKhachHang) : 0,
                'tang_truong' => $tongSoKhachHang > $tongSoKhachHangThangTruoc && $tongSoKhachHangThangTruoc > 0 ? round(($tongSoKhachHang - $tongSoKhachHangThangTruoc) * 100 / $tongSoKhachHangThangTruoc) : 0,
            ]
        ]);
    }

    /**
     * @throws Exception
     * @throws HttpException
     * @throws \Exception
     */
    public function actionTongQuanDoanhThu()
    {
        $type = 0;
        if (isset($this->dataGet['type'])) {
            $type = $this->dataGet['type'];
        }
        $data = [];
        $dichVu = DichVu::find()->andFilterWhere(['active' => 1])->orderBy(['seq' => SORT_ASC])->all();
        /** @var DichVu $item */
        foreach ($dichVu as $item) {
            $donDichVu = DonDichVu::find()->andFilterWhere(['active' => 1]);
            switch ($type) {
                case 0:
                {
                    $groupBy = ["date(created)"];
                    $to = date("Y-m-d");
                    $from = date('Y-m-d', strtotime($to . '-6 day'));
                    $donDichVu = $donDichVu
                        ->andFilterWhere(['>=', 'date(created)', $from])
                        ->andFilterWhere(['<=', 'date(created)', $to]);
                    $begin = new DateTime($from);
                    $end = new DateTime($to);
                    break;
                }
                case 1:
                {
                    $groupBy = ["month(created)", "year(created)"];
                    $to = date("Y-m-t");
                    $from = date('Y-m-1', strtotime($to . '-3 month'));
                    $donDichVu = $donDichVu
                        ->andFilterWhere(['>=', 'date(created)', $from])
                        ->andFilterWhere(['<=', 'date(created)', $to]);
                    $begin = new DateTime($from);
                    $end = new DateTime($to);
                    break;
                }
                case 2:
                {
                    $groupBy = ["year(created)"];
                    $to = date("Y");
                    $from = $to - 3;
                    $donDichVu = $donDichVu
                        ->andFilterWhere(['>=', 'year(created)', $from])
                        ->andFilterWhere(['<=', 'year(created)', $to]);
                    $begin = new DateTime(date("$from-1-1"));
                    $end = new DateTime(date("$to-12-31"));
                    break;
                }
                default:
                {
                    throw new HttpException(500, "Chọn loại biểu đồi không hợp lệ");
                }
            }
            $arrList = ArrayHelper::map(
                $donDichVu->andFilterWhere(['dich_vu_id' => $item->id])
                    ->groupBy($groupBy)->select(['date(created) created', "sum(tong_tien) as tong_tien"])
                    ->all(), 'created', 'tong_tien');
            $arrList2 = [];
            for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                $totalMoney = 0;
                if (!isset($arrList[$i->format("Y-m-d")])) {
                    $arrList[$i->format("Y-m-d")] = $totalMoney;
                }
                $arrList2[] = [
                    'created' => $i->format("Y-m-d"),
                    'tong_tien' => $arrList[$i->format("Y-m-d")],
                ];
            }
            $arr['id'] = $item->id;
            $arr['name'] = $item->ten_dich_vu;
            $arr['list'] = $arrList2;
            $data[] = $arr;
        }
        return $this->outputSuccess($data);
    }

    public function actionBaoCaoDoanhThu()
    {
        $donDichVu = DonDichVu::find();
        $donDichVu = $donDichVu->andFilterWhere(['>=', 'date(' . DonDichVu::tableName() . '.created)', $this->tuNgay]);
        $donDichVu = $donDichVu->andFilterWhere(['<=', 'date(' . DonDichVu::tableName() . '.created)', $this->denNgay]);
        if (isset($this->dataGet['dien_thoai']))
            if ($this->dataGet['dien_thoai'] != "") {
                $donDichVu = $donDichVu->andFilterWhere(['like', User::tableName() . '.dien_thoai', $this->dataGet['dien_thoai']]);
            }
        if (isset($this->dataGet['leader_kd_id']))
            if ($this->dataGet['leader_kd_id'] != "") {
                $donDichVu = $donDichVu->andFilterWhere([DonDichVu::tableName() . '.leader_kd_id' => $this->dataGet['leader_kd_id']]);
            }
        if (isset($this->dataGet['dia_chi']))
            if ($this->dataGet['dia_chi'] != "") {
                $donDichVu = $donDichVu->andFilterWhere(['like', DonDichVu::tableName() . '.dia_chi', $this->dataGet['dia_chi']]);
            }
        if (isset($this->dataGet['dich_vu_id']))
            if ($this->dataGet['dich_vu_id'] != "") {
                $donDichVu = $donDichVu->andFilterWhere([DonDichVu::tableName() . '.dich_vu_id', $this->dataGet['dich_vu_id']]);
            }
        $donDichVu = $donDichVu->andFilterWhere([DonDichVu::tableName() . '.active' => 1])
            ->leftJoin(User::tableName(), User::tableName() . '.id=' . DonDichVu::tableName() . '.user_id');
        $count = count($donDichVu->all());
        $donDichVu = $donDichVu->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy([DonDichVu::tableName() . '.created' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        $data = [];
        /** @var DonDichVu $item */
        foreach ($donDichVu as $item) {
            $phuHuynh = $item->phuHuynh;
            $giaoVien = $item->giaoVien;
            $data[] = [
                'id' => $item->id,
                'ma_don_hang' => $item->ma_don_hang,
                'ngay_thanh_toan' => !is_null($item->ngay_thanh_toan) ? date("d/m/Y", strtotime($item->ngay_thanh_toan)) : "",
                'phuHuynh' => is_null($phuHuynh) ? null : [
                    'id' => $phuHuynh->id,
                    'ho_ten' => $phuHuynh->hoten,
                    'anh_nguoi_dung' => $phuHuynh->getImage(),
                    'dien_thoai' => $phuHuynh->dien_thoai,
                ],
                'giaoVien' => is_null($giaoVien) ? null : [
                    'id' => $giaoVien->id,
                    'image' => $giaoVien->getImage(),
                    'hoten' => $giaoVien->hoten,
                    'dien_thoai' => $giaoVien->dien_thoai,
                    'trinh_do' => $giaoVien->getTrinhDo(),
                    'danh_gia' => $giaoVien->danh_gia,
                ],
                'goi' => $item->goiHocPhi->thanh_tien,
                'ma_don_hang' => $item->ma_don_hang,
                'dichVu' => $item->dichVu->ten_dich_vu,
                'trang_thai' => $item->trang_thai,
                'soBuoiHoanThanh' => $item->getTrangThaiTienDo(),
                'soTienConDu' => $item->soTienConDu(),
            ];
        }
        return $this->outputListSuccess2($data, $count);
    }

    public function actionExportExcelBaoCaoDoanhThu()
    {
        $donDichVu = DonDichVu::find();
        $donDichVu = $donDichVu->andFilterWhere(['>=', 'date(' . DonDichVu::tableName() . '.created)', $this->tuNgay]);
        $donDichVu = $donDichVu->andFilterWhere(['<=', 'date(' . DonDichVu::tableName() . '.created)', $this->denNgay]);
        if (isset($this->dataGet['dien_thoai']))
            if ($this->dataGet['dien_thoai'] != "") {
                $donDichVu = $donDichVu->andFilterWhere(['like', User::tableName() . '.dien_thoai', $this->dataGet['dien_thoai']]);
            }
        if (isset($this->dataGet['leader_kd_id']))
            if ($this->dataGet['leader_kd_id'] != "") {
                $donDichVu = $donDichVu->andFilterWhere([DonDichVu::tableName() . '.leader_kd_id' => $this->dataGet['leader_kd_id']]);
            }
        if (isset($this->dataGet['dia_chi']))
            if ($this->dataGet['dia_chi'] != "") {
                $donDichVu = $donDichVu->andFilterWhere(['like', DonDichVu::tableName() . '.dia_chi', $this->dataGet['dia_chi']]);
            }
        if (isset($this->dataGet['dich_vu_id']))
            if ($this->dataGet['dich_vu_id'] != "") {
                $donDichVu = $donDichVu->andFilterWhere([DonDichVu::tableName() . '.dich_vu_id', $this->dataGet['dich_vu_id']]);
            }
        $donDichVu = $donDichVu->andFilterWhere([DonDichVu::tableName() . '.active' => 1])->leftJoin(User::tableName(), User::tableName() . '.id=' . DonDichVu::tableName() . '.user_id');
        $donDichVu = $donDichVu->all();

        $donDichVuDoanhThu = DonDichVu::find();
        $donDichVuDoanhThu = $donDichVuDoanhThu->andFilterWhere(['>=', 'date(' . DonDichVu::tableName() . '.created)', $this->tuNgay]);
        $donDichVuDoanhThu = $donDichVuDoanhThu->andFilterWhere(['<=', 'date(' . DonDichVu::tableName() . '.created)', $this->denNgay]);
        if (isset($this->dataGet['dien_thoai']))
            if ($this->dataGet['dien_thoai'] != "") {
                $donDichVuDoanhThu = $donDichVuDoanhThu->andFilterWhere(['like', User::tableName() . '.dien_thoai', $this->dataGet['dien_thoai']]);
            }
        if (isset($this->dataGet['leader_kd_id']))
            if ($this->dataGet['leader_kd_id'] != "") {
                $donDichVuDoanhThu = $donDichVuDoanhThu->andFilterWhere([DonDichVu::tableName() . '.leader_kd_id' => $this->dataGet['leader_kd_id']]);
            }
        if (isset($this->dataGet['dia_chi']))
            if ($this->dataGet['dia_chi'] != "") {
                $donDichVuDoanhThu = $donDichVuDoanhThu->andFilterWhere(['like', DonDichVu::tableName() . '.dia_chi', $this->dataGet['dia_chi']]);
            }
        if (isset($this->dataGet['dich_vu_id']))
            if ($this->dataGet['dich_vu_id'] != "") {
                $donDichVuDoanhThu = $donDichVuDoanhThu->andFilterWhere([DonDichVu::tableName() . '.dich_vu_id', $this->dataGet['dich_vu_id']]);
            }
        $donDichVuDoanhThu = $donDichVuDoanhThu->andFilterWhere([DonDichVu::tableName() . '.active' => 1])->leftJoin(User::tableName(), User::tableName() . '.id=' . DonDichVu::tableName() . '.user_id');
        $tongDoanhThu = $donDichVuDoanhThu->sum('tong_tien');
        
        $donDichVuDoanhThuHoan = DonDichVu::find();
        $donDichVuDoanhThuHoan = $donDichVuDoanhThuHoan->andFilterWhere(['>=', 'date(' . DonDichVu::tableName() . '.created)', $this->tuNgay]);
        $donDichVuDoanhThuHoan = $donDichVuDoanhThuHoan->andFilterWhere(['<=', 'date(' . DonDichVu::tableName() . '.created)', $this->denNgay]);
        if (isset($this->dataGet['dien_thoai']))
            if ($this->dataGet['dien_thoai'] != "") {
                $donDichVuDoanhThuHoan = $donDichVuDoanhThuHoan->andFilterWhere(['like', User::tableName() . '.dien_thoai', $this->dataGet['dien_thoai']]);
            }
        if (isset($this->dataGet['leader_kd_id']))
            if ($this->dataGet['leader_kd_id'] != "") {
                $donDichVuDoanhThuHoan = $donDichVuDoanhThuHoan->andFilterWhere([DonDichVu::tableName() . '.leader_kd_id' => $this->dataGet['leader_kd_id']]);
            }
        if (isset($this->dataGet['dia_chi']))
            if ($this->dataGet['dia_chi'] != "") {
                $donDichVuDoanhThuHoan = $donDichVuDoanhThuHoan->andFilterWhere(['like', DonDichVu::tableName() . '.dia_chi', $this->dataGet['dia_chi']]);
            }
        if (isset($this->dataGet['dich_vu_id']))
            if ($this->dataGet['dich_vu_id'] != "") {
                $donDichVuDoanhThuHoan = $donDichVuDoanhThuHoan->andFilterWhere([DonDichVu::tableName() . '.dich_vu_id', $this->dataGet['dich_vu_id']]);
            }
        $donDichVuDoanhThuHoan = $donDichVuDoanhThuHoan->andFilterWhere([DonDichVu::tableName() . '.active' => 1])->leftJoin(User::tableName(), User::tableName() . '.id=' . DonDichVu::tableName() . '.user_id');
        $tongDoanhThuHoan = $donDichVuDoanhThuHoan->andFilterWhere(['trang_thai' => LichSuTrangThaiDon::DON_HOAN])->sum('so_tien_hoan');

        $donDichVuDoanhThuHuy = DonDichVu::find();
        $donDichVuDoanhThuHuy = $donDichVuDoanhThuHuy->andFilterWhere(['>=', 'date(' . DonDichVu::tableName() . '.created)', $this->tuNgay]);
        $donDichVuDoanhThuHuy = $donDichVuDoanhThuHuy->andFilterWhere(['<=', 'date(' . DonDichVu::tableName() . '.created)', $this->denNgay]);
        if (isset($this->dataGet['dien_thoai']))
            if ($this->dataGet['dien_thoai'] != "") {
                $donDichVuDoanhThuHuy = $donDichVuDoanhThuHuy->andFilterWhere(['like', User::tableName() . '.dien_thoai', $this->dataGet['dien_thoai']]);
            }
        if (isset($this->dataGet['leader_kd_id']))
            if ($this->dataGet['leader_kd_id'] != "") {
                $donDichVuDoanhThuHuy = $donDichVuDoanhThuHuy->andFilterWhere([DonDichVu::tableName() . '.leader_kd_id' => $this->dataGet['leader_kd_id']]);
            }
        if (isset($this->dataGet['dia_chi']))
            if ($this->dataGet['dia_chi'] != "") {
                $donDichVuDoanhThuHuy = $donDichVuDoanhThuHuy->andFilterWhere(['like', DonDichVu::tableName() . '.dia_chi', $this->dataGet['dia_chi']]);
            }
        if (isset($this->dataGet['dich_vu_id']))
            if ($this->dataGet['dich_vu_id'] != "") {
                $donDichVuDoanhThuHuy = $donDichVuDoanhThuHuy->andFilterWhere([DonDichVu::tableName() . '.dich_vu_id', $this->dataGet['dich_vu_id']]);
            }
        $donDichVuDoanhThuHuy = $donDichVuDoanhThuHuy->andFilterWhere([DonDichVu::tableName() . '.active' => 1])->leftJoin(User::tableName(), User::tableName() . '.id=' . DonDichVu::tableName() . '.user_id');
        $tongDoanhThuHuy = $donDichVuDoanhThuHuy->andFilterWhere(['trang_thai' => LichSuTrangThaiDon::DA_HUY])->sum('tong_tien');

        $tongDoanhThuMuonLay = $tongDoanhThu - $tongDoanhThuHoan - $tongDoanhThuHuy;
        $export = new exportExcelBaoCaoDoanhThu();
        $export->data = [
            'data' => $donDichVu,
            'tongDoanhThuMuonLay' => $tongDoanhThuMuonLay,
            'tuNgay' => date("d/m/Y", strtotime($this->tuNgay)),
            'denNgay' => date("d/m/Y", strtotime($this->denNgay)),
        ];
        $export->stream = false;
        $export->path_file = dirname(dirname(__DIR__)) . '/files_excel/';
        $file_name = $export->run();
        $file = CauHinh::getServer() . '/files_excel/' . $file_name;;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->outputSuccess($file);
    }

    public function actionTongQuanKhachHang()
    {

        $donDichVu = DonDichVu::find();
        $donDichVu = $donDichVu
            ->andFilterWhere([DonDichVu::tableName() . '.active' => 1])
            ->leftJoin(DichVu::tableName(), DichVu::tableName() . '.id=' . DonDichVu::tableName() . '.dich_vu_id')->select([
                'dich_vu_id',
                DichVu::tableName() . '.ten_dich_vu as ten_dich_vu',
                'count(if(' . DonDichVu::tableName() . '.trang_thai="' . LichSuTrangThaiDon::CHUA_CO_GIAO_VIEN . '",' . DonDichVu::tableName() . '.trang_thai,null)) as chua_co_gv',
                'count(if(' . DonDichVu::tableName() . '.trang_thai="' . LichSuTrangThaiDon::DANG_KHAO_SAT . '",' . DonDichVu::tableName() . '.trang_thai,null)) as dang_khao_sat',
                'count(if(' . DonDichVu::tableName() . '.trang_thai="' . LichSuTrangThaiDon::DANG_DAY . '",' . DonDichVu::tableName() . '.trang_thai,null)) as dang_day',
                'count(if(' . DonDichVu::tableName() . '.trang_thai="' . LichSuTrangThaiDon::DA_HUY . '",' . DonDichVu::tableName() . '.trang_thai,null)) as da_huy',
                'count(if(' . DonDichVu::tableName() . '.trang_thai="' . LichSuTrangThaiDon::HOAN_THANH . '",' . DonDichVu::tableName() . '.trang_thai,null)) as hoan_thanh',
            ]);
        $donDichVu->andFilterWhere(['>=', 'date(' . DonDichVu::tableName() . '.created)', $this->tuNgay]);
        $donDichVu->andFilterWhere(['<=', 'date(' . DonDichVu::tableName() . '.created)', $this->denNgay]);
        $donDichVu = $donDichVu->groupBy([DonDichVu::tableName() . '.dich_vu_id']);
        return $this->outputSuccess($donDichVu->createCommand()->queryAll());
    }

    public function actionDanhSachKhachHang()
    {
        $donDichVu = DonDichVu::find();
        if (isset($this->dataGet['tuNgay']))
            if ($this->dataGet['tuNgay'] != "") {
                $donDichVu = $donDichVu->andFilterWhere(['>=', 'date(' . DonDichVu::tableName() . '.created)', $this->dataGet['tuNgay']]);
            }
        if ($this->tuKhoa != "") {
            $donDichVu = $donDichVu->andFilterWhere(['or',
                ['like', User::tableName() . '.hoten', $this->tuKhoa],
                ['like', User::tableName() . '.dien_thoai', $this->tuKhoa]
            ]);
        }
        if (isset($this->dataGet['denNgay']))
            if ($this->dataGet['denNgay'] != "") {
                $donDichVu = $donDichVu->andFilterWhere(['<=', 'date(' . DonDichVu::tableName() . '.created)', $this->dataGet['denNgay']]);
            }
        if (isset($this->dataGet['dien_thoai']))
            if ($this->dataGet['dien_thoai'] != "") {
                $donDichVu = $donDichVu->andFilterWhere(['like', User::tableName() . '.dien_thoai', $this->dataGet['dien_thoai']]);
            }
        if (isset($this->dataGet['leader_kd_id']))
            if ($this->dataGet['leader_kd_id'] != "") {
                $donDichVu = $donDichVu->andFilterWhere([DonDichVu::tableName() . '.leader_kd_id' => $this->dataGet['leader_kd_id']]);
            }
        if (isset($this->dataGet['dia_chi']))
            if ($this->dataGet['dia_chi'] != "") {
                $donDichVu = $donDichVu->andFilterWhere(['like', DonDichVu::tableName() . '.dia_chi', $this->dataGet['dia_chi']]);
            }
        if (isset($this->dataGet['dich_vu_id']))
            if ($this->dataGet['dich_vu_id'] != "") {
                $donDichVu = $donDichVu->andFilterWhere([DonDichVu::tableName() . '.dich_vu_id' => $this->dataGet['dich_vu_id']]);
            }
        if (isset($this->dataGet['trang_thai']))
            if ($this->dataGet['trang_thai'] != "") {
                $donDichVu = $donDichVu->andFilterWhere([DonDichVu::tableName() . '.trang_thai' => $this->dataGet['trang_thai']]);
            }
        $donDichVu = $donDichVu->andFilterWhere([DonDichVu::tableName() . '.active' => 1])
            ->innerJoin(User::tableName(), User::tableName() . '.id=' . DonDichVu::tableName() . '.phu_huynh_id');
        $count = count($donDichVu->all());
        $donDichVu = $donDichVu->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy([DonDichVu::tableName() . '.created' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        $data = [];
        /** @var DonDichVu $item */
        foreach ($donDichVu as $item) {
            $phuHuynh = $item->phuHuynh;
            $data[] = [
                'id' => $item->id,
                'ngay_thanh_toan' => !is_null($item->ngay_thanh_toan) ? date("d/m/Y", strtotime($item->ngay_thanh_toan)) : "",
                'goi' => $item->goiHocPhi->thanh_tien * $item->so_buoi,
                'ma_don_hang' => $item->ma_don_hang,
                'dichVu' => $item->dichVu->ten_dich_vu,
                'trang_thai' => $item->trang_thai,
                'soBuoiHoanThanh' => $item->getTrangThaiTienDo(),
                'phuHuynh' => is_null($phuHuynh) ? null : [
                    'id' => $phuHuynh->id,
                    'image' => $phuHuynh->getImage(),
                    'hoten' => $phuHuynh->hoten,
                    'dien_thoai' => $phuHuynh->dien_thoai,
                ],
            ];
        }
        return $this->outputListSuccess2($data, $count);
    }

    public function actionExportExcelBaoCaoKhachHang()
    {
        $donDichVu = DonDichVu::find();
        if (isset($this->dataGet['tuNgay']))
            if ($this->dataGet['tuNgay'] != "") {
                $donDichVu = $donDichVu->andFilterWhere(['>=', 'date(' . DonDichVu::tableName() . '.created)', $this->dataGet['tuNgay']]);
            }
        if (isset($this->dataGet['denNgay']))
            if ($this->dataGet['denNgay'] != "") {
                $donDichVu = $donDichVu->andFilterWhere(['<=', 'date(' . DonDichVu::tableName() . '.created)', $this->dataGet['denNgay']]);
            }
        if (isset($this->dataGet['dien_thoai']))
            if ($this->dataGet['dien_thoai'] != "") {
                $donDichVu = $donDichVu->andFilterWhere(['like', User::tableName() . '.dien_thoai', $this->dataGet['dien_thoai']]);
            }
        if (isset($this->dataGet['leader_kd_id']))
            if ($this->dataGet['leader_kd_id'] != "") {
                $donDichVu = $donDichVu->andFilterWhere([DonDichVu::tableName() . '.leader_kd_id' => $this->dataGet['leader_kd_id']]);
            }
        if (isset($this->dataGet['dia_chi']))
            if ($this->dataGet['dia_chi'] != "") {
                $donDichVu = $donDichVu->andFilterWhere(['like', DonDichVu::tableName() . '.dia_chi', $this->dataGet['dia_chi']]);
            }
        if (isset($this->dataGet['dich_vu_id']))
            if ($this->dataGet['dich_vu_id'] != "") {
                $donDichVu = $donDichVu->andFilterWhere([DonDichVu::tableName() . '.dich_vu_id' => $this->dataGet['dich_vu_id']]);
            }
        if (isset($this->dataGet['trang_thai']))
            if ($this->dataGet['trang_thai'] != "") {
                $donDichVu = $donDichVu->andFilterWhere([DonDichVu::tableName() . '.trang_thai' => $this->dataGet['trang_thai']]);
            }
        $donDichVu = $donDichVu->andFilterWhere([DonDichVu::tableName() . '.active' => 1])
            ->leftJoin(User::tableName(), User::tableName() . '.id=' . DonDichVu::tableName() . '.user_id');
        $donDichVu = $donDichVu->all();
        $export = new exportExcelBaoCaoKhachHang();
        $export->data = [
            'data' => $donDichVu,
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

    public function actionBaoCaoTinhTrangDon()
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
        $thang = strtotime('-1 month', strtotime($this->tuNgay));

        $hienTai = $this->getTinhTrangDonDichVu($this->tuNgay, $this->denNgay);
        $thangTruoc = $this->getTinhTrangDonDichVu(date('Y-m-1', $thang), date('Y-m-t', $thang));
        return $this->outputSuccess([
            'du_kien' => [
                'tong_buoi' => $hienTai['tong_buoi'],
                'phan_tram' => round($hienTai['tong_buoi'] > $thangTruoc['tong_buoi'] && $thangTruoc['tong_buoi'] > 0 ? ($hienTai['tong_buoi'] - $thangTruoc['tong_buoi']) * 100 / $thangTruoc['tong_buoi'] : 0),
            ],
            'dang_day' => [
                'tong_buoi' => $hienTai['dang_day'],
                'phan_tram' => round($hienTai['dang_day'] > $thangTruoc['dang_day'] && $thangTruoc['dang_day'] > 0 ? ($hienTai['dang_day'] - $thangTruoc['dang_day']) * 100 / $thangTruoc['dang_day'] : 0),
            ],
            'da_hoan_thanh' => [
                'tong_buoi' => $hienTai['da_hoan_thanh'],
                'phan_tram' => round($hienTai['da_hoan_thanh'] > $thangTruoc['da_hoan_thanh'] && $thangTruoc['da_hoan_thanh'] > 0 ? ($hienTai['da_hoan_thanh'] - $thangTruoc['da_hoan_thanh']) * 100 / $thangTruoc['da_hoan_thanh'] : 0),
            ],
            'chua_vao_ca' => [
                'tong_buoi' => $hienTai['chua_vao_ca'],
                'phan_tram' => round($hienTai['chua_vao_ca'] > $thangTruoc['chua_vao_ca'] && $thangTruoc['chua_vao_ca'] > 0 ? ($hienTai['chua_vao_ca'] - $thangTruoc['chua_vao_ca']) * 100 / $thangTruoc['chua_vao_ca'] : 0),
            ],
        ]);
    }

    public function actionExportExcelBaoCaoTinhTrangDon()
    {
        $hienTai = $this->getTinhTrangDonDichVu($this->tuNgay, $this->denNgay);
        $export = new exportExcelBaoCaoTinhTrangDon();
        $export->data = [
            'tuNgay' => $this->tuNgay,
            'denNgay' => $this->denNgay,
            'du_kien' => $hienTai['tong_buoi'],
            'dang_day' => $hienTai['dang_day'],
            'da_hoan_thanh' => $hienTai['da_hoan_thanh'],
            'chua_vao_ca' => $hienTai['chua_vao_ca'],
        ];
        $export->stream = false;
        $export->path_file = dirname(dirname(__DIR__)) . '/files_excel/';
        $file_name = $export->run();
        $file = CauHinh::getServer() . '/files_excel/' . $file_name;;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->outputSuccess($file);
    }

    public function actionTongQuanUser()
    {
        $users = QuanLyUserVaiTro::find()
            ->andFilterWhere(['active' => 1, 'status' => 10, 'is_admin' => 0]);
        $users->andFilterWhere(['>=', 'date(' . QuanLyUserVaiTro::tableName() . '.created_at)', $this->tuNgay]);
        $users->andFilterWhere(['<=', 'date(' . QuanLyUserVaiTro::tableName() . '.created_at)', $this->denNgay]);
        $users->select([
            'vai_tro_name as vai_tro',
            'FORMAT(count(if(khoa_tai_khoan=0,id,null))*100/count(*),0) as tong',
            'FORMAT(count(if(khoa_tai_khoan=1,id,null))*100/count(vai_tro_name),0) as dung_hoat_dong',
            'FORMAT(count(if(khoa_tai_khoan=0,id,null))*100/count(vai_tro_name),0) as dang_hoat_dong',
        ]);

        $users->groupBy('vai_tro');
        $users = $users->createCommand()->queryAll();
        $phuHuynh = [
            'tong' => 0,
            'dung_hoat_dong' => 0,
            'dang_hoat_dong' => 0,
        ];
        $giaoVien = [
            'tong' => 0,
            'dung_hoat_dong' => 0,
            'dang_hoat_dong' => 0,
        ];
        foreach ($users as $user) {
            $user = (object)$user;
            if ($user->vai_tro == 'Giáo viên') {
                $giaoVien['tong'] = intval($user->tong);
                $giaoVien['dung_hoat_dong'] = intval($user->dung_hoat_dong);
                $giaoVien['dang_hoat_dong'] = intval($user->dang_hoat_dong);
            };
            if ($user->vai_tro == 'Phụ huynh') {
                $phuHuynh['tong'] = intval($user->tong);
                $phuHuynh['dung_hoat_dong'] = intval($user->dung_hoat_dong);
                $phuHuynh['dang_hoat_dong'] = intval($user->dang_hoat_dong);
            }
        }
        return $this->outputSuccess([
            'giaoVien' => $giaoVien,
            'phuHuynh' => $phuHuynh,
        ]);
    }

    public function actionDanhSachUser()
    {
        $users = QuanLyUserVaiTro::find()->andFilterWhere(['active' => 1, 'status' => 10, 'is_admin' => 0]);
        if (isset($this->dataGet['tuNgay']))
            if ($this->dataGet['tuNgay'] != "") {
                $users = $users->andFilterWhere(['>=', 'date(' . QuanLyUserVaiTro::tableName() . '.created_at)', $this->dataGet['tuNgay']]);
            }
        if (isset($this->dataGet['denNgay']))
            if ($this->dataGet['denNgay'] != "") {
                $users = $users->andFilterWhere(['<=', 'date(' . QuanLyUserVaiTro::tableName() . '.created_at)', $this->dataGet['denNgay']]);
            }
        if (isset($this->dataGet['dien_thoai']))
            if ($this->dataGet['dien_thoai'] != "") {
                $users = $users->andFilterWhere(['like', QuanLyUserVaiTro::tableName() . '.dien_thoai', $this->dataGet['dien_thoai']]);
            }
        if ($this->tuKhoa != "") {
            $users = $users->andFilterWhere(['like', QuanLyUserVaiTro::tableName() . '.hoten', $this->tuKhoa]);
        }
        if (isset($this->dataGet['dia_chi']))
            if ($this->dataGet['dia_chi'] != "") {
                $users = $users->andFilterWhere(['like', QuanLyUserVaiTro::tableName() . '.dia_chi', $this->dataGet['dia_chi']]);
            }
        if (isset($this->dataGet['dich_vu_id']))
            if ($this->dataGet['dich_vu_id'] != "") {
                $users = $users->andFilterWhere(['like', QuanLyUserVaiTro::tableName() . '.dich_vu_id', $this->dataGet['dich_vu_id']]);
            };
        $count = count($users->all());
        $users = $users->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy([QuanLyUserVaiTro::tableName() . '.created_at' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        $data = [];
        /** @var QuanLyUserVaiTro $item */
        foreach ($users as $item) {
            $data[] = [
                'id' => $item->id,
                'vai_tro' => $item->vai_tro_name,
                'created' => date('d/m/Y', strtotime($item->created_at)),
                'anh_nguoi_dung' => $item->getImage(),
                'hoten' => $item->hoten,
                'dien_thoai' => $item->dien_thoai,
                'trinh_do' => $item->vai_tro == User::GIAO_VIEN ? $item->trinh_do_name : "Khách hàng",
                'trang_thai' => $item->vai_tro == User::GIAO_VIEN ? $item->trang_thai_vao_ca : ($item->khoa_tai_khoan == 1 ? "Dừng hoạt động" : "Đang hoạt động"),
            ];
        }
        return $this->outputListSuccess2($data, $count);
    }

    public function actionExportExcelDanhSachUser()
    {
        $users = QuanLyUserVaiTro::find()->andFilterWhere(['active' => 1, 'status' => 10, 'is_admin' => 0]);
        if (isset($this->dataGet['tuNgay']))
            if ($this->dataGet['tuNgay'] != "") {
                $users = $users->andFilterWhere(['>=', 'date(' . QuanLyUserVaiTro::tableName() . '.created_at)', $this->dataGet['tuNgay']]);
            }
        if (isset($this->dataGet['denNgay']))
            if ($this->dataGet['denNgay'] != "") {
                $users = $users->andFilterWhere(['<=', 'date(' . QuanLyUserVaiTro::tableName() . '.created_at)', $this->dataGet['denNgay']]);
            }
        if (isset($this->dataGet['dien_thoai']))
            if ($this->dataGet['dien_thoai'] != "") {
                $users = $users->andFilterWhere(['like', QuanLyUserVaiTro::tableName() . '.dien_thoai', $this->dataGet['dien_thoai']]);
            }
        if ($this->tuKhoa != "") {
            $users = $users->andFilterWhere(['like', QuanLyUserVaiTro::tableName() . '.hoten', $this->tuKhoa]);
        }
        if (isset($this->dataGet['dia_chi']))
            if ($this->dataGet['dia_chi'] != "") {
                $users = $users->andFilterWhere(['like', QuanLyUserVaiTro::tableName() . '.dia_chi', $this->dataGet['dia_chi']]);
            }
        if (isset($this->dataGet['dich_vu_id']))
            if ($this->dataGet['dich_vu_id'] != "") {
                $users = $users->andFilterWhere(['like', QuanLyUserVaiTro::tableName() . '.dich_vu_id', $this->dataGet['dich_vu_id']]);
            };
        $users = $users->all();
        $export = new exportExcelBaoCaoUser();
        $export->data = [
            'data' => $users,
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

    public function getTinhTrangDonDichVu($tuNgay, $denNgay)
    {
        $soBuoi = TienDoKhoaHoc::find()
            ->andFilterWhere([">=", "date(" . TienDoKhoaHoc::tableName() . '.ngay_day)', $tuNgay])
            ->andFilterWhere(["<=", "date(" . TienDoKhoaHoc::tableName() . '.ngay_day)', $denNgay])
            ->andFilterWhere(["in",  TienDoKhoaHoc::tableName() . '.trang_thai', [ TienDoKhoaHoc::CHUA_DAY,TienDoKhoaHoc::DANG_DAY, TienDoKhoaHoc::DA_HOAN_THANH]])
            ->andFilterWhere(['active'=>1])
            ->count();
        $tienDo = TienDoKhoaHoc::find()
            ->select([
                'count(if(trang_thai="' . TienDoKhoaHoc::CHUA_DAY . '",trang_thai,null)) as chua_vao_ca',
                'count(if(trang_thai="' . TienDoKhoaHoc::DANG_DAY . '",trang_thai,null)) as dang_day',
                'count(if(trang_thai="' . TienDoKhoaHoc::DA_HOAN_THANH . '",trang_thai,null)) as da_hoan_thanh',
            ])
            ->andFilterWhere([">=", "date(" . TienDoKhoaHoc::tableName() . '.ngay_day)', $tuNgay])
            ->andFilterWhere(["<=", "date(" . TienDoKhoaHoc::tableName() . '.ngay_day)', $denNgay])
            ->andFilterWhere(['active' => 1])->createCommand()->queryAll();
        return [
            'tong_buoi' => $soBuoi < 0 ? 0 : $soBuoi,
            'dang_day' => $tienDo[0]['dang_day'] < 0 ? 0 : $tienDo[0]['dang_day'],
            'da_hoan_thanh' => $tienDo[0]['da_hoan_thanh'] < 0 ? 0 : $tienDo[0]['da_hoan_thanh'],
            'chua_vao_ca' => $tienDo[0]['chua_vao_ca'] < 0 ? 0 : $tienDo[0]['chua_vao_ca'],
        ];
    }

    public function actionTimKiem()
    {
        $this->checkGetInput(['tuKhoa']);
        $data = [];
        $donDichVu = DonDichVu::find()
            ->andFilterWhere(['active' => 1])
            ->andFilterWhere(['like', 'ma_don_hang', $this->tuKhoa])->limit(5)->all();
        $giaoVien = QuanLyUserVaiTro::find()
            ->andFilterWhere(['like', 'hoten', $this->tuKhoa])
            ->andFilterWhere(['active' => 1, 'is_admin' => 0, 'khoa_tai_khoan' => 0, 'status' => 10, 'vai_tro' => User::GIAO_VIEN])->limit(5)->all();

        $phuPhuynh = QuanLyUserVaiTro::find()
            ->andFilterWhere(['like', 'hoten', $this->tuKhoa])
            ->andFilterWhere(['active' => 1, 'is_admin' => 0, 'status' => 10, 'vai_tro' => User::PHU_HUYNH])->limit(5)->all();

        /** @var DonDichVu $item */
        foreach ($donDichVu as $item) {
            $data [] = [
                'id' => $item->id,
                'ma_don_hang' => $item->ma_don_hang,
                'image' => CauHinh::getImage($item->dichVu->image),
                'link' => CauHinh::getServer() . "/don-dich-vu/chi-tiet?id=$item->id",
                'type' => 0,
            ];
        }
        /** @var QuanLyUserVaiTro $item */
        foreach ($giaoVien as $item) {
            $data [] = [
                'id' => $item->id,
                'image' => $item->getImage(),
                'hoten' => $item->hoten,
                'link' => CauHinh::getServer() . "/giao-vien/chi-tiet?id=$item->id",
                'trinh_do' => $item->trinh_do_name,
                'danh_gia' => $item->danh_gia,
                'type' => 1,
            ];
        }
        /** @var QuanLyUserVaiTro $item */
        foreach ($phuPhuynh as $item) {
            $data [] = [
                'id' => $item->id,
                'image' => $item->getImage(),
                'hoten' => $item->hoten,
                'link' => CauHinh::getServer() . "/phu-huynh/chi-tiet?id=$item->id",
                'vai_tro' => (new User())->getVaiTro(),
                'type' => 2,
            ];
        }
        return $this->outputSuccess($data);
    }

    public function actionBaoCaoDoanhThuTheoNgay()
    {
        $tienDo = TienDoKhoaHoc::find()
            ->leftJoin(DonDichVu::tableName(), DonDichVu::tableName() . '.id=' . TienDoKhoaHoc::tableName() . '.don_dich_vu_id')
            ->andFilterWhere(['>=', 'date(ngay_day)', $this->tuNgay])
            ->andFilterWhere(['<=', 'date(ngay_day)', $this->denNgay])
            ->andFilterWhere([TienDoKhoaHoc::tableName() . '.active' => 1]);
        if (isset($this->dataGet['giao_vien_id'])) {
            if ($this->dataGet['giao_vien_id'] != '') {
                $tienDo->andFilterWhere([TienDoKhoaHoc::tableName() . '.giao_vien_id' => $this->dataGet['giao_vien_id']]);
            }
        }
        if (isset($this->dataGet['don_dich_vu_id'])) {
            if ($this->dataGet['don_dich_vu_id'] != '') {
                $tienDo->andFilterWhere([TienDoKhoaHoc::tableName() . '.don_dich_vu_id' => $this->dataGet['don_dich_vu_id']]);
            }
        }
        $user = QuanLyUserVaiTro::findOne(['id' => $this->uid ?? 0]);
        if ($user->vai_tro == VaiTro::LEADER_KD) {
            $tienDo->andFilterWhere([DonDichVu::tableName() . '.leader_kd_id' => $user->id]);
        }
        $count = count($tienDo->all());
        $tienDo = $tienDo->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy(['created' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        $data = [];
        /** @var TienDoKhoaHoc $item */
        foreach ($tienDo as $item) {
            $donDichVu = $item->donDichVu;
            $giaoVien = $donDichVu->giaoVien;
            $phuHuynh = $donDichVu->phuHuynh;
            $data[] = [
                'id' => $donDichVu->id,
                'created' => date("d/m/Y H:i", strtotime($item->created)),
                'ma_don_hang' => $donDichVu->ma_don_hang,
                'giaoVien' => is_null($giaoVien) ? null : [
                    'id' => $giaoVien->id,
                    'image' => $giaoVien->getImage(),
                    'hoten' => $giaoVien->hoten,
                    'dien_thoai' => $giaoVien->dien_thoai,
                    'trinh_do' => $giaoVien->getTrinhDo(),
                    'danh_gia' => $giaoVien->danh_gia,
                ],
                'phuHuynh' => is_null($phuHuynh) ? null : [
                    'id' => $phuHuynh->id,
                    'image' => $phuHuynh->getImage(),
                    'hoten' => $phuHuynh->hoten,
                    'dien_thoai' => $phuHuynh->dien_thoai,
                ],
                'soBuoiHoanThanh' => $donDichVu->getTrangThaiTienDo(),
                'don_gia' => $donDichVu->so_buoi > 0 ? $donDichVu->tong_tien / $donDichVu->so_buoi : 0,
                'trang_thai' => $item->trang_thai == TienDoKhoaHoc::CHUA_DAY ? "Chưa vào ca" : $item->trang_thai,
            ];
        }
        return $this->outputListSuccess2($data, $count);
    }

    public function actionExportExcelBaoCaoDoanhThuTheoNgay()
    {
        $tienDo = TienDoKhoaHoc::find()
            ->leftJoin(DonDichVu::tableName(), DonDichVu::tableName() . '.id=' . TienDoKhoaHoc::tableName() . '.don_dich_vu_id')
            ->andFilterWhere(['>=', 'date(ngay_day)', $this->tuNgay])
            ->andFilterWhere(['<=', 'date(ngay_day)', $this->denNgay])
            ->andFilterWhere(['active' => 1]);
        if (isset($this->dataGet['giao_vien_id'])) {
            if ($this->dataGet['giao_vien_id'] != '') {
                $tienDo->andFilterWhere([TienDoKhoaHoc::tableName() . '.giao_vien_id' => $this->dataGet['giao_vien_id']]);
            }
        }
        if (isset($this->dataGet['don_dich_vu_id'])) {
            if ($this->dataGet['don_dich_vu_id'] != '') {
                $tienDo->andFilterWhere([TienDoKhoaHoc::tableName() . '.don_dich_vu_id' => $this->dataGet['don_dich_vu_id']]);
            }
        }
        $user = QuanLyUserVaiTro::findOne(['id' => $this->uid ?? 0]);
        if ($user->vai_tro == VaiTro::LEADER_KD) {
            $tienDo->andFilterWhere([DonDichVu::tableName() . '.leader_kd_id' => $user->id]);
        }
        $tienDo = $tienDo->orderBy(['created' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();

        $export = new exportExcelBaoCaoDoanhThuTheoNgay();
        $export->data = [
            'data' => $tienDo,
            'tuNgay' => date("d/m/Y", strtotime($this->tuNgay)),
            'denNgay' => date("d/m/Y", strtotime($this->denNgay)),
        ];
        $export->stream = false;
        $export->path_file = dirname(dirname(__DIR__)) . '/files_excel/';
        $file_name = $export->run();
        $file = CauHinh::getServer() . '/files_excel/' . $file_name;;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->outputSuccess($file);
    }

    public function actionBaoCaoGiaoDich()
    {
        $giaoDich = GiaoDich::find()
            ->andFilterWhere(['>=', 'date(' . GiaoDich::tableName() . '.created)', $this->tuNgay])
            ->andFilterWhere(['<=', 'date(' . GiaoDich::tableName() . '.created)', $this->denNgay])
            ->andWhere('don_dich_vu_id is null');
        if (isset($this->dataGet['giao_vien_id'])) {
            if ($this->dataGet['giao_vien_id'] != '') {
                $giaoDich->andFilterWhere(['user_id' => $this->dataGet['giao_vien_id']]);
            }
        }

        $count = count($giaoDich->all());
        $giaoDich = $giaoDich->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy(['created' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        $data = [];
        /** @var GiaoDich $item */
        foreach ($giaoDich as $item) {
            $giaoVien = $item->user;
            $data[] = [
                'id' => $item->id,
                'created' => date("d/m/y H:i", strtotime($item->created)),
                'giaoVien' => is_null($giaoVien) ? null : [
                    'id' => $giaoVien->id,
                    'image' => $giaoVien->getImage(),
                    'hoten' => $giaoVien->hoten,
                    'dien_thoai' => $giaoVien->dien_thoai,
                    'trinh_do' => $giaoVien->getTrinhDo(),
                    'danh_gia' => $giaoVien->danh_gia,
                ],
                'so_tien' => $item->so_tien,
                'ghi_chu' => $item->ghi_chu,
                'type' => $item->type,
                'tieu_de' => $item->tieu_de,
                'vi_dien_tu' => $item->vi_dien_tu,
                'don_dich_vu_id' => $item->don_dich_vu_id
            ];
        }
        return $this->outputListSuccess2($data, $count);
    }

    public function actionExportExcelBaoCaoGiaoDich()
    {
        $giaoDich = GiaoDich::find()
            ->andFilterWhere(['>=', 'date(' . GiaoDich::tableName() . '.created)', $this->tuNgay])
            ->andFilterWhere(['<=', 'date(' . GiaoDich::tableName() . '.created)', $this->denNgay])
            ->andWhere('don_dich_vu_id is null');
        if (isset($this->dataGet['giao_vien_id'])) {
            if ($this->dataGet['giao_vien_id'] != '') {
                $giaoDich->andFilterWhere(['user_id' => $this->dataGet['giao_vien_id']]);
            }
        }

        $count = count($giaoDich->all());
        $giaoDich = $giaoDich->orderBy(['created' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        $export = new exportExcelBaoCaoGiaoDich();
        $export->data = [
            'data' => $giaoDich,
            'tuNgay' => date("d/m/Y", strtotime($this->tuNgay)),
            'denNgay' => date("d/m/Y", strtotime($this->denNgay)),
        ];
        $export->stream = false;
        $export->path_file = dirname(dirname(__DIR__)) . '/files_excel/';
        $file_name = $export->run();
        $file = CauHinh::getServer() . '/files_excel/' . $file_name;;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->outputSuccess($file);
    }
}
