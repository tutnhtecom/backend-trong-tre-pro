<?php

namespace backend\models;

use common\models\User;
use Yii;

/**
 * This is the model class for table "trong_tre_thong_bao_user".
 *
 * @property int $id
 * @property int $thong_bao_id
 * @property int $user_id
 *
 * @property ThongBao $thongBao
 * @property User $user
 */
class ThongBaoUser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trong_tre_thong_bao_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['thong_bao_id', 'user_id'], 'required'],
            [['thong_bao_id', 'user_id'], 'integer'],
            [['thong_bao_id'], 'exist', 'skipOnError' => true, 'targetClass' => ThongBao::className(), 'targetAttribute' => ['thong_bao_id' => 'id']],
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
            'thong_bao_id' => 'Thong Bao ID',
            'user_id' => 'User ID',
        ];
    }

    /**
     * Gets query for [[ThongBao]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getThongBao()
    {
        return $this->hasOne(ThongBao::className(), ['id' => 'thong_bao_id']);
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
