<?php

namespace backend\models;

use common\models\User;
use Yii;

/**
 * This is the model class for table "trong_tre_thuong_phat".
 *
 * @property int $id
 * @property int $active
 * @property string $created
 * @property string $updated
 * @property int $user_id
 * @property string|null $tieu_de
 * @property int|null $tong_tien
 * @property int $type_id
 * @property int $giao_vien_id
 *
 * @property User $giaoVien
 * @property DanhMuc $type
 * @property User $user
 */
class ThuongPhat extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trong_tre_thuong_phat';
    }
    const DI_LAI = 88;
    const TIEN_THUONG = 89;
    const THU_NHAP_KHAC = 90;
    const BAO_HIEM_XA_HOI = 91;
    const TRUY_THU_LUONG = 92;
    const TIEN_CAM_KET = 93;
    const GIAM_TRU_KHAC = 94;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'type_id', 'giao_vien_id'], 'required'],
            [['id', 'active', 'user_id', 'tong_tien', 'type_id', 'giao_vien_id'], 'integer'],
            [['created', 'updated'], 'safe'],
            [['tieu_de'], 'string'],
            [['giao_vien_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['giao_vien_id' => 'id']],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => DanhMuc::className(), 'targetAttribute' => ['type_id' => 'id']],
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
            'tieu_de' => 'Tieu De',
            'tong_tien' => 'Tong Tien',
            'type_id' => 'Type ID',
            'giao_vien_id' => 'Giao Vien ID',
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
     * Gets query for [[Type]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(DanhMuc::className(), ['id' => 'type_id']);
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
