<?php

namespace backend\models;

use common\models\User;
use Yii;

/**
 * This is the model class for table "trong_tre_cau_hoi".
 *
 * @property int $id
 * @property string|null $tieu_de
 * @property string|null $gioi_thieu
 * @property string|null $link
 * @property int|null $bai_hoc_id
 * @property string|null $created
 * @property string|null $updated
 * @property int $user_id
 * @property int|null $active
 *
 * @property BaiHoc $baiHoc
 * @property User $user
 */
class CauHoi extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trong_tre_cau_hoi';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['link'], 'string'],
            [['bai_hoc_id', 'user_id', 'active'], 'integer'],
            [['created', 'updated'], 'safe'],
            [['user_id'], 'required'],
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
            'tieu_de' => 'Tieu De',
            'gioi_thieu' => 'Gioi Thieu',
            'link' => 'Link',
            'bai_hoc_id' => 'Bai Hoc ID',
            'created' => 'Created',
            'updated' => 'Updated',
            'user_id' => 'User ID',
            'active' => 'Active',
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
