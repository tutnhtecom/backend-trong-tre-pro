<?php

namespace backend\models;

use common\models\User;
use Yii;

/**
 * This is the model class for table "trong_tre_lich_su_da_xem".
 *
 * @property int $id
 * @property int|null $active
 * @property string|null $created
 * @property string|null $updated
 * @property int|null $user_id
 * @property int|null $giao_vien_id
 * @property int|null $don_dich_vu_id
 * @property string|null $ghi_chu
 *
 * @property DonDichVu $donDichVu
 * @property User $giaoVien
 * @property User $user
 */
class LichSuDaXem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trong_tre_lich_su_da_xem';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['active', 'user_id', 'giao_vien_id', 'don_dich_vu_id'], 'integer'],
            [['created', 'updated'], 'safe'],
            [['ghi_chu'], 'string'],
            [['don_dich_vu_id'], 'exist', 'skipOnError' => true, 'targetClass' => DonDichVu::className(), 'targetAttribute' => ['don_dich_vu_id' => 'id']],
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
            'don_dich_vu_id' => 'Don Dich Vu ID',
            'ghi_chu' => 'Ghi Chu',
        ];
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
}
