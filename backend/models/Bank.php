<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "trong_tre_bank".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $code
 * @property string|null $bin
 * @property string|null $shortName
 * @property string|null $logo
 * @property int|null $transferSupported
 * @property int|null $lookupSupported
 * @property string|null $short_name
 * @property int|null $support
 * @property int|null $isTransfer
 * @property string|null $swift_code
 */
class Bank extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trong_tre_bank';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['logo'], 'string'],
            [['transferSupported', 'lookupSupported', 'support', 'isTransfer'], 'integer'],
            [['name'], 'string', 'max' => 500],
            [['code', 'bin', 'shortName', 'short_name', 'swift_code'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'code' => 'Code',
            'bin' => 'Bin',
            'shortName' => 'Short Name',
            'logo' => 'Logo',
            'transferSupported' => 'Transfer Supported',
            'lookupSupported' => 'Lookup Supported',
            'short_name' => 'Short Name',
            'support' => 'Support',
            'isTransfer' => 'Is Transfer',
            'swift_code' => 'Swift Code',
        ];
    }
}
