<?php

namespace backend\models;

use common\models\myActiveRecord;
use common\models\User;
use Yii;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "{{%danh_muc}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $type
 * @property string $code
 * @property string $ghi_chu
 * @property int|null $parent_id
 * @property int|null $active
 *
 * @property DanhMuc $parent
 * @property DanhMuc[] $danhMucs
 * @property User[] $users
 */
//enum('Phòng ban', 'Loại công việc', 'Nhóm nhân viên', 'Kết quả thực hiện', 'Yêu cầu kết quả', 'Tần suất thực hiện', 'Chức vụ', 'Điểm số','Quy trình công việc liên quan')
class DanhMuc extends myActiveRecord
{

   const TRINH_DO_HOC_TAP = 'Trình độ';
   const GIO_LINH_HOAT = 'Giờ linh hoạt';
   const NAP_TIEN = 'Nạp tiền';
   const LOAI_DICH_VU = 'Loại dịch vụ';
   const TRU_TIEN = 'Rút tiền';
   const DICH_VU = 'Dịch vụ';
   const CHON_CA = 'Chọn ca';
   const CHON_KHUNG_GIO = 'Chọn khung giờ';
   const LOAI_TIN_TUC = 'Loại tin tức';
   const LOAI_GIAO_VIEN = 'Loại giáo viên';
   const AN_TRUA = 43;
   const THEM_GIO = 44;
   const PHU_PHI = 51;
   const TRINH_DO = 'Nhóm giáo viên';
   const Do_TUOI = 'Độ tuổi';
   const TRANG_THAI_DON = 'Trạng thái đơn';
   const GOI_HOC = 'Gói học';
   const THONG_BAO = 'Chủ đề thông báo';
   const TO_THONG_BAO = 'Thông báo';
   const CAP_DO = 'Cấp độ';
   const PHAN_LOAI_HOC_PHAN = 'Phân loại học phần';
   const TRANG_THAI_KET_QUA_DAO_TAO = 'Trạng thái kết quả đào tạo';
   const TRANG_THAI_BAN_GIAO = 'Trạng thái bàn giao';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trong_tre_danh_muc';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            [['name', 'type'], 'trim'],
            [['type'], 'string'],
            [['parent_id', 'active'], 'integer'],
            [['name', 'code'], 'string', 'max' => 200],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => DanhMuc::className(), 'targetAttribute' => ['parent_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Tên',
            'type' => 'Phân loại',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDanhMucs()
    {
        return $this->hasMany(DanhMuc::className(), ['parent_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['branch_id' => 'id']);
    }

}
