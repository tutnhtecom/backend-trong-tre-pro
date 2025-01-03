<?php

namespace backend\models;

use common\models\User;
use Yii;

/**
 * This is the model class for table "trong_tre_lich_su_trang_thai_nhan_lich".
 *
 * @property int $id
 * @property int|null $active
 * @property string|null $created
 * @property string|null $updated
 * @property int|null $user_id
 * @property int|null $nhan_lich_id
 * @property string|null $trang_thai
 * @property int|null $ghi_chu
 *
 * @property NhanLich $nhanLich
 * @property User $user
 */
class LichSuTrangThaiNhanLich extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trong_tre_lich_su_trang_thai_nhan_lich';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['active', 'user_id', 'nhan_lich_id', 'ghi_chu'], 'integer'],
            [['created', 'updated'], 'safe'],
            [['nhan_lich_id'], 'exist', 'skipOnError' => true, 'targetClass' => NhanLich::className(), 'targetAttribute' => ['nhan_lich_id' => 'id']],
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
            'nhan_lich_id' => 'Nhan Lich ID',
            'trang_thai' => 'Trang Thai',
            'ghi_chu' => 'Ghi Chu',
        ];
    }

    /**
     * Gets query for [[NhanLich]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNhanLich()
    {
        return $this->hasOne(NhanLich::className(), ['id' => 'nhan_lich_id']);
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
