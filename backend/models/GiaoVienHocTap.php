<?php

namespace backend\models;

use common\models\User;
use Yii;

/**
 * This is the model class for table "trong_tre_giao_vien_hoc_tap".
 *
 * @property int $id
 * @property int $active
 * @property string|null $created
 * @property string|null $updated
 * @property int $user_id
 * @property int $giao_vien_id
 * @property int $phan_tram
 * @property int $bai_hoc_id
 *
 * @property BaiHoc $baiHoc
 * @property User $giaoVien
 * @property User $user
 */
class GiaoVienHocTap extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trong_tre_giao_vien_hoc_tap';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['active', 'user_id', 'giao_vien_id', 'bai_hoc_id'], 'integer'],
            [['created', 'updated'], 'safe'],
            [['user_id', 'giao_vien_id', 'bai_hoc_id'], 'required'],
            [['bai_hoc_id'], 'exist', 'skipOnError' => true, 'targetClass' => BaiHoc::className(), 'targetAttribute' => ['bai_hoc_id' => 'id']],
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
            'bai_hoc_id' => 'Bai Hoc ID',
        ];
    }

    /**
     * Gets query for [[BaiHoc]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBaiHoc()
    {
        return $this->hasOne(BaiHoc::className(), ['id' => 'bai_hoc_id']);
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
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
    }
    public function beforeSave($insert)
    {
        if ($insert){
            $this->created =date('Y-m-d H:i:s');
        }
        $this->updated =date('Y-m-d H:i:s');

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }
}
