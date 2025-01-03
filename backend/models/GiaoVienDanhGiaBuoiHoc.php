<?php

namespace backend\models;

use common\models\User;
use Yii;

/**
 * This is the model class for table "trong_tre_giao_vien_danh_gia_buoi_hoc".
 *
 * @property int $id
 * @property int|null $active
 * @property string|null $created
 * @property string|null $updated
 * @property int $user_id
 * @property int $buoi_hoc_id
 * @property string|null $tieu_de
 * @property string|null $muc_do
 * @property string|null $muc_do_da_cho
 * @property int|null $nhan_xet
 * @property string|null $noi_dung_nhan_xet
 * @property string|null $goi_y
 * @property int $danh_muc_id
 * @property int $parent_id
 * @property int $type
 *
 * @property TienDoKhoaHoc $buoiHoc
 * @property DanhMuc $danhMuc
 * @property User $user
 */
class GiaoVienDanhGiaBuoiHoc extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trong_tre_giao_vien_danh_gia_buoi_hoc';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['active', 'user_id', 'buoi_hoc_id', 'danh_muc_id'], 'integer'],
            [['created', 'updated'], 'safe'],
            [['user_id', 'buoi_hoc_id', 'danh_muc_id'], 'required'],
            [['muc_do'], 'string'],
            [['tieu_de', 'muc_do_da_cho'], 'string'],
            [['buoi_hoc_id'], 'exist', 'skipOnError' => true, 'targetClass' => TienDoKhoaHoc::className(), 'targetAttribute' => ['buoi_hoc_id' => 'id']],
            [['danh_muc_id'], 'exist', 'skipOnError' => true, 'targetClass' => DanhMuc::className(), 'targetAttribute' => ['danh_muc_id' => 'id']],
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
            'buoi_hoc_id' => 'Buoi Hoc ID',
            'tieu_de' => 'Tieu De',
            'muc_do' => 'Muc Do',
            'muc_do_da_cho' => 'Muc Do Da Cho',
            'nhan_xet' => 'Nhan Xet',
            'danh_muc_id' => 'Danh Muc ID',
        ];
    }

    /**
     * Gets query for [[BuoiHoc]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBuoiHoc()
    {
        return $this->hasOne(TienDoKhoaHoc::className(), ['id' => 'buoi_hoc_id']);
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
    public function getCacBuoi(){
        $buoi =  GiaoVienDanhGiaBuoiHoc::find()->andFilterWhere(['parent_id'=>$this->id])->all();
        $data = [];
        /** @var GiaoVienDanhGiaBuoiHoc $item */
        foreach ($buoi as $item){
            $data[]= [
                'id' => $item->id,
                'tieu_de' => $item->tieu_de,
                'muc_do' => json_decode($item->muc_do),
                'muc_do_da_cho' => $item->muc_do_da_cho,
                'nhan_xet' => $item->getNhanXet(),
                'noi_dung_nhan_xet' => $item->noi_dung_nhan_xet,
                'goi_y' => json_decode($item->goi_y)
            ];
        }
        return $data;
    }
}
