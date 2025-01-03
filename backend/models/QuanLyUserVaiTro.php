<?php

namespace backend\models;

use common\models\User;
use Yii;

/**
 * This is the model class for table "{{%quan_ly_user_vai_tro}}".
 *
 * @property int $id
 * @property string $username
 * @property string|null $anh_nguoi_dung
 * @property string|null $password_hash
 * @property string|null $email
 * @property string|null $auth_key
 * @property int|null $status
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $password
 * @property string|null $hoten
 * @property string|null $dien_thoai
 * @property string|null $dia_chi
 * @property int|null $active
 * @property string|null $mobile_token
 * @property int|null $ma_kich_hoat
 * @property string|null $ngay_sinh
 * @property int|null $dien_thoai_du_phong
 * @property string|null $trinh_do
 * @property string|null $dich_vu
 * @property int|null $nhom
 * @property string|null $danh_gia
 * @property string|null $gioi_tinh
 * @property float|null $vi_dien_tu
 * @property string|null $cmnd_cccd
 * @property string|null $bang_cap
 * @property string|null $ghi_chu
 * @property int|null $is_admin
 * @property string|null $vai_tro_name
 * @property string|null $quyen_han
 * @property string|null $nhom_name
 * @property string|null $trinh_do_name
 * @property string|null $trang_thai_vao_ca
 * @property string|null $id_facebook
 * @property string|null $id_google
 * @property string|null $token_google
 * @property string|null $token_facebook
 * @property int|null $vai_tro
 * @property int|null $is_finish
 * @property int|null $leader_id
 * @property int|null $khoa_tai_khoan
 */
class QuanLyUserVaiTro extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%quan_ly_user_vai_tro}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'active', 'ma_kich_hoat', 'dien_thoai_du_phong', 'nhom', 'is_admin', 'vai_tro'], 'integer'],
            [['username'], 'required'],
            [['anh_nguoi_dung', 'mobile_token', 'gioi_tinh', 'bang_cap', 'ghi_chu'], 'string'],
            [['created_at', 'updated_at', 'ngay_sinh'], 'safe'],
            [['vi_dien_tu'], 'number'],
            [['username', 'password_hash', 'email', 'password', 'hoten', 'vai_tro_name'], 'string', 'max' => 100],
            [['auth_key'], 'string', 'max' => 32],
            [['dien_thoai', 'danh_gia', 'cmnd_cccd'], 'string', 'max' => 20],
            [['dia_chi'], 'string', 'max' => 200],
            [['trinh_do', 'dich_vu'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'anh_nguoi_dung' => 'Anh Nguoi Dung',
            'password_hash' => 'Password Hash',
            'email' => 'Email',
            'auth_key' => 'Auth Key',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'password' => 'Password',
            'hoten' => 'Hoten',
            'dien_thoai' => 'Dien Thoai',
            'dia_chi' => 'Dia Chi',
            'active' => 'Active',
            'mobile_token' => 'Mobile Token',
            'ma_kich_hoat' => 'Ma Kich Hoat',
            'ngay_sinh' => 'Ngay Sinh',
            'dien_thoai_du_phong' => 'Dien Thoai Du Phong',
            'trinh_do' => 'Trinh Do',
            'dich_vu' => 'Dich Vu',
            'nhom' => 'Nhom',
            'danh_gia' => 'Danh Gia',
            'gioi_tinh' => 'Gioi Tinh',
            'vi_dien_tu' => 'Vi Dien Tu',
            'cmnd_cccd' => 'Cmnd Cccd',
            'bang_cap' => 'Bang Cap',
            'ghi_chu' => 'Ghi Chu',
            'is_admin' => 'Is Admin',
            'vai_tro_name' => 'Vai Tro Name',
            'vai_tro' => 'Vai Tro',
        ];
    }
    public function getImage (){
        return CauHinh::getServer() . '/upload-file/' . ($this->anh_nguoi_dung == null ? "user-nomal.jpg" : $this->anh_nguoi_dung);
    }

    public function getTrinhDo()
    {
        $trinhDoName = DanhMuc::findOne($this->trinh_do);
        return is_null($trinhDoName) ? "" : $trinhDoName->name;
    }
    public function getBangCap(){
        $str =json_decode( $this->bang_cap);
        if (isset($str->trinh_do)){
            return $str->trinh_do." • ".$str->chuyen_nganh." • ".$str->truong_dao_tao;
        }
        return  $this->bang_cap;
    }
    public function getLeader(){
        $user =User::findOne($this->leader_id);
        if (is_null($user)){
            return "Không có";
        }
        return $user->hoten;
    }
}
