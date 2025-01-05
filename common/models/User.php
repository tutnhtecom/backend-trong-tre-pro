<?php

namespace common\models;
use backend\controllers\CoreApiController;
use backend\models\CauHinh;
use backend\models\ChucNang;
use backend\models\ChungChi;
use backend\models\DanhMuc;
use backend\models\DonDichVu;
use backend\models\LichSuTrangThaiDon;
use backend\models\PhanQuyen;
use backend\models\QuanLyUserVaiTro;
use backend\models\VaiTro;
use backend\models\Vaitrouser;
use Yii;
use yii\base\NotSupportedException;
use yii\bootstrap\Html;
use yii\db\ActiveRecord;
use yii\web\HttpException;
use yii\web\IdentityInterface;
use yii\web\Response;

/**
 * @property integer $id
 * @property string|null $username
 * @property string|null $password_hash
 * @property string|null $password_reset_token
 * @property string|null $email
 * @property string|null $auth_key
 * @property string|null $ma_kich_hoat
 * @property string $phone
 * @property int|null $status
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $password
 * @property string|null $nhom
 * @property string|null $hoten
 * @property string|null $dien_thoai
 * @property string|null $cmnd
 * @property string|null $dia_chi
 * @property int|null $active
 * @property int|null $is_admin
 * @property int|null $user_id
 * @property int|null $kich_hoat
 * @property int|null $trinh_do
 * @property int|null $vi_dien_tu
 * @property int|null $khoa_tai_khoan
 * @property int|null $is_finish
 * @property string|null $ngay_sinh
 * @property string|null $bang_cap
 * @property string|null $ho_ten_con
 * @property string|null $ngay_sinh_cua_con
 * @property string|null $anh_nguoi_dung
 * @property string|null $ghi_chu
 * @property string|null $danh_gia
 * @property string|null $trang_thai_vao_ca
 * @property string|null $chu_tai_khoan
 * @property string|null $so_tai_khoan
 * @property string|null $ten_ngan_hang
 * @property string|null $cmnd_cccd
 * @property string|null $dich_vu
 * @property string|null $id_facebook
 * @property string|null $token_facebook
 * @property string|null $id_google
 * @property string|null $token_google
 *
 */
class User extends ActiveRecord implements IdentityInterface
{

    const GIAO_VIEN=10;
    const PHU_HUYNH=11;
    const ADMIN='1';
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;
    const DANG_TRONG_KHOA_HOC = "Đang trong khóa học";
    const DANG_RANH = "Đang rảnh";

    public $vai_tros;

    public static function tableName()
    {
        return 'trong_tre_user';
    }

    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }

    public function attributeLabels()
    {
        return [

        ];
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        if(is_null($token)) {
            throw new HttpException(401, 'Tài khoản của bạn đang được đăng nhập trên thiết bị khác');
        } else {
            if (empty($token))
                $user = null;
            else {
                $user = User::findOne([
                    'auth_key' => $token,
                    'status' => 10,
                ]);
            }
        }
        if (is_null($user)) {
            throw new HttpException(401, 'Tài khoản của bạn đang được đăng nhập trên thiết bị khác');
        } else {
            if (!User::isAccess1($user->id)){
                throw new HttpException(403 ,"Bạn không có quyền truy cập chức năng này!");
            }
           return $user;
        }
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }
    public static function isAccess1($uid)
    {        
        if ($uid==1){
            return true;
        }
        if(!isset($_SERVER['REDIRECT_URL'])) $_SERVER['REDIRECT_URL'] = '/admin-api/chi-tiet-user?id='.$uid;
        $sever =explode('/',$_SERVER['REDIRECT_URL']);
        if (count($sever)!==3){
            return false;
        }
        $action = ucfirst($sever[2]);
        $data = [];
        foreach (explode("-",$sever[1]) as $item){
            $data[] = ucfirst($item);
        }
        $controller = join("",$data);
        $controller_action = "{$controller};{$action}";
        $chucNang = ChucNang::findOne(['controller_action'=>$controller_action]);
        if(is_null($chucNang)){
            return true;
        }
        $user = QuanLyUserVaiTro::findOne(['id'=>$uid]);
        $phanQuyen = PhanQuyen::findOne(['vai_tro_id'=>$user->vai_tro,'chuc_nang_id'=>$chucNang->id]);
        if(is_null($phanQuyen)){
            return false;
        }
        return  true;
    }

    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }


    public function beforeSave($insert)
    {
        if ($insert){
            $this->created_at=date('Y-m-d H:i:s');
        }else{
            $this->updated_at=date('Y-m-d H:i:s');
        }

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($this->id != 1) {
            $vaitro = Vaitrouser::findAll(['user_id' => $this->id]);
            foreach ($vaitro as $item) {
                $item->delete();
            }
            if (isset($this->vai_tros)) {
                foreach ($this->vai_tros as $item) {
                    $vaitronguoidung = new Vaitrouser();
                    $vaitronguoidung->vaitro_id = $item;
                    $vaitronguoidung->user_id = $this->id;
                    if (!$vaitronguoidung->save()) {
                        throw new HttpException(500, Html::errorSummary($vaitronguoidung));
                    }
                }
            }

        }
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
    }

    public function isAccess($arrRoles)
    {
        return !is_null(Vaitrouser::find()->andFilterWhere(['in', 'vaitro', $arrRoles])->andFilterWhere(['user_id' => Yii::$app->user->getId()])->one());
//        return 1;
    }

    public function getVaitrousers()
    {
        return $this->hasMany(Vaitrouser::className(), ['user_id' => 'id']);
    }

    public function beforeDelete()
    {
        Vaitrouser::deleteAll(['user_id' => $this->id]);
        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }

    public static function hasVaiTro($typeVaiTro, $user_id = null)
    {
        if (is_null($user_id))
            $user_id = Yii::$app->user->id;
        $vaiTro = VaiTro::findOne(['id' => $typeVaiTro]);
        if (is_null($vaiTro))
            return false;
        $userVaiTro = Vaitrouser::findOne(['user_id' => $user_id, 'vaitro_id' => $vaiTro->id]);
        if (!is_null($userVaiTro))
            return true;
        return false;
    }

    public function getImage (){
        return CauHinh::getServer() . '/upload-file/' . ($this->anh_nguoi_dung == null ? "user-nomal.jpg" : $this->anh_nguoi_dung);
    }

    public function getTrinhDo()
    {
        $trinhDoName = DanhMuc::findOne($this->trinh_do);
        return is_null($trinhDoName) ? "" : $trinhDoName->name;
    }

    public function updateDanhGiaGiaoVien()
    {
        $donDichVu = DonDichVu::find()->andFilterWhere(['giao_vien_id' => $this->id, 'trang_thai' => LichSuTrangThaiDon::HOAN_THANH, 'active' => 1]);
        $this->updateAttributes(['danh_gia' => round($donDichVu->sum('danh_gia') / $donDichVu->count(), 1) . "/5"]);
        return true;
    }

    public function vaoCa()
    {
        $this->updateAttributes(['trang_thai_vao_ca' => self::DANG_TRONG_KHOA_HOC]);
    }

    public function ketCa()
    {
        $this->updateAttributes(['trang_thai_vao_ca' => self::DANG_RANH]);
    }

    public function getChungChi()
    {
        $chungChi = ChungChi::find()->andFilterWhere(['user_id' => $this->id, 'active' => 1])->select(['file_path'])->all();
        $data = [];
        foreach ($chungChi as $item) {
            $data[] = $item->file_path !== null ? (CauHinh::getServer() . '/upload-file/' . $item->file_path) : null;
        }
        return $data;
    }


    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        // TODO: Implement toArray() method.
    }

    public static function instance($refresh = false)
    {
        // TODO: Implement instance() method.
    }
    public  function getVaiTro(){
        return "Phụ huynh";
    }
    public function getBangCap(){
        $str =json_decode( $this->bang_cap);
        if (isset($str->trinh_do)){
            return $str;
        }
        return  [
            'trinh_do'=>null,
            'chuyen_nganh'=>$this->bang_cap,
            'truong_dao_tao'=>null,
        ];
    }
}
