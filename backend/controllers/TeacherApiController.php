<?php namespace backend\controllers;

use backend\models\BaiHoc;
use backend\models\BanGiao;
use backend\models\Bank;
use backend\models\Banner;
use backend\models\CauHinh;
use backend\models\ChungChi;
use backend\models\ChuongTrinhHoc;
use backend\models\DanhMuc;
use backend\models\DichVu;
use backend\models\DonDichVu;
use backend\models\GiaoDich;
use backend\models\GiaoVienDanhGiaBuoiHoc;
use backend\models\GiaoVienHocTap;
use backend\models\GoiHoc;
use backend\models\HocPhan;
use backend\models\KetQuaDaoTao;
use backend\models\KhieuNai;
use backend\models\KhieuNaiBangLuong;
use backend\models\KhoaHoc;
use backend\models\KhungThoiGian;
use backend\models\LichSuDaXem;
use backend\models\LichSuTrangThaiDon;
use backend\models\NhanLich;
use backend\models\PhieuLuong;
use backend\models\QuanLyDonDichVu;
use backend\models\QuanLyUserVaiTro;
use backend\models\ThongBao;
use backend\models\ThongBaoUser;
use backend\models\TienDoKhoaHoc;
use backend\models\TinTuc;
use common\models\myAPI;
use common\models\User;
use Yii;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\VarDumper;
use yii\web\HttpException;
use yii\web\UploadedFile;

class TeacherApiController extends CoreApiController
{
    public $vai_tro = User::GIAO_VIEN;
    public $dieu_khoan = 15;

    //Đăng kí
    public function actionRegister()
    {
        $this->checkField([
            'dien_thoai',
            'password',
            'password_confirm'
        ]);
        if ($this->dataPost['dien_thoai'] == "") {
            throw new HttpException(500, 'Vui lòng nhập số điện thoại');
        }
        if (!$this->validatePhone($this->dataPost['dien_thoai'])) {
            throw new HttpException(500, 'Định dạnh số điện thoại không hợp lệ');
        }
        if (strlen($this->dataPost['password']) < 6) {
            throw new HttpException(500, 'Mật khẩu tối thiểu 6 kí tự');
        }
        if ($this->dataPost['password_confirm'] == "") {
            throw new HttpException(500, 'Vui lòng nhập lại mật khẩu');
        }
        if ($this->dataPost['password_confirm'] !== $this->dataPost['password']) {
            throw new HttpException(500, 'Nhập lại mật khẩu không chính xác');
        }
        $userOld = QuanLyUserVaiTro::findOne(['dien_thoai' => $this->dataPost['dien_thoai'], 'status' => 10, 'vai_tro' => $this->vai_tro]);
        if (!is_null($userOld)) {
            throw new HttpException(500, 'Số điện thoại đã tồn tại');
        }
        //Save User
        $user = new User();
        $user->dien_thoai = $this->dataPost['dien_thoai'];
        $user->username = $this->dataPost['dien_thoai'];
        $user->active = 1;
        $user->is_finish = 0;
        $user->auth_key = \Yii::$app->security->generateRandomString();;
        $user->vai_tros = [$this->vai_tro];
        $user->password_hash = Yii::$app->security->generatePasswordHash($this->dataPost['password']);
        if (!$user->save()) {
            throw new HttpException(500, Html::errorSummary($user));
        };
        $user = QuanLyUserVaiTro::findOne(['id' => $user->id]);
        return $this->outputSuccess([
            'id' => $user->id,
            'anh_nguoi_dung' => CauHinh::getImage($user->anh_nguoi_dung),
            'auth_key' => $user->auth_key,
            'hoten' => $user->hoten,
            'is_finish' => $user->is_finish,
            'vai_tro' => $user->vai_tro_name,
        ], 'Đăng kí tài khoản thành công');
    }

    public function actionHoanTatDangKi()
    {
        //Kiểm tra tham so
        $this->checkField([
            'dich_vu',
        ]);
        $fields = [
            'dich_vu' => $this->dataPost['dich_vu'],
            'updated_at' => date("Y-m-d H:i:s"),
        ];
        //Kiểm tra dữ liệu đầu vào
//        if ($this->dataPost['hoten'] == "") {
//            throw new HttpException(500, "Vui lòng nhập họ tên");
//        }
//        if ($this->dataPost['ngay_sinh'] == "") {
//            throw new HttpException(500, "Vui lòng chọn ngày sinh");
//        }
//        if ($this->dataPost['dia_chi'] == "") {
//            throw new HttpException(500, "Vui lòng nhập địa chỉ");
//        }
//        if ($this->dataPost['dien_thoai'] == "") {
//            throw new HttpException(500, 'Vui lòng nhập số điện thoại');
//        }
//        if (!$this->validatePhone($this->dataPost['dien_thoai'])) {
//            throw new HttpException(500, 'Định dạnh số điện thoại không hợp lệ');
//        }
//        $userOld = QuanLyUserVaiTro::find()->andFilterWhere(['dien_thoai' => $this->dataPost['dien_thoai'], 'id' => $this->uid, 'status' => 10, 'vai_tro' => $this->vai_tro])->andFilterWhere(['<>', 'id', $this->uid])->one();
//        if (!is_null($userOld)) {
//            throw new HttpException(500, 'Số điện thoại đã tồn tại');
//        }
        // Up load file ảnh đại diện lên server
        $file = UploadedFile::getInstanceByName('anh_nguoi_dung');
        if (!empty($file)) {
            $path = (dirname(dirname(__DIR__))) . '/upload-file';
            $link = date('Y/m/d') . '/' . '_' . \Yii::$app->security->generateRandomString() . $this->get_extension_image($file->type);
            $fields['anh_nguoi_dung'] = $link;
            if (FileHelper::createDirectory($path . '/' . date('Y/m/d') . '/', $mode = 0775, $recursive = true)) {
                $file->saveAs($path . '/' . $link);
            }
        }
        $user = User::findOne($this->uid);
        //Up load file anh chứng chỉ lên server
        $files = UploadedFile::getInstancesByName('chung_chi');
        if (count($files) > 0) {
            foreach ($files as $file) {
                $path = (dirname(dirname(__DIR__))) . '/upload-file';
                $link = date('Y/m/d') . '/' . '_' . \Yii::$app->security->generateRandomString() . $this->get_extension_image($file->type);
                if (FileHelper::createDirectory($path . '/' . date('Y/m/d') . '/', $mode = 0775, $recursive = true)) {
                    $file->saveAs($path . '/' . $link);
                }
                //Lưu tên file anh chứng chỉ
                $chungChi = new ChungChi();
                $chungChi->user_id = $user->id;
                $chungChi->file_path = $link;
                if (!$chungChi->save()) {
                    throw new HttpException(500, Html::errorSummary($chungChi));
                }
            }
        }
        $fields['is_finish'] = 1;
        //Update các thông tin thành viên
        $user->updateAttributes($fields);
        return $this->outputSuccess("", "Hoàn tất đăng kí thành công");
    }

    public function actionDichVu()
    {
        $dichVu = DichVu::find()->andFilterWhere(['active' => 1])->select(['id', 'ten_dich_vu', 'image', 'khoa_dich_vu'])->orderBy(['seq' => SORT_ASC]);
        $dichVu = $dichVu->all();
        foreach ($dichVu as $item) {
            $item->image = CauHinh::getImage($item->image);
        }
        return $this->outputSuccess($dichVu);
    }

    public function actionTrinhDo()
    {
        $trinhDo = DanhMuc::find()->andFilterWhere(['type' => DanhMuc::TRINH_DO, 'active' => 1])->andFilterWhere(['in', 'id', [26, 27, 28]])->select(['id', 'name'])->all();
        return $this->outputSuccess($trinhDo);
    }

    public function actionDieuKhoan()
    {
        $dieuKhoan = CauHinh::findOne($this->dieu_khoan);
        $arr = explode('<br />', nl2br($dieuKhoan->ghi_chu));
        $results = [];
        foreach ($arr as $index => $item) {
            $results[] = trim($item);
        }
        return $this->outputSuccess(is_null($dieuKhoan) ? "" : join('<br>', $results));
    }

    //Đăng nhập
    public function actionLogin()
    {
        //Kiểm tra dữ liệu đầu vào
        if (!isset($this->dataPost['dien_thoai']) && !isset($this->dataPost['email'])) {
            throw new HttpException(400, 'Vui lòng truyền tham số dien_thoai hoặc email');
        }
        if (isset($this->dataPost['dien_thoai'])) {
            if ($this->dataPost['dien_thoai'] == "")
                throw new HttpException(500, 'Vui lòng nhập số điện thoại');
            else {
                $userCheck = QuanLyUserVaiTro::findOne(['dien_thoai' => $this->dataPost['dien_thoai'], 'status' => 10, 'vai_tro' => $this->vai_tro]);
            }
        }
        if (isset($this->dataPost['email'])) {
            if ($this->dataPost['email'] == "")
                throw new HttpException(500, 'Vui lòng nhập email');
            else {
                $userCheck = QuanLyUserVaiTro::findOne(['email' => $this->dataPost['email'], 'status' => 10, 'vai_tro' => $this->vai_tro]);
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
            $user = QuanLyUserVaiTro::findOne(['id' => $user->id]);
            return $this->outputSuccess([
                'id' => $user->id,
                'anh_nguoi_dung' => CauHinh::getImage($user->anh_nguoi_dung),
                'auth_key' => $user->auth_key,
                'hoten' => $user->hoten,
                'trinh_do' => $user->trinh_do_name,
                'is_finish' => $user->is_finish,
                'tai_nguyen' => CauHinh::getContent(29),
                'dieu_khoan' => CauHinh::getContent(36),
                'chinh_sach_dieu_khoan' => CauHinh::findOne(43)->getNoiDung(43),
                'chinh_sach_bao_mat' => CauHinh::findOne(42)->getNoiDung(42),
                'quy_che_hoan_huy' => CauHinh::findOne(44)->getNoiDung(44),
                'noi_quy' => CauHinh::getContent(35),
                'facebook' => CauHinh::getContent(32),
                'youtube' => CauHinh::getContent(33),
                'web' => CauHinh::getContent(34),
            ], "Đăng nhập thành công");
        } else {
            throw new HttpException(500, 'Tài khoản hoặc mật khẩu không chính xác');
        }
    }

    //Đăng nhập facebook
    public function actionLoginGoogle()
    {
        $this->checkField(['access_token']);
        $loginInfor = $this->googleLogin($this->dataPost['access_token']);
        if (is_null($loginInfor)) {
            throw new HttpException(500, "Token không hợp lệ");
        }
        if (!isset($loginInfor->sub)) {
            throw new HttpException(500, "Token không hợp lệ");
        }
        $userCheck = QuanLyUserVaiTro::findOne([
            'id_google' => $loginInfor->sub, 'active' => 1,
            'status' => 10, 'vai_tro' => $this->vai_tro
        ]);
        if (is_null($userCheck)) {
            $userCheck = new User();
            $userCheck->id_google = $loginInfor->sub;
            $userCheck->token_facebook = $this->dataPost['access_token'];
            $userCheck->hoten = $loginInfor->name;
            $userCheck->active = 1;
            $userCheck->is_finish = 0;
            $userCheck->auth_key = \Yii::$app->security->generateRandomString();;
            $userCheck->vai_tros = [$this->vai_tro];
            if (!$userCheck->save()) {
                throw new HttpException(500, Html::errorSummary($userCheck));
            };
        }
        $user = User::findOne($userCheck->id);
        $fields = [];
        $fields['auth_key'] = \Yii::$app->security->generateRandomString();
        if (isset($this->dataPost['mobile_token'])) {
            if ($this->dataPost['mobile_token'] != "") {
                $fields['mobile_token'] = $this->dataPost['mobile_token'];
            }
        }
        $user->updateAttributes($fields);
        $user = QuanLyUserVaiTro::findOne(['id' => $user->id]);
        return $this->outputSuccess([
            'id' => $user->id,
            'anh_nguoi_dung' => CauHinh::getImage($user->anh_nguoi_dung),
            'auth_key' => $user->auth_key,
            'hoten' => $user->hoten,
            'trinh_do' => $user->trinh_do_name,
            'is_finish' => $user->is_finish,
            'tai_nguyen' => CauHinh::getContent(29),
            'dieu_khoan' => CauHinh::getContent(36),
            'noi_quy' => CauHinh::getContent(35),
            'facebook' => CauHinh::getContent(32),
            'youtube' => CauHinh::getContent(33),
            'web' => CauHinh::getContent(34),
        ], "Đăng nhập thành công");
    }

    public function actionLoginFacebook()
    {
        $this->checkField(['access_token']);
        $loginInfor = $this->facebookLogin($this->dataPost['access_token']);
        if (is_null($loginInfor)) {
            throw new HttpException(500, "Token không hợp lệ");
        }
        if (!isset($loginInfor->id)) {
            throw new HttpException(500, "Token không hợp lệ");
        }
        $userCheck = QuanLyUserVaiTro::findOne([
            'id_facebook' => $loginInfor->id, 'active' => 1,
            'status' => 10, 'vai_tro' => $this->vai_tro
        ]);
        if (is_null($userCheck)) {
            $userCheck = new User();
            $userCheck->id_facebook = $loginInfor->id;
            $userCheck->token_google = $this->dataPost['access_token'];
            $userCheck->hoten = $loginInfor->name;
            $userCheck->active = 1;
            $userCheck->is_finish = 0;
            $userCheck->auth_key = \Yii::$app->security->generateRandomString();;
            $userCheck->vai_tros = [$this->vai_tro];
            if (!$userCheck->save()) {
                throw new HttpException(500, Html::errorSummary($userCheck));
            };
        }
        $user = User::findOne($userCheck->id);
        $fields = [];
        $fields['auth_key'] = \Yii::$app->security->generateRandomString();
        if (isset($this->dataPost['mobile_token'])) {
            if ($this->dataPost['mobile_token'] != "") {
                $fields['mobile_token'] = $this->dataPost['mobile_token'];
            }
        }
        $user->updateAttributes($fields);
        $user = QuanLyUserVaiTro::findOne(['id' => $user->id]);
        return $this->outputSuccess([
            'id' => $user->id,
            'anh_nguoi_dung' => CauHinh::getImage($user->anh_nguoi_dung),
            'auth_key' => $user->auth_key,
            'hoten' => $user->hoten,
            'trinh_do' => $user->trinh_do_name,
            'is_finish' => $user->is_finish,
            'tai_nguyen' => CauHinh::getContent(29),
            'dieu_khoan' => CauHinh::getContent(36),
            'noi_quy' => CauHinh::getContent(35),
            'facebook' => CauHinh::getContent(32),
            'youtube' => CauHinh::getContent(33),
            'web' => CauHinh::getContent(34),
        ], "Đăng nhập thành công");
    }

    public function actionLogout()
    {
        User::updateAll(['auth_key' => null, 'mobile_token' => null], ['id' => $this->uid]);
        return $this->outputSuccess("", "Đăng xuất thành công");
    }

    public function actionQuenMatKhau()
    {
        if (!isset($this->dataPost['dien_thoai']) && !isset($this->dataPost['email'])) {
            throw new HttpException(400, "Vui lòng truyền tham số dien_thoai hoặc email");
        }
        $rand = rand(100000, 999999);
        $email = CauHinh::findOne(2)->ghi_chu;
        if (isset($this->dataPost['dien_thoai'])) {
            if (!$this->validatePhone($this->dataPost['dien_thoai'])) {
                throw new HttpException(500, 'Định dạng số điện thoại không hợp lệ');
            } else {
                $user = QuanLyUserVaiTro::findOne(['dien_thoai' => $this->dataPost['dien_thoai'], 'status' => 10, 'vai_tro' => $this->vai_tro]);
                if (is_null($user)) {
                    throw new HttpException(500, 'Thông tin số điện thoại chưa được đăng ký');
                }
                $updateUser = User::findOne($user->id);
                $updateUser->updateAttributes(['ma_kich_hoat' => $rand]);
                $this->sendSMS($user->dien_thoai, 'Ma OTP cua TrongTrePro la ' . $rand.'. Tran trong cam on.');
                return $this->outputSuccess("", "Mã xác thực đã gửi đến số điện thoại của bạn, vui lòng kiểm tra");
            }
        }
        if (isset($this->dataPost['email'])) {
            if (!$this->validateEmail($this->dataPost['email'])) {
                throw new HttpException(500, 'Định dạng email không hợp lệ');
            } else {
                $user = QuanLyUserVaiTro::findOne(['email' => $this->dataPost['email'], 'status' => 10, 'vai_tro' => $this->vai_tro]);
                if (is_null($user)) {
                    throw new HttpException(500, 'Thông tin email chưa được đăng ký');
                }
                $updateUser = User::findOne($user->id);
                $updateUser->updateAttributes(['ma_kich_hoat' => $rand]);
                $this->sendEMail('Trông trẻ Pro', $email, $user->email, 'Giáo viên', 'Quên mật khẩu', '
                   Mã xác thực tài khoản: ' . $rand . '
                ');
                return $this->outputSuccess("", "Mã xác thực đã gửi đến email của bạn, vui lòng kiểm tra");
            }
        }

    }

    public function actionKiemTraMaXacThuc()
    {
        if (!isset($this->dataPost['dien_thoai']) && !isset($this->dataPost['email'])) {
            throw new HttpException(400, "Vui lòng truyền tham số dien_thoai hoặc email");
        }
        $this->checkField(['ma_xac_thuc']);
        if ($this->dataPost['ma_xac_thuc'] == "") {
            throw new HttpException(500, 'Vui lòng nhập mã xác thực');
        }
        if (isset($this->dataPost['dien_thoai'])) {
            if (!$this->validatePhone($this->dataPost['dien_thoai'])) {
                throw new HttpException(500, 'Định dạng số điện thoại không hợp lệ');
            } else {
                $user = QuanLyUserVaiTro::findOne(['dien_thoai' => $this->dataPost['dien_thoai'], 'status' => 10, 'vai_tro' => $this->vai_tro]);
                if (is_null($user)) {
                    throw new HttpException(500, 'Thông tin số điện thoại chưa được đăng ký');
                }
            }
        }
        if (isset($this->dataPost['email'])) {
            if (!$this->validateEmail($this->dataPost['email'])) {
                throw new HttpException(500, 'Định dạng email không hợp lệ');
            } else {
                $user = QuanLyUserVaiTro::findOne(['email' => $this->dataPost['email'], 'status' => 10, 'vai_tro' => $this->vai_tro]);
                if (is_null($user)) {
                    throw new HttpException(500, 'Thông tin email chưa được đăng ký');
                }
            }
        }
        $checkUser = User::findOne($user->id);
        if ($this->dataPost['ma_xac_thuc'] != $checkUser->ma_kich_hoat) {
            throw new HttpException(500, 'Mã xác thực không chính xác');
        }
        $checkUser->updateAttributes(['ma_kich_hoat' => null, 'auth_key' => \Yii::$app->security->generateRandomString()]);
        $user = QuanLyUserVaiTro::findOne(['id' => $checkUser->id]);
        return $this->outputSuccess([
            'id' => $user->id,
            'anh_nguoi_dung' => CauHinh::getImage($user->anh_nguoi_dung),
            'auth_key' => $user->auth_key,
            'hoten' => $user->hoten,
            'trinh_do' => $user->trinh_do_name,
        ]);
    }

    public function actionTaoMatKhauMoi()
    {
        $user = User::findOne($this->uid);
        $this->checkField(['password', 'password_comfirm']);
        if (strlen($this->dataPost['password']) < 6) {
            throw new HttpException(500, 'Mật khẩu tối thiểu 6 kí tự');
        }
        if ($this->dataPost['password'] !== $this->dataPost['password_comfirm']) {
            throw new HttpException(500, 'Nhập lại mật khẩu không chính xác');
        }
        $user->updateAttributes(['auth_key' => null, 'password_hash' => Yii::$app->security->generatePasswordHash($this->dataPost['password'])]);
        return $this->outputSuccess("", "Đổi mật khẩu thành công");
    }

    public function actionHome()
    {
        $danhSachDonDaNhan = ArrayHelper::map(NhanLich::find()->andFilterWhere(['giao_vien_id' => $this->uid, 'active' => 1])->all(), 'don_dich_vu_id', 'don_dich_vu_id');
        $donDichVu = DonDichVu::find()->andFilterWhere([DonDichVu::tableName() . '.active' => 1])
            ->leftJoin(KhungThoiGian::tableName(), KhungThoiGian::tableName() . '.id=' . DonDichVu::tableName() . '.chon_ca_id')
            ->andWhere('leader_kd_id is not null')
            ->andFilterWhere(['in', DonDichVu::tableName() . '.trang_thai', [LichSuTrangThaiDon::DANG_KHAO_SAT, LichSuTrangThaiDon::CHUA_CO_GIAO_VIEN]]);
        if ($this->tuKhoa != "") {
            $donDichVu->andFilterWhere(['like', DonDichVu::tableName() . '.ma_don_hang', $this->tuKhoa]);
        }
        if (count($danhSachDonDaNhan) > 0) {
            $donDichVu->andFilterWhere(['not in', DonDichVu::tableName() . '.id', $danhSachDonDaNhan]);
        }
        if (isset($this->dataGet['type'])) {
            if ($this->dataGet['type'] != "") {
                if ($this->dataGet['type'] == 1) {
                    $donDichVu->andFilterWhere(['=', KhungThoiGian::tableName() . '.type', 10]);
                } else if ($this->dataGet['type'] == 0) {
                    $donDichVu->andFilterWhere(['<>', KhungThoiGian::tableName() . '.type', 10]);
                }
            }
        }
        $user = User::findOne($this->uid);
        if ($user->trinh_do != 26) {
            $donDichVu->andFilterWhere(['<>', DonDichVu::tableName() . '.loai_giao_vien', 26]);
        }
        $dichVu = explode(',', $user->dich_vu);
        $donDichVu->andFilterWhere(['in', DonDichVu::tableName() . '.dich_vu_id', $dichVu]);
        $donDichVu = $donDichVu->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy([DonDichVu::tableName() . '.created' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();

        $dataDV = [];
        /** @var DonDichVu $item */
        foreach ($donDichVu as $item) {
            $dataDV[] = [
                'id' => $item->id,
                'ma_don_hang' => $item->ma_don_hang,
                'ten_dich_vu' => $item->dichVu->ten_dich_vu,
                'so_buoi' => $item->so_buoi,
                'tong_tien' => $item->tong_tien,
            ];
        }
        $banner = Banner::find()->select(['id', 'link', 'image'])->andFilterWhere(['active' => 1, 'status' => 1]);
        $banner = $banner->all();
        /** @var Banner $item */
        foreach ($banner as $item) {
            $item->image = CauHinh::getImage($item->image);
        }
        $daoTao = KhoaHoc::find()
            ->select(['tieu_de', 'image', 'id'])
            ->andFilterWhere(['active' => 1, 'type' => KhoaHoc::GIAO_VIEN]);
        $daoTao = $daoTao->all();
        foreach ($daoTao as $item) {
            $item->image = CauHinh::getImage($item->image);
        }
        return $this->outputSuccess([
            'donDichVu' => $dataDV,
            'types' => $this->getDanhMuc(DanhMuc::LOAI_TIN_TUC),
            'daoTao' => $daoTao,
            'banner' => $banner
        ]);
    }

    public function actionTinTuc()
    {
        $this->checkGetInput(['type', 'tuKhoa']);
        $types = $this->getDanhMuc(DanhMuc::LOAI_TIN_TUC);
        $moiNhat = TinTuc::find()->andFilterWhere(['active' => 1, 'status' => 1])->select(['id', 'tieu_de', 'noi_dung', 'anh_dai_dien', 'link', "DATE_FORMAT(created, '%d/%m/%Y') as date"])
            ->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy(['created' => $this->sort == 1 ? SORT_DESC : SORT_ASC]);
        if (isset($this->dataGet['type'])) {
            if ($this->dataGet['type'] != "") {
                $moiNhat->andFilterWhere(['type' => $this->dataGet['type']]);
            }
        }
        if ($this->tuKhoa != "") {
            $moiNhat->andFilterWhere(['like', 'tieu_de', $this->tuKhoa]);
        }
        $count = $moiNhat->count();
        $data = [];
        foreach ($moiNhat->createCommand()->queryAll() as $item) {
            $item['id'] = intval($item['id']);
            $data [] = $item;
        }
        return $this->outputListSuccess2([
            'tinTuc' => $data,
            'types' => $types
        ], $count);
    }

    public function actionDanhSachDonMoi()
    {
        $this->checkGetInput(['tuKhoa']);
        $danhSachDonDaNhan = ArrayHelper::map(NhanLich::find()->andFilterWhere(['giao_vien_id' => $this->uid, 'active' => 1])->all(), 'don_dich_vu_id', 'don_dich_vu_id');
        $donDichVu = DonDichVu::find()->andFilterWhere([DonDichVu::tableName() . '.active' => 1])
            ->leftJoin(KhungThoiGian::tableName(), KhungThoiGian::tableName() . '.id=' . DonDichVu::tableName() . '.chon_ca_id')
            ->andWhere('leader_kd_id is not null')
            ->andFilterWhere(['in', DonDichVu::tableName() . '.trang_thai', [LichSuTrangThaiDon::DANG_KHAO_SAT, LichSuTrangThaiDon::CHUA_CO_GIAO_VIEN]]);
        if ($this->tuKhoa != "") {
            $donDichVu->andFilterWhere(['like', DonDichVu::tableName() . '.ma_don_hang', $this->tuKhoa]);
        }
        if (count($danhSachDonDaNhan) > 0) {
            $donDichVu->andFilterWhere(['not in', DonDichVu::tableName() . '.id', $danhSachDonDaNhan]);
        }
        if (isset($this->dataGet['type'])) {
            if ($this->dataGet['type'] != "") {
                if ($this->dataGet['type'] == 1) {
                    $donDichVu->andFilterWhere(['=', KhungThoiGian::tableName() . '.type', 10]);
                } else if ($this->dataGet['type'] == 0) {
                    $donDichVu->andFilterWhere(['<>', KhungThoiGian::tableName() . '.type', 10]);
                }
            }
        }
        $user = User::findOne($this->uid);
        if ($user->trinh_do != 26) {
            $donDichVu->andFilterWhere(['<>', DonDichVu::tableName() . '.loai_giao_vien', 26]);
        }
        $dichVu = explode(',', $user->dich_vu);
        $donDichVu->andFilterWhere(['in', DonDichVu::tableName() . '.dich_vu_id', $dichVu]);
        $count = $donDichVu->count();
        $donDichVu = $donDichVu->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy([DonDichVu::tableName() . '.created' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        $data = [];
        if (count($donDichVu) > 0) {
            foreach ($donDichVu as $item) {
                /**@var $item DonDichVu */
                $data [] = [
                    'id' => $item->id,
                    'ma_don_hang' => $item->ma_don_hang,
                    'gio_bat_dau' => $item->gio_bat_dau,
                    'so_buoi' => $item->so_buoi,
                    'tong_tien' => $item->tong_tien,
                    'dichVu' => $item->dichVu->ten_dich_vu,
                    'diaChi' => $item->dia_chi,
                    'thoi_gian' => $item->getThoiGian(),
                    'chonCa' => $item->getCaDayName(),
                    'lich_hoc' => $item->getNamebyThu(),
                    'da_xem' => $item->da_xem,
                    'ghi_chu' => $item->ghi_chu,
                ];
            }
        }
        return $this->outputListSuccess2($data, $count);
    }

    public function actionChiTietDonMoi()
    {
        $this->checkGetInput(['id']);
        if ($this->dataGet['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $donDichVu = DonDichVu::findOne($this->dataGet['id']);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $lichSu = LichSuDaXem::find()->andFilterWhere(['giao_vien_id' => $this->uid, 'don_dich_vu_id' => $this->dataGet['id']])->all();
        $nhanLich = NhanLich::findOne(['giao_vien_id' => $this->uid, 'don_dich_vu_id' => $this->dataGet['id'], 'active' => 1]);
        if (!is_null($nhanLich)) {
            if ($nhanLich->trang_thai !== NhanLich::CHUA_NHAN) {
                throw new HttpException(500, "Đơn dịch vụ đang chờ duyệt");
            }
        }
        $soBuoiDay = TienDoKhoaHoc::find()->andFilterWhere(['don_dich_vu_id' => $donDichVu->id, 'trang_thai' => TienDoKhoaHoc::DA_HOAN_THANH, 'active' => 1])->count();
        if (count($lichSu) == 0) {
            $donDichVu->updateAttributes(['da_xem' => $donDichVu->da_xem + 1]);
            $lichSuNew = new LichSuDaXem();
            $lichSuNew->user_id = $this->uid;
            $lichSuNew->giao_vien_id = $this->uid;
            $lichSuNew->don_dich_vu_id = $this->dataGet['id'];
            if (!$lichSuNew->save()) {
                throw new HttpException(500, Html::errorSummary($lichSuNew));
            };
        }
        return $this->outputSuccess([
            'id' => $donDichVu->id,
            'ma_don_hang' => $donDichVu->ma_don_hang,
            'gio_bat_dau' => $donDichVu->gio_bat_dau,
            'trang_thai' => !is_null($nhanLich) ? $nhanLich->trang_thai : NhanLich::CHUA_NHAN,
            'dichVu' => $donDichVu->dichVu->ten_dich_vu,
            'soBuoiHoanThanh' => $donDichVu->getTrangThaiTienDo(),
            'tong_tien' => $donDichVu->tong_tien,
            'trang_thai_thanh_toan' => $donDichVu->trang_thai_thanh_toan,
            'lich_hoc' => $donDichVu->getNamebyThu(),
            'thoi_gian' => $donDichVu->getThoiGian(),
            'chonCa' => $donDichVu->getCaDayName(),
            'so_gio' => $donDichVu->chonCa->so_gio,
            'phuHuynh' => [
                'dia_chi' => $donDichVu->dia_chi,
                'ghi_chu' => $donDichVu->ghi_chu,
            ],
        ]);
    }

    public function actionNhanLich()
    {
        $this->checkField(['id']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $donDichVu = DonDichVu::findOne($this->dataPost['id']);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $nhanLich = NhanLich::findOne(['giao_vien_id' => $this->uid, 'don_dich_vu_id' => $this->dataPost['id'], 'active' => 1]);
        if (!is_null($nhanLich)) {
            throw new HttpException(500, "Đơn hàng " . $donDichVu->ma_don_hang . " bạn đã nhận trước đó!");
        }
        $nhanLich = new NhanLich();
        $nhanLich->user_id = $this->uid;
        $nhanLich->giao_vien_id = $this->uid;
        $nhanLich->don_dich_vu_id = $this->dataPost['id'];
        $nhanLich->trang_thai = NhanLich::DANG_CHO_DUYET;
        if (!$nhanLich->save()) {
            throw new HttpException(500, Html::errorSummary($nhanLich));
        } else {
            $donDichVu->updated = date('y-m-d H:i:s');
            $donDichVu->trang_thai = LichSuTrangThaiDon::DANG_KHAO_SAT;
            $donDichVu->save();
        }
        $lichSu = LichSuDaXem::find()->andFilterWhere(['giao_vien_id' => $this->uid, 'don_dich_vu_id' => $this->dataPost['id']])->all();
        if (count($lichSu) == 0) {
            $donDichVu->updateAttributes(['da_xem' => $donDichVu->da_xem + 1]);
            $lichSuNew = new LichSuDaXem();
            $lichSuNew->user_id = $this->uid;
            $lichSuNew->giao_vien_id = $this->uid;
            $lichSuNew->don_dich_vu_id = $this->dataPost['id'];
            if (!$lichSuNew->save()) {
                throw new HttpException(500, Html::errorSummary($lichSuNew));
            };
        }
        $thongBao = new ThongBao();
        $thongBao->to_id = 60;
        $thongBao->type_id = 65;
        $thongBao->noi_dung =
            "Giáo viên: " . $nhanLich->giaoVien->hoten . " • " . $nhanLich->giaoVien->getTrinhDo() . " gửi yêu cầu nhận lịch";
        $thongBao->tieu_de = "Nhận lịch";
        $this->saveThongBao($thongBao);
        $email = CauHinh::findOne(2)->ghi_chu;
        $emailAdmin = CauHinh::findOne(39)->content;
        $this->sendEMail('Trông trẻ Pro', $email, $emailAdmin, $thongBao->tieu_de, $thongBao->tieu_de, str_replace('\n','<br>',$thongBao->noi_dung));
        $this->sendEMail('Trông trẻ Pro', $email, $donDichVu->leaderKd->email, $thongBao->tieu_de, $thongBao->tieu_de, str_replace('\n','<br>',$thongBao->noi_dung));
        return $this->outputSuccess("", "Nhận lịch thành công");
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

    public function actionTuChoi()
    {
        $this->checkField(['id']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $donDichVu = DonDichVu::findOne(['id' => $this->dataPost['id'], 'giao_vien_id' => $this->uid]);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($donDichVu->trang_thai == LichSuTrangThaiDon::DANG_DAY) {
            throw new HttpException(500, "Khóa đang trong quá trình dạy!");
        }
        if (is_null($donDichVu->giao_vien_id)) {
            throw new HttpException(500, "Đơn hàng chưa có giáo viên");
        }
        $donDichVu->giao_vien_id = null;
        if (!$donDichVu->save()) {
            throw new HttpException(500, Html::errorSummary($donDichVu));
        } else {
            $nhanLich = NhanLich::findOne(['giao_vien_id' => $this->uid, 'active' => 1, 'don_dich_vu_id' => $donDichVu->id]);
            $nhanLich->trang_thai = NhanLich::DA_HUY;
            if (!$nhanLich->save()) {
                throw new HttpException(500, Html::errorSummary($nhanLich));
            }
            $thongBao = new ThongBao();
            $thongBao->to_id = 61;
            $thongBao->type_id = 65;
            $thongBao->noi_dung = "Giáo viên từ chối khóa học" . $donDichVu->ma_don_hang . "!. \nChương trình: "
                . $donDichVu->dichVu->ten_dich_vu . ". \nBởi: " . $donDichVu->phuHuynh->hoten .
                " • " . $donDichVu->phuHuynh->getVaiTro();
            $thongBao->tieu_de = "Giáo viên từ chối";
            $this->saveThongBao($thongBao);
        } $email = CauHinh::findOne(2)->ghi_chu;
        $emailAdmin = CauHinh::findOne(39)->content;
        $this->sendEMail('Trông trẻ Pro', $email, $emailAdmin, $thongBao->tieu_de, $thongBao->tieu_de, str_replace('\n','<br>',$thongBao->noi_dung));
        $this->sendEMail('Trông trẻ Pro', $email, $donDichVu->leaderKd->email, $thongBao->tieu_de, $thongBao->tieu_de, str_replace('\n','<br>',$thongBao->noi_dung));
        return $this->outputSuccess("", "Từ chối thành công");
    }

    public function actionDanhSachDonDaNhan()
    {
        $this->checkGetInput(['tuKhoa']);
        $danhSachDonDaNhan = ArrayHelper::map(NhanLich::find()->andFilterWhere(['giao_vien_id' => $this->uid, 'active' => 1])->andFilterWhere(['<>', 'trang_thai', NhanLich::CHUA_NHAN])->all(), 'don_dich_vu_id', 'don_dich_vu_id');
        $donDichVu = DonDichVu::find()->andFilterWhere([DonDichVu::tableName() . '.active' => 1])
            ->leftJoin(KhungThoiGian::tableName(), KhungThoiGian::tableName() . '.id=' . DonDichVu::tableName() . '.chon_ca_id');
        if ($this->tuKhoa != "") {
            $donDichVu->andFilterWhere(['like', DonDichVu::tableName() . '.ma_don_hang', $this->tuKhoa]);
        }
        if (count($danhSachDonDaNhan) > 0) {
            $donDichVu->andFilterWhere(['in', DonDichVu::tableName() . '.id', $danhSachDonDaNhan]);
        } else {
            return $this->outputListSuccess2([], 0);
        }
        if (isset($this->dataGet['type'])) {
            if ($this->dataGet['type'] != "") {
                if ($this->dataGet['type'] == 1) {
                    $donDichVu->andFilterWhere(['=', KhungThoiGian::tableName() . '.type', 10]);
                } else if ($this->dataGet['type'] == 0) {
                    $donDichVu->andFilterWhere(['<>', KhungThoiGian::tableName() . '.type', 10]);
                }
            }
        }
        $count = $donDichVu->count();
        $donDichVu = $donDichVu->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy(['updated' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        $data = [];
        if (count($donDichVu) > 0) {
            foreach ($donDichVu as $index => $item) {
                /**@var $item DonDichVu */
                $nhanLich = NhanLich::findOne(['giao_vien_id' => $this->uid, 'active' => 1, 'don_dich_vu_id' => $item->id]);
                $leaderKD = QuanLyUserVaiTro::findOne(['id' => $item->leader_kd_id]);
                $data [] = [
                    'id' => $item->id,
                    'ma_don_hang' => $item->ma_don_hang,
                    'trang_thai' => is_null($nhanLich) ? [
                        'id' => 82,
                        'name' => NhanLich::CHUA_NHAN,
                    ] : $nhanLich->getTrangThaiID(),
                    'dichVu' => $item->dichVu->ten_dich_vu,
                    'dia_chi' => $item->dia_chi,
                    'thoi_gian' => $item->getThoiGian(),
                    'chonCa' => $item->getCaDayName(),
                    'lich_hoc' => $item->getNamebyThu(),
                    'tong_tien' => $item->tong_tien,
                    'so_buoi' => $item->so_buoi,
                    'buoi_hien_tai' => $item->getBuoiHienTai(),
                    'leaderKD' => is_null($leaderKD) ? null : [
                        'hoten' => $leaderKD->hoten,
                        'vai_tro' => $leaderKD->vai_tro_name,
                        'anh_nguoi_dung' => CauHinh::getImage($leaderKD->anh_nguoi_dung),
                        'dien_thoai' => $leaderKD->dien_thoai,
                    ]
                ];
            }
        }
        return $this->outputListSuccess2($data, $count);
    }

    public function actionChiTietDonDaDuyet()
    {
        $this->checkGetInput(['id']);
        if ($this->dataGet['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $donDichVu = DonDichVu::findOne($this->dataGet['id']);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $nhanLich = NhanLich::findOne(['giao_vien_id' => $this->uid, 'don_dich_vu_id' => $this->dataGet['id'], 'active' => 1]);
        if (is_null($nhanLich)) {
            throw new HttpException(500, "Đơn hàng " . $donDichVu->ma_don_hang . " chưa được duyệt, vui lòng liên hệ quản lý vận hành!");
        }
        if ($nhanLich->trang_thai != NhanLich::DA_DUYET) {
            throw new HttpException(500, "Đơn hàng " . $donDichVu->ma_don_hang . " chưa được duyệt, vui lòng liên hệ quản lý vận hành!");
        }
        return $this->outputSuccess([
            'id' => $donDichVu->id,
            'ma_don_hang' => $donDichVu->ma_don_hang,
            'trang_thai' => !is_null($nhanLich) ? $nhanLich->trang_thai : NhanLich::CHUA_NHAN,
            'dichVu' => $donDichVu->dichVu->ten_dich_vu,
            'gio_bat_dau' => $donDichVu->gio_bat_dau,
            'dia_chi' => $donDichVu->dia_chi,
            'thoi_gian' => $donDichVu->getThoiGian(),
            'chonCa' => $donDichVu->getCaDayName(),
            'lich_hoc' => $donDichVu->getNamebyThu(),
            'phuHuynh' => [
                'hoten' => $donDichVu->phuHuynh->hoten,
                'vai_tro' => $donDichVu->phuHuynh->getVaiTro(),
                'anh_nguoi_dung' => $donDichVu->phuHuynh->getImage(),
                'dien_thoai' => $donDichVu->phuHuynh->dien_thoai
            ],
            'leaderKD' => $donDichVu->getLeader(),
            'noi_dung_khao_sat' => $donDichVu->noi_dung_khao_sat
        ]);
    }

    public function actionDongYDiLam()
    {
        $this->checkField(['id']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $donDichVu = DonDichVu::findOne($this->dataPost['id']);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $nhanLich = NhanLich::findOne(['giao_vien_id' => $this->uid, 'don_dich_vu_id' => $this->dataPost['id'], 'active' => 1]);
        if (is_null($nhanLich)) {
            throw new HttpException(500, "Đơn hàng " . $donDichVu->ma_don_hang . " chưa được duyệt, vui lòng liên hệ quản lý vận hành!");
        }
        if ($nhanLich->trang_thai != NhanLich::DA_DUYET) {
            throw new HttpException(500, "Đơn hàng " . $donDichVu->ma_don_hang . " chưa được duyệt, vui lòng liên hệ quản lý vận hành!");
        }
        if ($donDichVu->giao_vien_id == $this->uid && $donDichVu->giao_vien_dong_thuan == 1) {
            throw new HttpException(500, "Đơn hàng " . $donDichVu->ma_don_hang . " bạn đã đồng ý đi làm trước đó!");
        }
        $donDichVu->giao_vien_dong_thuan = 1;
        if ($donDichVu->giao_vien_dong_thuan == 1 && $donDichVu->phu_huynh_dong_thuan == 1) {
            $donDichVu->trang_thai = LichSuTrangThaiDon::DANG_DAY;
            $nhanLich->trang_thai = NhanLich::DANG_DAY;
            $giao_vien_id = $this->uid;
            $tienDoKhoaHoc = TienDoKhoaHoc::find()->andFilterWhere([TienDoKhoaHoc::tableName() . '.don_dich_vu_id' => $donDichVu->id])
                    ->andFilterWhere([TienDoKhoaHoc::tableName() . '.trang_thai' => TienDoKhoaHoc::CHUA_DAY])->all();
            foreach ($tienDoKhoaHoc as $value) {
                $tienDo = TienDoKhoaHoc::findOne(['id'=>$value->id]);
                $tienDo->updateAttributes(['giao_vien_id' => $giao_vien_id]);
            }     
            if (!$nhanLich->save()) {
                throw new HttpException(500, Html::errorSummary($nhanLich));
            }
        }
        if (!$donDichVu->save()) {
            throw new HttpException(500, Html::errorSummary($donDichVu));
        }
        $thongBao = new ThongBao();
        $thongBao->to_id = 60;
        $thongBao->type_id = 65;
        $thongBao->noi_dung =
            "Giáo viên: " . $nhanLich->giaoVien->hoten . " • " . $nhanLich->giaoVien->getTrinhDo() . " đồng ý phụ trách khóa học " . $donDichVu->ma_don_hang;
        $thongBao->tieu_de = "Đồng ý đi làm";
        $this->saveThongBao($thongBao);
        $email = CauHinh::findOne(2)->ghi_chu;
        $emailAdmin = CauHinh::findOne(39)->content;
        $this->sendEMail('Trông trẻ Pro', $email, $emailAdmin, $thongBao->tieu_de, $thongBao->tieu_de, str_replace('\n','<br>',$thongBao->noi_dung));
        $this->sendEMail('Trông trẻ Pro', $email, $donDichVu->leaderKd->email, $thongBao->tieu_de, $thongBao->tieu_de, str_replace('\n','<br>',$thongBao->noi_dung));
        return $this->outputSuccess("", "Đồng ý đi làm thành công");
    }

    public function actionChiTietCaDay()
    {
        $this->checkGetInput(['id', 'buoi']);
        if ($this->dataGet['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $donDichVu = DonDichVu::findOne($this->dataGet['id']);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $nhanLich = NhanLich::findOne(['giao_vien_id' => $this->uid, 'don_dich_vu_id' => $this->dataGet['id'], 'active' => 1]);
        if (is_null($nhanLich)) {
            throw new HttpException(500, "Đơn hàng " . $donDichVu->ma_don_hang . " chưa được duyệt, vui lòng liên hệ quản lý vận hành!");
        }

        if (intval($_GET['buoi']) == 0 || intval($_GET['buoi']) > $donDichVu->so_buoi) {
            throw new HttpException(500, "Thông tin buổi học không hợp lệ");
        }
        $tienDo = $donDichVu->tienDoKhoaHoc(intval($_GET['buoi']));
        $phuHuynh = $donDichVu->phuHuynh;
        return $this->outputSuccess([
            'tienDo' => $tienDo,
            'gio_bat_dau' => $donDichVu->gio_bat_dau,
            'leaderKD' => $donDichVu->getLeader(),
            'dichVu' => $donDichVu->dichVu->ten_dich_vu,
            'soBuoiHoanThanh' => $donDichVu->getTrangThaiTienDo(),
            'tong_tien' => $donDichVu->tong_tien,
            'trang_thai_thanh_toan' => $donDichVu->trang_thai_thanh_toan,
            'lich_hoc' => $donDichVu->getNamebyThu(),
            'thoi_gian' => $donDichVu->getThoiGian(),
            'chonCa' => $donDichVu->getCaDayName(),
            'so_gio' => $tienDo['so_gio'],
            'phuHuynh' => [
                'hoten' => $phuHuynh->hoten,
                'id' => $phuHuynh->id,
                'anh_nguoi_dung' => $phuHuynh->getImage(),
                'vai_tro' => $phuHuynh->getVaiTro(),
                'dien_thoai' => $phuHuynh->dien_thoai
            ],
            'dia_chi' => $donDichVu->dia_chi,
            'ghi_chu' => $donDichVu->ghi_chu,
            'noi_dung_khao_sat' => $donDichVu->noi_dung_khao_sat
        ]);
    }

    public function actionDoiNgayDay()
    {

        $this->checkField(['ca_day_id', 'ngay_day']);
        if ($this->dataPost['ca_day_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền ca_day_id");
        }
        if ($this->dataPost['ngay_day'] == "") {
            throw new HttpException(500, "Vui lòng chọn ngày");
        }
        $caDay = TienDoKhoaHoc::findOne(['id' => $this->dataPost['ca_day_id'], 'active' => 1]);
        if (is_null($caDay)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($caDay->trang_thai !== TienDoKhoaHoc::CHUA_DAY) {
            throw new HttpException(500, "Đổi lịch chỉ áp dụng cho các buổi học chưa dạy");
        }
        /** @var TienDoKhoaHoc $checkDate */
        $checkDate = TienDoKhoaHoc::find()->andFilterWhere(['don_dich_vu_id'=>$caDay->don_dich_vu_id, 'active' => 1])
            ->andFilterWhere(['date(ngay_day)'=>myAPI::convertDMY2YMD($this->dataPost['ngay_day'])])
            ->andFilterWhere(['<>','trang_thai',TienDoKhoaHoc::DA_HUY])->one();
        if (!is_null($checkDate)){
            throw new HttpException(500, "Ngày dạy đã trùng với buổi số {$checkDate->buoi}");
        }
        $caDay->ngay_day = $this->dataPost['ngay_day'];
        if (!$caDay->save()) {
            throw new HttpException(500, Html::errorSummary($caDay));
        }
        return $this->outputSuccess("", "Đổi ngày dạy thành công");
    }

    public function actionDoiGioDay()
    {
        $this->checkField(['ca_day_id', 'khung_gio_id']);
        if ($this->dataPost['ca_day_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền ca_day_id");
        }
        if ($this->dataPost['khung_gio_id'] == "") {
            throw new HttpException(500, "Vui lòng chọn khung giờ");
        }
        $caDay = TienDoKhoaHoc::findOne(['id' => $this->dataPost['ca_day_id'], 'active' => 1]);
        if (is_null($caDay)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($caDay->trang_thai !== TienDoKhoaHoc::CHUA_DAY) {
            throw new HttpException(500, "Đổi lịch chỉ áp dụng cho các buổi học chưa dạy");
        }
        $caDay->ca_day_id = $this->dataPost['khung_gio_id'];

        if (!$caDay->save()) {
            throw new HttpException(500, Html::errorSummary($caDay));
        }

        return $this->outputSuccess("", "Đổi giờ dạy thành công");
    }

    public function actionVaoCa()
    {
        $this->checkField(['ca_day_id']);
        if ($this->dataPost['ca_day_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền ca_day_id");
        }
        $tienDo = TienDoKhoaHoc::findOne($this->dataPost['ca_day_id']);
        if (is_null($tienDo)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($tienDo->giao_vien_id !== $this->uid) {
            throw new HttpException(500, "Bạn không có quyền truy cập ca dạy này");
        }
        if ($tienDo->trang_thai !== TienDoKhoaHoc::CHUA_DAY) {
            throw new HttpException(500, "Ca dạy đang có trạng thái là $tienDo->trang_thai");
        }
        if (in_array($tienDo->donDichVu->trang_thai,[ LichSuTrangThaiDon::DA_HUY,LichSuTrangThaiDon::DON_HOAN]) ) {
            throw new HttpException(500, "Bạn không có quyền truy cập buổi học này !");
        }
        if ($tienDo->buoi != $tienDo->donDichVu->getBuoiHienTai()) {
            throw new HttpException(500, "Buổi học hiện tại là {$tienDo->donDichVu->getBuoiHienTai()}");
        }
        $tienDo->vao_ca = date('Y-m-d H:i:s');
        $tienDo->giao_vien_id = $this->uid;
//        if ($tienDo->giaoVien->trang_thai_vao_ca == User::DANG_TRONG_KHOA_HOC) {
//            throw new HttpException(500, "Bạn đang trong ca dạy khác!");
//        }
        $tienDo->trang_thai = TienDoKhoaHoc::DANG_DAY;
        if (!$tienDo->save()) {
            throw new HttpException(500, Html::errorSummary($tienDo));
        }
        $thongBao = new ThongBao();
        $thongBao->to_id = 60;
        $thongBao->type_id = 65;
        $thongBao->noi_dung =
            "Ca dạy của giáo viên bắt đầu lúc " . date("H:i", strtotime($tienDo->gio_day)) . ". \nBạn vui lòng kiểm tra lại thông tin. \nChúc bạn may mắn!";
        $thongBao->tieu_de = "Khóa học đang vào ca!";
        $this->saveThongBao($thongBao);
        $email = CauHinh::findOne(2)->ghi_chu;
        $emailAdmin = CauHinh::findOne(39)->content;
        $this->sendEMail('Trông trẻ Pro', $email, $emailAdmin, $thongBao->tieu_de, $thongBao->tieu_de, str_replace('\n','<br>',$thongBao->noi_dung));
        $this->sendEMail('Trông trẻ Pro', $email, $tienDo->donDichVu->leaderKd->email, $thongBao->tieu_de, $thongBao->tieu_de, str_replace('\n','<br>',$thongBao->noi_dung));
        return $this->outputSuccess("", "Vào ca thành công");
    }

    public function actionKetCa()
    {
        $this->checkField(['ca_day_id']);
        if ($this->dataPost['ca_day_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền ca_day_id");
        }
        $tienDo = TienDoKhoaHoc::findOne($this->dataPost['ca_day_id']);
        if (is_null($tienDo)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($tienDo->giao_vien_id !== $this->uid) {
            throw new HttpException(500, "Bạn không có quyền truy cập ca dạy này");
        }
        if ($tienDo->trang_thai !== TienDoKhoaHoc::DANG_DAY) {
            throw new HttpException(500, "Ca dạy đang không trong buổi dạy hoặc đã hoàn thành");
        }
//        if ($tienDo->giaoVien->trang_thai_vao_ca == User::DANG_RANH) {
//            throw new HttpException(500, "Bạn đang không trong ca dạy!");
//        }
        $tienDo->ket_ca = date('Y-m-d H:i:s');
        $tienDo->giao_vien_id = $this->uid;
        $tienDo->trang_thai = TienDoKhoaHoc::DA_HOAN_THANH;
        if (!$tienDo->save()) {
            throw new HttpException(500, Html::errorSummary($tienDo));
        }
        $tienDo->giaoVien->ketCa();
        $thongBao = new ThongBao();
        $thongBao->to_id = 60;
        $thongBao->type_id = 65;
        $thongBao->noi_dung =
            "Dịch vụ: " . $tienDo->donDichVu->dichVu->ten_dich_vu . " \nBởi: " . $tienDo->giaoVien->hoten . " • Giáo viên";
        $thongBao->tieu_de = "Buổi học đã hoàn thành!!";
        $this->saveThongBao($thongBao);
        $email = CauHinh::findOne(2)->ghi_chu;
        $emailAdmin = CauHinh::findOne(39)->content;
        $this->sendEMail('Trông trẻ Pro', $email, $emailAdmin, $thongBao->tieu_de, $thongBao->tieu_de, str_replace('\n','<br>',$thongBao->noi_dung));
        $this->sendEMail('Trông trẻ Pro', $email, $tienDo->donDichVu->leaderKd->email, $thongBao->tieu_de, $thongBao->tieu_de, str_replace('\n','<br>',$thongBao->noi_dung));
        return $this->outputSuccess("", "Hoàn thành ca dạy thành công");
    }

    public function actionNhanXetBuoiHoc()
    {
        $this->checkField(['ca_day_id', 'nhan_xet_buoi_hoc', 'danh_gia', 'formDanhGia']);
        if ($this->dataPost['ca_day_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền ca_day_id");
        }
        $tienDo = TienDoKhoaHoc::findOne($this->dataPost['ca_day_id']);
        if (is_null($tienDo)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($tienDo->giao_vien_id !== $this->uid) {
            throw new HttpException(500, "Bạn không có quyền truy cập ca dạy này");
        }
        if ($tienDo->trang_thai !== TienDoKhoaHoc::DA_HOAN_THANH) {
            throw new HttpException(500, "Ca dạy chưa hoàn thành");
        }
        if ($this->dataPost['nhan_xet_buoi_hoc'] == "") {
            throw new HttpException(500, "Vui lòng nhập nhận xét");
        }
        if ($this->dataPost['danh_gia'] == "") {
            throw new HttpException(500, "Vui lòng chọn đánh giá");
        }
        if ($this->dataPost['formDanhGia'] == "") {
            throw new HttpException(500, "Vui lòng truyền formDanhGia");
        }

        $tienDo->nhan_xet_buoi_hoc = $this->dataPost['nhan_xet_buoi_hoc'];
//
        $tienDo->danh_gia = $this->dataPost['danh_gia'];
        $image = $this->saveImage();
        if ($image != "") {
            $tienDo->image = $image;
        }
        $video = $this->saveVideo();
        if ($video != "") {
            $tienDo->video = $video;
        }
        if (!$tienDo->save()) {
            throw new HttpException(500, Html::errorSummary($tienDo));
        }
        if (is_string($this->dataPost['formDanhGia'])) {
            $this->dataPost['formDanhGia'] = json_decode($this->dataPost['formDanhGia']);
        }
        if (count($this->dataPost['formDanhGia']) > 0) {
            foreach ($this->dataPost['formDanhGia'] as $danhGia) {
                $danhGia = (object)$danhGia;
                $gvDanhGia = GiaoVienDanhGiaBuoiHoc::findOne($danhGia->id);
                if (is_null($gvDanhGia)) {
                    throw new HttpException(500, "Vui lòng kiểm tra lại tham số đánh giá");
                }
                if (isset($danhGia->muc_do_da_chon)) {
                    if ($danhGia->muc_do_da_chon != "") {
                        $gvDanhGia->muc_do_da_cho = $danhGia->muc_do_da_chon;
                    }
                }
                if (isset($danhGia->noi_dung_nhan_xet)) {
                    if ($danhGia->noi_dung_nhan_xet != "") {
                        $gvDanhGia->noi_dung_nhan_xet = $danhGia->noi_dung_nhan_xet;
                    }
                }
                if (!$gvDanhGia->save()) {
                    throw new HttpException(500, Html::errorSummary($gvDanhGia));
                }
            }
        }

        return $this->outputSuccess("", "Nhận xét ca dạy thành công");

    }

    public function actionChiTietNhanXet()
    {
        $this->checkGetInput(['ca_day_id']);
        if ($this->dataGet['ca_day_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền ca_day_id");
        }
        $tienDo = TienDoKhoaHoc::findOne($this->dataGet['ca_day_id']);
        if (is_null($tienDo)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($tienDo->giao_vien_id !== $this->uid) {
            throw new HttpException(500, "Bạn không có quyền truy cập ca dạy này");
        }
        if ($tienDo->trang_thai !== TienDoKhoaHoc::DA_HOAN_THANH) {
            throw new HttpException(500, "Ca dạy chưa hoàn thành");
        }
        $donDichVu = $tienDo->donDichVu;
        $giaoVien = $tienDo->giaoVien;
        return $this->outputSuccess([
            'id' => $donDichVu->id,
            'ma_don_hang' => $donDichVu->ma_don_hang,
            'giaoVien' => is_null($giaoVien) ? null : [
                'id' => $giaoVien->id,
                'anh_nguoi_dung' => $giaoVien->getImage(),
                'hoten' => $giaoVien->hoten,
                'trinh_do' => $giaoVien->getTrinhDo(),
            ],
            'tienDo' => [
                'buoi' => $tienDo->buoi,
                'thoi_gian' => $tienDo->getTimeSuccess(),
                'nhan_xet_buoi_hoc' => $tienDo->nhan_xet_buoi_hoc,
                'image' => CauHinh::getImage($tienDo->image),
                'video' => CauHinh::getLink($tienDo->video),
                'danh_gia' => $tienDo->danh_gia
            ],
            'formDanhGia' => $tienDo->getFormDanhGia()
        ]);
    }

    public function actionChuongTrinhHoc()
    {
        $this->checkGetInput(['ca_day_id']);
        if ($this->dataGet['ca_day_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền ca_day_id");
        }
        $tienDo = TienDoKhoaHoc::findOne($this->dataGet['ca_day_id']);
        if (is_null($tienDo)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($tienDo->donDichVu->giao_vien_id !== $this->uid) {
            throw new HttpException(500, "Bạn không có quyền truy cập ca dạy này");
        }
        if (is_null($tienDo->donDichVu->goi_hoc_id)) {
            return $this->outputSuccess([]);
        }
        $goiHoc = json_decode($tienDo->donDichVu->goi_hoc_id);

        if (!is_array($goiHoc)) {
            return $this->outputSuccess([]);
        }
        if (count($goiHoc) == 0) {
            return $this->outputSuccess([]);
        }
        return $this->outputSuccess($tienDo->donDichVu->getChuongTrinhDay());
    }


    public function actionLienHeVaTroGiup()
    {
        $cauHinh = new CauHinh();
        $image = CauHinh::findOne(19);
        return $this->outputSuccess([
            'image' => CauHinh::getImage($image->image),
            'donViChuQuan' => $cauHinh->getNoiDung(7),
            'truSo' => $cauHinh->getNoiDung(8),
            'website' => $cauHinh->getNoiDung(9),
            'hotline' => $cauHinh->getNoiDung(20),
            'email' => $cauHinh->getNoiDung(2),
            'nhanLich' => $cauHinh->getNoiDung(17),
            'rutTien' => $cauHinh->getNoiDung(18),
        ]);
    }

    public function actionThongTinCaNhan()
    {
        $user = User::findOne($this->uid);
        return $this->outputSuccess([
            'id' => $user->id,
            'anh_nguoi_dung' => $user->getImage(),
            'hoten' => $user->hoten,
            'ngay_sinh' => date("d/m/Y", strtotime($user->ngay_sinh)),
            'dia_chi' => $user->dia_chi,
            'email' => $user->email,
            'cmnd_cccd' => $user->cmnd_cccd,
            'bang_cap' => $user->getBangCap(),
            'trinh_do' => $user->getTrinhDo(),
            'dien_thoai' => $user->dien_thoai,
            'vi_dien_tu' => $user->vi_dien_tu,
            'chu_tai_khoan' => $user->chu_tai_khoan,
            'so_tai_khoan' => $user->so_tai_khoan,
            'ten_ngan_hang' => $user->ten_ngan_hang,
            'chung_chi' => $user->getChungChi()
        ]);

    }

    public function actionCapNhatThongTinCaNhan()
    {
        $user = User::findOne($this->uid);
        $fields = [];
        if (isset($this->dataPost['hoten'])) {
            $fields['hoten'] = $this->dataPost['hoten'];
        }
        if (isset($this->dataPost['ngay_sinh'])) {
            $fields['ngay_sinh'] = myAPI::convertDMY2YMD($this->dataPost['ngay_sinh']);
        }
        if (isset($this->dataPost['dia_chi'])) {
            $fields['dia_chi'] = $this->dataPost['dia_chi'];
        }
        if (isset($this->dataPost['email'])) {
            if ($this->dataPost['email'] != "") {
                if (!$this->validateEmail($this->dataPost['email'])) {
                    throw new HttpException(500, "Email không đúng định dạng!");
                }
                $emailOld = User::find()->andFilterWhere(['email' => $this->dataPost['email']])
                    ->andFilterWhere(['<>', 'id', $this->uid])
                    ->all();
                if (count($emailOld) > 0) {
                    throw new HttpException(500, "Email đã tồn tại!");
                }
            }
            $fields['email'] = $this->dataPost['email'];
        }

        if (isset($this->dataPost['cmnd_cccd'])) {
            $fields['cmnd_cccd'] = $this->dataPost['cmnd_cccd'];
        }
        if (isset($this->dataPost['bang_cap']) || isset($this->dataPost['truong_dao_tao']) || isset($this->dataPost['chuyen_nganh'])) {
            $this->checkField(['chuyen_nganh', 'truong_dao_tao', 'bang_cap']);
            $fields['bang_cap'] = json_encode([
                'trinh_do' => $this->dataPost['bang_cap'],
                'chuyen_nganh' => $this->dataPost['chuyen_nganh'],
                'truong_dao_tao' => $this->dataPost['truong_dao_tao'],
            ]);

        }
        if (isset($this->dataPost['dien_thoai_du_phong'])) {
            $fields['dien_thoai_du_phong'] = $this->dataPost['dien_thoai_du_phong'];
        }

        // Up load file ảnh đại diện lên server
        $file = UploadedFile::getInstanceByName('anh_nguoi_dung');
        if (!empty($file)) {
            $path = (dirname(dirname(__DIR__))) . '/upload-file';
            $link = date('Y/m/d') . '/' . '_' . \Yii::$app->security->generateRandomString() . $this->get_extension_image($file->type);
            $fields['anh_nguoi_dung'] = $link;
            if (FileHelper::createDirectory($path . '/' . date('Y/m/d') . '/', $mode = 0775, $recursive = true)) {
                $file->saveAs($path . '/' . $link);
            }
        }
        //Up load file anh chứng chỉ lên server
        $files = UploadedFile::getInstancesByName('chung_chi');
        if (count($files) > 0) {
            foreach ($files as $file) {
                $path = (dirname(dirname(__DIR__))) . '/upload-file';
                $link = date('Y/m/d') . '/' . '_' . \Yii::$app->security->generateRandomString() . $this->get_extension_image($file->type);
                if (FileHelper::createDirectory($path . '/' . date('Y/m/d') . '/', $mode = 0775, $recursive = true)) {
                    $file->saveAs($path . '/' . $link);
                }
                //Lưu tên file anh chứng chỉ
                $chungChi = new ChungChi();
                $chungChi->user_id = $user->id;
                $chungChi->file_path = $link;
                if (!$chungChi->save()) {
                    throw new HttpException(500, Html::errorSummary($chungChi));
                }
            }
        }
        //Update các thông tin thành viên
        $user->updateAttributes($fields);
        return $this->outputSuccess("", "Cập nhật thông tin thành công");
    }

    public function actionDoiMatKhau()
    {
        $this->checkField(['password_old', 'password', 'password_confirm']);
        if ($this->dataPost['password_old'] == "")
            throw new HttpException(500, 'Vui lòng điền mật khẩu cũ');
        if ($this->dataPost['password'] == "")
            throw new HttpException(500, 'Vui lòng điền mật khẩu mới');
        if ($this->dataPost['password_confirm'] !== $this->dataPost['password']) {
            throw new HttpException(500, 'Nhập lại mật khẩu không chính xác');
        }
        $user = User::findOne($this->uid);
        if (!\Yii::$app->security->validatePassword($this->dataPost['password_old'], $user->password_hash))
            throw new HttpException(500, 'Mật khẩu cũ của bạn không chính xác');
        if (strlen($this->dataPost['password']) < 6)
            throw new HttpException(500, 'Mật khẩu tối thiểu 6 kí tự');
        $user->updateAttributes(['password_hash' => \Yii::$app->security->generatePasswordHash(($this->dataPost['password']))]);
        return $this->outputSuccess("", 'Đổi mật khẩu thành công, vui lòng đăng nhập lại');
    }

    public function actionCapNhatThongTinNganHang()
    {
        $user = User::findOne($this->uid);
        $this->checkField(['ten_ngan_hang', 'so_tai_khoan', 'chu_tai_khoan']);
        $user->updateAttributes([
            'ten_ngan_hang' => $this->dataPost['ten_ngan_hang'],
            'so_tai_khoan' => $this->dataPost['so_tai_khoan'],
            'chu_tai_khoan' => $this->dataPost['chu_tai_khoan'],
        ]);
        return $this->outputSuccess("", "Cập nhật thông tin ngân hàng");
    }

    public function actionGetBank()
    {
        $fields = [
            'id',
            'name',
            'code',
            'bin',
            'shortName',
            'logo',
            'transferSupported',
            'lookupSupported',
            'short_name',
            'support',
            'isTransfer',
            'swift_code',
        ];
        $data = [];
        $bank = (json_decode($this->getBank()))->data;
        foreach ($bank as $item) {
            $bankitem = new Bank();
            foreach ($fields as $field) {
                $bankitem->{$field} = $item->{$field};
            }
            if (!$bankitem->save()) {
                throw new HttpException(500, Html::errorSummary($bankitem));
            };
        }
        return $this->outputSuccess($data);
    }

    public function getBank()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.vietqr.io/v2/banks',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Cookie: connect.sid=s%3AvppDzrIjxmmrxN9cgJCuNQQz7oNMJLx2.2LZ4tdgwdmlGsN3zf011UzGLR7NIVj%2BuFJ9QP2c9GQQ'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public function actionDanhSachNganHang()
    {
        $this->checkGetInput(['tuKhoa']);
        $bank = Bank::find()
            ->select(['id', 'logo', 'code', 'name', 'short_name']);;
        if ($this->tuKhoa != "") {
            $bank->andFilterWhere(['like', 'hoten', $this->tuKhoa]);
        }
        $count = count($bank->all());
        $bank = $bank->orderBy(['short_name' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        return $this->outputListSuccess2($bank, $count);
    }

    public function actionLichSuTaiChinh()
    {
        $giaoDich = GiaoDich::find()
            ->andFilterWhere(['active' => 1, 'user_id' => $this->uid]);
        if ($this->tuKhoa != "") {
            $giaoDich->andFilterWhere(['like', 'tieu_de', $this->tuKhoa]);
        }
        $count = count($giaoDich->all());
        $giaoDich = $giaoDich->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy(['created' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        $data = [];
        if (count($giaoDich) > 0) {
            foreach ($giaoDich as $item) {
                /* @var $item GiaoDich */
                $taiKhoan = '*******' . substr($item->user->dien_thoai, strlen($item->user->dien_thoai) - 3, strlen($item->user->dien_thoai));
                $data[] = [
                    'id' => $item->id,
                    'created' => date('d/m/Y • H:i', strtotime($item->created)),
                    'type' => $item->type,
                    'tieu_de' => $item->tieu_de,
                    'so_tien' => $item->so_tien,
                    'noi_dung' => !is_null($item->donDichVu) ? $item->donDichVu->ma_don_hang : $taiKhoan,
                    'vi_dien_tu' => $item->vi_dien_tu,
                    'ghi_chu' => $item->ghi_chu,
                ];
            }
        }
        $user = User::findOne($this->uid);
        return $this->outputListSuccess2([
            'giaoDich' => $data,
            'id' => $user->id,
            'hoten' => $user->hoten,
            'anh_nguoi_dung' => $user->getImage(),
            'vi_dien_tu' => $user->vi_dien_tu,
        ], $count);
    }

    public function actionDanhSach()
    {
        $this->checkGetInput(['tuKhoa']);
        $khoaHoc = KhoaHoc::find()
            ->select(['tieu_de', 'image', 'id'])
            ->andFilterWhere(['active' => 1, 'type' => KhoaHoc::GIAO_VIEN]);
        if ($this->tuKhoa != "") {
            $khoaHoc->andFilterWhere(['like', 'tieu_de', $this->tuKhoa]);
        }
        $count = $khoaHoc->count();
        $khoaHoc = $khoaHoc->orderBy(['created' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        if (count($khoaHoc) > 0) {
            foreach ($khoaHoc as $item) {
                $item->image = CauHinh::getImage($item->image);
            }
        }
        return $this->outputSuccess($khoaHoc, $count);
    }

    /**
     * @throws HttpException
     */
    public function actionDanhSachHocPhan()
    {
        $this->checkGetInput(['khoa_hoc_id']);
        $user = User::findOne($this->uid);
        $data = [
            'coBan' => null,
            'nangCao' => null,
        ];
        $khoaHoc = KhoaHoc::findOne($this->dataGet['khoa_hoc_id']);
        if (is_null($khoaHoc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $khoaHoc = HocPhan::find()
            ->select(['tieu_de', 'image', 'id', 'type_id', 'khoa_hoc_id', 'cap_do_id'])
            ->andFilterWhere(['active' => 1, 'bat_khoa_hoc' => 1, 'khoa_hoc_id' => $this->dataGet['khoa_hoc_id']]);
        $khoaHoc = $khoaHoc->all();
        /** @var HocPhan $item */
        foreach ($khoaHoc as $item) {
            switch ($item->type_id) {
                case 54:
                {
                    if ($item->cap_do_id == 52) {
                        $data['coBan'][] = [
                            'id' => $item->id,
                            'tieu_de' => $item->tieu_de,
                            'image' => CauHinh::getImage($item->image),
                        ];
                    } else {
                        $data['nangCao'][] = [
                            'id' => $item->id,
                            'tieu_de' => $item->tieu_de,
                            'image' => CauHinh::getImage($item->image),
                        ];
                    }
                    break;
                }
                case 55:
                {
                    if ($user->khoa_tai_khoan == 0) {
                        if ($item->cap_do_id == 52) {
                            $data['coBan'][] = [
                                'id' => $item->id,
                                'tieu_de' => $item->tieu_de,
                                'image' => CauHinh::getImage($item->image),
                            ];
                        } else {
                            $data['nangCao'][] = [
                                'id' => $item->id,
                                'tieu_de' => $item->tieu_de,
                                'image' => CauHinh::getImage($item->image),
                            ];
                        }

                    }
                    break;
                }
                case 57:
                {
                    $dichVu = explode(',', $user->dich_vu);
                    if (count($dichVu) > 0) {
                        if (in_array($item->khoaHoc->dich_vu_id, $dichVu)) {
                            if ($item->cap_do_id == 52) {
                                $data['coBan'][] = [
                                    'id' => $item->id,
                                    'tieu_de' => $item->tieu_de,
                                    'image' => CauHinh::getImage($item->image),
                                ];
                            } else {
                                $data['nangCao'][] = [
                                    'id' => $item->id,
                                    'tieu_de' => $item->tieu_de,
                                    'image' => CauHinh::getImage($item->image),
                                ];
                            }
                        }
                    }
                    break;
                }
                case 56:
                {
                    if (in_array($this->uid, $item->getListGiaoVienDaGan())) {
                        if ($item->cap_do_id == 52) {
                            $data['coBan'][] = [
                                'id' => $item->id,
                                'tieu_de' => $item->tieu_de,
                                'image' => CauHinh::getImage($item->image),
                            ];
                        } else {
                            $data['nangCao'][] = [
                                'id' => $item->id,
                                'tieu_de' => $item->tieu_de,
                                'image' => CauHinh::getImage($item->image),
                            ];
                        }

                    }
                    break;
                }

            }
        }
        return $this->outputSuccess($data);
    }

    public function actionDanhSachBaiHoc()
    {
        $this->checkGetInput(['hoc_phan_id', 'tuKhoa']);
        if ($this->dataGet['hoc_phan_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền hoc_phan_id");
        }
        $hocPhan = HocPhan::findOne($this->dataGet['hoc_phan_id']);
        if (is_null($hocPhan)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $baiHoc = BaiHoc::find()
            ->select(['tieu_de', 'id', 'phan_tram'])
            ->andFilterWhere(['active' => 1, 'hoc_phan_id' => $this->dataGet['hoc_phan_id']]);
        if ($this->tuKhoa != "") {
            $baiHoc->andFilterWhere(['like', 'tieu_de', $this->tuKhoa]);
        }
        $count = $baiHoc->count();
        $baiHoc = $baiHoc->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy(['created' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        /** @var BaiHoc $item */
        foreach ($baiHoc as $item) {
            $item->phan_tram = $item->getPhanTramByGiaoVien($this->uid);
        }
        return $this->outputListSuccess2($baiHoc, $count);
    }

    public function actionChiTietBaiHoc()
    {
        $this->checkGetInput(['bai_hoc_id']);
        if ($this->dataGet['bai_hoc_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền bai_hoc_id");
        }
        $baiHoc = BaiHoc::findOne($this->dataGet['bai_hoc_id']);
        if (is_null($baiHoc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        return $this->outputSuccess([
            'id' => $baiHoc->id,
            'tieu_de' => $baiHoc->tieu_de,
            'cauHoi' => $baiHoc->cauHoi(),
            'baiKiemTra' => $baiHoc->kiemTra()
        ]);
    }

    public function actionDanhSachThongBao()
    {
        $this->checkGetInput(['tuKhoa']);
        $thongBao = ThongBao::find()
            ->select(['tieu_de', 'noi_dung', 'image', 'created', 'id', 'user_id'])
            ->andFilterWhere(['active' => 1]);
        $thongBaoUser = ArrayHelper::map(ThongBaoUser::find()->andFilterWhere(['user_id' => $this->uid])->all(), 'thong_bao_id', 'thong_bao_id');
        if (count($thongBaoUser) > 0) {
            $thongBao = $thongBao->andFilterWhere(['or',
                ['user_id' => $this->uid],
                ['in', 'id', $thongBaoUser]
            ]);
        } else {
            $thongBao = $thongBao->andFilterWhere(['user_id' => $this->uid]);
        }

        if ($this->tuKhoa != "") {
            $thongBao->andFilterWhere(['like', 'tieu_de', $this->tuKhoa]);
        }
        if ($this->dataGet['type'] != "") {
            $thongBao->andFilterWhere(['type_id' => $this->dataGet['type']]);
        }
        $count = count($thongBao->all());
        $thongBao = $thongBao->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy(['created' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        $data = [];
        if (count($thongBao) > 0) {
            foreach ($thongBao as $item) {
                /** @var ThongBao $item */
                $user = $item->user;
                $data[$item->getDate()]['date'] = $item->getDate();
                $data[$item->getDate()]['data'][] = [
                    'id' => $item->id,
                    'noi_dung' => $item->noi_dung,
                    'image' => CauHinh::getImage($item->image),
                    'created' => $item->getAfterTime(),
                    'tieu_de' => $item->tieu_de,
                    'user' => [
                        'id' => $user->id,
                        'hoten' => $user->hoten,
                        'anh_nguoi_dung' => $user->getImage(),
                    ],

                ];
            }
        }
        $data2 = [];
        foreach ($data as $item) {
            $data2[] = $item;
        }
        return $this->outputListSuccess2([
            'thong_bao' => $data2,
            'type' => $this->getDanhMuc(DanhMuc::THONG_BAO)
        ], $count);

    }

    public function actionGiaoVienHocTap()
    {
        $this->checkField(['bai_hoc_id', 'phan_tram']);
        if ($this->dataPost['bai_hoc_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền hoc_phan_id");
        }
        $baiHoc = BaiHoc::findOne($this->dataPost['bai_hoc_id']);
        if (is_null($baiHoc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }

        $gvHocTap = GiaoVienHocTap::findOne(['bai_hoc_id' => $baiHoc->id, 'giao_vien_id' => $this->uid, 'active' => 1]);
        if (is_null($gvHocTap)) {
            $gvHocTap = new GiaoVienHocTap();
        }
        $gvHocTap->bai_hoc_id = $baiHoc->id;
        $gvHocTap->giao_vien_id = $this->uid;
        $gvHocTap->user_id = $this->uid;
        $gvHocTap->phan_tram = intval($this->dataPost['phan_tram']);
        if (!$gvHocTap->save()) {
            throw new HttpException(500, Html::errorSummary($gvHocTap));
        }

        return $this->outputSuccess("", "Hoàn thành bài học thành công");
    }

    public function actionDanhSachDangHoc()
    {
        $ketQua = KetQuaDaoTao::find()
            ->select(['id', 'bai_hoc_id', 'trang_thai', 'created'])
            ->andFilterWhere(['active' => 1, 'giao_vien_id' => $this->uid]);
        $count = $ketQua->count();
        $ketQua = $ketQua->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy(['created' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        $data = [];
        /** @var KetQuaDaoTao $item */
        foreach ($ketQua as $item) {
            $baiHoc = $item->baiHoc;
            $hocPhan = $baiHoc->hocPhan;
            $khoaHoc = $hocPhan->khoaHoc;
            $data [] = [
                'id' => $item->id,
                'chuongTrinh' => $khoaHoc->tieu_de,
                'created' => date('d/m/Y', strtotime($item->created)),
                'capDo' => $hocPhan->capDo->name,
                'hocPhan' => $hocPhan->tieu_de,
                'baiHoc' => $baiHoc->tieu_de,
                'trang_thai' => $item->getTrangThai()
            ];
        }
        return $this->outputListSuccess2($data, $count);
    }

    public function actionDanhSachThanhTuu()
    {
        $ketQua = KetQuaDaoTao::find()
            ->select(['id', 'bai_hoc_id', 'trang_thai', 'created'])
            ->andFilterWhere(['active' => 1, 'giao_vien_id' => $this->uid, 'trang_thai' => KetQuaDaoTao::DAT]);
        $count = $ketQua->count();
        $ketQua = $ketQua->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy(['created' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        $data = [];
        /** @var KetQuaDaoTao $item */
        foreach ($ketQua as $item) {
            $baiHoc = $item->baiHoc;
            $hocPhan = $baiHoc->hocPhan;
            $khoaHoc = $hocPhan->khoaHoc;
            $data [] = [
                'id' => $item->id,
                'chuongTrinh' => $khoaHoc->tieu_de,
                'created' => date('d/m/Y', strtotime($item->created)),
                'capDo' => $hocPhan->capDo->name,
                'hocPhan' => $hocPhan->tieu_de,
                'baiHoc' => $baiHoc->tieu_de,
                'trang_thai' => $item->getTrangThai()
            ];
        }
        return $this->outputListSuccess2($data, $count);
    }

    public function actionLuuBaiKiemTra()
    {
        $this->checkField(['bai_hoc_id', 'link']);
        if ($this->dataPost['bai_hoc_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền hoc_phan_id");
        }
        $baiHoc = BaiHoc::findOne($this->dataPost['bai_hoc_id']);
        if (is_null($baiHoc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($this->dataPost['link'] == "") {
            throw new HttpException(500, "Vui lòng truyền link kết quả");
        }
        $gvKiemTra = new KetQuaDaoTao();
        $gvKiemTra->bai_hoc_id = $baiHoc->id;
        $gvKiemTra->giao_vien_id = $this->uid;
        $gvKiemTra->user_id = $this->uid;
        $gvKiemTra->link = $this->dataPost['link'];
        $gvKiemTra->trang_thai = KetQuaDaoTao::DANG_CHO_DUYET;
        if (!$gvKiemTra->save()) {
            throw new HttpException(500, Html::errorSummary($gvKiemTra));
        }
        return $this->outputSuccess("", "Hoàn thành bài học thành công");
    }

    public function actionKetQuaDaoTao()
    {
        $this->checkGetInput(['ket_qua_id']);
        if ($this->dataGet['ket_qua_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền ket_qua_id");
        }
        $ketQua = KetQuaDaoTao::findOne($this->dataGet['ket_qua_id']);
        if (is_null($ketQua)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $giaoVien = $ketQua->giaoVien;
        $baiHoc = $ketQua->baiHoc;
        $hocPhan = $baiHoc->hocPhan;
        $khoaHoc = $hocPhan->khoaHoc;
        return $this->outputSuccess([
            'giaoVien' => is_null($giaoVien) ? null : [
                'id' => $giaoVien->id,
                'hoten' => $giaoVien->hoten,
                'trinh_do' => $giaoVien->getTrinhDo(),
                'danh_gia' => $giaoVien->danh_gia,
                'dien_thoai' => $giaoVien->dien_thoai,
                'anh_nguoi_dung' => $giaoVien->getImage()
            ],
            'id' => $ketQua->id,
            'chuongTrinh' => $khoaHoc->tieu_de,
            'created' => date('d/m/Y', strtotime($ketQua->created)),
            'capDo' => $hocPhan->capDo->name,
            'hocPhan' => $hocPhan->tieu_de,
            'baiHoc' => $baiHoc->tieu_de,
            'link' => $ketQua->link,
            'trang_thai' => $ketQua->getTrangThai()
        ]);
    }

    public function actionChiTietKetQuaHoanThanh()
    {
        $this->checkGetInput(['ket_qua_id']);
        if ($this->dataGet['ket_qua_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền ket_qua_id");
        }
        $ketQua = KetQuaDaoTao::findOne($this->dataGet['ket_qua_id']);
        if (is_null($ketQua)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($ketQua->trang_thai != KetQuaDaoTao::DAT) {
            throw new HttpException(500, "Kết quả đào tạo chưa đạt");
        }
        $baiHoc = $ketQua->baiHoc;
        $hocPhan = $baiHoc->hocPhan;
        $khoaHoc = $hocPhan->khoaHoc;
        return $this->outputSuccess([
            'id' => $ketQua->id,
            'chuongTrinh' => $khoaHoc->tieu_de,
            'created' => date('d/m/Y', strtotime($ketQua->created)),
            'capDo' => $hocPhan->capDo->name,
            'hocPhan' => $hocPhan->tieu_de,
            'baiHoc' => $baiHoc->tieu_de,
            'trang_thai' => $ketQua->getTrangThai()
        ]);
    }

    public function actionTrangThaiKetQuaDaoTao()
    {
        $trangThai = $this->getDanhMuc(DanhMuc::TRANG_THAI_KET_QUA_DAO_TAO);
        return $this->outputSuccess($trangThai);
    }

    public function actionDanhSachPhieuLuong()
    {
        $phieuLuong = PhieuLuong::find()
            ->andFilterWhere(['active' => 1, 'giao_vien_id' => $this->uid]);
        $nam = date("Y");
        if (isset($this->dataGet['nam'])) {
            if ($this->dataGet['nam'] != "") {
                $nam = $this->dataGet['nam'];
            }
        }
        $phieuLuong = $phieuLuong->andFilterWhere(['=', 'year(created)', $nam]);
        if ($this->tuKhoa != "") {
            $phieuLuong = $phieuLuong->andFilterWhere(['or',
                ['like', 'tieu_de', $this->tuKhoa],
                ['like', 'ghi_chu', $this->tuKhoa],
            ]);
        }
        $count = $phieuLuong->count();
        $phieuLuong = $phieuLuong->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy(['updated' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        $data = [];
        /** @var PhieuLuong $item */
        foreach ($phieuLuong as $item) {
            $data[] = [
                'created' => date('d/m/Y • H:i', strtotime($item->created)),
                'tieu_de' => $item->tieu_de,
                'id' => $item->id,
                'ghi_chu' => $item->ghi_chu,
            ];
        }
        return $this->outputListSuccess2($data, $count);
    }

    public function actionChiTietPhieuLuong()
    {

        $this->checkGetInput(['phieu_luong_id']);
        if ($this->dataGet['phieu_luong_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền phieu_luong_id");
        }
        $phieuLuong = PhieuLuong::findOne($this->dataGet['phieu_luong_id']);
        if (is_null($phieuLuong)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        return $this->outputSuccess([
            'thoi_gian' => date("d/m/Y", strtotime($phieuLuong->tu_ngay)) . " - " . date("d/m/Y", strtotime($phieuLuong->den_ngay)),
            'hoten' => $phieuLuong->giaoVien->hoten,
            'id' => $phieuLuong->id,
            'donDichVu' => json_decode($phieuLuong->chi_tiet_luong),
            'themGio' => $phieuLuong->them_gio,
            'tongThucTe' => $phieuLuong->tong_luong_thuc_te,
            'anTrua' => $phieuLuong->an_trua,
            'phuPhiKhac' => json_decode($phieuLuong->phu_phi_khac),
            'giamTru' => json_decode($phieuLuong->giam_tru),
            'thanhTien' => $phieuLuong->thanh_tien,
            'tongPhuPhi' => strval(floatval($phieuLuong->tong_phu_phi)),
            'tongGiamTru' => strval(floatval($phieuLuong->tong_giam_tru)),

        ]);
    }

    public function actionXacNhanPhieuLuong()
    {
        $this->checkField(['phieu_luong_id']);
        if ($this->dataPost['phieu_luong_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền phieu_luong_id");
        }
        $phieuLuong = PhieuLuong::findOne($this->dataPost['phieu_luong_id']);
        if (is_null($phieuLuong)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($phieuLuong->trang_thai != PhieuLuong::CHUA_XAC_NHAN) {
            throw new HttpException(500, "Phiếu lương đã xác nhận từ trước đó");
        }
        $phieuLuong->trang_thai = PhieuLuong::DA_XAC_NHAN;
        if (!$phieuLuong->save()) {
            throw new HttpException(500, Html::errorSummary($phieuLuong));
        }
        return $this->outputSuccess("", "Xác nhận phiếu lương thành công");
    }

    public function actionKhieuNai()
    {
        $this->checkField(['phieu_luong_id', 'noi_dung']);
        if ($this->dataPost['phieu_luong_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền phieu_luong_id");
        }
        $phieuLuong = PhieuLuong::findOne($this->dataPost['phieu_luong_id']);
        if (is_null($phieuLuong)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($phieuLuong->trang_thai != PhieuLuong::CHUA_XAC_NHAN) {
            throw new HttpException(500, "Phiếu lương đã xác nhận, không thể khiếu nại");
        }
        $khieuNai = new  KhieuNaiBangLuong();
        $khieuNai->noi_dung = $this->dataPost['noi_dung'];
        $khieuNai->user_id = $this->uid;
        $khieuNai->giao_vien_id = $this->uid;
        $khieuNai->phieu_luong_id = $phieuLuong->id;
        $khieuNai->trang_thai = KhieuNai::CHUA_XU_LY;
        if (!$khieuNai->save()) {
            throw new HttpException(500, Html::errorSummary($khieuNai));
        }
        return $this->outputSuccess("", "Khiếu nại phiếu lương thành công");
    }

    public function actionChiTietBanGiao()
    {
        $this->checkGetInput(['ca_day_id']);
        if ($this->dataGet['ca_day_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền ca_day_id");
        }

        $tienDo = TienDoKhoaHoc::findOne($this->dataGet['ca_day_id']);
        if (is_null($tienDo)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $banGiao = BanGiao::find()
            ->andFilterWhere(['giao_vien_id' => $this->uid])
            ->andFilterWhere(['don_dich_vu_id' => $tienDo->don_dich_vu_id])
            ->andFilterWhere(['trang_thai' => BanGiao::XAC_NHAN_BAN_GIAO])
            ->one();
        if (is_null($banGiao)) {
            throw new HttpException(500, "Buổi học hiện ko có giáo cụ");
        }
        /** @var BanGiao $banGiao */
        $giaoVien = $banGiao->giaoVien;
        return $this->outputSuccess([
            'id' => $banGiao->id,
            'giaoVien' => is_null($giaoVien) ? null : [
                'id' => $giaoVien->id,
                'hoten' => $giaoVien->hoten,
                'anh_nguoi_dung' => $giaoVien->getImage(),
                'trinh_do' => $giaoVien->getTrinhDo(),
                'dien_thoai' => $giaoVien->dien_thoai,
            ],
            'ngay_nhan' => date('d/m/Y', strtotime($banGiao->ngay_nhan)),
            'codeGiaoCu' => $banGiao->getCodeGiaoCu(),
            'ngay_tra' => is_null($banGiao->ngay_tra) ? null : date("d/m/Y", strtotime($banGiao->ngay_tra)),
            'ghi_chu' => $banGiao->ghi_chu,
        ]);
    }

    public function actionGiaoVienHoanTra()
    {
        $this->checkField(['id', 'ngay_tra', 'ghi_chu']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $banGiao = BanGiao::findOne(['id' => $this->dataPost['id'], 'giao_vien_id' => $this->uid]);
        if (is_null($banGiao)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($banGiao->trang_thai != BanGiao::XAC_NHAN_BAN_GIAO) {
            throw new HttpException(400, "Giáo cụ đã được trả trước đó, đang chờ xác nhận");
        }
        $banGiao->ngay_tra = myAPI::convertDMY2YMD($this->dataPost['ngay_tra']);
        $banGiao->trang_thai = BanGiao::CHUA_XU_LY;
        $banGiao->ghi_chu = $this->dataPost['ghi_chu'];
        if (!$banGiao->save()) {
            throw new HttpException(500, \yii\helpers\Html::errorSummary($banGiao));
        }
        return $this->outputSuccess("", "Đang xử lý bàn giao, vui lòng chờ!");
    }

    public function actionGetTrinhDoBangCap()
    {
        return $this->outputSuccess($this->getDanhMuc(DanhMuc::TRINH_DO_HOC_TAP));
    }

    public function actionHuyCa()
    {
        $this->checkField(['ca_day_id']);
        if ($this->dataPost['ca_day_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền ca_day_id");
        }
        $tienDo = TienDoKhoaHoc::findOne($this->dataPost['ca_day_id']);
        if (is_null($tienDo)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($tienDo->donDichVu->giao_vien_id !== $this->uid) {
            throw new HttpException(500, "Bạn không có quyền truy cập ca dạy này");
        }
        if ($tienDo->trang_thai !== TienDoKhoaHoc::CHUA_DAY) {
            throw new HttpException(500, "Ca dạy đang có trạng thái $tienDo->trang_thai");
        }
        if ($tienDo->buoi != $tienDo->donDichVu->getBuoiHienTai()) {
            throw new HttpException(500, "Buổi học hiện tại là {$tienDo->donDichVu->getBuoiHienTai()}");
        }
        $tienDo->trang_thai = TienDoKhoaHoc::DA_HUY;
        if (!$tienDo->save()) {
            throw new HttpException(500, Html::errorSummary($tienDo));
        }
        $donDichVu = $tienDo->donDichVu;
        $donDichVu->so_buoi = $donDichVu->so_buoi + 1;
        if (!$donDichVu->save()) {
            throw new HttpException(500, Html::errorSummary($tienDo));
        }
        $thongBao = new ThongBao();
        $thongBao->to_id = 60;
        $thongBao->type_id = 65;
        $thongBao->noi_dung =
            "Ca dạy của giáo viên đã hủy lúc " . date("H:i", strtotime($tienDo->gio_day)) . ". \nBạn vui lòng kiểm tra lại thông tin. \nChúc bạn may mắn!";
        $thongBao->tieu_de = "Hủy buổi học!";
        $this->saveThongBao($thongBao);
        $email = CauHinh::findOne(2)->ghi_chu;
        $emailAdmin = CauHinh::findOne(39)->content;
        $this->sendEMail('Trông trẻ Pro', $email, $emailAdmin, $thongBao->tieu_de, $thongBao->tieu_de, str_replace('\n','<br>',$thongBao->noi_dung));
        $this->sendEMail('Trông trẻ Pro', $email, $donDichVu->leaderKd->email, $thongBao->tieu_de, $thongBao->tieu_de, str_replace('\n','<br>',$thongBao->noi_dung));
        return $this->outputSuccess("", "Hủy buổi học thành công");
    }

    public function actionGetPhieuKhaoSat()
    {
        $this->checkGetInput(['don_dich_vu_id']);
        $nhanLich = NhanLich::find()->andFilterWhere(['giao_vien_id' => $this->uid, 'don_dich_vu_id' => $this->dataGet['don_dich_vu_id'], 'active' => 1])->one();
        /** @var NhanLich $nhanLich */
        if (is_null($nhanLich)) {
            throw new HttpException(500, "Không xác định dữ liệu");
        }
        return $this->outputSuccess($nhanLich->form_danh_gia);
    }

    public function actionGetPhieuDanDo()
    {
        $this->checkGetInput(['don_dich_vu_id']);
        $donDichVu = DonDichVu::findOne($this->dataGet['don_dich_vu_id']);
        if (is_null($donDichVu)) {
            throw new HttpException(500, "Không xác định dữ liệu");
        }
        return $this->outputSuccess($donDichVu->phieu_dan_do);
    }
}
