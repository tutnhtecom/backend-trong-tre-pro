<?php

namespace backend\models;

use common\models\User;
use Yii;

/**
 * This is the model class for table "trong_tre_lich_su_ket_qua_dao_tao".
 *
 * @property int $id
 * @property int|null $active
 * @property string|null $created
 * @property string|null $updated
 * @property int $user_id
 * @property int $ket_qua_id
 * @property int $giao_vien_id
 * @property int|null $link
 * @property string|null $trang_thai
 * @property string|null $ghi_chu
 *
 * @property User $giaoVien
 * @property User $user
 */
class LichSuKetQuaDaoTao extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trong_tre_lich_su_ket_qua_dao_tao';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'user_id', 'ket_qua_id', 'giao_vien_id'], 'required'],
            [['id', 'active', 'user_id', 'ket_qua_id', 'giao_vien_id'], 'integer'],
            [['created', 'updated'], 'safe'],
            [['trang_thai', 'ghi_chu'], 'string'],
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
            'ket_qua_id' => 'Ket Qua ID',
            'giao_vien_id' => 'Giao Vien ID',
            'link' => 'Link',
            'trang_thai' => 'Trang Thai',
            'ghi_chu' => 'Ghi Chu',
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
}
