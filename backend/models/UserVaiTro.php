<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%user_vai_tro}}".
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $password
 * @property string $hoten
 * @property string $dien_thoai
 * @property string $dia_chi
 * @property string $cmnd
 * @property string $anhdaidien
 * @property integer $VIP
 * @property double $vi_dien_tu
 * @property integer $hoat_dong
 * @property integer $customer
 * @property integer $branch_id
 * @property string $full_name
 * @property string $birth_day
 * @property double $credits
 * @property string $qr_codes
 * @property string $ma_chuc_danh
 * @property string $loai_hop_dong
 * @property integer $vai_tro_id
 * @property string $vai_tro
 */
class UserVaiTro extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_vai_tro}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'VIP', 'hoat_dong', 'customer', 'branch_id', 'vai_tro_id'], 'integer'],
            [['created_at', 'updated_at', 'birth_day'], 'safe'],
            [['vi_dien_tu', 'credits'], 'number'],
            [['loai_hop_dong'], 'string'],
            [['username', 'password_hash', 'email', 'password', 'hoten', 'anhdaidien', 'full_name', 'qr_codes', 'vai_tro'], 'string', 'max' => 100],
            [['password_reset_token'], 'string', 'max' => 45],
            [['auth_key'], 'string', 'max' => 32],
            [['dien_thoai', 'cmnd'], 'string', 'max' => 20],
            [['dia_chi'], 'string', 'max' => 200],
            [['ma_chuc_danh'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'auth_key' => 'Auth Key',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'password' => 'Password',
            'hoten' => 'Hoten',
            'dien_thoai' => 'Dien Thoai',
            'dia_chi' => 'Dia Chi',
            'cmnd' => 'Cmnd',
            'anhdaidien' => 'Anhdaidien',
            'VIP' => 'Vip',
            'vi_dien_tu' => 'Vi Dien Tu',
            'hoat_dong' => 'Hoat Dong',
            'customer' => 'Customer',
            'branch_id' => 'Branch ID',
            'full_name' => 'Full Name',
            'birth_day' => 'Birth Day',
            'credits' => 'Credits',
            'qr_codes' => 'Qr Codes',
            'ma_chuc_danh' => 'Ma Chuc Danh',
            'loai_hop_dong' => 'Loai Hop Dong',
            'vai_tro_id' => 'Vai Tro ID',
            'vai_tro' => 'Vai Tro',
        ];
    }
}
