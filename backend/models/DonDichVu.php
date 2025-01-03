<?php

namespace backend\models;

use common\models\myAPI;
use common\models\User;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\HttpException;

/**
 * This is the model class for table "trong_tre_don_dich_vu".
 *
 * @property int $id
 * @property int $phu_huynh_id
 * @property int $dich_vu_id
 * @property string|null $dia_chi
 * @property int|null $chon_ca_id
 * @property int|null $giao_vien_id
 * @property int|null $so_luong_be
 * @property int|null $them_gio_id
 * @property int|null $goi_hoc_phi_id
 * @property float|null $hoc_phi
 * @property float|null $phu_cap
 * @property float|null $tong_tien
 * @property string|null $ghi_chu
 * @property string|null $thoi_gian_bat_dau
 * @property string|null $thoi_gian_ket_thuc
 * @property string|null $ma_don_hang
 * @property string|null $trang_thai
 * @property string|null $noi_dung_khao_sat
 * @property int|null $hinh_thuc_thanh_toan_id
 * @property string|null $ghi_chu_thanh_toan
 * @property string|null $trang_thai_thanh_toan
 * @property int|null $leader_kd_id
 * @property int|null $lich_sinh_hoat_id
 * @property int|null $loai_giao_vien
 * @property int|null $user_id
 * @property int|null $active
 * @property int|null $da_xem
 * @property string|null $created
 * @property string|null $thu
 * @property string|null $updated
 * @property string|null $noi_dung_danh_gia
 * @property string|null $ngay_thanh_toan
 * @property string|null $goi_hoc_id
 * @property string|null $li_do_huy
 * @property int|null $danh_gia
 * @property int|null $chuong_trinh_hoc_id
 * @property int|null $status
 * @property int|null $so_tien_hoan
 * @property int|null $so_buoi
 * @property int|null $so_buoi_hoan
 * @property int|null $giao_vien_dong_thuan
 * @property int|null $phu_huynh_dong_thuan
 * @property int|null $tong_tien_goc
 * @property string|null $gio_bat_dau
 * @property string|null $phieu_dan_do
 *
 * @property KhungThoiGian $chonCa
 * @property DanhMuc $hinhThucThanhToan
 * @property DichVu $dichVu
 * @property User $giaoVien
 * @property GiaDichVu $goiHocPhi
 * @property User $leaderKd
 * @property DanhMuc $loaiGiaoVien
 * @property ChuongTrinhHoc $chuongTrinhHoc
 * @property User $phuHuynh
 * @property DanhMuc $themGio
 * @property User $user
 */
class DonDichVu extends \yii\db\ActiveRecord
{
    /**
     * @var mixed|null
     */

    /**
     * {@inheritdoc}
     */
    public $an_trua_id;
    public $them_gio_id;

    public static function tableName()
    {
        return 'trong_tre_don_dich_vu';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['phu_huynh_id', 'dich_vu_id'], 'required'],
            // [['phu_huynh_id', 'dich_vu_id', 'chon_ca_id', 'giao_vien_id', 'so_luong_be', 'them_gio_id', 'goi_hoc_phi_id', 'hinh_thuc_thanh_toan_id', 'leader_kd_id', 'lich_sinh_hoat_id', 'loai_giao_vien', 'user_id', 'active'], 'integer'],
            // [['dia_chi', 'ghi_chu', 'trang_thai', 'noi_dung_khao_sat', 'ghi_chu_thanh_toan', 'trang_thai_thanh_toan'], 'string'],
            // [['hoc_phi', 'phu_cap', 'tong_tien'], 'number'],
            // [['thoi_gian_bat_dau', 'thoi_gian_ket_thuc', 'created', 'updated'], 'safe'],
            // [['ma_don_hang'], 'string', 'max' => 11],
            // [['chon_ca_id'], 'exist', 'skipOnError' => true, 'targetClass' => KhungThoiGian::className(), 'targetAttribute' => ['chon_ca_id' => 'id']],
            // [['hinh_thuc_thanh_toan_id'], 'exist', 'skipOnError' => true, 'targetClass' => DanhMuc::className(), 'targetAttribute' => ['hinh_thuc_thanh_toan_id' => 'id']],
            // [['dich_vu_id'], 'exist', 'skipOnError' => true, 'targetClass' => DichVu::className(), 'targetAttribute' => ['dich_vu_id' => 'id']],
            // [['giao_vien_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['giao_vien_id' => 'id']],
            // [['goi_hoc_phi_id'], 'exist', 'skipOnError' => true, 'targetClass' => GiaDichVu::className(), 'targetAttribute' => ['goi_hoc_phi_id' => 'id']],
            // [['leader_kd_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['leader_kd_id' => 'id']],
            // [['loai_giao_vien'], 'exist', 'skipOnError' => true, 'targetClass' => DanhMuc::className(), 'targetAttribute' => ['loai_giao_vien' => 'id']],
            // [['phu_huynh_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['phu_huynh_id' => 'id']],
            // [['them_gio_id'], 'exist', 'skipOnError' => true, 'targetClass' => DanhMuc::className(), 'targetAttribute' => ['them_gio_id' => 'id']],
            // [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            // [['chuong_trinh_hoc_id'], 'exist', 'skipOnError' => true, 'targetClass' => ChuongTrinhHoc::className(), 'targetAttribute' => ['chuong_trinh_hoc_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'phu_huynh_id' => 'Phu Huynh ID',
            'dich_vu_id' => 'Dich Vu ID',
            'dia_chi' => 'Dia Chi',
            'chon_ca_id' => 'Chon Ca ID',
            'giao_vien_id' => 'Giao Vien ID',
            'so_luong_be' => 'So Luong Be',
            'them_gio_id' => 'Them Gio ID',
            'goi_hoc_phi_id' => 'Goi Hoc Phi ID',
            'hoc_phi' => 'Hoc Phi',
            'phu_cap' => 'Phu Cap',
            'tong_tien' => 'Tong Tien',
            'ghi_chu' => 'Ghi Chu',
            'thoi_gian_bat_dau' => 'Thoi Gian Bat Dau',
            'thoi_gian_ket_thuc' => 'Thoi Gian Ket Thuc',
            'ma_don_hang' => 'Ma Don Hang',
            'trang_thai' => 'Trang Thai',
            'noi_dung_khao_sat' => 'Noi Dung Khao Sat',
            'hinh_thuc_thanh_toan_id' => 'Hinh Thuc Thanh Toan ID',
            'ghi_chu_thanh_toan' => 'Ghi Chu Thanh Toan',
            'trang_thai_thanh_toan' => 'Trang Thai Thanh Toan',
            'leader_kd_id' => 'Leader Kd ID',
            'lich_sinh_hoat_id' => 'Lich Sinh Hoat ID',
            'loai_giao_vien' => 'Loai Giao Vien',
            'user_id' => 'User ID',
            'active' => 'Active',
            'created' => 'Created',
            'updated' => 'Updated',
        ];
    }

    /**
     * Gets query for [[ChonCa]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChonCa()
    {
        return $this->hasOne(KhungThoiGian::className(), ['id' => 'chon_ca_id']);
    }

    /**
     * Gets query for [[HinhThucThanhToan]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHinhThucThanhToan()
    {
        return $this->hasOne(DanhMuc::className(), ['id' => 'hinh_thuc_thanh_toan_id']);
    }

    /**
     * Gets query for [[DichVu]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDichVu()
    {
        return $this->hasOne(DichVu::className(), ['id' => 'dich_vu_id']);
    }

    /**
     * Gets query for [[GiaoVien]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGiaoVien()
    {
        return $this->hasOne(User::className(), ['id' => 'giao_vien_id']);
    }

    /**
     * Gets query for [[GoiHocPhi]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGoiHocPhi()
    {
        return $this->hasOne(GiaDichVu::className(), ['id' => 'goi_hoc_phi_id']);
    }

    /**
     * Gets query for [[LeaderKd]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeaderKd()
    {
        return $this->hasOne(User::className(), ['id' => 'leader_kd_id']);
    }

    public function getChuongTrinhHoc()
    {
        return $this->hasOne(ChuongTrinhHoc::className(), ['id' => 'chuong_trinh_hoc_id']);
    }

    /**
     * Gets query for [[LoaiGiaoVien]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLoaiGiaoVien()
    {
        return $this->hasOne(DanhMuc::className(), ['id' => 'loai_giao_vien']);
    }

    /**
     * Gets query for [[PhuHuynh]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPhuHuynh()
    {
        return $this->hasOne(User::className(), ['id' => 'phu_huynh_id']);
    }

    /**
     * Gets query for [[ThemGio]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getThemGio()
    {
        return $this->hasOne(DanhMuc::className(), ['id' => 'them_gio_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created = date('Y-m-d H:i:s');
            $this->tong_tien_goc = $this->tong_tien;
            $this->so_buoi = $this->goiHocPhi->so_buoi;
        }
        $this->updated = date('Y-m-d H:i:s');
        $this->thoi_gian_bat_dau = myAPI::convertDMY2YMD($this->thoi_gian_bat_dau);
        $model = DonDichVu::findOne($this->id);
        if (!is_null($model)) {
            if ($model->giao_vien_id != null && $model->giao_vien_id != $this->giao_vien_id) {
                $nhanLich = NhanLich::findOne(['don_dich_vu_id' => $this->id, 'giao_vien_id' => $model->giao_vien_id]);
                if (is_null($nhanLich)) {
                    $nhanLich = new NhanLich();
                    $nhanLich->user_id = $this->user_id;
                    $nhanLich->giao_vien_id = $model->giao_vien_id;
                    $nhanLich->don_dich_vu_id = $this->id;
                    $nhanLich->trang_thai = NhanLich::DA_HUY;
                    if (!$nhanLich->save()) {
                        throw new HttpException(500, Html::errorSummary($nhanLich));
                    }
                } else {
                    $nhanLich->trang_thai = NhanLich::DA_HUY;
                    if (!$nhanLich->save()) {
                        throw new \yii\web\HttpException(500, \yii\helpers\Html::errorSummary($nhanLich));
                    }
                }
            }
        }

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    /**
     * @throws HttpException
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($this->giao_vien_dong_thuan == 1 || $this->phu_huynh_dong_thuan == 1) {
            foreach (range(1, $this->so_buoi) as $buoi) {
                $this->tienDoKhoaHoc($buoi, true);
            }
        }
        if (is_null($this->ma_don_hang)) {
            $this->updateAttributes(['ma_don_hang' => 'DH' . sprintf("%07d", $this->id)]);
        }
        if ($this->trang_thai != "") {
            $trangThai = new LichSuTrangThaiDon();
            $trangThai->don_hang_id = $this->id;
            $trangThai->trang_thai = $this->trang_thai;
            $trangThai->tong_tien = $this->tong_tien;
            $trangThai->user_id = $this->user_id;
            $trangThai->so_buoi_hoan = $this->so_buoi_hoan;
            $trangThai->so_tien_hoan = $this->so_tien_hoan;
            if ($this->leader_kd_id != null) {
                $trangThai->leader_kd_id = $this->leader_kd_id;
            }
            $trangThai->giao_vien_id = $this->giao_vien_id;
            $trangThai->li_do_huy = $this->li_do_huy;
            if (!$trangThai->save()) {
                throw new \yii\web\HttpException(500, Html::errorSummary($trangThai));
            }
        }
        $this->updateAttributes(['thoi_gian_ket_thuc' => $this->getDateByBuoi($this->so_buoi)]);
        if (isset($changedAttributes->thoi_gian_bat_dau) || isset($changedAttributes->thu)) {
            if ($this->thoi_gian_bat_dau != "" && $this->thu != "") {
//     VarDumper::dump($this->getDateByBuoi($this->so_buoi-1));exit();
//                $tienDos = TienDoKhoaHoc::find()->andFilterWhere(['don_dich_vu_id' => $this->id])->andFilterWhere(['<>', 'trang_thai', TienDoKhoaHoc::DA_HOAN_THANH])->all();
//                $countSuccess = TienDoKhoaHoc::find()->andFilterWhere(['don_dich_vu_id' => $this->id])->andFilterWhere(['trang_thai' => TienDoKhoaHoc::DA_HOAN_THANH])->count();
//                $lastDate = TienDoKhoaHoc::find()->andFilterWhere(['don_dich_vu_id' => $this->id])->andFilterWhere(['trang_thai' => TienDoKhoaHoc::DA_HOAN_THANH])->max('ngay_day');
//                /** @var TienDoKhoaHoc $tienDo */
//                foreach ($tienDos as $tienDo) {
//                    $tienDo->ngay_day = $this->getDateByBuoi($tienDo->buoi - $countSuccess, date('Y-m-d', strtotime("+1 day", strtotime($lastDate))));
//                    $tienDo->save();
//                }
            }
        }
        if ($this->trang_thai_thanh_toan != "") {
            $trangThai = new LichSuTrangThaiThanhToan();
            $trangThai->don_hang_id = $this->id;
            $trangThai->trang_thai = $this->trang_thai_thanh_toan;
            $trangThai->hinh_thuc_thanh_toan_id = $this->hinh_thuc_thanh_toan_id;
            $trangThai->ghi_chu = $this->ghi_chu_thanh_toan;
            $trangThai->user_id = $this->user_id;
            $trangThai->tong_tien = $this->tong_tien;
            if (!$trangThai->save()) {
                throw new \yii\web\HttpException(500, Html::errorSummary($trangThai));
            } else {
                if ($this->trang_thai_thanh_toan == LichSuTrangThaiThanhToan::DA_THANH_TOAN) {
                    $this->updateAttributes(['ngay_thanh_toan' => date('Y-m-d')]);
                }
            }
        }
//        if ($insert){
//            if ($this->goi_hoc_phi_id!=""){
//                $soBuoi = $this->goiHocPhi->so_buoi;
//                foreach (range(1,$soBuoi) as $item){
//                    $tienDoKhoaHoc =  new TienDoKhoaHoc();
//                    $tienDoKhoaHoc->buoi = $item;
//                    $tienDoKhoaHoc->tong_buoi = $soBuoi;
//                }
//            }
//        }

        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
    }

    public
    function getThoiGianKetThuc($first_day, $thu, $so_buoi)
    {
        $arrThu = explode(',', $thu);
        $soBuoiTrongTuan = count($arrThu);
        $soNgay = ceil($so_buoi / $soBuoiTrongTuan) * 7;
        return date("Y-m-d", strtotime('+' . $soNgay . ' day', strtotime($first_day)));
    }

    public
    function getBuoiHienTai()
    {
        $hienTai = TienDoKhoaHoc::find()->andFilterWhere(['don_dich_vu_id' => $this->id, 'active' => 1])->andFilterWhere(['in', 'trang_thai', [TienDoKhoaHoc::DA_HOAN_THANH, TienDoKhoaHoc::DA_HUY]])->count();
        return $hienTai + 1 > $this->so_buoi ? $this->so_buoi : $hienTai + 1;
    }

    public
    static function getThuName($date)
    {
        $week = array("CN", "Thứ 2", "Thứ 3", "Thứ 4", "Thứ 5", "Thứ 6", "Thứ 7");
        $w = date('w', strtotime($date));
        $day_of_week = $week[$w];
        return $day_of_week;
    }

    public
    function getNamebyThu()
    {
        $arrThu = explode(',', $this->thu);
        sort($arrThu);
        $key = array_search(1, $arrThu);
        if (in_array(1, $arrThu)) {
            unset($arrThu[$key]);
            return "Thứ " . join(', ', $arrThu) . ", CN hàng tuần";
        } else {
            return "Thứ " . join(', ', $arrThu) . " hàng tuần";
        }
    }

    public
    function actionGetThoiGian()
    {
        return DanhMuc::find()->andFilterWhere(['name' => $this->trang_thai, 'type' => DanhMuc::TRANG_THAI_DON])->select(['id', 'name'])->one();
    }

    public
    function tienDoKhoaHoc($buoi, $check = false)
    {
        $tienDoKhoaHoc = TienDoKhoaHoc::findOne(['don_dich_vu_id' => $this->id, 'buoi' => $buoi, 'active' => 1]);

        if (is_null($tienDoKhoaHoc)) {
            $date = $this->getDateByBuoi($buoi);
            $tienDoKhoaHoc = new TienDoKhoaHoc();
            $tienDoKhoaHoc->buoi = $buoi;
            $tienDoKhoaHoc->tong_buoi = $this->so_buoi;
            $tienDoKhoaHoc->ngay_day = $date;
            $tienDoKhoaHoc->gio_day = $this->gio_bat_dau;
            $tienDoKhoaHoc->thu = intval(date('w', strtotime($date))) + 1;
            $tienDoKhoaHoc->ca_day_id = $this->chon_ca_id;
            $tienDoKhoaHoc->user_id = $this->giao_vien_id;
            $tienDoKhoaHoc->giao_vien_id = $this->giao_vien_id;
            $tienDoKhoaHoc->so_gio = $this->getSoGio($tienDoKhoaHoc->gio_day);
            $tienDoKhoaHoc->trang_thai = TienDoKhoaHoc::CHUA_DAY;
            $tienDoKhoaHoc->don_dich_vu_id = $this->id;
            if (!$tienDoKhoaHoc->save()) {
                throw new \yii\web\HttpException(500, \yii\helpers\Html::errorSummary($tienDoKhoaHoc));
            }
        } else {
            $tienDoKhoaHoc->updateAttributes(['so_gio' => $this->getSoGio($tienDoKhoaHoc->gio_day)]);
            if ($tienDoKhoaHoc->tong_buoi != $this->so_buoi) {
                $tienDoKhoaHoc->updateAttributes(['tong_buoi' => $this->so_buoi]);
            }
        }
        $tienDoKhoaHoc = TienDoKhoaHoc::findOne(['don_dich_vu_id' => $this->id, 'buoi' => $buoi, 'active' => 1]);
        if ($check) {
            return true;
        }
        return [
            'id' => $tienDoKhoaHoc->id,
            'buoi' => intval($tienDoKhoaHoc->buoi),
            'tong_buoi' => $tienDoKhoaHoc->tong_buoi,
            'ngay_day' => $this->getThuName($tienDoKhoaHoc->ngay_day) . " • " . date("d/m/Y", strtotime($tienDoKhoaHoc->ngay_day)),
            'ca_day' => $tienDoKhoaHoc->getCaDayName(),
            'ca_id' => $tienDoKhoaHoc->caDay->type,
            'khung_gio_id' => $tienDoKhoaHoc->caDay->khung_gio,
            'ke_hoach_day' => $this->getChuongTrinhDay(),
            'ke_hoach_day_theo_buoi' => $tienDoKhoaHoc->getChuongTrinhDayTheoBuoi(),
            'don_dich_vu_id' => $tienDoKhoaHoc->don_dich_vu_id,
            'nhan_xet_buoi_hoc' => $tienDoKhoaHoc->nhan_xet_buoi_hoc,
            'trang_thai' => $tienDoKhoaHoc->getTrangThaiID(),
            'so_gio' => $tienDoKhoaHoc->so_gio,
            'image' => CauHinh::getImage($tienDoKhoaHoc->image),
            'video' => $tienDoKhoaHoc->video,
            'danh_gia' => $tienDoKhoaHoc->danh_gia,
            'giao_vien_id' => $tienDoKhoaHoc->giao_vien_id
        ];

    }

    public
    function getSoGio($gioDay)
    {
        return intval($this->chonCa->khungGio->ghi_chu);
    }

    public
    function getDateByBuoi($buoi, $date = null)
    {
        if (is_null($date) || strtotime($date)) {
            $date = $this->thoi_gian_bat_dau;
        }
        $arrThu = explode(',', $this->thu);

        $arrThu = $this->sortThu($arrThu, $date);
        $tuan = ceil($buoi / count($arrThu)) - 1;

        $keyThu = ($buoi % count($arrThu) == 0 ? $buoi - $tuan * count($arrThu) : $buoi % count($arrThu)) - 1;
        $thu = $arrThu[$keyThu] - 1;
        $count = 0;

        while (date('w', strtotime($date)) !== $thu || $count == $tuan) {
            if (date('w', strtotime($date)) == $thu) {

                if ($count == $tuan) {
                    break;
                }
                $count++;
            }
            $date = date("Y-m-d", strtotime('+1 day', strtotime($date)));
        };
        return $date;
    }

    public
    function sortThu($arrThu, $date)
    {
        sort($arrThu);

        while (!in_array(date('w', strtotime($date)) + 1, $arrThu)) {
            $date = date("Y-m-d", strtotime('+1 day', strtotime($date)));
            if (in_array(date('w', strtotime($date)), $arrThu)) {
                break;
            }
        };
        $day = date("w", strtotime($date)) + 1;
        $arr = [];
        foreach ($arrThu as $index => $item) {
            if ($day != $item) {
                $arr[] = $item;
                unset($arrThu[$index]);
            } else {
                break;
            }
        }
        return array_merge($arrThu, $arr);
    }

    public
    function getTrangThaiTienDo()
    {
        $tienDo = TienDoKhoaHoc::find()->andFilterWhere(['don_dich_vu_id' => $this->id, 'active' => 1, 'trang_thai' => TienDoKhoaHoc::DA_HOAN_THANH])->count();
        return $tienDo . "/" . $this->so_buoi;
    }

    public
    function soTienConDu()
    {
        if (intval($this->so_buoi) == 0) {
            return 0;
        }
        $tienDo = TienDoKhoaHoc::find()->andFilterWhere(['don_dich_vu_id' => $this->id, 'active' => 1, 'trang_thai' => TienDoKhoaHoc::DA_HOAN_THANH])->count();
        $soTien = $this->tong_tien * ($this->so_buoi - $tienDo) / $this->so_buoi;
        return $soTien;
    }

    public
    function soBuoiHoanThanh()
    {
        $tienDo = TienDoKhoaHoc::find()->andFilterWhere(['don_dich_vu_id' => $this->id, 'active' => 1, 'trang_thai' => TienDoKhoaHoc::DA_HOAN_THANH])->count();
        return $tienDo;
    }

    public
    function soBuoiGiaoVienHoanThanh()
    {
        $tienDo = TienDoKhoaHoc::find()->andFilterWhere(['don_dich_vu_id' => $this->id, 'giao_vien_id' => $this->giao_vien_id, 'active' => 1, 'trang_thai' => TienDoKhoaHoc::DA_HOAN_THANH])->count();
        return $tienDo . "/" . $this->so_buoi;
    }

    public
    function getThoiGian()
    {
        return date('d/m/Y', strtotime($this->thoi_gian_bat_dau)) . " - " . date('d/m/Y', strtotime($this->thoi_gian_ket_thuc));
    }

    public
    function getCaDayName()
    {
        return $this->chonCa->type0->name . " (" . $this->chonCa->khungGio->name . " " . $this->gio_bat_dau . ")";
    }

    public
    function updateTongTien()
    {
        $phuPhi = PhuPhi::find()->andFilterWhere(['don_dich_vu_id' => $this->id, 'active' => 1])->andFilterWhere(['not in', 'type_id', [DanhMuc::AN_TRUA, DanhMuc::THEM_GIO]])->sum('tong_tien');
        $giaHanDon = GiaHanDon::find()->andFilterWhere(['don_dich_vu_id' => $this->id])->sum('tong_tien');
        $this->updateAttributes(['tong_tien' => $this->tong_tien_goc + $giaHanDon + $phuPhi]);
    }

    public
    function hoanThanhKhoaHoc()
    {
        $this->trang_thai = LichSuTrangThaiDon::HOAN_THANH;
        $phuPhi = PhuPhi::find()->andFilterWhere(['don_dich_vu_id' => $this->id, 'active' => 1])->andFilterWhere(['not in', 'type_id', [DanhMuc::AN_TRUA, DanhMuc::THEM_GIO]])->andFilterWhere(['is_phu_phi' => 0])->all();
        /** @var PhuPhi $item */
        foreach ($phuPhi as $item) {
            if ($item->tong_tien > 0) {
                if (!is_null($this->giao_vien_id)) {
                    // He so tien phu phi
                    $ppKhac = CauHinh::getContent(28);
                    $giaoDich = new GiaoDich();
                    $giaoDich->so_tien = $item->tong_tien * $ppKhac / 100;
                    $giaoDich->ghi_chu = $item->ghi_chu;
                    $giaoDich->type = GiaoDich::NAP_TIEN;
                    $giaoDich->user_id = $this->giao_vien_id;
                    $giaoDich->type_id = $item->type_id;
                    $giaoDich->tieu_de = "Cộng tiền cho đơn số";
                    $giaoDich->don_dich_vu_id = $item->don_dich_vu_id;
                    $giaoDich->phu_phi_id = $item->id;
                    $giaoDich->created = $item->created;
                    if (!$giaoDich->save()) {
                        throw new HttpException(500, Html::errorSummary($giaoDich));
                    }
                    $item->updateAttributes(['is_phu_phi' => 1]);
                }
            }
        }
        if (!$this->save()) {
            throw new HttpException(500, Html::errorSummary($this));
        }
    }

    public
    function getDonGia()
    {
        $goiHocPhi = $this->goiHocPhi;
        return round($goiHocPhi->tong_tien);
    }

    public
    function getChuongTrinhDay()
    {
        if (is_null($this->goi_hoc_id)) {
            return null;
        }
        $goiHoc = json_decode($this->goi_hoc_id);
        if (!is_array($goiHoc)) {
            return null;
        }
        if (count($goiHoc) == 0) {
            return null;
        }
        $data = [];
        $danhSachChuongTrinh = GoiHoc::find()->andFilterWhere(['in', 'id', $goiHoc])->all();
        /** @var GoiHoc $item */
        foreach ($danhSachChuongTrinh as $item) {
            $buoiHoc = $item->getBuoiHoc();
            $data[$item->nhom_id]['id'] = $item->nhom_id;
            $data[$item->nhom_id]['name'] = $item->nhom->name;
            $data[$item->nhom_id]['ten_chuong_trinh'] = "Chương trình " . $this->dichVu->ten_dich_vu . ": ";
            $data[$item->nhom_id]['goiHoc'][] = [
                'id' => $item->id,
                'tieu_de' => $item->tieu_de,
                'buoiHoc' => $buoiHoc,
                'giaoCu' => $item->getGiaoCu()
            ];
        }
        foreach ($data as $item) {
            $data3[] = $item;
        }
        return $data3;
    }

    public
    function getLeader()
    {
        if (!is_null($this->leaderKd)) {
            return [
                'id' => $this->leader_kd_id,
                'hoten' => $this->leaderKd->hoten,
                'dien_thoai' => $this->leaderKd->dien_thoai,
            ];
        }
    }

    public
    function giaHanDon()
    {
        $giaHan = GiaHanDon::find()->andFilterWhere(['don_dich_vu_id' => $this->id])->all();
        $data = [];
        /** @var GiaHanDon $item */
        foreach ($giaHan as $item) {
            $data [] = [
                'id' => $item->id,
                'tong_tien' => $item->tong_tien,
                'so_buoi' => $item->so_buoi,
                'created' => date('d-m-Y', strtotime($item->created)),
            ];
        }
        return $data;
    }

    public
    function totalDonDichVu()
    {
        $tongTienAnTrua = 0;
        $tongTienThemGio = 0;
        $hocPhi = 0;
        $phuPhiThemTre = CauHinh::getContent(37);
        $soLuongTre = $this->so_luong_be;
        $thanhTien = 0;
        if (!is_null($this->an_trua_id)) {
            $anTrua = DanhMuc::findOne(['id' => $this->an_trua_id, 'type' => 'Ăn trưa']);
            if (!is_null($anTrua)) {
                $ghiChu = json_decode($anTrua->ghi_chu);
                if (isset($ghiChu->tong_tien)) {
                    $tongTienAnTrua = intval($ghiChu->tong_tien);
                }
            }
        }
        if (!is_null($this->them_gio_id)) {
            $themGio = DanhMuc::findOne(['id' => $this->them_gio_id, 'type' => 'Thêm giờ']);
            if (!is_null($themGio)) {
                $ghiChu = json_decode($themGio->ghi_chu);
                if (isset($ghiChu->tong_tien)) {
                    $tongTienThemGio = intval($ghiChu->tong_tien);
                }
            }

        }
        if (!is_null($this->goi_hoc_phi_id)) {
            $goiDichVu = $this->goiHocPhi;
            if (!is_null($goiDichVu)) {
                $thanhTien = $goiDichVu->tong_tien * $goiDichVu->so_buoi - $goiDichVu->tong_tien * $goiDichVu->khuyen_mai * $goiDichVu->so_buoi / 100;
                $hocPhi = $thanhTien + $thanhTien * ($soLuongTre - 1) * $phuPhiThemTre / 100;
                $tongTienAnTrua = $tongTienAnTrua * $goiDichVu->so_buoi;
                $tongTienThemGio = $tongTienThemGio * $goiDichVu->so_buoi;
            }
        }
        $phuCap = ($tongTienAnTrua + $tongTienThemGio) * $soLuongTre;
        $tongTien = $hocPhi + $phuCap;
        return [
            'hocPhi' => $hocPhi,
            'phuCap' => $phuCap,
            'tongTien' => $tongTien,
        ];
    }

    public
    function updatePhuCap($phuPhiUpdate)
    {
        $phuPhiAnTrua = PhuPhi::findOne(['don_dich_vu_id' => $this->id, 'type_id' => DanhMuc::AN_TRUA]);
        if (is_null($phuPhiAnTrua)) {
            $phuPhiAnTrua = new PhuPhi();
        }
        $phuPhiAnTrua->don_dich_vu_id = $this->id;
        $phuPhiAnTrua->user_id = $this->user_id;
        $phuPhiAnTrua->type_id = DanhMuc::AN_TRUA;
        $phuPhiAnTrua->tieu_de = "Ăn trưa";

        $phuPhiThemGio = PhuPhi::findOne(['don_dich_vu_id' => $this->id, 'type_id' => DanhMuc::THEM_GIO]);
        if (is_null($phuPhiThemGio)) {
            $phuPhiThemGio = new PhuPhi();
        }
        $phuPhiThemGio->don_dich_vu_id = $this->id;
        $phuPhiThemGio->user_id = $this->user_id;
        $phuPhiThemGio->type_id = DanhMuc::THEM_GIO;
        $phuPhiThemGio->tieu_de = "Thêm giờ";


        switch ($phuPhiUpdate) {
            case 0:
            {
                $phuPhiAnTrua->ghi_chu = "";
                $phuPhiAnTrua->tong_tien = 0;
                $phuPhiThemGio->ghi_chu = "";
                $phuPhiThemGio->tong_tien = 0;
                break;
            }
            case 100000:
            {
                $phuPhiAnTrua->ghi_chu = "";
                $phuPhiAnTrua->tong_tien = 0;
                $phuPhiThemGio->ghi_chu = "(+100k)";
                $phuPhiThemGio->tong_tien = 100000;
                break;
            }
            case 200000:
            {
                $phuPhiAnTrua->ghi_chu = "";
                $phuPhiAnTrua->tong_tien = 0;
                $phuPhiThemGio->ghi_chu = "(+200k)";
                $phuPhiThemGio->tong_tien = 200000;
                break;
            }
            case 30000:
            {
                $phuPhiAnTrua->ghi_chu = "PH trả phụ cấp 30k/ngày";
                $phuPhiAnTrua->tong_tien = 30000;
                $phuPhiThemGio->ghi_chu = "";
                $phuPhiThemGio->tong_tien = 0;
                break;
            }
            case 130000:
            {
                $phuPhiAnTrua->ghi_chu = "PH trả phụ cấp 30k/ngày";
                $phuPhiAnTrua->tong_tien = 30000;
                $phuPhiThemGio->ghi_chu = "(+100k)";
                $phuPhiThemGio->tong_tien = 100000;
                break;
            }
            case 230000:
            {
                $phuPhiAnTrua->ghi_chu = "PH trả phụ cấp 30k/ngày";
                $phuPhiAnTrua->tong_tien = 30000;
                $phuPhiThemGio->ghi_chu = "(+200k)";
                $phuPhiThemGio->tong_tien = 200000;
                break;
            }
        }
        if (!$phuPhiAnTrua->save()) {
            throw new HttpException(500, Html::errorSummary($phuPhiAnTrua));
        }
        if (!$phuPhiThemGio->save()) {
            throw new HttpException(500, Html::errorSummary($phuPhiThemGio));
        }
    }

    public static function find()
    {
        $user = QuanLyUserVaiTro::findOne(['id' => \Yii::$app->controller->uid?? 0]);
        if (is_null($user)) {
            return parent::find();
        }
        if ($user->vai_tro == VaiTro::LEADER_KD) {
            return parent::find()->andFilterWhere(['leader_kd_id' => $user->id]);
        }
        return parent::find();
    }
}
