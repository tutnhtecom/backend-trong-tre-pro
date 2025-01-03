<?php

namespace backend\models;

use common\models\User;
use Yii;

/**
 * This is the model class for table "trong_tre_lich_su_trang_thai_phieu_luong".
 *
 * @property int $id
 * @property int|null $active
 * @property string|null $created
 * @property string|null $updated
 * @property int $user_id
 * @property int $phieu_luong_id
 * @property int $tong_tien
 * @property int $ghi_chu
 * @property string $trang_thai
 *
 * @property User $user
 */
class LichSuTrangThaiPhieuLuong extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trong_tre_lich_su_trang_thai_phieu_luong';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['active', 'user_id', 'phieu_luong_id'], 'integer'],
            [['created', 'updated'], 'safe'],
            [['user_id', 'phieu_luong_id', 'trang_thai'], 'required'],
            [['trang_thai'], 'string'],
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
            'phieu_luong_id' => 'Phieu Luong ID',
            'trang_thai' => 'Trang Thai',
        ];
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
