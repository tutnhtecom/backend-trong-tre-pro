<?php

namespace backend\models;

use common\models\myActiveRecord;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%vai_tro}}".
 *
 * @property integer $id
 * @property string $name
 *
 * @property PhanQuyen[] $phanQuyens
 * @property Vaitrouser[] $vaitrousers
 */
class VaiTro extends ActiveRecord
{
    const LEADER_KD = 12;
    const NHAN_VIEN = 'Nhân viên';
    const QUAN_LY_CHI_NHANH = 'Quản lý chi nhánh';
    const GIAM_DOC = 'Giám đốc';
    const QUAN_LY = 'Quản lý';
    const KHACH_HANG = 'Khách hàng';
    const TRUONG_PHONG = 'Trưởng phòng';
    const CONG_TAC_VIEN = 'Cộng tác viên';
    const QUAN_LY_CONG_TAC_VIEN = 'Quản lý cộng tác viên';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trong_tre_vai_tro';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhanQuyens()
    {
        return $this->hasMany(PhanQuyen::className(), ['vai_tro_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVaitrousers()
    {
        return $this->hasMany(Vaitrouser::className(), ['vaitro_id' => 'id']);
    }
}
