<?php

namespace backend\models;

use common\models\myActiveRecord;
use Yii;

/**
 * This is the model class for table "_phan_quyen".
 *
 * @property integer $id
 * @property integer $chuc_nang_id
 * @property integer $vai_tro_id
 *
 * @property ChucNang $chucNang
 * @property VaiTro $vaiTro
 */
class PhanQuyen extends myActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trong_tre_phan_quyen';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['chuc_nang_id', 'vai_tro_id'], 'required'],
            [['chuc_nang_id', 'vai_tro_id'], 'integer'],
            [['chuc_nang_id'], 'exist', 'skipOnError' => true, 'targetClass' => ChucNang::className(), 'targetAttribute' => ['chuc_nang_id' => 'id']],
            [['vai_tro_id'], 'exist', 'skipOnError' => true, 'targetClass' => VaiTro::className(), 'targetAttribute' => ['vai_tro_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'chuc_nang_id' => 'Chuc Nang ID',
            'vai_tro_id' => 'Vai Tro ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChucNang()
    {
        return $this->hasOne(ChucNang::className(), ['id' => 'chuc_nang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVaiTro()
    {
        return $this->hasOne(VaiTro::className(), ['id' => 'vai_tro_id']);
    }
}
