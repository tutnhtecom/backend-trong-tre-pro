<?php namespace backend\controllers;

use backend\models\BanGiao;
use backend\models\CauHinh;
use backend\models\DanhMuc;
use backend\models\DonDichVu;
use backend\models\GiaoCu;
use backend\models\LichSuTrangThaiDon;
use backend\models\QuanLyBanGiao;
use common\models\exportExcelBaoCaoDoanhThu;
use common\models\exportExcelBaoCaoGiaoCu;
use common\models\myAPI;
use common\models\User;
use Yii;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\web\HttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class GiaoCuController extends CoreApiController
{
    public function actionDanhSach()
    {
        $this->checkGetInput(['tuKhoa']);
        $giaoCu = GiaoCu::find()
            ->select(['id', 'code', 'so_luong_tong', 'so_luong_ton', 'image'])
            ->andFilterWhere(['active' => 1]);
        if ($this->dataGet['tuKhoa'] != "") {
            $giaoCu->andFilterWhere(['like', 'code', $this->dataGet['tuKhoa']]);
        }
        $count = count($giaoCu->all());
        $giaoCu = $giaoCu->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy(['created' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        if (count($giaoCu) > 0) {
            foreach ($giaoCu as $item) {
                $item->image = CauHinh::getImage($item->image);
            }
        }
        return $this->outputListSuccess2($giaoCu, $count);
    }

    public function actionExportExcelDanhSachGiaoCu()
    {
        $giaoCu = GiaoCu::find()
            ->andFilterWhere(['active' => 1]);
        if ($this->tuKhoa != "") {
            $giaoCu->andFilterWhere(['like', 'code', $this->tuKhoa]);
        }
        $giaoCu = $giaoCu->all();
        $export = new exportExcelBaoCaoGiaoCu();
        $export->data = [
            'data' => $giaoCu,
            'tuNgay' => "",
            'denNgay' => "",
        ];
        $export->stream = false;
        $export->path_file = dirname(dirname(__DIR__)) . '/files_excel/';
        $file_name = $export->run();
        $file = CauHinh::getServer() . '/files_excel/' . $file_name;;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->outputSuccess($file);
    }

    public function actionDanhSachGiaoCu()
    {

        $giaoCu = GiaoCu::find()
            ->select(['id', 'code', 'image'])
            ->andFilterWhere(['active' => 1]);
        $giaoCu = $giaoCu->all();
        if (count($giaoCu) > 0) {
            foreach ($giaoCu as $item) {
                $item->image = CauHinh::getImage($item->image);
            }
        }
        return $this->outputSuccess($giaoCu);
    }

    public function actionChiTiet()
    {
        $this->checkGetInput(['id']);
        if ($this->dataGet['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $giaoCu = GiaoCu::find()->select(['id', 'code', 'so_luong_tong', 'so_luong_ton', 'ghi_chu', 'image'])
            ->andFilterWhere(['id' => $this->dataGet['id'], 'active' => 1])->one();
        if (is_null($giaoCu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $giaoCu->image = CauHinh::getImage($giaoCu->image);
        return $this->outputSuccess($giaoCu);
    }

    public function actionTaoMoi()
    {
        $this->checkField(['code', 'so_luong_tong', 'so_luong_ton', 'ghi_chu']);
        if ($this->dataPost['code'] == "") {
            throw new HttpException(500, "Vui lòng nhập mã gói giáo cụ");
        }
        $giaoCu = GiaoCu::findOne(['code' => $this->dataPost['code'], 'active' => 1]);
        if (!is_null($giaoCu)) {
            throw new HttpException(500, "Mã giáo cụ " . $this->dataPost['code'] . " đã tồn tại");
        }
        $giaoCu = new GiaoCu();
        $giaoCu->code = $this->dataPost['code'];
        $giaoCu->so_luong_tong = intval($this->dataPost['so_luong_tong']);
        $giaoCu->so_luong_ton = intval($this->dataPost['so_luong_ton']);
        $giaoCu->ghi_chu = $this->dataPost['ghi_chu'];
        $giaoCu->user_id = $this->uid;
        $file = UploadedFile::getInstanceByName('image');
        if (!empty($file)) {
            $path = (dirname(dirname(__DIR__))) . '/upload-file';
            $link = date('Y/m/d') . '/' . '_' . \Yii::$app->security->generateRandomString() . $this->get_extension_image($file->type);
            $giaoCu->image = $link;
            if (FileHelper::createDirectory($path . '/' . date('Y/m/d') . '/', $mode = 0775, $recursive = true)) {
                $file->saveAs($path . '/' . $link);
            }
        }
        if (!$giaoCu->save()) {
            throw new HttpException(500, Html::errorSummary($giaoCu));
        }
        return $this->outputSuccess("", "Thêm giáo cụ thành công");
    }

    public function actionSua()
    {
        $this->checkField(['id', 'code', 'so_luong_tong', 'so_luong_ton', 'ghi_chu']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $giaoCu = GiaoCu::findOne(['id' => $this->dataPost['id'], 'active' => 1]);
        if (is_null($giaoCu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($this->dataPost['code'] == "") {
            throw new HttpException(500, "Vui lòng nhập mã gói giáo cụ");
        }
        $giaoCuOld = GiaoCu::find()->andFilterWhere(['code' => $this->dataPost['code'], 'active' => 1])->andFilterWhere(['<>', 'id', $giaoCu->id])->one();
        if (!is_null($giaoCuOld)) {
            throw new HttpException(500, "Mã giáo cụ " . $this->dataPost['code'] . " đã tồn tại");
        }
        $giaoCu->code = $this->dataPost['code'];
        $giaoCu->so_luong_tong = $this->dataPost['so_luong_tong'];
        $giaoCu->so_luong_ton = $this->dataPost['so_luong_ton'];
        $giaoCu->ghi_chu = $this->dataPost['ghi_chu'];
        $giaoCu->user_id = $this->uid;
        $file = UploadedFile::getInstanceByName('image');
        if (!empty($file)) {
            $path = (dirname(dirname(__DIR__))) . '/upload-file';
            $link = date('Y/m/d') . '/' . '_' . \Yii::$app->security->generateRandomString() . $this->get_extension_image($file->type);
            $giaoCu->image = $link;
            if (FileHelper::createDirectory($path . '/' . date('Y/m/d') . '/', $mode = 0775, $recursive = true)) {
                $file->saveAs($path . '/' . $link);
            }
        }
        if (!$giaoCu->save()) {
            throw new HttpException(500, Html::errorSummary($giaoCu));
        }
        return $this->outputSuccess("", "Cập nhật giáo cụ thành công");
    }

    public function actionXoaGiaoCu()
    {
        $this->checkField(['id']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $giaoCu = GiaoCu::findOne(['id' => $this->dataPost['id'], 'active' => 1]);
        if (is_null($giaoCu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $giaoCu->active = 0;
        if (!$giaoCu->save()) {
            throw new HttpException(500, Html::errorSummary($giaoCu));
        }
        return $this->outputSuccess("", "Xóa giáo cụ thành công");
    }


    public function actionTaoMoiBanGiao()
    {
        $this->checkField(['giao_vien_id', 'giaoCu', 'ghi_chu', 'don_dich_vu_id', 'ngay_nhan']);
        if ($this->dataPost['giao_vien_id'] == "") {
            throw new HttpException(500, "Vui lòng chọn giáo viên");
        }
        if ($this->dataPost['giaoCu'] == "") {
            throw new HttpException(500, "Vui lòng chọn giáo cụ");
        }
        $giaoCus = $this->dataPost['giaoCu'];
        $soLuong = 0;
        if (!is_array($giaoCus)) {
            throw new HttpException(500, "Danh sách giáo cụ không đúng định dạng");
        }
        if (count($giaoCus) == 0) {
            throw new HttpException(500, "Vui lòng chọn tối thiểu 1 bộ giáo cụ");
        }
        foreach ($giaoCus as $index => $item) {
            $giaoCu = GiaoCu::findOne($item['id']);
            $stt = $index + 1;
            if (is_null($giaoCu)) {
                throw new HttpException(500, "Không tìm thấy giáo cụ số $stt");
            }
            if (intval($item['so_luong']) <= 0) {
                throw new HttpException(500, "Số lượng giáo cụ số $stt tối thiểu là 1");
            }
            if ($giaoCu->so_luong_ton < intval($item['so_luong'])) {
                throw new HttpException(500, "Giáo cụ $giaoCu->code hiện tại tồn còn $giaoCu->so_luong_ton");
            }
            $soLuong += $item['so_luong'];
        }
        $donDichVu = DonDichVu::findOne($this->dataPost['don_dich_vu_id']);
        if (is_null($donDichVu)) {
            throw new HttpException(500, "Không tìm thấy thông tin khóa học");
        }
        if (!in_array($donDichVu->trang_thai, [LichSuTrangThaiDon::DANG_DAY])) {
            throw new HttpException(500, "Đơn dịch vụ hiện không trong quá trình dạy");
        }
        $banGiao = BanGiao::find()
            ->andFilterWhere(['giao_vien_id' => $this->dataPost['giao_vien_id']])
            ->andFilterWhere(['don_dich_vu_id' => $this->dataPost['don_dich_vu_id']])
            ->andFilterWhere(['<>', 'trang_thai', BanGiao::XAC_NHAN_HOAN_TRA])->one();

        /** @var BanGiao $banGiao */
        if (!is_null($banGiao)) {
            throw  new HttpException(500, "Giáo viên {$banGiao->giaoVien->hoten} đã mượn giáo cụ {$banGiao->getCodeGiaoCu()} cho đơn dịch vụ $donDichVu->ma_don_hang vào ngày " . date("d/m/Y", strtotime($banGiao->ngay_nhan)));
        }
        if ($donDichVu->giao_vien_id != $this->dataPost['giao_vien_id']) {
            $giaoVien = User::findOne($this->dataPost['giao_vien_id']);
            throw new HttpException(500, "Giáo viên {$giaoVien->hoten} hiện không phụ trách đơn dịch vụ $donDichVu->ma_don_hang");
        }
        $banGiao = new BanGiao();
        $banGiao->chi_tiet_giao_cu = json_encode($this->dataPost['giaoCu']);
        $banGiao->giao_vien_id = $this->dataPost['giao_vien_id'];
        $banGiao->ghi_chu = $this->dataPost['ghi_chu'];
        $banGiao->so_luong = $soLuong;
        $banGiao->don_dich_vu_id = $this->dataPost['don_dich_vu_id'];
        $banGiao->user_id = $this->uid;
        $banGiao->trang_thai = BanGiao::XAC_NHAN_BAN_GIAO;
        if (strval($this->dataPost['ngay_nhan']) != "" && ($this->dataPost['ngay_nhan'] != 'null')) {
            $banGiao->ngay_nhan = myAPI::convertDMY2YMD($this->dataPost['ngay_nhan']);
            $banGiao->ngay_tra = myAPI::convertDMY2YMD($this->dataPost['ngay_nhan']);
        } else {
            $banGiao->ngay_nhan = date('Y-m-d');
            $banGiao->ngay_tra = date('Y-m-d');
        }
        if (!$banGiao->save()) {
            throw new HttpException(500, Html::errorSummary($banGiao));
        }
        return $this->outputSuccess("", "Thêm bàn giao thành công");
    }

    public function actionChiTietBanGiao()
    {
        $this->checkGetInput(['id']);
        if ($this->dataGet['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $banGiao = BanGiao::findOne($this->dataGet['id']);
        if (is_null($banGiao)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $giaoVien = $banGiao->giaoVien;
        return $this->outputSuccess([
            'id' => $banGiao->id,
            'so_luong' => $banGiao->so_luong,
            'giaoVien' => is_null($giaoVien) ? null : [
                'id' => $giaoVien->id,
                'hoten' => $giaoVien->hoten,
                'anh_nguoi_dung' => $giaoVien->getImage(),
                'trinh_do' => $giaoVien->getTrinhDo(),
                'dien_thoai' => $giaoVien->dien_thoai,
            ],
            'ngay_nhan' => date('d/m/Y', strtotime($banGiao->ngay_nhan)),
            'codeGiaoCu' => $banGiao->getCodeGiaoCu(),
            'giaoCu' => $banGiao->getGiaoCu(),
            'ngay_tra' => is_null($banGiao->ngay_tra) ? null : date("d/m/Y", strtotime($banGiao->ngay_tra)),
            'ghi_chu' => $banGiao->ghi_chu,
            'xacNhanBanGiao' => in_array($banGiao->trang_thai, [BanGiao::XAC_NHAN_BAN_GIAO, BanGiao::CHUA_XU_LY, BanGiao::XAC_NHAN_HOAN_TRA]),
            'xacNhanHoanTra' => in_array($banGiao->trang_thai, [BanGiao::XAC_NHAN_HOAN_TRA]),
            'ma_don_hang' => $banGiao->donDichVu->ma_don_hang
        ]);
    }

    public function actionDanhSachBanGiao()
    {
        $this->checkGetInput(['tuKhoa']);
        $banGiaos = QuanLyBanGiao::find();
        if ($this->dataGet['tuKhoa'] != "") {
            $banGiaos->andFilterWhere(['or',
                ['like', 'code', $this->dataGet['tuKhoa']],
                ['like', 'hoten', $this->dataGet['tuKhoa']],
            ]);
        }
        if (isset($this->dataGet['trang_thai'])) {
            if ($this->dataGet['trang_thai'] != "") {
                $banGiaos->andFilterWhere(['trang_thai' => $this->dataGet['trang_thai']]);
            }
        }
        if (isset($this->dataGet['giao_cu_id'])) {
            if ($this->dataGet['giao_cu_id'] != "") {
                $banGiaos->andFilterWhere(['or',
                    ['like', 'chi_tiet_giao_cu', ':' . $this->dataGet['giao_cu_id'] . ','],
                    ['like', 'chi_tiet_giao_cu', '"' . $this->dataGet['giao_cu_id'] . '",'],
                ]);
            }
        }
        $count = count($banGiaos->all());
        $banGiaos = $banGiaos->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy(['created' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        $data = [];
        if (count($banGiaos) > 0) {
            /** @var QuanLyBanGiao $banGiao */
            foreach ($banGiaos as $banGiao) {
                $data [] = [
                    'id' => $banGiao->id,
                    'so_luong' => $banGiao->so_luong,
                    'giaoVien' => is_null($banGiao->giao_vien_id) ? null : [
                        'id' => $banGiao->giao_vien_id,
                        'hoten' => $banGiao->hoten,
                        'anh_nguoi_dung' => $banGiao->getImage(),
                    ],
                    'ngay_nhan' => date('d/m/Y', strtotime($banGiao->ngay_nhan)),
                    'codeGiaoCu' => $banGiao->getCodeGiaoCu(),
                    'giaoCu' => $banGiao->getGiaoCu(),
                    'ngay_tra' => is_null($banGiao->ngay_tra) ? null : date("d/m/Y", strtotime($banGiao->ngay_tra)),
                    'ghi_chu' => $banGiao->ghi_chu,
                    'ma_don_hang' => $banGiao->ma_don_hang,
                    'trang_thai' => $banGiao->trang_thai
                ];
            }
        }
        return $this->outputListSuccess2($data, $count);
    }

    public function actionGiaoVienHoanTra()
    {
        $this->checkField(['id', 'ngay_tra', 'ghi_chu']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $banGiao = BanGiao::findOne($this->dataPost['id']);
        if (is_null($banGiao)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($banGiao->trang_thai != BanGiao::XAC_NHAN_BAN_GIAO) {
            throw new HttpException(500, "Giáo cụ đã được trả trước đó, đang chờ xác nhận");
        }
        $banGiao->ngay_tra = myAPI::convertDMY2YMD($this->dataPost['ngay_tra']);
        $banGiao->trang_thai = BanGiao::CHUA_XU_LY;
        $banGiao->ghi_chu = $this->dataPost['ghi_chu'];
        if (!$banGiao->save()) {
            throw new HttpException(500, Html::errorSummary($banGiao));
        }
        return $this->outputSuccess("", "Đang xử lý bàn giao, vui lòng chờ!");
    }

    public function actionXacNhanHoanTra()
    {
        $this->checkField(['id']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $banGiao = BanGiao::findOne($this->dataPost['id']);
        if (is_null($banGiao)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($banGiao->trang_thai != BanGiao::CHUA_XU_LY) {
            throw new HttpException(500, "Vui lòng kiểm tra lại phiếu");
        }
        $banGiao->trang_thai = BanGiao::XAC_NHAN_HOAN_TRA;
        $banGiao->ghi_chu = $this->dataPost['ghi_chu'] ?? '';
        if (!$banGiao->save()) {
            throw new HttpException(500, Html::errorSummary($banGiao));
        }
        return $this->outputSuccess("", "Hoàn trả thành công!");
    }

    public function actionBaoCao()
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
        $dangMuon = BanGiao::find()
            ->andFilterWhere(['trang_thai' => BanGiao::XAC_NHAN_BAN_GIAO])
            ->andFilterWhere(['=', 'year(created)', $nam])
            ->andFilterWhere(['=', 'month(created)', $thang])
            ->andFilterWhere(['active' => 1])->sum("so_luong");
        $soLuongTon = GiaoCu::find()
            ->andFilterWhere(['<=', 'date(created)', date("Y-m-t", strtotime("$nam-$thang"))])
            ->andFilterWhere(['active' => 1])->sum('so_luong_ton');
        $daTra = BanGiao::find()
            ->andFilterWhere(['trang_thai' => BanGiao::XAC_NHAN_HOAN_TRA])
            ->andFilterWhere(['=', 'year(created)', $nam])
            ->andFilterWhere(['=', 'month(created)', $thang])
            ->andFilterWhere(['active' => 1])->sum("so_luong");
        $soLuongHong = GiaoCu::find()
            ->andFilterWhere(['=', 'year(created)', $nam])
            ->andFilterWhere(['=', 'month(created)', $thang])
            ->andFilterWhere(['active' => 1])->sum('so_luong_hong');
        return $this->outputSuccess([
            'dang_muon' => intval($dangMuon),
            'so_luong_ton' => intval($soLuongTon),
            'da_tra' => intval($daTra),
            'so_luong_hong' => intval($soLuongHong),
        ]);
    }

    public function actionTrangThaiBanGiao()
    {
        return $this->outputSuccess($this->getDanhMuc(DanhMuc::TRANG_THAI_BAN_GIAO));
    }
}
