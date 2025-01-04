<?php namespace backend\controllers;

use backend\models\CauHinh;
use backend\models\ChucNang;
use backend\models\DanhMuc;
use backend\models\PhanQuyen;
use backend\models\QuanLyUserVaiTro;
use backend\models\VaiTro;
use backend\models\Vaitrouser;
use common\models\User;
use Yii;
use yii\bootstrap\Html;
use yii\helpers\FileHelper;
use yii\web\HttpException;
use yii\web\UploadedFile;

class AdminApiController extends CoreApiController
{
    public $vai_tro = User::ADMIN;
    public $dieu_khoan = 15;

    //Đăng nhập
    public function actionLogin()
    {
        $this->checkField(['dien_thoai']);
        if (isset($this->dataPost['dien_thoai'])) {
            if ($this->dataPost['dien_thoai'] == "")
                throw new HttpException(500, 'Vui lòng nhập số điện thoại');
            else {
                $userCheck = QuanLyUserVaiTro::findOne(['dien_thoai' => $this->dataPost['dien_thoai'], 'status' => 10, 'is_admin' => 1]);
            }
        }
        $this->checkField(['password']);
        if ("" == ($this->dataPost['password']))
            throw new HttpException(500, 'Vui lòng điền mật khẩu');
        if (is_null($userCheck))
            throw new HttpException(500, 'Tài khoản hoặc mật khẩu không chính xác');
        $user = User::findOne($userCheck->id);
        $fields = [];
        if (\Yii::$app->security->validatePassword($this->dataPost['password'], $user->password_hash)) {
            $fields['auth_key'] = \Yii::$app->security->generateRandomString();
            if (isset($this->dataPost['mobile_token'])) {
                if ($this->dataPost['mobile_token'] != "") {
                    $fields['mobile_token'] = $this->dataPost['mobile_token'];
                }
            }
            $user->updateAttributes($fields);
            $user = QuanLyUserVaiTro::findOne(['id'=>$user->id]);
            return $this->outputSuccess([
                'id'=>$user->id,
                'anh_nguoi_dung'=>CauHinh::getImage($user->anh_nguoi_dung),
                'auth_key'=>$user->auth_key,
                'hoten'=>$user->hoten,
                'email'=>$user->email,
                'dien_thoai'=>$user->dien_thoai,
                'vai_tro'=>$user->vai_tro_name,
            ], "Đăng nhập thành công");
        } else {
            throw new HttpException(500, 'Tài khoản hoặc mật khẩu không chính xác');
        }
    }

    public function actionLogout()
    {
        User::updateAll(['auth_key' => null, 'mobile_token' => null], ['id' => $this->uid]);
        return $this->outputSuccess("", "Đăng xuất thành công");
    }

    public function actionPhanQuyen()
    {
        if (!User::hasVaiTro(User::ADMIN, $this->uid)) {
            throw new HttpException(400, "Bạn không có quyền truy cập chức năng này");
        }
        $vaiTro = VaiTro::find()->andFilterWhere(['not in', 'id', [10, 11]])->all();
        $chucNang = ChucNang::find()->select(['id', 'name', 'ghi_chu'])->all();
        $phanQuyen = PhanQuyen::find()->select(['vai_tro_id', 'chuc_nang_id'])->all();
        return $this->outputSuccess([
            'vai_tro' => $vaiTro,
            'chuc_nang' => $chucNang,
            'phanQuyen' => $phanQuyen
        ]);
    }

    public function actionDanhSachUser()
    {
        $this->checkGetInput(['vai_tro_id']);
        $users = QuanLyUserVaiTro::find()
            ->select(['vai_tro_name', 'hoten', 'quyen_han', 'id'])
            ->andFilterWhere(['active' => 1, 'status' => 10, 'is_admin' => 1]);
        if ($this->tuKhoa != "") {
            $users->andFilterWhere(['like', 'hoten', $this->tuKhoa]);
        }
        if ($this->dataGet['vai_tro_id'] != "") {
            $users->andFilterWhere(['vai_tro' => $this->dataGet['vai_tro_id']]);
        }
        $count = count($users->all());
        $users = $users->limit($this->limit)->offset(($this->page-1)*$this->limit)->orderBy(['created_at'=>$this->sort==1?SORT_DESC:SORT_ASC])->all();
        if (count($users)>0){
            foreach ($users as $item){
                $item->anh_nguoi_dung = CauHinh::getServer().'/upload-file/'.($item->anh_nguoi_dung==null?"user-nomal.jpg":$item->anh_nguoi_dung);
            }
        }
        return $this->outputListSuccess2($users, $count);
    }
    public function actionDanhSachQuyen()
    {
        $vaiTro = VaiTro::find()->andFilterWhere(['not in', 'id', [10, 11]])->all();
        return $this->outputSuccess($vaiTro);
    }

    public function actionChinhSuaQuyen()
    {
        $this->checkField(['vai_tro_id']);
        if ($this->dataPost['id']==""){
            throw new HttpException(500,"Vui lòng truyền id");
        }
        $vaitro = Vaitrouser::findAll(['user_id' => $this->dataPost['id']]);
        foreach ($vaitro as $item) {
            $item->delete();
        }
        $vaiTroUser = new Vaitrouser();
        $vaiTroUser->vaitro_id = $this->dataPost['vai_tro_id'];
        $vaiTroUser->user_id = $this->dataPost['id'];
        if (!$vaiTroUser->save()) {
            throw new HttpException(500, Html::errorSummary($vaiTroUser));
        }
        return $this->outputSuccess("", "Cập nhật quyền hạn thành công");
    }

    public function actionXoaTaiKhoan()
    {
        $this->checkField(['id']);
        if ($this->dataPost['id']==""){
            throw new HttpException(500,"Vui lòng truyền id");
        }
        $user = User::findOne($this->dataPost['id']);
        if (is_null($user)) {
            throw new HttpException(403,"Không xác định dữ liệu");
        }

        $user->updateAttributes(['auth_key' => null, 'active' => 0, 'status' => 0]);
        return $this->outputSuccess("", "Xóa tài khoản thành công");
    }

    public function actionThemQuanTriVien()
    {
        $this->checkField([
            'hoten',
            'dien_thoai',
            'password',
            'vai_tro_id',
        ]);
        if ($this->dataPost['hoten'] == "") {
            throw new HttpException(500, "Vui lòng nhập họ tên");
        }
        if ($this->dataPost['dien_thoai'] == "") {
            throw new HttpException(500, 'Vui lòng nhập số điện thoại');
        }
        if (!$this->validatePhone($this->dataPost['dien_thoai'])) {
            throw new HttpException(500, 'Định dạnh số điện thoại không hợp lệ');
        }
        $userOld = QuanLyUserVaiTro::findOne(['dien_thoai' => $this->dataPost['dien_thoai'], 'status' => 10, 'is_admin' => 1]);
        if (!is_null($userOld)) {
            throw new HttpException(500, 'Số điện thoại đã tồn tại');
        }
        if (strlen($this->dataPost['password']) < 6) {
            throw new HttpException(500, "Mật khẩu tối thiểu 6 kí tự");
        }
        if ($this->dataPost['vai_tro_id'] == "") {
            throw new HttpException(500, "Vui lòng chọn phân quyền");
        }

        $user = new User();
        $user->hoten = $this->dataPost['hoten'];
        $user->dien_thoai = $this->dataPost['dien_thoai'];
        $user->password_hash = Yii::$app->security->generatePasswordHash($this->dataPost['password']);
        $user->username = $this->dataPost['dien_thoai'];
        $user->is_admin = 1;
        $user->vai_tros = [$this->dataPost['vai_tro_id']];
        $files = UploadedFile::getInstanceByName('anh_nguoi_dung');
        if (!empty($files)) {
            $path = (dirname(dirname(__DIR__))) . '/upload-file';
            $link = date('Y/m/d') . '/' . '_' . \Yii::$app->security->generateRandomString() . $this->get_extension_image($files->type);
            $user->anh_nguoi_dung = $link;
            if (FileHelper::createDirectory($path . '/' . date('Y/m/d') . '/', $mode = 0775, $recursive = true)) {
                $files->saveAs($path . '/' . $link);
            }
        }
        if (isset($this->dataPost['email'])) {
            if ($this->dataPost['email'] != "") {
                if (!$this->validateEmail($this->dataPost['email'])) {
                    throw new HttpException(500, "Email không đúng định dạng!");
                }
                $emailOld = User::find()->andFilterWhere(['email' => $this->dataPost['email']])->all();
                if (count($emailOld) > 0) {
                    throw new HttpException(500, "Email đã tồn tại!");
                }
            }
            $user->email= $this->dataPost['email'];
        }
        if (!$user->save()) {
            throw new HttpException(500, Html::errorSummary($user));
        };
        return $this->outputSuccess("", 'Đăng kí tài khoản thành công');
    }

    public function actionChiTietUser()
    {        
        $vaiTro = VaiTro::find()->andFilterWhere(['not in', 'id', [User::GIAO_VIEN, User::PHU_HUYNH]])->all();
        $this->checkGetInput(['id']);
        if ($this->dataGet['id']==""){
            throw new HttpException(500,"Vui lòng truyền id");
        }
        $user = QuanLyUserVaiTro::find()->select(['vai_tro_name', 'hoten', 'anh_nguoi_dung', 'quyen_han', 'dien_thoai', 'vai_tro','email', 'id'])
            ->andFilterWhere(['id' => $this->dataGet['id']])->one();
        $user->anh_nguoi_dung = CauHinh::getServer().'/upload-file/'.($user->anh_nguoi_dung==null?"user-nomal.jpg":$user->anh_nguoi_dung);
        return $this->outputSuccess(['user' => $user, 'vai_tro' => $vaiTro]);
    }

    public function actionCapNhatQuanTriVien()
    {
        $this->checkField([
            'id',
            'hoten',
            'dien_thoai',
            'password',
            'vai_tro_id',
        ]);
        if ($this->dataPost['id']==""){
            throw new HttpException(500,"Vui lòng truyền id");
        }
        if ($this->dataPost['hoten'] == "") {
            throw new HttpException(500, "Vui lòng nhập họ tên");
        }
        if ($this->dataPost['dien_thoai'] == "") {
            throw new HttpException(500, 'Vui lòng nhập số điện thoại');
        }
        if (!$this->validatePhone($this->dataPost['dien_thoai'])) {
            throw new HttpException(500, 'Định dạnh số điện thoại không hợp lệ');
        }
        $userOld = QuanLyUserVaiTro::find()->andFilterWhere(['<>', 'id', $this->dataPost['id']])->andFilterWhere(['dien_thoai' => $this->dataPost['dien_thoai'], 'status' => 10, 'is_admin' => 1])->one();
        if (!is_null($userOld)) {
            throw new HttpException(500, 'Số điện thoại đã tồn tại');
        }
        if ($this->dataPost['vai_tro_id'] == "") {
            throw new HttpException(500, "Vui lòng chọn phân quyền");
        }

        $user = User::findOne($this->dataPost['id']);
        $user->hoten = $this->dataPost['hoten'];
        $user->dien_thoai = $this->dataPost['dien_thoai'];
        if ($this->dataPost['password'] != "") {
            if (strlen($this->dataPost['password']) < 6) {
                throw new HttpException(500, "Mật khẩu tối thiểu 6 kí tự");
            }
            $user->password_hash = Yii::$app->security->generatePasswordHash($this->dataPost['password']);
        }
        $user->username = $this->dataPost['dien_thoai'];
        $user->is_admin = 1;
        $user->vai_tros = [$this->dataPost['vai_tro_id']];
        $files = UploadedFile::getInstanceByName('anh_nguoi_dung');
        if (!empty($files)) {
            $path = (dirname(dirname(__DIR__))) . '/upload-file';
            $link = date('Y/m/d') . '/' . '_' . \Yii::$app->security->generateRandomString() . $this->get_extension_image($files->type);
            $user->anh_nguoi_dung = $link;
            if (FileHelper::createDirectory($path . '/' . date('Y/m/d') . '/', $mode = 0775, $recursive = true)) {
                $files->saveAs($path . '/' . $link);
            }
        }
        if (isset($this->dataPost['email'])) {
            if ($this->dataPost['email'] != "") {
                if (!$this->validateEmail($this->dataPost['email'])) {
                    throw new HttpException(500, "Email không đúng định dạng!");
                }
                $emailOld = User::find()->andFilterWhere(['email' => $this->dataPost['email']])
                    ->andFilterWhere(['<>', 'id', $user->id])
                    ->all();
                if (count($emailOld) > 0) {
                    throw new HttpException(500, "Email đã tồn tại!");
                }
            }
            $user->email= $this->dataPost['email'];
        }
        if (!$user->save()) {
            throw new HttpException(500, Html::errorSummary($user));
        };
        return $this->outputSuccess("", 'Cập nhật thông tin tài khoản thành công');
    }
    public function actionSuaPhanQuyen(){
        $this->checkField(['vai_tro_id','chuc_nang_id','checked']);
        if ($this->dataPost['vai_tro_id']==""){
            throw new HttpException(500, "Vui lòng truyền tham số vai_tro_id");
        }
        if ($this->dataPost['chuc_nang_id']==""){
            throw new HttpException(500, "Vui lòng truyền tham số chuc_nang_id");
        }
        if (!in_array($this->dataPost['checked'],[1,0])){
            throw new HttpException(500, "checked phải là 1 hoặc 0");
        }
        PhanQuyen::deleteAll(['vai_tro_id'=>$this->dataPost['vai_tro_id'],'chuc_nang_id'=>$this->dataPost['chuc_nang_id']]);
        if ($this->dataPost['checked']==1){
            $phanQuyen = new PhanQuyen();
            $phanQuyen->vai_tro_id = $this->dataPost['vai_tro_id'];
            $phanQuyen->chuc_nang_id = $this->dataPost['chuc_nang_id'];
            if(!$phanQuyen->save()){
                throw new HttpException(500,Html::errorSummary($phanQuyen));
            }

        }
        return $this->outputSuccess("","Lưu thông tin phân quyền thành công");
    }
    public function actionDanhSachLeaderkd()
    {
        $users = QuanLyUserVaiTro::find()
            ->select(['anh_nguoi_dung', 'hoten', 'id'])
            ->andFilterWhere(['active' => 1,'vai_tro'=>VaiTro::LEADER_KD, 'status' => 10, 'is_admin' => 1]);
        $users = $users->all();
       if (count($users)>0){
           /** @var QuanLyUserVaiTro $item */
           foreach ($users as $item){
                $item->anh_nguoi_dung = $item->getImage();
            }
        }
        return $this->outputSuccess($users);
    }
    public function actionGetNhomGiaoVien(){
        return $this->outputSuccess($this->getDanhMuc(DanhMuc::TRINH_DO));
    }
    public function actionKiemTraMatKhau()
    {
        $this->checkField(['password']);
        $user = User::findOne($this->uid);
        if (!\Yii::$app->security->validatePassword($this->dataPost['password'], $user->password_hash)) {
            throw new HttpException(500, "Mật khẩu không chính xác");
        }
        return $this->outputSuccess("", "Nhập mật khẩu thành công");
    }

}
