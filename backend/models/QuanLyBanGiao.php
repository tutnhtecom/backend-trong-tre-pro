<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "trong_tre_quan_ly_ban_giao".
 *
 * @property int $id
 * @property int $active
 * @property string|null $created
 * @property string|null $updated
 * @property int $user_id
 * @property int $giao_vien_id
 * @property int $so_luong
 * @property string|null $ngay_nhan
 * @property string|null $ngay_tra
 * @property int $giao_cu_id
 * @property int $ghi_chu
 * @property string $trang_thai
 * @property string|null $hoten
 * @property string|null $dien_thoai
 * @property string|null $anh_nguoi_dung
 * @property string|null $chi_tiet_giao_cu
 * @property string|null $code
 */
class QuanLyBanGiao extends \yii\db\ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'trong_tre_quan_ly_ban_giao';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['id', 'active', 'user_id', 'giao_vien_id', 'giao_cu_id', 'ghi_chu'], 'integer'],
      [['created', 'updated', 'ngay_nhan', 'ngay_tra'], 'safe'],
      [['user_id', 'giao_vien_id', 'giao_cu_id', 'ghi_chu', 'trang_thai'], 'required'],
      [['trang_thai', 'anh_nguoi_dung'], 'string'],
      [['hoten'], 'string', 'max' => 100],
      [['dien_thoai'], 'string', 'max' => 20],
      [['code'], 'string'],
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
      'ngay_nhan' => 'Ngay Nhan',
      'ngay_tra' => 'Ngay Tra',
      'giao_cu_id' => 'Giao Cu ID',
      'ghi_chu' => 'Ghi Chu',
      'trang_thai' => 'Trang Thai',
      'hoten' => 'Hoten',
      'dien_thoai' => 'Dien Thoai',
      'anh_nguoi_dung' => 'Anh Nguoi Dung',
      'code' => 'Code',
    ];
  }

  public function getImage()
  {
    return CauHinh::getServer() . '/upload-file/' . ($this->anh_nguoi_dung == null ? "user-nomal.jpg" : $this->anh_nguoi_dung);
  }

  public function getCodeGiaoCu()
  {
    $data = [];
    if (!is_null($this->chi_tiet_giao_cu)) {
      $giaoCus = json_decode($this->chi_tiet_giao_cu);
      if (count($giaoCus) > 0) {
        foreach ($giaoCus as $item) {
          $giaoCu = GiaoCu::findOne($item->id);
          $data[] = $giaoCu->code;
        }
      }
    }
    return join(', ', $data);
  }
  public function getGiaoCu()
  {
    $data = [];
    if (!is_null($this->chi_tiet_giao_cu)) {
      $giaoCus = json_decode($this->chi_tiet_giao_cu);
      if (count($giaoCus)>0){
        foreach ($giaoCus as $item){
          $giaoCu = GiaoCu::findOne($item->id);
          $data[]= [
            'id' => $giaoCu->id,
            'code' => $giaoCu->code,
            'image' => CauHinh::getImage($giaoCu->image),
            'so_luong'=>$item->so_luong
          ];
        }
      }
    }
    return $data;
  }
}
