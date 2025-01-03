<?php

namespace backend\models;

use common\models\User;
use Yii;

/**
 * This is the model class for table "trong_tre_gan_giao_vien_dao_tao".
 *
 * @property int $id
 * @property int $giao_vien_id
 * @property int $hoc_phan_id
 *
 * @property User $giaoVien
 * @property HocPhan $hocPhan
 */
class GanGiaoVienDaoTao extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trong_tre_gan_giao_vien_dao_tao';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['giao_vien_id', 'hoc_phan_id'], 'required'],
            [['giao_vien_id', 'hoc_phan_id'], 'integer'],
            [['giao_vien_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['giao_vien_id' => 'id']],
            [['hoc_phan_id'], 'exist', 'skipOnError' => true, 'targetClass' => HocPhan::className(), 'targetAttribute' => ['hoc_phan_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'giao_vien_id' => 'Giao Vien ID',
            'hoc_phan_id' => 'Hoc Phan ID',
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
     * Gets query for [[HocPhan]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHocPhan()
    {
        return $this->hasOne(HocPhan::className(), ['id' => 'hoc_phan_id']);
    }
}
