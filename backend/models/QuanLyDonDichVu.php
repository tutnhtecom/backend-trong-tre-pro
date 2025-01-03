<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "trong_tre_quan_ly_don_dich_vu".
 *
 * @property int $id
 * @property string|null $ma_don_hang
 * @property string|null $created
 * @property string|null $trang_thai
 * @property string|null $hoten_phu_huynh
 * @property string|null $dien_thoai_phu_huynh
 * @property string|null $anh_dai_dien_phu_huynh
 * @property string|null $ten_dich_vu
 * @property string|null $chon_ca
 * @property string|null $dia_chi
 * @property string|null $thoi_gian_bat_dau
 * @property string|null $thoi_gian_ket_thuc
 * @property int|null $active
 * @property string|null $ho_ten_giao_vien
 * @property string|null $dien_thoai_giao_vien
 * @property int|null $trinh_do_giao_vien
 * @property string|null $anh_nguoi_dung_giao_vien
 * @property string|null $danh_gia_giao_vien
 * @property string|null $so_buoi_da_hoan_thanh
 * @property string|null $thoi_gian
 * @property string|null $ghi_chu
 * @property string|null $thu
 * @property int|null $so_buoi
 * @property float|null $tong_tien
 * @property int|null $dich_vu_id
 * @property int|null $da_xem
 * @property int|null $leader_kd_id
 * @property int|null $phu_huynh_id
 * @property int|null $giao_vien_id
 */
class QuanLyDonDichVu extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trong_tre_quan_ly_don_dich_vu';
    }
    public $trangthaiNhanLich;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'active', 'trinh_do_giao_vien', 'so_buoi'], 'integer'],
            [['created', 'thoi_gian_bat_dau', 'thoi_gian_ket_thuc'], 'safe'],
            [['trang_thai', 'anh_dai_dien_phu_huynh', 'dia_chi', 'anh_nguoi_dung_giao_vien'], 'string'],
            [['ma_don_hang'], 'string', 'max' => 11],
            [['hoten_phu_huynh', 'ho_ten_giao_vien'], 'string', 'max' => 100],
            [['dien_thoai_phu_huynh', 'dien_thoai_giao_vien', 'danh_gia_giao_vien'], 'string', 'max' => 20],
            [['ten_dich_vu'], 'string'],
            [['chon_ca'], 'string', 'max' => 204],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ma_don_hang' => 'Ma Don Hang',
            'created' => 'Created',
            'trang_thai' => 'Trang Thai',
            'hoten_phu_huynh' => 'Hoten Phu Huynh',
            'dien_thoai_phu_huynh' => 'Dien Thoai Phu Huynh',
            'anh_dai_dien_phu_huynh' => 'Anh Dai Dien Phu Huynh',
            'ten_dich_vu' => 'Ten Dich Vu',
            'chon_ca' => 'Chon Ca',
            'dia_chi' => 'Dia Chi',
            'thoi_gian_bat_dau' => 'Thoi Gian Bat Dau',
            'thoi_gian_ket_thuc' => 'Thoi Gian Ket Thuc',
            'active' => 'Active',
            'ho_ten_giao_vien' => 'Ho Ten Giao Vien',
            'dien_thoai_giao_vien' => 'Dien Thoai Giao Vien',
            'trinh_do_giao_vien' => 'Trinh Do Giao Vien',
            'anh_nguoi_dung_giao_vien' => 'Anh Nguoi Dung Giao Vien',
            'danh_gia_giao_vien' => 'Danh Gia Giao Vien',
            'so_buoi' => 'So Buoi',
        ];
    }
    public  function getNamebyThu(){
        $arrThu = explode(',',$this->thu);
        $key = array_search(1,$arrThu);
        if(in_array(1,$arrThu)){
            unset($arrThu[$key]);
            return "Thứ ".join(', ',$arrThu).", chủ nhật hàng tuần";
        }else{
            return "Thứ ".join(', ',$arrThu)."hàng tuần";
        }
    }
}
