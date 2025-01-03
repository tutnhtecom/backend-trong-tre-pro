<?php

namespace backend\models;

use common\models\User;
use Yii;
use yii\helpers\Html;
use yii\web\HttpException;

/**
 * This is the model class for table "trong_tre_phieu_luong".
 *
 * @property int $id
 * @property int $active
 * @property string|null $created
 * @property string|null $updated
 * @property int $user_id
 * @property int $giao_vien_id
 * @property string|null $tu_ngay
 * @property string|null $den_ngay
 * @property string|null $chi_tiet_luong
 * @property float|null $them_gio
 * @property float|null $tong_phu_phi
 * @property float|null $tong_giam_tru
 * @property float|null $tong_luong_thuc_te
 * @property float|null $an_trua
 * @property string|null $phu_phi_khac
 * @property string|null $giam_tru
 * @property float|null $thanh_tien
 * @property float|null $so_tien_thanh_toan
 * @property string|null $tieu_de
 * @property string|null $ghi_chu
 * @property string|null $trang_thai
 *
 * @property User $giaoVien
 * @property User $user
 */
class PhieuLuong extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trong_tre_phieu_luong';
    }

    const CHUA_XAC_NHAN = "Chưa xác nhận";
    const DA_XAC_NHAN = "Đã xác nhận";
    const THANH_TOAN_MOT_PHAN = "Thanh toán một phần";
    const DA_THANH_TOAN = "Đã thanh toán";

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'giao_vien_id'], 'required'],
            [['id', 'active', 'user_id', 'giao_vien_id'], 'integer'],
            [['created', 'updated', 'tu_ngay', 'den_ngay'], 'safe'],
            [['chi_tiet_luong'], 'string'],
            [['tieu_de'], 'string'],
            [['giao_vien_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['giao_vien_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
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
            'tu_ngay' => 'Tu Ngay',
            'den_ngay' => 'Den Ngay',
            'chi_tiet_luong' => 'Chi Tiet Luong',
            'them_gio' => 'Them Gio',
            'tong_luong_thuc_te' => 'Tong Luong Thuc Te',
            'an_trua' => 'An Trua',
            'di_lai' => 'Di Lai',
            'tien_thuong' => 'Tien Thuong',
            'thu_nhap_khac' => 'Thu Nhap Khac',
            'bao_hiem_xa_hoi' => 'Bao Hiem Xa Hoi',
            'truy_thu_luong' => 'Truy Thu Luong',
            'tien_cam_ket' => 'Tien Cam Ket',
            'giam_tru_khac' => 'Giam Tru Khac',
            'thanh_tien' => 'Thanh Tien',
            'tieu_de' => 'Tieu De',
        ];
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
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    public static function geLuongTheoThangbyGiaoVien($id,$thang,$nam){
        // Lọc theo ngay
        $lichSuLuong = ChiLuong::find()
            ->andFilterWhere([ChiLuong::tableName() . '.active' => 1, ChiLuong::tableName() . '.giao_vien_id' =>$id]);
        //Query Dữ liệu theo tháng
        $lichSuLuong->andFilterWhere(['=', 'month(' . ChiLuong::tableName() . '.created)', $thang]);
        $lichSuLuong->andFilterWhere(['=', 'year(' . ChiLuong::tableName() . '.created)', $nam]);

        $tongLuongThucTe = $lichSuLuong->sum('tong_tien+them_gio');
        // Lay du lieu an trua phu phi
        $anTrua = $lichSuLuong->sum('an_trua');
        // Lay du lieu an trua phu phi

        // Danh sach cac loai phu phi
        $phuPhiKhac = GiaoDich::find()
            ->andFilterWhere([GiaoDich::tableName() . '.user_id' => $id, GiaoDich::tableName() . '.type' => GiaoDich::NAP_TIEN])
            ->andFilterWhere(['=', 'month(' . GiaoDich::tableName() . '.created)', $thang])
            ->andFilterWhere(['=', 'year(' . GiaoDich::tableName() . '.created)', $nam])
            ->andWhere('type_id is not null');
        $totalPhuPhiKhac = $phuPhiKhac->sum('so_tien');
        //Các loai khau tru
        $khauTru = GiaoDich::find()->andFilterWhere([GiaoDich::tableName() . '.user_id' => $id, GiaoDich::tableName() . '.type' => GiaoDich::RUT_TIEN])
            ->andFilterWhere(['=', 'month(' . GiaoDich::tableName() . '.created)', $thang])
            ->andFilterWhere(['=', 'year(' . GiaoDich::tableName() . '.created)', $nam])
            ->andWhere('type_id is not null');
        $totalKhauTru = $khauTru->sum('so_tien');
        return  strval(floatval($tongLuongThucTe +$anTrua + $totalPhuPhiKhac - $totalKhauTru));
    }
    public static function getBangLuongTheoThangbyGiaoVien($id,$thang,$nam){
        // Lọc theo ngay
        $lichSuLuong = ChiLuong::find()
            ->andFilterWhere([ChiLuong::tableName() . '.active' => 1, ChiLuong::tableName() . '.giao_vien_id' =>$id]);
        //Query Dữ liệu theo tháng
        $lichSuLuong->andFilterWhere(['=', 'month(' . ChiLuong::tableName() . '.created)', $thang]);
        $lichSuLuong->andFilterWhere(['=', 'year(' . ChiLuong::tableName() . '.created)', $nam]);
        return $lichSuLuong->asArray()->all();
    }
}
