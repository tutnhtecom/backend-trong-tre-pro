<?php

namespace backend\models;

use common\models\User;
use Yii;

/**
 * This is the model class for table "trong_tre_lich_su_trang_thai_don".
 *
 * @property int $id
 * @property int|null $don_hang_id
 * @property string|null $trang_thai
 * @property string|null $created
 * @property int|null $giao_vien_id
 * @property int|null $leader_kd_id
 * @property int|null $so_buoi_hoan
 * @property float|null $tong_tien
 * @property string|null $li_do_huy
 * @property int|null $user_id
 * @property int|null $so_tien_hoan
 * @property int|null $active
 * @property string|null $updated
 *
 * @property DonDichVu $donHang
 * @property User $giaoVien
 * @property User $leaderKd
 * @property User $user
 */
class LichSuTrangThaiDon extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trong_tre_lich_su_trang_thai_don';
    }
    const CHUA_CO_GIAO_VIEN = 'Chưa có GV';
    const DANG_KHAO_SAT = 'Đang khảo sát';
    const DANG_DAY = 'Đang dạy';
    const DA_HUY = 'Đã hủy';
    const HOAN_THANH = 'Đã hoàn thành';
    const DON_HOAN = 'Đơn hoàn';

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['don_hang_id', 'giao_vien_id', 'leader_kd_id', 'so_buoi_hoan', 'user_id', 'active'], 'integer'],
            [['trang_thai', 'li_do_huy'], 'string'],
            [['created', 'updated'], 'safe'],
            [['tong_tien'], 'number'],
            [['don_hang_id'], 'exist', 'skipOnError' => true, 'targetClass' => DonDichVu::className(), 'targetAttribute' => ['don_hang_id' => 'id']],
            [['giao_vien_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['giao_vien_id' => 'id']],
            [['leader_kd_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['leader_kd_id' => 'id']],
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
            'don_hang_id' => 'Don Hang ID',
            'trang_thai' => 'Trang Thai',
            'created' => 'Created',
            'giao_vien_id' => 'Giao Vien ID',
            'leader_kd_id' => 'Leader Kd ID',
            'so_buoi_hoan' => 'So Buoi Hoan',
            'tong_tien' => 'Tong Tien',
            'li_do_huy' => 'Li Do Huy',
            'user_id' => 'User ID',
            'active' => 'Active',
            'updated' => 'Updated',
        ];
    }

    /**
     * Gets query for [[DonHang]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDonHang()
    {
        return $this->hasOne(DonDichVu::className(), ['id' => 'don_hang_id']);
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
     * Gets query for [[LeaderKd]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeaderKd()
    {
        return $this->hasOne(User::className(), ['id' => 'leader_kd_id']);
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
