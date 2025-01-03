<?php

namespace backend\models;

use common\models\User;
use Yii;
use yii\helpers\Html;
use yii\web\HttpException;

/**
 * This is the model class for table "trong_tre_ban_giao".
 *
 * @property int $id
 * @property int $active
 * @property string|null $created
 * @property string|null $updated
 * @property int $user_id
 * @property int $giao_vien_id
 * @property int $don_dich_vu_id
 * @property int $so_luong
 * @property string|null $ngay_nhan
 * @property string|null $ngay_tra
 * @property int $giao_cu_id
 * @property string $chi_tiet_giao_cu
 * @property string $ghi_chu
 * @property string $trang_thai
 *
 * @property DonDichVu $donDichVu
 * @property User $giaoVien
 * @property User $user
 */
class BanGiao extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trong_tre_ban_giao';
    }

    const CHUA_XU_LY = 'Chưa xử lý';
    const XAC_NHAN_BAN_GIAO = 'Xác nhận bàn giao';
    const XAC_NHAN_HOAN_TRA = 'Xác nhận hoàn trả';

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['active', 'user_id', 'giao_vien_id'], 'integer'],
            [['created', 'updated', 'ngay_nhan', 'ngay_tra'], 'safe'],
            [['user_id', 'giao_vien_id', 'trang_thai'], 'required'],
            [['trang_thai'], 'string'],
            [['giao_vien_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['giao_vien_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['don_dich_vu_id'], 'exist', 'skipOnError' => true, 'targetClass' => DonDichVu::className(), 'targetAttribute' => ['don_dich_vu_id' => 'id']],
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
    public function getDonDichVu()
    {
        return DonDichVu::find()->where(['id' => $this->don_dich_vu_id])->one();;
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

    public function afterSave($insert, $changedAttributes)
    {
        if (!is_null($this->chi_tiet_giao_cu)) {
          $giaoCus = json_decode($this->chi_tiet_giao_cu);
          if (count($giaoCus)>0){
            foreach ($giaoCus as $item){
              $giaoCu = GiaoCu::findOne($item->id);
              if ($this->trang_thai == self::XAC_NHAN_BAN_GIAO) {
                $giaoCu->so_luong_ton -= $item->so_luong;
              } else if ($this->trang_thai == self::XAC_NHAN_HOAN_TRA) {
                $giaoCu->so_luong_ton += $item->so_luong;
              }
              if (!$giaoCu->save()) {
                throw new HttpException(500, Html::errorSummary($giaoCu));
              }
            }
          }

        }
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
    }

  public function getCodeGiaoCu()
  {
    $data = [];
    if (!is_null($this->chi_tiet_giao_cu)) {
      $giaoCus = json_decode($this->chi_tiet_giao_cu);
      if (count($giaoCus)>0){
        foreach ($giaoCus as $item){
          $giaoCu = GiaoCu::findOne($item->id);
          $data[]= $giaoCu->code;
        }
      }
    }
    return join(', ',$data);
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
