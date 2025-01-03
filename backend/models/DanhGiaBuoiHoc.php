<?php

namespace backend\models;

use common\models\User;
use Yii;

/**
 * This is the model class for table "trong_tre_danh_gia_buoi_hoc".
 *
 * @property int $id
 * @property int|null $active
 * @property string|null $created
 * @property string|null $updated
 * @property int $user_id
 * @property int $dich_vu_id
 * @property string|null $tieu_de
 * @property string|null $muc_do
 * @property string|null $nhan_xet
 * @property string|null $goi_y
 * @property string|null $cac_buoi
 * @property int $danh_muc_id
 * @property int $parent_id
 * @property int $type
 *
 * @property DanhMuc $danhMuc
 * @property DichVu $dichVu
 * @property User $user
 */
class DanhGiaBuoiHoc extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trong_tre_danh_gia_buoi_hoc';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['active', 'user_id', 'dich_vu_id', 'danh_muc_id'], 'integer'],
            [['created', 'updated'], 'safe'],
            [['user_id', 'dich_vu_id', 'danh_muc_id'], 'required'],
            [['muc_do', 'nhan_xet', 'goi_y'], 'string'],
            [['tieu_de'], 'string'],
            [['danh_muc_id'], 'exist', 'skipOnError' => true, 'targetClass' => DanhMuc::className(), 'targetAttribute' => ['danh_muc_id' => 'id']],
            [['dich_vu_id'], 'exist', 'skipOnError' => true, 'targetClass' => DichVu::className(), 'targetAttribute' => ['dich_vu_id' => 'id']],
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
            'dich_vu_id' => 'Dich Vu ID',
            'tieu_de' => 'Tieu De',
            'muc_do' => 'Muc Do',
            'nhan_xet' => 'Nhan Xet',
            'goi_y' => 'Goi Y',
            'danh_muc_id' => 'Danh Muc ID',
        ];
    }

    /**
     * Gets query for [[DanhMuc]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDanhMuc()
    {
        return $this->hasOne(DanhMuc::className(), ['id' => 'danh_muc_id']);
    }

    /**
     * Gets query for [[DichVu]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDichVu()
    {
        return $this->hasOne(DichVu::className(), ['id' => 'dich_vu_id']);
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
    public function getNhanXet(){
        return $this->nhan_xet==1?true:false;
    }
}
