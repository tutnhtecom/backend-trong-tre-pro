<?php

namespace backend\models;

use common\models\User;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\web\HttpException;

/**
 * This is the model class for table "trong_tre_thong_bao".
 *
 * @property int $id
 * @property int|null $active
 * @property string|null $created
 * @property string|null $updated
 * @property int|null $user_id
 * @property int|null $type_id
 * @property string|null $noi_dung
 * @property string|null $image
 * @property int|null $to_id
 * @property string|null $tieu_de
 * @property string|null $giao_vien_id
 * @property string|null $phu_huynh_id
 * @property string|null $dich_vu_id
 * @property string|null $lao_dong_id
 *
 * @property DanhMuc $type
 * @property User $user
 * @property DanhMuc $to
 */
class ThongBao extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trong_tre_thong_bao';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['active', 'user_id', 'type_id', 'to_id'], 'integer'],
            [['created', 'updated'], 'safe'],
            [['noi_dung', 'image'], 'string'],
            [['giao_vien_id', 'phu_huynh_id', 'dich_vu_id', 'lao_dong_id'], 'string'],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => DanhMuc::className(), 'targetAttribute' => ['type_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['to_id'], 'exist', 'skipOnError' => true, 'targetClass' => DanhMuc::className(), 'targetAttribute' => ['to_id' => 'id']],
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
            'type_id' => 'Type ID',
            'noi_dung' => 'Noi Dung',
            'image' => 'Image',
            'to_id' => 'To ID',
            'giao_vien_id' => 'Giao Vien ID',
            'phu_huynh_id' => 'Phu Huynh ID',
            'dich_vu_id' => 'Dich Vu ID',
            'lao_dong_id' => 'Lao Dong ID',
        ];
    }

    /**
     * Gets query for [[Type]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(DanhMuc::className(), ['id' => 'type_id']);
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
     * Gets query for [[To]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTo()
    {
        return $this->hasOne(DanhMuc::className(), ['id' => 'to_id']);
    }
    public function beforeSave($insert)
    {
        if ($insert){
            $this->created =date('Y-m-d H:i:s');
            if ($this->tieu_de==null){
                $this->tieu_de =$this->type->name;
            }
        }
        $this->updated =date('Y-m-d H:i:s');

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }
    public function getDate(){
        $date = date('d/m/Y',strtotime($this->created));
        if ($date == date('d/m/Y')){
            return "Hôm nay";
        };
        return  $date;
    }
    public function getAfterTime(){
        $datestr = $this->created;
        $date = strtotime($datestr);

        $diff = time() - $date;
        $days = floor($diff / (60 * 60 * 24));
        $hours = round(($diff - $days * 60 * 60 * 24) / (60 * 60));
        $phut = round(($diff - $days * 60 * 60 * 24) / 60);
        $time = "";
        if ($days > 0) {
            $time = date("H:i", $date);
        } elseif ($hours > 0) {
            $time = $hours . " giờ trước";
        } else {
            $time = $phut . " phút trước";
        }
        return $time . " • " . date("d/m/Y", $date);
    }

    public function afterSave($insert, $changedAttributes)
    {
        $type = $this->to_id;
        $userStr  = "";
        switch (intval($type)) {
            case 60:
            {
                $userStr = $this->giao_vien_id;
                break;
            }
            case 61:
            {
                $userStr = $this->phu_huynh_id;
                break;
            }
            case 62:
            {
                $userStr = $this->dich_vu_id;
                break;
            }
            case 63:
            {
                $userStr = $this->lao_dong_id;
                break;
            }
        }
        if (!is_null($userStr) && $userStr != "0" && $userStr != "") {
            $users = explode(',', $userStr);
            if (count($users) > 0) {
                foreach ($users as $item){
                    $thongBaoUser = new ThongBaoUser();
                    $thongBaoUser->user_id = intval($item);
                    $thongBaoUser->thong_bao_id = $this->id;
                    if (!$thongBaoUser->save()){
                        throw new HttpException(500,Html::errorSummary($thongBaoUser));
                    };
                }
            }
        }
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
    }
}
