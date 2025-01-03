<?php

namespace backend\models;

use common\models\User;
use Yii;

/**
 * This is the model class for table "trong_tre_bai_kiem_tra".
 *
 * @property int $id
 * @property int|null $active
 * @property string|null $created
 * @property string|null $updated
 * @property int $user_id
 * @property int $bai_hoc_id
 * @property string|null $link
 *
 * @property BaiHoc $baiHoc
 * @property User $user
 */
class BaiKiemTra extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trong_tre_bai_kiem_tra';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['active', 'user_id', 'bai_hoc_id'], 'integer'],
            [['created', 'updated'], 'safe'],
            [['user_id', 'bai_hoc_id'], 'required'],
            [['link'], 'string'],
            [['bai_hoc_id'], 'exist', 'skipOnError' => true, 'targetClass' => BaiHoc::className(), 'targetAttribute' => ['bai_hoc_id' => 'id']],
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
            'bai_hoc_id' => 'Bai Hoc ID',
            'link' => 'Link',
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
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
