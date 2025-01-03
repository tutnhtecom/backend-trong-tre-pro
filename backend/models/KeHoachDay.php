<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "trong_tre_ke_hoach_day".
 *
 * @property int $id
 * @property int|null $active
 * @property string|null $created
 * @property string|null $updated
 * @property int $user_id
 * @property int|null $buoi
 * @property string|null $noi_dung
 * @property int $goi_hoc_id
 */
class KeHoachDay extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trong_tre_ke_hoach_day';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['active', 'user_id', 'buoi', 'goi_hoc_id'], 'integer'],
            [['created', 'updated'], 'safe'],
            [['user_id', 'goi_hoc_id'], 'required'],
            [['noi_dung'], 'string'],
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
            'buoi' => 'Buoi',
            'noi_dung' => 'Noi Dung',
            'goi_hoc_id' => 'Goi Hoc ID',
        ];
    }
}
