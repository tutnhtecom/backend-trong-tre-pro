<?php

namespace backend\models;

use common\models\User;
use Yii;

/**
 * This is the model class for table "trong_tre_buoi_hoc".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $so_buoi
 * @property float|null $tong_tien
 * @property int|null $chiet_khau
 * @property float|null $thanh_tien
 * @property string|null $created
 * @property string|null $updated
 * @property int|null $active
 *
 * @property TrongTreUser $user
 */
class BuoiHoc extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trong_tre_buoi_hoc';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'so_buoi', 'chiet_khau', 'active'], 'integer'],
            [['tong_tien', 'thanh_tien'], 'number'],
            [['created', 'updated'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrongTreUser::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'so_buoi' => 'So Buoi',
            'tong_tien' => 'Tong Tien',
            'chiet_khau' => 'Chiet Khau',
            'thanh_tien' => 'Thanh Tien',
            'created' => 'Created',
            'updated' => 'Updated',
            'active' => 'Active',
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
    public function beforeSave($insert)
    {
        if ($insert){
            $this->created =date('Y-m-d H:i:s');
        }
        $this->updated =date('Y-m-d H:i:s');

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
    }
}
