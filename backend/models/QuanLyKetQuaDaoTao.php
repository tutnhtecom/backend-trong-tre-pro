<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "trong_tre_quan_ly_ket_qua_dao_tao".
 *
 * @property int $id
 * @property int|null $active
 * @property string|null $created
 * @property string|null $updated
 * @property int|null $user_id
 * @property int $giao_vien_id
 * @property int $bai_hoc_id
 * @property string|null $trang_thai
 * @property string|null $ghi_chu
 * @property string|null $baiHoc
 * @property string|null $hocPhan
 * @property string|null $khoaHoc
 * @property string|null $capDo
 * @property string|null $hoten
 * @property string|null $dien_thoai
 * @property string|null $anh_nguoi_dung
 * @property string|null $trinh_do
 */
class QuanLyKetQuaDaoTao extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trong_tre_quan_ly_ket_qua_dao_tao';
    }
    const DANG_CHO_DUYET = 'Đang chờ duyệt';
    const DAT = 'Đạt';
    const CHUA_DAT = 'Chưa đạt';
    const HOC_LAI = 'Học lại';

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'giao_vien_id', 'bai_hoc_id'], 'required'],
            [['id', 'active', 'user_id', 'giao_vien_id', 'bai_hoc_id'], 'integer'],
            [['created', 'updated'], 'safe'],
            [['trang_thai', 'ghi_chu', 'anh_nguoi_dung'], 'string'],
            [['baiHoc', 'hocPhan', 'khoaHoc'], 'string'],
            [['capDo', 'hoten', 'trinh_do'], 'string', 'max' => 100],
            [['dien_thoai'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'active' => 'Active',
            'created' => 'Created',
            'updated' => 'Updated',
            'user_id' => 'User ID',
            'giao_vien_id' => 'Giao Vien ID',
            'bai_hoc_id' => 'Bai Hoc ID',
            'trang_thai' => 'Trang Thai',
            'ghi_chu' => 'Ghi Chu',
            'baiHoc' => 'Bai Hoc',
            'hocPhan' => 'Hoc Phan',
            'khoaHoc' => 'Khoa Hoc',
            'capDo' => 'Cap Do',
            'hoten' => 'Hoten',
            'dien_thoai' => 'Dien Thoai',
            'anh_nguoi_dung' => 'Anh Nguoi Dung',
            'trinh_do' => 'Trinh Do',
        ];
    }
    public function getTrangThai()
    {
        $arr = [
            self::DANG_CHO_DUYET => 79,
            self::DAT => 80,
            self::CHUA_DAT => 81,
            self::HOC_LAI => 103,
        ];
        return [
            'id' => $arr[$this->trang_thai],
            'name' => $this->trang_thai
        ];
    }
    public function getImage (){
        return CauHinh::getServer() . '/upload-file/' . ($this->anh_nguoi_dung == null ? "user-nomal.jpg" : $this->anh_nguoi_dung);
    }
}
