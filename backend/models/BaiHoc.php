<?php

namespace backend\models;

use common\models\User;
use Yii;

/**
 * This is the model class for table "trong_tre_bai_hoc".
 *
 * @property int $id
 * @property int|null $active
 * @property string|null $created
 * @property string|null $updated
 * @property int|null $user_id
 * @property int|null $phan_tram
 * @property string|null $tieu_de
 * @property int|null $hoc_phan_id
 * @property int|null $thu_tu
 *
 * @property HocPhan $hocPhan
 * @property User $user
 * @property BaiKiemTra[] $baiKiemTras
 * @property CauHoi[] $cauHois
 */
class BaiHoc extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trong_tre_bai_hoc';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['active', 'user_id', 'hoc_phan_id'], 'integer'],
            [['created', 'updated'], 'safe'],
            [['tieu_de'], 'string'],
            [['hoc_phan_id'], 'exist', 'skipOnError' => true, 'targetClass' => HocPhan::className(), 'targetAttribute' => ['hoc_phan_id' => 'id']],
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
            'hoc_phan_id' => 'Hoc Phan ID',
        ];
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

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Gets query for [[BaiKiemTras]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBaiKiemTras()
    {
        return $this->hasMany(BaiKiemTra::className(), ['bai_hoc_id' => 'id']);
    }
    /**
     * Gets query for [[CauHois]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCauHois()
    {
        return $this->hasMany(CauHoi::className(), ['bai_hoc_id' => 'id']);
    }
    public function cauHoi(){
        return CauHoi::find()->andFilterWhere(['bai_hoc_id'=>$this->id,'active'=>1])->select(['tieu_de','id','gioi_thieu','link'])->all();
    }
    public function kiemTra(){
        return BaiKiemTra::find()->andFilterWhere(['bai_hoc_id'=>$this->id,'active'=>1])->select(['id','link'])->one();
    }
    public function getPhanTramByGiaoVien($id){
        $gvHocTap = GiaoVienHocTap::findOne(['bai_hoc_id' => $this->id, 'giao_vien_id' => $id, 'active' => 1]);
        if (is_null($gvHocTap)){
            return 0;
        }
        return $gvHocTap->phan_tram;
    }
}
