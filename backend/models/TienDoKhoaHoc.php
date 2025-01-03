<?php

namespace backend\models;

use common\models\myAPI;
use common\models\User;
use Yii;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\HttpException;

/**
 * This is the model class for table "trong_tre_tien_do_khoa_hoc".
 *
 * @property int $id
 * @property int|null $buoi
 * @property int|null $tong_buoi
 * @property int|null $thu
 * @property string|null $ngay_day
 * @property int|null $ca_day_id
 * @property string|null $ke_hoach_day
 * @property string|null $nhan_xet_buoi_hoc
 * @property string|null $vao_ca
 * @property int|null $user_id
 * @property int|null $active
 * @property string $created
 * @property string $updated
 * @property string|null $ket_ca
 * @property int|null $giao_vien_id
 * @property string|null $form_danh_gia
 * @property string $trang_thai
 * @property string $gio_day
 * @property string $danh_gia
 * @property string $video
 * @property string $image
 * @property string $phu_huynh_danh_gia
 * @property string $phu_huynh_nhan_xet
 * @property int $so_gio
 * @property int|null $don_dich_vu_id
 * @property int|null $giao_cu_id
 * @property int|null $ban_giao_id
 *
 * @property KhungThoiGian $caDay
 * @property User $giaoVien
 * @property User $user
 * @property DonDichVu $donDichVu
 * @property GiaoCu $giaoCu
 * @property BanGiao $banGiao
 */
class TienDoKhoaHoc extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trong_tre_tien_do_khoa_hoc';
    }

    const CHUA_DAY = 'Chưa dạy';
    const DANG_DAY = 'Đang dạy';
    const DA_HOAN_THANH = 'Đã hoàn thành';
    const DA_HUY = 'Đã hủy';

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['trang_thai'], 'required'],
            [['id', 'buoi', 'tong_buoi', 'thu', 'ca_day_id', 'user_id', 'active', 'giao_vien_id', 'don_dich_vu_id'], 'integer'],
            [['ngay_day', 'vao_ca', 'created', 'updated', 'ket_ca'], 'safe'],
            [['ca_day_id'], 'exist', 'skipOnError' => true, 'targetClass' => KhungThoiGian::className(), 'targetAttribute' => ['ca_day_id' => 'id']],
            [['giao_vien_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['giao_vien_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['don_dich_vu_id'], 'exist', 'skipOnError' => true, 'targetClass' => DonDichVu::className(), 'targetAttribute' => ['don_dich_vu_id' => 'id']],
            [['giao_cu_id'], 'exist', 'skipOnError' => true, 'targetClass' => GiaoCu::className(), 'targetAttribute' => ['giao_cu_id' => 'id']],
            [['ban_giao_id'], 'exist', 'skipOnError' => true, 'targetClass' => BanGiao::className(), 'targetAttribute' => ['ban_giao_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'buoi' => 'Buoi',
            'tong_buoi' => 'Tong Buoi',
            'thu' => 'Thu',
            'ngay_day' => 'Ngay Day',
            'ca_day_id' => 'Ca Day ID',
            'ke_hoach_day' => 'Ke Hoach Day',
            'nhan_xet_buoi_hoc' => 'Nhan Xet Buoi Hoc',
            'vao_ca' => 'Vao Ca',
            'user_id' => 'User ID',
            'active' => 'Active',
            'created' => 'Created',
            'updated' => 'Updated',
            'ket_ca' => 'Ket Ca',
            'giao_vien_id' => 'Giao Vien ID',
            'form_danh_gia' => 'Form Danh Gia',
            'trang_thai' => 'Trang Thai',
            'don_dich_vu_id' => 'Don Dich Vu ID',
        ];
    }

    /**
     * Gets query for [[CaDay]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCaDay()
    {
        return $this->hasOne(KhungThoiGian::className(), ['id' => 'ca_day_id']);
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

    public function getBanGiao()
    {
        return $this->hasOne(BanGiao::className(), ['id' => 'ban_giao_id']);
    }

    public function getGiaoCu()
    {
        return $this->hasOne(GiaoCu::className(), ['id' => 'giao_cu_id']);
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

    /**
     * Gets query for [[DonDichVu]].
     *
     * @return array|\yii\db\ActiveQuery|\yii\db\ActiveRecord
     */
    public function getDonDichVu()
    {
        return DonDichVu::find()->where(['id' => $this->don_dich_vu_id])->one();;
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created = date('Y-m-d H:i:s');
        }
        $this->updated = date('Y-m-d H:i:s');
        $this->ngay_day = myAPI::convertDMY2YMD($this->ngay_day);
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function getCaDayName()
    {
        return $this->caDay->type0->name . " (" . $this->caDay->khungGio->name . " " . $this->donDichVu->gio_bat_dau . ")";
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->updateSortBuoiHocTheoDate();
        if ($this->trang_thai == self::DANG_DAY) {
            $this->giaoVien->vaoCa();
//            $this->giao_cu_id = $this->getGiaoCuGiaoVien();
//            //Sinh phiếu bàn giao
//            if (!is_null($this->giao_cu_id )){
//                $banGiao = new BanGiao();
//                $banGiao->giao_cu_id = $this->giao_cu_id ;
//                $banGiao->giao_vien_id = $this->giao_vien_id;
//                $banGiao->ghi_chu = "Nhận bàn giao giáo cụ";
//                $banGiao->user_id = $this->giao_vien_id;
//                $banGiao->trang_thai = BanGiao::XAC_NHAN_BAN_GIAO;
//                if (!is_null($this->ngay_day)) {
//                    $banGiao->ngay_nhan = $this->ngay_day;
//                } else {
//                    $banGiao->ngay_nhan = date('Y-m-d');
//                }
//                if (!$banGiao->save()) {
//                    throw new HttpException(500, \yii\helpers\Html::errorSummary($banGiao));
//                }else{
//                    $this->ban_giao_id = $banGiao->id;
//                }
//            }
//            $this->updateAttributes(['giao_cu_id'=>$this->giao_cu_id,'ban_giao_id'=>  $this->ban_giao_id]);
        } else if ($this->trang_thai == self::DA_HOAN_THANH) {
            $viecLam = LichSuViecLamGiaoVien::findOne(['don_dich_vu_id' => $this->don_dich_vu_id, 'giao_vien_id' => $this->giao_vien_id, 'active' => 1]);
            if (is_null($viecLam)) {
                $viecLam = new LichSuViecLamGiaoVien();
                $viecLam->don_dich_vu_id = $this->don_dich_vu_id;
                $viecLam->tong_buoi = $this->tong_buoi;
                $viecLam->so_buoi = 0;
                $viecLam->user_id = $this->user_id;
                $viecLam->don_gia = $this->donDichVu->getDonGia();
                $viecLam->giao_vien_id = $this->giao_vien_id;
                $viecLam->trang_thai = LichSuViecLamGiaoVien::DA_HOAN_THANH;
                if (!$viecLam->save()) {
                    throw new HttpException(500, Html::errorSummary($viecLam));
                }
            }
            $viecLam->updateAttributes(['so_buoi' => $this->donDichVu->soBuoiGiaoVienHoanThanh()]);
            if ($this->donDichVu->soBuoiHoanThanh() == $this->tong_buoi) {
                $this->donDichVu->hoanThanhKhoaHoc();
                $nhanLich = NhanLich::findOne(['giao_vien_id' => $this->giao_vien_id, 'don_dich_vu_id' => $this->don_dich_vu_id, 'active' => 1]);
                $nhanLich->trang_thai = NhanLich::DA_HOAN_THANH;
                if (!$nhanLich->save()) {
                    throw new HttpException(500, \yii\helpers\Html::errorSummary($nhanLich));
                }
            }
            $chiLuong = ChiLuong::findOne(['buoi_hoc_id' => $this->id]);
            if (is_null($chiLuong)) {
                $anTrua = PhuPhi::findOne(['don_dich_vu_id' => $this->don_dich_vu_id, 'type_id' => DanhMuc::AN_TRUA, 'active' => 1]);
                $themGio = PhuPhi::findOne(['don_dich_vu_id' => $this->don_dich_vu_id, 'type_id' => DanhMuc::THEM_GIO, 'active' => 1]);
                $heSoLuong = CauHinh::getContent(24);
                $themTre = CauHinh::getContent(26);
                $ppThemGio = CauHinh::getContent(27);
                $ppAnTrua = CauHinh::getContent(25);
                $chiLuong = new ChiLuong();
                $chiLuong->don_dich_vu_id = $this->don_dich_vu_id;
                $chiLuong->don_gia = is_null($this->donDichVu) ? 0 : $this->donDichVu->getDonGia();
                $chiLuong->tong_tien = $chiLuong->don_gia * $heSoLuong / 100 + $chiLuong->don_gia * ($this->donDichVu->so_luong_be - 1) * $themTre / 100;
                $chiLuong->an_trua = is_null($anTrua) ? 0 : $anTrua->tong_tien * $this->donDichVu->so_luong_be * $ppAnTrua / 100;
                $chiLuong->them_gio = is_null($themGio) ? 0 : $themGio->tong_tien * $this->donDichVu->so_luong_be * $ppThemGio / 100;
                $chiLuong->thanh_tien = $chiLuong->tong_tien + $chiLuong->an_trua + $chiLuong->them_gio;
                $chiLuong->giao_vien_id = $this->giao_vien_id;
                $chiLuong->buoi_hoc_id = $this->id;
                $chiLuong->user_id = $this->user_id;
                $chiLuong->created = is_null($this->ket_ca) ? $this->created : $this->ket_ca;
                if (!$chiLuong->save()) {
                    throw new HttpException(500, Html::errorSummary($chiLuong));
                }
            }
        }
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
    }

    public function getGiaoCuGiaoVien()
    {
        $buoi = $this->buoi;
        $chuongTrinhDay = $this->donDichVu->getChuongTrinhDay();
        $giao_cu_id = null;
        if (!is_null($chuongTrinhDay)) {
            if (count($chuongTrinhDay) > 0) {
                foreach ($chuongTrinhDay as $item) {
                    if ($buoi > count($item['buoiHoc'])) {

                        $buoi -= count($item['buoiHoc']);
                    } else {
                        $giaoCu = GoiHoc::findOne($item['id']);
                        if (!is_null($giaoCu)) {
                            $giao_cu_id = $giaoCu->giao_cu_id;
                            break;
                        }
                    }

                }
            }
        }
        return $giao_cu_id;
    }

    public function getTimeSuccess()
    {
        return date('d/m/Y', strtotime($this->vao_ca)) . " • " . date('H:i', strtotime($this->vao_ca)) . " - " . date('H:i', strtotime($this->ket_ca));
    }

    public function formChamSocTre()
    {
        $data = [];
        $danhGia = GiaoVienDanhGiaBuoiHoc::find()->andFilterWhere(['buoi_hoc_id' => $this->id, 'active' => 1, 'danh_muc_id' => 69]);
        $danhGia = $danhGia->andWhere('parent_id is null')->all();
        if (count($danhGia) == 0) {
            $danhGia = DanhGiaBuoiHoc::find()->andFilterWhere(['dich_vu_id' => $this->donDichVu->dich_vu_id, 'active' => 1, 'danh_muc_id' => 69])->andWhere('parent_id is null')
                ->all();
            /** @var DanhGiaBuoiHoc $item */
            foreach ($danhGia as $item) {
                $gvDanhGia = new  GiaoVienDanhGiaBuoiHoc();
                $gvDanhGia->tieu_de = $item->tieu_de;
                $gvDanhGia->buoi_hoc_id = $this->id;
                if (is_null($item->cac_buoi)) {
                    $gvDanhGia->muc_do = ($item->muc_do);
                }
                $gvDanhGia->user_id = $this->user_id;
                $gvDanhGia->danh_muc_id = $item->danh_muc_id;
                $gvDanhGia->goi_y = $item->goi_y;
                $gvDanhGia->nhan_xet = $item->nhan_xet;
                $gvDanhGia->type = isset($item->type) ? $item->type : 0;

                if (!$gvDanhGia->save()) {
                    throw new HttpException(500, \yii\helpers\Html::errorSummary($gvDanhGia));
                } else {
                    if (!is_null($item->cac_buoi)) {
                        $cacBuoi = json_decode($item->cac_buoi);
                        foreach ($cacBuoi as $buoi) {
                            $mucDoBuoi = new GiaoVienDanhGiaBuoiHoc();
                            $mucDoBuoi->tieu_de = $buoi;
                            $mucDoBuoi->buoi_hoc_id = $this->id;
                            $mucDoBuoi->muc_do = ($item->muc_do);
                            $mucDoBuoi->parent_id = $gvDanhGia->id;
                            $mucDoBuoi->danh_muc_id = 69;
                            $mucDoBuoi->user_id = $this->user_id;
                            if (!$mucDoBuoi->save()) {
                                throw new HttpException(500, Html::errorSummary($mucDoBuoi));
                            }
                        }

                    }
                }
                $data[$item['danh_muc_id']]['danhMuc'] = $item->danhMuc->name;
                $data[$item['danh_muc_id']]['data'][] = [
                    'id' => $gvDanhGia->id,
                    'tieu_de' => html_entity_decode($item->tieu_de),
                    'muc_do' => json_decode($item->muc_do),
                    'muc_do_da_cho' => $gvDanhGia->muc_do_da_cho,
                    'nhan_xet' => $item->getNhanXet(),
                    'noi_dung_nhan_xet' => $gvDanhGia->noi_dung_nhan_xet,
                    'goi_y' => json_decode($item->goi_y),
                    'type' => ($item->type),
                    'cac_buoi' => $gvDanhGia->getCacBuoi()
                ];
            }
        } else {
            /** @var GiaoVienDanhGiaBuoiHoc $item */
            foreach ($danhGia as $item) {
                $data[$item['danh_muc_id']]['danhMuc'] = $item->danhMuc->name;
                $data[$item['danh_muc_id']]['data'][] = [
                    'id' => $item->id,
                    'tieu_de' => html_entity_decode($item->tieu_de),
                    'muc_do' => json_decode($item->muc_do),
                    'muc_do_da_cho' => $item->muc_do_da_cho,
                    'nhan_xet' => $item->getNhanXet(),
                    'noi_dung_nhan_xet' => $item->noi_dung_nhan_xet,
                    'goi_y' => json_decode($item->goi_y),
                    'type' => ($item->type),
                    'cac_buoi' => $item->getCacBuoi()
                ];
            }
        }
        return $data;
    }

    public function getFormDanhGia()
    {
        $data = [];
        $formGGS = $this->formGiaoDucSom();
        foreach ($formGGS as $item) {
            $data[] = $item;
        }
        $loai_dich_vu = $this->donDichVu->dichVu->loai_dich_vu_id;
        if ($loai_dich_vu == DichVu::CHAM_SOC_TRE) {
            $formCST = $this->formChamSocTre();
            foreach ($formCST as $item) {
                $data[] = $item;
            }
        }
        return $data;
    }

    public function formGiaoDucSom()
    {
        $data = [];
        $danhGia = GiaoVienDanhGiaBuoiHoc::find()->andFilterWhere(['buoi_hoc_id' => $this->id, 'active' => 1, 'danh_muc_id' => 68]);
        $danhGia = $danhGia->andWhere('parent_id is null')->all();
        if (count($danhGia) == 0) {
            $danhGia = $this->getFormDanhGiaByChuongTrinh();
            foreach ($danhGia as $item) {
                if ($item != "") {
                    $gvDanhGia = new  GiaoVienDanhGiaBuoiHoc();
                    $gvDanhGia->tieu_de = $item;
                    $gvDanhGia->buoi_hoc_id = $this->id;
                    $gvDanhGia->muc_do = json_encode([
                        'Khó', 'Phù hợp', 'Dễ'
                    ]);
                    $gvDanhGia->user_id = $this->user_id;
                    $gvDanhGia->danh_muc_id = 68;
                    $gvDanhGia->goi_y = json_encode([
                        'Bé rất thích thú khi tham gia', 'Bé không hào hứng', 'Bé chưa tập trung'
                    ]);
                    $gvDanhGia->nhan_xet = 1;
                    if (!$gvDanhGia->save()) {
                        throw new HttpException(500, \yii\helpers\Html::errorSummary($gvDanhGia));
                    }
                    $data[68]['danhMuc'] = $gvDanhGia->danhMuc->name;
                    $data[68]['data'][] = [
                        'id' => $gvDanhGia->id,
                        'tieu_de' => html_entity_decode($gvDanhGia->tieu_de),
                        'muc_do' => json_decode($gvDanhGia->muc_do),
                        'muc_do_da_cho' => $gvDanhGia->muc_do_da_cho,
                        'nhan_xet' => $gvDanhGia->getNhanXet(),
                        'noi_dung_nhan_xet' => $gvDanhGia->noi_dung_nhan_xet,
                        'goi_y' => json_decode($gvDanhGia->goi_y),
                        'type' => $gvDanhGia->type,
                        'cac_buoi' => $gvDanhGia->getCacBuoi()
                    ];
                }
            }
        } else {
            /** @var GiaoVienDanhGiaBuoiHoc $item */
            foreach ($danhGia as $item) {
                $data[$item['danh_muc_id']]['danhMuc'] = $item->danhMuc->name;
                $data[$item['danh_muc_id']]['data'][] = [
                    'id' => $item->id,
                    'tieu_de' => html_entity_decode($item->tieu_de),
                    'muc_do' => json_decode($item->muc_do),
                    'muc_do_da_cho' => $item->muc_do_da_cho,
                    'nhan_xet' => $item->getNhanXet(),
                    'noi_dung_nhan_xet' => $item->noi_dung_nhan_xet,
                    'goi_y' => json_decode($item->goi_y),
                    'type' => ($item->type),
                    'cac_buoi' => $item->getCacBuoi()
                ];
            }
        }
        return $data;
    }

    public function getTrangThaiID()
    {
        $arr = [
            'Chưa dạy' => 75,
            'Đang dạy' => 76,
            'Đã hoàn thành' => 77,
            'Đã hủy' => 78,
        ];
        return [
            'id' => isset($arr[$this->trang_thai]) ? $arr[$this->trang_thai] : 78,
            'name' => $this->trang_thai
        ];
    }

    public function updateSortBuoiHocTheoDate()
    {
        $list = TienDoKhoaHoc::find()->andFilterWhere(['active' => 1, 'don_dich_vu_id' => $this->don_dich_vu_id])->orderBy(['ngay_day' => SORT_ASC])->all();
        $buoiHoc = [];
        /** @var TienDoKhoaHoc $item */
        foreach ($list as $item) {
            $buoiHoc[] = $item->buoi;
        }
        sort($buoiHoc);
        foreach ($list as $index => $item) {
            $item->updateAttributes(['buoi' => $buoiHoc[$index]]);
        }
    }

    public function getFormDanhGiaByChuongTrinh()
    {
        if (is_null($this->donDichVu->goi_hoc_id)) {
            return [];
        }
        $baiHocs = json_decode($this->donDichVu->goi_hoc_id);
        if (is_array($baiHocs)){
            $data = [];
            if (count($baiHocs) > 0) {
                foreach ($baiHocs as $item) {
                    $baiHoc = KeHoachDay::find()->andFilterWhere(['goi_hoc_id' => $item, 'active' => 1])->orderBy(['buoi' => SORT_ASC])->all();
                    if (count($baiHoc) > 0) {
                        foreach ($baiHoc as $value) {
                            $data[] = CauHinh::spliptText($value->noi_dung);
                        }
                    }

                }
            }
        }
        return isset($data[$this->buoi - 1]) ? $data[$this->buoi - 1] : [];
    }

    public function getChuongTrinhDayTheoBuoi()
    {
        if (is_null($this->donDichVu->goi_hoc_id)) {
            return [];
        }
        $baiHocs = json_decode($this->donDichVu->goi_hoc_id);
        $data = [];
        if (is_array($baiHocs)) {
            if (count($baiHocs) > 0) {
                foreach ($baiHocs as $item) {
                    $baiHoc = KeHoachDay::find()->andFilterWhere(['goi_hoc_id' => $item, 'active' => 1])->select(['id', 'noi_dung'])->orderBy(['buoi' => SORT_ASC])->all();
                    if (count($baiHoc) > 0) {
                        foreach ($baiHoc as $value) {
                            $data[] = $value;
                        }
                    }

                }
            }
        }
        return isset($data[$this->buoi - 1]) ? $data[$this->buoi - 1] : [];
    }
}
