<?php

namespace backend\models;

use common\models\User;
use Yii;

/**
 * This is the model class for table "trong_tre_khieu_nai_bang_luong".
 *
 * @property int $id
 * @property int $active
 * @property string $created
 * @property string $updated
 * @property int $user_id
 * @property int $giao_vien_id
 * @property int $phieu_luong_id
 * @property string $noi_dung
 * @property string $trang_thai
 * @property int $noi_dung_phan_hoi
 *
 * @property User $giaoVien
 * @property PhieuLuong $phieuLuong
 * @property User $user
 */
class KhieuNaiBangLuong extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trong_tre_khieu_nai_bang_luong';
    }
    const CHUA_XU_LY = "Chưa xử lý";
    const DA_XU_LY = "Đã xử lý";
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'active', 'created', 'updated', 'user_id', 'giao_vien_id', 'phieu_luong_id'], 'integer'],
            [['giao_vien_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['giao_vien_id' => 'id']],
            [['phieu_luong_id'], 'exist', 'skipOnError' => true, 'targetClass' => PhieuLuong::className(), 'targetAttribute' => ['phieu_luong_id' => 'id']],
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
            'phieu_luong_id' => 'Phieu Luong ID',
            'noi_dung' => 'Noi Dung',
            'trang_thai' => 'Trang Thai',
            'noi_dung_phan_hoi' => 'Noi Dung Phan Hoi',
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
     * Gets query for [[PhieuLuong]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPhieuLuong()
    {
        return $this->hasOne(PhieuLuong::className(), ['id' => 'phieu_luong_id']);
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
}
