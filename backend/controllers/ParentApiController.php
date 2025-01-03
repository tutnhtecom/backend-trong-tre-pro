<?php namespace backend\controllers;

use backend\models\Banner;
use backend\models\CauHinh;
use backend\models\ChuongTrinhHoc;
use backend\models\DanhMuc;
use backend\models\DichVu;
use backend\models\DonDichVu;
use backend\models\GiaDichVu;
use backend\models\HoaDon;
use backend\models\KeHoachDay;
use backend\models\KetQuaDaoTao;
use backend\models\KhieuNai;
use backend\models\KhungThoiGian;
use backend\models\LichSuTrangThaiDon;
use backend\models\LichSuTrangThaiThanhToan;
use backend\models\LichSuViecLamGiaoVien;
use backend\models\NhanLich;
use backend\models\PhuPhi;
use backend\models\PhuPhiDichVu;
use backend\models\QuanLyUserVaiTro;
use backend\models\QuyenLoi;
use backend\models\ThongBao;
use backend\models\ThongBaoUser;
use backend\models\TienDoKhoaHoc;
use backend\models\TinTuc;
use common\models\User;
use Yii;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\VarDumper;
use yii\web\HttpException;
use yii\web\UploadedFile;

class ParentApiController extends CoreApiController
{
    public $vai_tro = User::PHU_HUYNH;
    public $dieu_khoan = 1;
    public $limit = 10;

    //Đăng kí
    public function actionRegister()
    {
        $this->checkField([
            'email',
            'dien_thoai',
            'password',
            'password_confirm'
        ]);
        $user = new User();
        if (isset($this->dataPost['hoten'])){
            if ($this->dataPost['hoten'] == "") {
                throw new HttpException(500, 'Vui lòng nhập họ tên');
            }
            $user->hoten = $this->dataPost['hoten'];
        }
        if ($this->dataPost['email'] == "") {
            throw new HttpException(500, 'Vui lòng nhập email');
        }
        if (!$this->validateEmail($this->dataPost['email'])) {
            throw new HttpException(500, 'Định dạnh email không hợp lệ');
        }
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
        $userEmail = QuanLyUserVaiTro::findOne(['email' => $this->dataPost['email'], 'status' => 10, 'vai_tro' => $this->vai_tro]);
        if (!is_null($userEmail)) {
            throw new HttpException(500, 'Email đã tồn tại');
        }
        $userOld = QuanLyUserVaiTro::findOne(['dien_thoai' => $this->dataPost['dien_thoai'], 'status' => 10, 'vai_tro' => User::PHU_HUYNH]);
        if (!is_null($userOld)) {
            throw new HttpException(500, 'Số điện thoại đã tồn tại');
        }
        //Save User
        $user->dien_thoai = $this->dataPost['dien_thoai'];
        $user->email = $this->dataPost['email'];
        $user->username = $this->dataPost['email'];
        $user->vai_tros = [$this->vai_tro];
        $user->password_hash = Yii::$app->security->generatePasswordHash($this->dataPost['password']);
        if (!$user->save()) {
            throw new HttpException(500, Html::errorSummary($user));
        };
        return $this->outputSuccess("", 'Đăng kí tài khoản thành công');
    }

    //Xong
    public function actionDieuKhoan()
    {
        $dieuKhoan = CauHinh::findOne($this->dieu_khoan);
        $arr = explode('<br />', nl2br($dieuKhoan->content));
        $results = [];
        foreach ($arr as $index => $item) {
            $results[] = trim($item);
        }
        return $this->outputSuccess(is_null($dieuKhoan) ? "" : join('<br>', $results));
    }

    //Đăng nhập
    public function actionLogin()
    {
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
            if ($user->active == 0) {
                $rand = rand(100000, 999999);
                if (isset($this->dataPost['email'])) {
                    $fields['ma_kich_hoat'] = $rand;
                    $email = CauHinh::findOne(2)->ghi_chu;
                    $this->sendEMail('Trông trẻ Pro', $email, $this->dataPost['email'], 'Phụ huynh', 'Kích hoạt tài khoản', '
                       Mã kích hoạt tài khoản: ' . $rand . '
                    ');
                }
                if (isset($this->dataPost['dien_thoai'])) {
                    $fields['ma_kich_hoat'] = $rand;
                }
            }
            $user->updateAttributes($fields);
            $user = QuanLyUserVaiTro::findOne(['id' => $user->id]);
            return $this->outputSuccess([
                'id' => $user->id,
                'anh_nguoi_dung' => CauHinh::getImage($user->anh_nguoi_dung),
                'auth_key' => $user->auth_key,
                'hoten' => $user->hoten,
                'vai_tro' => $user->vai_tro_name,
                'tai_nguyen' => CauHinh::getContent(29),
                'dieu_khoan' => CauHinh::getContent(30),
                'chinh_sach_dieu_khoan' => CauHinh::findOne(43)->getNoiDung(43),
                'chinh_sach_bao_mat' => CauHinh::findOne(42)->getNoiDung(42),
                'quy_che_hoan_huy' => CauHinh::findOne(44)->getNoiDung(44),
                'tai_lieu' => CauHinh::getContent(31),
                'facebook' => CauHinh::getContent(32),
                'youtube' => CauHinh::getContent(33),
                'web' => CauHinh::getContent(34),
            ], "Đăng nhập thành công");
        } else {
            throw new HttpException(500, 'Tài khoản hoặc mật khẩu không chính xác');
        }
    }

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
            'vai_tro' => $user->vai_tro_name,
            'tai_nguyen' => CauHinh::getContent(29),
            'dieu_khoan' => CauHinh::getContent(30),
            'tai_lieu' => CauHinh::getContent(31),
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
            'vai_tro' => $user->vai_tro_name,
            'tai_nguyen' => CauHinh::getContent(29),
            'dieu_khoan' => CauHinh::getContent(30),
            'tai_lieu' => CauHinh::getContent(31),
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

    //Xác thực tài khoản
    public function actionXacThucTaiKhoan()
    {
        $user = User::findOne($this->uid);
        $this->checkField(['ma_kich_hoat']);
        if ($this->dataPost['ma_kich_hoat'] == "") {
            throw new HttpException(500, 'Vui lòng nhập mã kích hoạt');
        }
        //Kiểm tra tài khoản đã kích hoạt chưa
        if ($user->active == 0) {
            if ($this->dataPost['ma_kich_hoat'] == $user->ma_kich_hoat) {
                $user->updateAttributes(['active' => 1, 'ma_kich_hoat' => null]);
            } else {
                throw new HttpException(500, 'Mã kích hoạt không đúng');
            }
        } else {
            $user->updateAttributes(['auth_key' => null]);
            throw new HttpException(500, 'Tài khoản bạn đã được kích hoạt vui lòng thoát ra và đăng nhập lại');
        }
        return $this->outputSuccess("", "Kích hoạt tài khoản thành công");
    }

    //Tạo mật khẩu mới
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

    //Gưỉ lại mã kích hoạt
    public function actionGuiLaiMaKichHoat()
    {
        $user = User::findOne($this->uid); // Kiểm tra user
        //Kiểm tra truyền sang là điện thoại hay email
        $this->checkField(['is_email']);
        // Kiểm tra tài khoản này đã kích hoạt chưa,  nếu rồi cho đăng xuất
        if ($user->active == 0) {
            $rand = rand(100000, 999999);
            //Nếu là email thì gui mã qua email
            if ($this->dataPost['is_email'] == 1) {
                $fields['ma_kich_hoat'] = $rand;
                $email = CauHinh::findOne(2)->ghi_chu;
                $this->sendEMail('Trông trẻ Pro', $email, $user->email, 'Phụ huynh', 'Kích hoạt tài khoản', '
                       Mã kích hoạt tài khoản: ' . $rand . '
                    ');
            } else {
                // Nếu là điện thoại thì gửi mã xác nhận qua số điện thoại
                $fields['ma_kich_hoat'] = $rand;
            }
            //Update mã kích hoạt
            $user->updateAttributes($fields);
        } else {
            $user->updateAttributes(['auth_key' => null]);
            throw new HttpException(500, 'Tài khoản bạn đã được kích hoạt vui lòng thoát ra và đăng nhập lại');
        }
        return $this->outputSuccess("", 'Gửi lại mã thành công, bạn vui lòng kiểm tra lại ' . ($this->dataPost['is_email'] == 1 ? "email" : "điện thoại"));
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
//        throw new HttpException(500,json_encode($this->dataPost));
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
            'vai_tro' => $user->vai_tro_name,
        ]);
    }

    //Home
    public function actionHome()
    {
        $banner = Banner::find()->select(['id', 'link', 'image'])->andFilterWhere(['active' => 1, 'status' => 1]);
        $banner = $banner->all();
        /** @var Banner $item */
        foreach ($banner as $item) {
            $item->image = CauHinh::getImage($item->image);
        }
        $dichVu = DichVu::find()->andFilterWhere(['active' => 1])->select(['id', 'ten_dich_vu', 'image', 'khoa_dich_vu'])->orderBy(['seq'=>SORT_ASC]);
        $dichVu = $dichVu->all();
        foreach ($dichVu as $item) {
            $item->image = CauHinh::getImage($item->image);
        }
        return $this->outputSuccess([
            'dichVu' => $dichVu,
            'tinTuc' => $this->getDanhMuc(DanhMuc::LOAI_TIN_TUC),
            'banner' => $banner
        ]);
    }

    //Tin tức
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
        if ($this->dataGet['tuKhoa'] != "") {
            $moiNhat->andFilterWhere(['like', 'tieu_de', $this->dataGet['tuKhoa']]);
        }
        $count = $moiNhat->count();
        $data = [];
        foreach ($moiNhat->createCommand()->queryAll() as $item) {
            $item['id'] = intval($item['id']);
            $item['anh_dai_dien'] = CauHinh::getImage($item['anh_dai_dien']);
            $data [] = $item;
        }
        return $this->outputListSuccess2([
            'tinTuc' => $data,
            'types' => $types
        ], $count);
    }

    //Chi tiết dịch vụ
    public function actionChiTietDichVu()
    {
        $this->checkGetInput(['id']);
        if ($this->dataGet['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền tham số id");
        }
        $dichVu = DichVu::findOne(['id' => $this->dataGet['id'], 'active' => 1]);
        if (is_null($dichVu)) {
            throw new HttpException(500, "Không xác định dịch vụ");
        }
        $quyenLoi = QuyenLoi::find()->select(['id', 'name', 'link'])->andFilterWhere(['active' => 1, 'dich_vu_id' => $dichVu->id])->all();
        $giaDichVu = GiaDichVu::find()->andFilterWhere(['dich_vu_id' => $dichVu->id, 'active' => 1])->select(['tong_tien'])->orderBy(['tong_tien' => SORT_ASC])->one();
        $canKet = $dichVu->cam_ket;

        return $this->outputSuccess([
            'id' => $dichVu->id,
            'ten_dich_vu' => $dichVu->ten_dich_vu,
            'image' => CauHinh::getImage($dichVu->image),
            'doTuoi' => $dichVu->doTuoi->name,
            'quyenLoi' => $quyenLoi,
            'gia_tri' => $dichVu->gia_tri,
            'hop_dong_dich_vu' => $dichVu->hop_dong_dich_vu,
            'cam_ket' => $canKet,
            'link' => $dichVu->link,
            'so_tien' => is_null($giaDichVu) ? 0 : intval($giaDichVu->tong_tien)
        ]);
    }

    public function actionLoadFormGoiHocPhi()
    {
        $this->checkGetInput(['chon_ca_id']);
        if ($this->dataGet['chon_ca_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền tham số chon_ca_id");
        }
        $ca = DanhMuc::findOne(['id' => $this->dataGet['chon_ca_id'], 'active' => 1, 'type' => DanhMuc::CHON_CA]);
        if (is_null($ca)) {
            throw new HttpException(500, "Không xác định dịch vụ");
        }
        if ($ca->name == "Cả ngày") {
            $anTrua = DanhMuc::find()->andFilterWhere(['type' => 'Ăn trưa', 'active' => 1])->all();
            $dataAnTrua = [];
            /** @var DanhMuc $item */
            foreach ($anTrua as $item) {
                $ghiChu = json_decode($item->ghi_chu);
                $dataAnTrua[] = [
                    'id' => $item->id,
                    'tieu_De' => $item->name,
                    'ghi_chu' => $ghiChu->ghi_chu,
                    'tong_tien' => $ghiChu->tong_tien
                ];
            }
        } else {
            $dataAnTrua = null;
        }
        $themGio = DanhMuc::find()->andFilterWhere(['type' => 'Thêm giờ', 'active' => 1])->all();
        $dataThemGio = [];
        /** @var DanhMuc $item */
        foreach ($themGio as $item) {
            $ghiChu = json_decode($item->ghi_chu);
            $dataThemGio[] = [
                'id' => $item->id,
                'tieu_De' => $item->name,
                'ghi_chu' => $ghiChu->ghi_chu,
                'tong_tien' => $ghiChu->tong_tien
            ];
        }
        $loaiGiaoVien = [
            ['id' => 26, 'name' => 'Chuyên viên'],
        ];
        if (isset($this->dataGet['dich_vu_id'])) {
            $dichVu = DichVu::findOne($this->dataGet['dich_vu_id']);
            if (!is_null($dichVu)){
                if ($dichVu->loai_dich_vu_id == DichVu::CHAM_SOC_TRE){
                    $loaiGiaoVien = [
                        ['id' => 26, 'name' => 'Chuyên viên'],
                        ['id' => 27, 'name' => 'Nhân viên'],
                    ];
                }
            }
        }
        return $this->outputSuccess([
            'loaiGiaoVien'=>$loaiGiaoVien,
            'anTrua' => $dataAnTrua,
            'themGio' => $dataThemGio
        ]);
    }

    public function actionDanhSachGiaBuoiHoc()
    {
        $this->checkGetInput(['dich_vu_id', 'trinh_do', 'khung_gio_id']);
        if (!in_array($this->dataGet['trinh_do'], [26, 27])) {
            throw new HttpException(500, "Loại giáo viên không hợp lệ");
        }
        $giaBuoiHoc = GiaDichVu::find()
            ->select(['id', 'so_buoi', 'khuyen_mai', '(tong_tien*so_buoi) as tong_tien', '(tong_tien*so_buoi-tong_tien*so_buoi*khuyen_mai/100) as thanh_tien'])
            ->andFilterWhere(['active' => 1, 'dich_vu_id' => $this->dataGet['dich_vu_id'], 'khung_gio_id' => $this->dataGet['khung_gio_id']]);
        $giaBuoiHoc->andFilterWhere(['trinh_do' => $this->dataGet['trinh_do']]);
        $count = count($giaBuoiHoc->all());
        $giaBuoiHoc = $giaBuoiHoc->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy(['ID' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
//        throw new HttpException(500,$this->dataGet['khung_gio_id']);
        return $this->outputListSuccess2($giaBuoiHoc, $count);
    }

    /**
     * @throws HttpException
     */
    public function actionTaoDon()
    {
        $fields = [
            'dich_vu_id',
            'dia_chi',
            'thu',
            'thoi_gian_bat_dau',
            'chon_ca_id',
            'loai_giao_vien',
            'so_luong_be',
            'goi_hoc_phi_id',
            'hoc_phi',
            'phu_cap',
            'tong_tien',
            'ghi_chu',
            'hinh_thuc_thanh_toan_id',

        ];
        $this->checkField([
            'dich_vu_id',
            'dia_chi',
            'thu',
            'thoi_gian_bat_dau',
            'chon_ca_id',
            'loai_giao_vien',
            'so_luong_be',
            'an_trua_id',
            'them_gio_id',
            'goi_hoc_phi_id',
            'hoc_phi',
            'phu_cap',
            'tong_tien',
            'ghi_chu',
            'hinh_thuc_thanh_toan_id',
        ]);
        if ($this->dataPost['dia_chi'] == "" || $this->dataPost['dia_chi'] == "null") {
            throw new HttpException(500, "Vui lòng nhập địa chỉ");
        }
        if ($this->dataPost['thoi_gian_bat_dau'] == "") {
            throw new HttpException(500, "Vui lòng chọn ngày bắt đầu");
        }
        if ($this->dataPost['thu'] == "") {
            throw new HttpException(500, "Vui lòng chọn thứ");
        }
        if ($this->dataPost['chon_ca_id'] == "") {
            throw new HttpException(500, "Vui lòng chọn ca học");
        }
        if ($this->dataPost['so_luong_be'] == "") {
            throw new HttpException(500, "Vui lòng chọn số lượng bé");
        }
        if ($this->dataPost['an_trua_id'] == "") {
            throw new HttpException(500, "Vui lòng chọn ăn trưa");
        }
        if ($this->dataPost['them_gio_id'] == "") {
            throw new HttpException(500, "Vui lòng chọn thêm giờ");
        }
        if ($this->dataPost['goi_hoc_phi_id'] == "") {
            throw new HttpException(500, "Vui lòng chọn gói học");
        }
        $dangKi = new DonDichVu();
        foreach ($fields as $field) {
            $dangKi->{$field} = $this->dataPost[$field];
        }
        $dangKi->user_id = $this->uid;
        $dangKi->phu_huynh_id = $this->uid;
        $dangKi->trang_thai = LichSuTrangThaiDon::CHUA_CO_GIAO_VIEN;
        $dangKi->trang_thai_thanh_toan = LichSuTrangThaiThanhToan::CHUA_THANH_TOAN;
        if (isset($this->dataPost['gio_bat_dau'])) {
            if ($dangKi->dichVu->loai_dich_vu_id == DichVu::CHAM_SOC_TRE) {
                if (strtotime($this->dataPost['gio_bat_dau']) > strtotime('08:00:00')&&in_array($dangKi->chonCa->type,[10,11])) {
                    throw new HttpException(500, "Giờ bắt đầu cần trước 8:00");
                }
                if (strtotime($this->dataPost['gio_bat_dau']) > strtotime('14:00:00')&&in_array($dangKi->chonCa->type,[12])) {
                    throw new HttpException(500, "Giờ bắt đầu cần trước 14:00");
                }
                if (strtotime($this->dataPost['gio_bat_dau']) > strtotime('18:00:00')&&in_array($dangKi->chonCa->type,[13])) {
                    throw new HttpException(500, "Giờ bắt đầu cần trước 18:00");
                }
            }
            $dangKi->gio_bat_dau = $this->dataPost['gio_bat_dau'];
        }
        if (!$dangKi->save()) {
            throw new HttpException(500, Html::errorSummary($dangKi));
        } else {
            if (isset($this->dataPost['hoa_don_id'])) {
                if ($this->dataPost['hoa_don_id'] !== "") {
                    $hoaDon = HoaDon::findOne($this->dataPost['hoa_don_id']);
                    $hoaDon->don_dich_vu_id = $dangKi->id;
                    $hoaDon->save();
                }

            }
            $anTrua = DanhMuc::findOne($this->dataPost['an_trua_id']);
            if (!is_null($anTrua)) {
                $ghiChuAnTrua = json_decode($anTrua->ghi_chu);
                $dangKi->updateAttributes(['ma_don_hang' => 'DH' . sprintf("%07d", $dangKi->id)]);
                $phuPhiAnTrua = new PhuPhi();
                $phuPhiAnTrua->don_dich_vu_id = $dangKi->id;
                $phuPhiAnTrua->user_id = $this->uid;
                $phuPhiAnTrua->type_id = DanhMuc::AN_TRUA;
                $phuPhiAnTrua->ghi_chu = $ghiChuAnTrua->ghi_chu;
                $phuPhiAnTrua->tong_tien = $ghiChuAnTrua->tong_tien;
                $phuPhiAnTrua->tieu_de = "Ăn trưa";
                if (!$phuPhiAnTrua->save()) {
                    throw new HttpException(500, Html::errorSummary($phuPhiAnTrua));
                }
            }
            $themGio = DanhMuc::findOne($this->dataPost['them_gio_id']);
            if (!is_null($themGio)) {
                $ghiChuThemGio = json_decode($themGio->ghi_chu);
                $phuPhiThemGio = new PhuPhi();
                $phuPhiThemGio->don_dich_vu_id = $dangKi->id;
                $phuPhiThemGio->user_id = $this->uid;
                $phuPhiThemGio->type_id = DanhMuc::THEM_GIO;
                $phuPhiThemGio->ghi_chu = $ghiChuThemGio->ghi_chu;
                $phuPhiThemGio->tong_tien = $ghiChuThemGio->tong_tien;
                $phuPhiThemGio->tieu_de = "Thêm giờ";
                if (!$phuPhiThemGio->save()) {
                    throw new HttpException(500, Html::errorSummary($phuPhiThemGio));
                }
            }
            $thongBao = new ThongBao();
            $thongBao->to_id = 61;
            $thongBao->type_id = 65;
            $thongBao->noi_dung = "Phụ huynh đăng kí khóa học thành công!. \nChương trình: "
                . $dangKi->dichVu->ten_dich_vu . ". \nBởi: " . $dangKi->phuHuynh->hoten .
                " • " . $dangKi->phuHuynh->getVaiTro();
            $thongBao->tieu_de = "Đăng kí khóa học";
            $this->saveThongBao($thongBao);
            $email = CauHinh::findOne(2)->ghi_chu;
            $emailAdmin = CauHinh::findOne(39)->content;
            $this->sendEMail('Trông trẻ Pro', $email, $emailAdmin, $thongBao->tieu_de, $thongBao->tieu_de, str_replace('\n','<br/>',$thongBao->noi_dung));
        }
        return $this->outputSuccess(["id" => $dangKi->id], "Đăng kí dịch vụ thành công");
    }

    public function actionThongTinHoaDon()
    {
        $user = User::findOne(['id' => $this->uid]);
        if ($user->ngay_sinh_cua_con != "") {
            $user->ngay_sinh_cua_con = date('Y', strtotime($user->ngay_sinh_cua_con));
        }
        return $this->outputSuccess([
            'hoten' => $user->hoten,
            'cmnd_cccd' => $user->cmnd_cccd,
            'dia_chi' => $user->dia_chi,
            'ma_so_thue' => $user->ma_so_thue,
            'email' => $user->email,
            'ho_ten_con' => $user->ho_ten_con,
            'ngay_sinh_cua_con' => $user->ngay_sinh_cua_con
        ]);
    }

    public function actionHoaDon()
    {
        $fields = [
            'ho_ten',
            'cmnd_cccd',
            'dia_chi',
            'ma_so_thue',
            'email',
            'ho_ten_con',
            'nam_sinh_cua_con',
            'don_dich_vu_id',
        ];
        $this->checkField([
            'ho_ten',
            'cmnd_cccd',
            'dia_chi',
            'email',
            'don_dich_vu_id',
        ]);
        if ($this->dataPost['ho_ten'] == "") {
            throw new HttpException(500, "Vui lòng nhập họ tên");
        }
        if ($this->dataPost['cmnd_cccd'] == "") {
            throw new HttpException(500, "Vui lòng nhập CMND/CCCD");
        }
        if ($this->dataPost['dia_chi'] == "") {
            throw new HttpException(500, "Vui lòng nhập địa chỉ");
        }
        if ($this->dataPost['email'] == "") {
            throw new HttpException(500, "Vui lòng nhập email");
        }
        if (!$this->validateEmail($this->dataPost['email'])) {
            throw new HttpException(500, "Email không đúng định dạng");

        }
        $hoaDon = new HoaDon();
        foreach ($fields as $field) {
            $hoaDon->{$field} = $this->dataPost[$field];
        }
        $hoaDon->user_id = $this->uid;
        if (!$hoaDon->save()) {
            throw new HttpException(500, Html::errorSummary($hoaDon));
        }
        return $this->outputSuccess($hoaDon->id, "Lưu thông tin hóa đơn thành công");
    }

    public function actionInfoBanking()
    {
        $this->checkGetInput(['don_dich_vu_id']);
        $dangKiDichVu = DonDichVu::findOne($this->dataGet['don_dich_vu_id']);
        if (is_null($dangKiDichVu)) {
            throw new HttpException(500, "Không xác định dữ liệu đăng kí");
        }
        $cauHinh = new CauHinh();
        $tenNganHang = $cauHinh->getContent($cauHinh->tenNganHang);
        $soTaiKhoan = $cauHinh->getContent($cauHinh->soTaiKhoan);
        $ghiChuChuyenKhoan = $cauHinh->getContent($cauHinh->ghiChuChuyenKhoan)." ".$dangKiDichVu->ma_don_hang;
        $chuTaiKhoan = $cauHinh->getContent($cauHinh->chuTaiKhoan);
        $img = "https://img.vietqr.io/image/" . $tenNganHang . "-" . $soTaiKhoan . "-compact2.png?amount=" . $dangKiDichVu->tong_tien . "&addInfo=" . $ghiChuChuyenKhoan . "&&accountName=" . $chuTaiKhoan;
        return $this->outputSuccess([
            'img' => $img,
            'ghiChu' => $ghiChuChuyenKhoan,
        ]);
    }

    public function actionXoaUserFacebook()
    {
        return $this->outputSuccess("", "Xóa dữ liệu thành công");
    }

    public function actionUploadImageBanking()
    {
        $this->checkField(['don_dich_vu_id']);
        $dangKiDichVu = DonDichVu::findOne($this->dataPost['don_dich_vu_id']);
        if (is_null($dangKiDichVu)) {
            throw new HttpException(500, "Không xác định dữ liệu đăng kí");
        }
        $files = UploadedFile::getInstanceByName('file');
        if (!empty($files)) {
            $path = (dirname(dirname(__DIR__))) . '/upload-file';
            $link = date('Y/m/d') . '/' . '_' . \Yii::$app->security->generateRandomString() . $this->get_extension_image($files->type);
            $dangKiDichVu->updateAttributes(['ghi_chu_thanh_toan' => $link]);
            if (FileHelper::createDirectory($path . '/' . date('Y/m/d') . '/', $mode = 0775, $recursive = true)) {
                $files->saveAs($path . '/' . $link);
            }
        }
//    else {
//      throw new HttpException(400, "Không tìm thấy file");
//    }
        return $this->outputSuccess('', "Lưu thông tin chuyển khoản thành công");
    }

    public function actionGetBank()
    {
        $data = [];
        $bank = (\GuzzleHttp\json_decode($this->getBank()))->data;
        foreach ($bank as $item) {
            $arr = [];
            $arr['id'] = $item->id;
            $arr['name'] = $item->name;
            $data[] = $arr;
        }
        return $this->outputSuccess([
            'data' => $data
        ]);
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

    public function actionChinhSuaThongTinCaNhan()
    {
        $user = User::findOne($this->uid);
        $fields = [];
        if (isset($this->dataPost['hoten'])) {
            $fields['hoten'] = $this->dataPost['hoten'];
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
        if (isset($this->dataPost['dia_chi'])) {
            $fields['dia_chi'] = $this->dataPost['dia_chi'];
        }
        $files = UploadedFile::getInstanceByName('anh_nguoi_dung');
        if (!empty($files)) {
            $path = (dirname(dirname(__DIR__))) . '/upload-file';
            $link = date('Y/m/d') . '/' . '_' . \Yii::$app->security->generateRandomString() . $this->get_extension_image($files->type);
            $fields['anh_nguoi_dung'] = $link;
            if (FileHelper::createDirectory($path . '/' . date('Y/m/d') . '/', $mode = 0775, $recursive = true)) {
                $files->saveAs($path . '/' . $link);
            }
        }
        $user->updateAttributes($fields);
        return $this->outputSuccess("", "Cập nhật thông tin thành công");
    }

    public function actionThongTinCaNhan()
    {
        $user = QuanLyUserVaiTro::findOne(['id' => $this->uid]);
        return $this->outputSuccess([
            'hoten' => $user->hoten,
            'vai_tro' => $user->vai_tro_name,
            'dien_thoai' => $user->dien_thoai,
            'email' => $user->email,
            'dia_chi' => $user->dia_chi,
            'anh_nguoi_dung' => CauHinh::getImage($user->anh_nguoi_dung),
        ]);
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
        ]);
    }

    public function actionGuiKhieuNai()
    {
        $this->checkField(['noi_dung']);
        if ($this->dataPost['noi_dung'] == "") {
            throw new HttpException(500, "Vui lòng nhập nội dung");
        }
        $khieuNai = new  KhieuNai();
        $khieuNai->noi_dung = $this->dataPost['noi_dung'];
        $khieuNai->user_id = $this->uid;
        if (!$khieuNai->save()) {
            throw new HttpException(500, Html::errorSummary($khieuNai));
        }
        return $this->outputSuccess("", "Gửi khiếu nại, góp ý thành công");
    }

    public function actionGioiThieuApp()
    {
        $cauHinh = CauHinh::findOne(13);
        return $this->outputSuccess([
            'title' => $cauHinh->name,
            'content' => $cauHinh->ghi_chu,
            'image' => CauHinh::getImage($cauHinh->image),
        ]);
    }

    public function actionHeSinhThaiGiaoDuc()
    {
        $cauHinh = CauHinh::findOne(14);
        return $this->outputSuccess([
            'image' => CauHinh::getImage($cauHinh->image),
            'content' => $cauHinh->ghi_chu
        ]);
    }

    public function actionDanhSachDon()
    {
        $donDichVu = DonDichVu::find()
            ->andFilterWhere(['phu_huynh_id' => $this->uid, 'active' => 1]);
        if ($this->dataGet['tuKhoa'] != "") {
            $donDichVu->andFilterWhere(['like', 'ma_don_hang', $this->dataGet['tuKhoa']]);
        }
        $count = $donDichVu->count();
        $donDichVu = $donDichVu->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy(['created' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        $data = [];
        if (count($donDichVu) > 0) {
            foreach ($donDichVu as $item) {
                /** @var $item DonDichVu */
                $data[] = [
                    'id' => $item->id,
                    'ma_don_hang' => $item->ma_don_hang,
                    'created' => date("d/m/Y • H:i", strtotime($item->created)),
                    'trang_thai' => $item->actionGetThoiGian(),
                    'soBuoiHoanThanh' => $item->getTrangThaiTienDo(),
                    'dichVu' => $item->dichVu->ten_dich_vu,
                    'chonCa' => $item->getCaDayName(),
                    'gio_bat_dau' => $item->gio_bat_dau,
                    'thoi_gian' => $item->getThoiGian()
                ];
            }
        }
        return $this->outputListSuccess2($data, $count);
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

    public function actionThongTinGiaoVien()
    {
        $this->checkGetInput(['id']);
        if ($this->dataGet['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $donDichVu = DonDichVu::findOne(['id' => $this->dataGet['id'], 'phu_huynh_id' => $this->uid]);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if (is_null($donDichVu->giaoVien)) {
            throw new HttpException(500, "Đang tìm giáo viên");
        }
        if ($donDichVu->phu_huynh_dong_thuan == 1) {
            throw new HttpException(500, "Đơn của bạn đang chờ giáo viên đồng thuận");
        }
        $giaoVien = $donDichVu->giaoVien;
        return $this->outputSuccess([
            'noi_dung_khao_sat' => $donDichVu->noi_dung_khao_sat,
            'giaoVien' => [
                'id' => $giaoVien->id,
                'anh_nguoi_dung' => $giaoVien->getImage(),
                'danh_gia' => $giaoVien->danh_gia,
                'hoten' => $giaoVien->hoten,
                'trinh_do' => $giaoVien->getTrinhDo(),
                'dien_thoai' => $giaoVien->dien_thoai,
            ],
            'leaderKD' => $donDichVu->getLeader()
        ]);
    }

    public function actionDongThuan()
    {
        $this->checkField(['id']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $donDichVu = DonDichVu::findOne(['id' => $this->dataPost['id'], 'phu_huynh_id' => $this->uid]);
        $giao_vien_id = $donDichVu->giao_vien_id;
        $don_dich_vu_id = $donDichVu->id;
       
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($donDichVu->trang_thai == LichSuTrangThaiDon::DANG_DAY) {
            throw new HttpException(500, "Khóa đang trong quá trình dạy!");
        }
        if (is_null($donDichVu->giao_vien_id)) {
            throw new HttpException(500, "Đơn hàng chưa có giáo viên");
        }
        $nhanLich = NhanLich::findOne(['giao_vien_id' => $donDichVu->giao_vien_id, 'don_dich_vu_id' => $donDichVu->id, 'active' => 1]);
        $donDichVu->phu_huynh_dong_thuan = 1;
        if ($donDichVu->giao_vien_dong_thuan == 1 && $donDichVu->phu_huynh_dong_thuan == 1) {
            $donDichVu->trang_thai = LichSuTrangThaiDon::DANG_DAY;
            if (is_null($nhanLich)) {
                $nhanLich = new NhanLich();
            }
            $nhanLich->user_id = $this->uid;
            $nhanLich->giao_vien_id = $donDichVu->giao_vien_id;
            $nhanLich->don_dich_vu_id = $donDichVu->id;
            $nhanLich->trang_thai = NhanLich::DANG_DAY;
            //Duyệt các  Tiến Độ khoá học khác với giáo viên dậy mới
            $tienDoKhoaHoc = TienDoKhoaHoc::find()->andFilterWhere([TienDoKhoaHoc::tableName() . '.don_dich_vu_id' => $don_dich_vu_id])
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
        } else {
            $thongBao = new ThongBao();
            $thongBao->to_id = 61;
            $thongBao->type_id = 65;
            $thongBao->noi_dung = "Phụ huynh đồng ý giáo viên " . $donDichVu->giaoVien->hoten . " phụ trách khóa học!. \nChương trình: "
                . $donDichVu->dichVu->ten_dich_vu . ". \nBởi: " . $donDichVu->phuHuynh->hoten .
                " • " . $donDichVu->phuHuynh->getVaiTro();
            $thongBao->tieu_de = "Phụ huynh đồng thuận";
            $this->saveThongBao($thongBao);
            $email = CauHinh::findOne(2)->ghi_chu;
            $emailAdmin = CauHinh::findOne(39)->content;
            $this->sendEMail('Trông trẻ Pro', $email, $emailAdmin, $thongBao->tieu_de, $thongBao->tieu_de, str_replace('\n','<br>',$thongBao->noi_dung));
            $this->sendEMail('Trông trẻ Pro', $email, $donDichVu->leaderKd->email, $thongBao->tieu_de, $thongBao->tieu_de, str_replace('\n','<br>',$thongBao->noi_dung));
        }
        return $this->outputSuccess("", "Đồng thuận thành công");
    }

    public function actionTuChoi()
    {
        $this->checkField(['id']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $donDichVu = DonDichVu::findOne(['id' => $this->dataPost['id'], 'phu_huynh_id' => $this->uid]);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($donDichVu->trang_thai == LichSuTrangThaiDon::DANG_DAY) {
            throw new HttpException(500, "Khóa đang trong quá trình dạy!");
        }
        if (is_null($donDichVu->giao_vien_id)) {
            throw new HttpException(500, "Đơn hàng chưa có giáo viên");
        }
        $giaoVien = $donDichVu->giaoVien;
        $donDichVu->giao_vien_id = null;
        if (!$donDichVu->save()) {
            throw new HttpException(500, Html::errorSummary($donDichVu));
        } else {
            $thongBao = new ThongBao();
            $thongBao->to_id = 61;
            $thongBao->type_id = 65;
            $thongBao->noi_dung = "Phụ huynh từ chối giáo viên " . $giaoVien->hoten . " phụ trách khóa học!. \nChương trình: "
                . $donDichVu->dichVu->ten_dich_vu . ". \nBởi: " . $giaoVien->hoten .
                " • " . $donDichVu->phuHuynh->getVaiTro();
            $thongBao->tieu_de = "Phụ huynh từ chối";
            $this->saveThongBao($thongBao);
            $email = CauHinh::findOne(2)->ghi_chu;
            $emailAdmin = CauHinh::findOne(39)->content;
            $this->sendEMail('Trông trẻ Pro', $email, $emailAdmin, $thongBao->tieu_de, $thongBao->tieu_de, str_replace('\n','<br>',$thongBao->noi_dung));
            $this->sendEMail('Trông trẻ Pro', $email, $donDichVu->leaderKd->email, $thongBao->tieu_de, $thongBao->tieu_de, str_replace('\n','<br>',$thongBao->noi_dung));
        }
        return $this->outputSuccess("", "Từ chối thành công");
    }

    public function actionThongTinKhoaHoc()
    {
        $this->checkGetInput(['id']);
        if ($this->dataGet['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $donDichVu = DonDichVu::findOne(['id' => $this->dataGet['id'], 'phu_huynh_id' => $this->uid]);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }

        if (intval($_GET['buoi']) == 0 || intval($_GET['buoi']) > $donDichVu->so_buoi) {
            throw new HttpException(500, "Thông tin buổi học không hợp lệ");
        }
        $giaoVien = $donDichVu->giaoVien;
        $tienDo = $donDichVu->tienDoKhoaHoc($this->dataGet['buoi']);
        return $this->outputSuccess([
            'id' => $donDichVu->id,
            'ma_don_hang' => $donDichVu->ma_don_hang,
            'gio_bat_dau' => $donDichVu->gio_bat_dau,
            'giaoVien' => is_null($giaoVien) ? null : [
                'id' => $giaoVien->id,
                'anh_nguoi_dung' => $giaoVien->getImage(),
                'danh_gia' => $giaoVien->danh_gia,
                'trang_thai' => $giaoVien->trang_thai_vao_ca,
                'hoten' => $giaoVien->hoten,
                'trinh_do' => $giaoVien->getTrinhDo(),
                'dien_thoai' => $giaoVien->dien_thoai,

            ],
            'dichVu' => $donDichVu->dichVu->ten_dich_vu,
            'soBuoiHoanThanh' => $donDichVu->getTrangThaiTienDo(),
            'tong_tien' => $donDichVu->tong_tien,
            'trang_thai_thanh_toan' => $donDichVu->trang_thai_thanh_toan,
            'lich_hoc' => $donDichVu->getNamebyThu(),
            'thoi_gian' => $donDichVu->getThoiGian(),
            'chonCa' => $donDichVu->getCaDayName(),
            'dia_chi' => $donDichVu->dia_chi,
            'so_gio' => $tienDo['so_gio'],
            'noi_dung_khao_sat' => $donDichVu->noi_dung_khao_sat,
            'tienDo' => $tienDo,
            'leaderKD' => $donDichVu->getLeader()
        ]);
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
            ],
            'dia_chi' => $donDichVu->dia_chi,
            'ghi_chu' => $donDichVu->ghi_chu,
            'noi_dung_khao_sat' => $donDichVu->noi_dung_khao_sat
        ]);
    }


    public function actionDanhGiaGiaoVien()
    {
        $this->checkField(['id', 'danh_gia', 'noi_dung_danh_gia']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $donDichVu = DonDichVu::findOne(['id' => $this->dataPost['id'], 'phu_huynh_id' => $this->uid]);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if (intval($this->dataPost['danh_gia']) == 0) {
            throw new HttpException(500, "Vui lòng chọn đánh giá");
        }
        if ($donDichVu->trang_thai != LichSuTrangThaiDon::HOAN_THANH) {
            throw new HttpException(500, "Khóa học chưa hoàn thành!");
        }
        $donDichVu->updateAttributes(['danh_gia' => $this->dataPost['danh_gia'], 'noi_dung_danh_gia' => $this->dataPost['noi_dung_danh_gia']]);
        $donDichVu->giaoVien->updateDanhGiaGiaoVien();
        $thongBao = new ThongBao();
        $thongBao->to_id = 61;
        $thongBao->type_id = 65;
        $thongBao->noi_dung =
            "Nội dung: " . $this->dataPost['noi_dung_danh_gia']
            . "\nKhóa học: " . $donDichVu->ma_don_hang
            . "\nGiáo viên: " . $donDichVu->giaoVien->hoten
            . "\nBởi: " . " • " . $donDichVu->phuHuynh->getVaiTro();
        $thongBao->tieu_de = "Đánh giá giáo viên";
        $this->saveThongBao($thongBao);
        $email = CauHinh::findOne(2)->ghi_chu;
        $emailAdmin = CauHinh::findOne(39)->content;
        $this->sendEMail('Trông trẻ Pro', $email, $emailAdmin, $thongBao->tieu_de, $thongBao->tieu_de, str_replace('\n','<br>',$thongBao->noi_dung));
        $this->sendEMail('Trông trẻ Pro', $email, $donDichVu->leaderKd->email, $thongBao->tieu_de, $thongBao->tieu_de, str_replace('\n','<br>',$thongBao->noi_dung));
        return $this->outputSuccess("", "Cám ơn Phụ huynh đã gửi đánh giá. Chúng tôi sẽ liên hệ lại sớm nhất có thể.");
    }

    public function actionChiTietDonHuy()
    {
        $this->checkGetInput(['id']);
        if ($this->dataGet['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $donDichVu = DonDichVu::findOne(['id' => $this->dataGet['id'], 'phu_huynh_id' => $this->uid]);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($donDichVu->trang_thai != LichSuTrangThaiDon::DA_HUY) {
            throw new HttpException(500, "Khóa học chưa bị hủy!");
        }
        return $this->outputSuccess([
            'id' => $donDichVu->id,
            'ma_don_hang' => $donDichVu->ma_don_hang,
            'trang_thai' => $donDichVu->trang_thai,
            'gio_bat_dau' => $donDichVu->gio_bat_dau,
            'dichVu' => $donDichVu->dichVu->ten_dich_vu,
            'soBuoiHoanThanh' => $donDichVu->getTrangThaiTienDo(),
            'tong_tien' => $donDichVu->tong_tien,
            'trang_thai_thanh_toan' => $donDichVu->trang_thai_thanh_toan,
            'lich_hoc' => $donDichVu->getNamebyThu(),
            'thoi_gian' => $donDichVu->getThoiGian(),
            'chonCa' => $donDichVu->getCaDayName(),
            'dia_chi' => $donDichVu->dia_chi,
            'so_gio' => $donDichVu->chonCa->so_gio
        ]);
    }

    public function actionDangTimGiaoVien()
    {
        $this->checkGetInput(['id']);
        if ($this->dataGet['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $donDichVu = DonDichVu::findOne(['id' => $this->dataGet['id'], 'phu_huynh_id' => $this->uid]);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }

        return $this->outputSuccess([
            'id' => $donDichVu->id,
            'ma_don_hang' => $donDichVu->ma_don_hang,
            'trang_thai' => $donDichVu->actionGetThoiGian(),
            'gio_bat_dau' => $donDichVu->gio_bat_dau,
            'dichVu' => $donDichVu->dichVu->ten_dich_vu,
            'soBuoiHoanThanh' => $donDichVu->getTrangThaiTienDo(),
            'tong_tien' => $donDichVu->tong_tien,
            'trang_thai_thanh_toan' => $donDichVu->trang_thai_thanh_toan,
            'lich_hoc' => $donDichVu->getNamebyThu(),
            'thoi_gian' => $donDichVu->getThoiGian(),
            'chonCa' => $donDichVu->getCaDayName(),
            'dia_chi' => $donDichVu->dia_chi,
            'so_gio' => $donDichVu->getSoGio(explode(' - ', $donDichVu->chonCa->khungGio->name)[0] . ":00"),
            'leaderKD' => $donDichVu->getLeader()
        ]);
    }

    public function actionHuyDon()
    {
        throw new HttpException(500, "Quý khách muốn hủy đơn dịch vụ liên hệ Hotline: 096.345.9888 để được hỗ trợ");

        $this->checkField(['id', 'li_do_huy']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $donDichVu = DonDichVu::findOne(['id' => $this->dataPost['id'], 'phu_huynh_id' => $this->uid]);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $donDichVu->trang_thai = LichSuTrangThaiDon::DA_HUY;
        $donDichVu->li_do_huy = $this->dataPost['li_do_huy'];
        if (!$donDichVu->save()) {
            throw new HttpException(500, \yii\helpers\Html::errorSummary($donDichVu));
        }
        return $this->outputSuccess('', 'Hủy đơn hàng thành công');
    }

    public function actionThongTinChiTietGiaoVien()
    {
        $this->checkGetInput(['giao_vien_id']);
        if ($this->dataGet['giao_vien_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền giao_vien_id");
        }
        $giaoVien = User::findOne($this->dataGet['giao_vien_id']);
        if (is_null($giaoVien)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }

        return $this->outputSuccess([
            'id' => $giaoVien->id,
            'anh_nguoi_dung' => $giaoVien->getImage(),
            'danh_gia' => $giaoVien->danh_gia,
            'hoten' => $giaoVien->hoten,
            'trinh_do' => $giaoVien->getTrinhDo(),
            'dien_thoai' => $giaoVien->dien_thoai,
            'ngay_sinh' => $giaoVien->ngay_sinh,
            'bang_cap' => $giaoVien->getBangCap(),
            'chung_chi' => $giaoVien->getChungChi(),
        ]);
    }

    public function actionLichSuCaDay()
    {
        $this->checkGetInput(['giao_vien_id']);
        if ($this->dataGet['giao_vien_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền giao_vien_id");
        }
        $giaoVien = User::findOne($this->dataGet['giao_vien_id']);
        if (is_null($giaoVien)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $donDichVu = LichSuViecLamGiaoVien::find()
            ->andFilterWhere(['giao_vien_id' => $this->dataGet['giao_vien_id'], 'trang_thai' => LichSuTrangThaiDon::HOAN_THANH, 'active' => 1])
            ->orderBy(['created' => $this->sort == 1 ? SORT_DESC : SORT_ASC]);
        $count = $donDichVu->count();
        $donDichVu = $donDichVu->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy(['created' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        $data = [];
        if (count($donDichVu) > 0) {
            foreach ($donDichVu as $item) {
                /** @var $item LichSuViecLamGiaoVien */
                $value = $item->donDichVu;
                $data[] = [
                    'id' => $value->id,
                    'ma_don_hang' => $value->ma_don_hang,
                    'created' => date("d/m/Y • H:i", strtotime($value->created)),
                    'trang_thai' => $item->trang_thai,
                    'dichVu' => $value->dichVu->ten_dich_vu,
                    'soBuoiHoanThanh' => $item->donDichVu->soBuoiGiaoVienHoanThanh(),
                    'noi_dung_danh_gia' => $value->giao_vien_id != $this->dataGet['giao_vien_id'] ? "" : $value->noi_dung_danh_gia
                ];
            }
        }
        return $this->outputListSuccess2($data, $count);
    }

    public function actionDanhSachKhungGio()
    {
        $this->checkGetInput(['type', 'dich_vu_id']);
        $khungGio = KhungThoiGian::find()
            ->select(['id', 'khung_gio', 'noi_dung'])
            ->andFilterWhere(['active' => 1, 'dich_vu_id' => $this->dataGet['dich_vu_id']]);
        if ($this->dataGet['type'] != "") {
            $khungGio->andFilterWhere(['type' => $this->dataGet['type']]);
        }
        $khungGio = $khungGio->all();
        /** @var KhungThoiGian $item */
        foreach ($khungGio as $item) {
            $item->khung_gio = $item->khungGio->name;
        }
        return $this->outputSuccess($khungGio);
    }

    public function actionGetCa()
    {
        $type = $this->getDanhMuc(DanhMuc::CHON_CA);
        return $this->outputSuccess($type);
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
        if ($this->dataGet['tuKhoa'] != "") {
            $thongBao->andFilterWhere(['like', 'tieu_de', $this->dataGet['tuKhoa']]);
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
        if ($tienDo->donDichVu->phu_huynh_id !== $this->uid) {
            throw new HttpException(500, "Bạn không có quyền truy cập ca dạy này");
        }
        if (is_null($tienDo->donDichVu->goi_hoc_id)) {
            return $this->outputSuccess([]);
        }
        $goiHoc = json_decode($tienDo->donDichVu->goi_hoc_id);
        if (count($goiHoc) == 0) {
            return $this->outputSuccess([]);
        }
        return $this->outputSuccess($tienDo->donDichVu->getChuongTrinhDay());
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
        if ($tienDo->donDichVu->phu_huynh_id !== $this->uid) {
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
                'video' => $tienDo->video,
                'danh_gia' => $tienDo->danh_gia
            ],
            'formDanhGia' => $tienDo->getFormDanhGia()
        ]);
    }

    public function actionDanhSachDaoTao()
    {
        $this->checkGetInput(['giao_vien_id']);
        if ($this->dataGet['giao_vien_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền giao_vien_id");
        }
        $giaoVien = User::findOne($this->dataGet['giao_vien_id']);
        if (is_null($giaoVien)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $ketQua = KetQuaDaoTao::find()
            ->select(['id', 'trang_thai', 'created', 'bai_hoc_id'])
            ->andFilterWhere(['active' => 1, 'giao_vien_id' => $this->dataGet['giao_vien_id'], 'trang_thai' => KetQuaDaoTao::DAT]);
        $count = $ketQua->count();
        $ketQua = $ketQua->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy(['created' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        $data = [];
        /** @var KetQuaDaoTao $item */
        foreach ($ketQua as $item) {
            $baiHoc = $item->baiHoc;
            $hocPhan = $baiHoc->hocPhan;
            $data [] = [
                'id' => $item->id,
                'created' => date('d/m/Y', strtotime($item->created)),
                'hocPhan' => $hocPhan->tieu_de,
                'trang_thai' => $item->getTrangThai()
            ];
        }
        return $this->outputListSuccess2($data, $count);
    }

    public function actionDanhGiaBuoiHoc()
    {
        $this->checkField(['ca_day_id', 'danh_gia', 'nhan_xet']);
        if ($this->dataPost['ca_day_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền ca_day_id");
        }
        $tienDo = TienDoKhoaHoc::findOne($this->dataPost['ca_day_id']);
        if (is_null($tienDo)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($tienDo->donDichVu->phu_huynh_id !== $this->uid) {
            throw new HttpException(500, "Bạn không có quyền truy cập ca dạy này");
        }
        if ($tienDo->trang_thai !== TienDoKhoaHoc::DA_HOAN_THANH) {
            throw new HttpException(500, "Ca dạy chưa hoàn thành");
        }
        if (intval($this->dataPost['danh_gia']) < 1 || intval($this->dataPost['danh_gia']) > 5) {
            throw new HttpException(500, "Vui lòng đánh giá");
        }
        $tienDo->phu_huynh_danh_gia = $this->dataPost['danh_gia'];
        $tienDo->phu_huynh_nhan_xet = $this->dataPost['nhan_xet'];
        if (!$tienDo->save()) {
            throw new HttpException(500, Html::errorSummary($tienDo));
        }
        return $this->outputSuccess("", "Đánh giá buổi học thành công");
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

    public function actionGetPhieuKhaoSat()
    {
        $this->checkGetInput(['don_dich_vu_id']);
        $donDichVu = DonDichVu::findOne($this->dataGet['don_dich_vu_id']);
        if (is_null($donDichVu)) {
            throw new HttpException(500, "Không xác định dữ liệu");
        }
        $nhanLich = NhanLich::find()->andFilterWhere(['giao_vien_id' => $donDichVu->giao_vien_id, 'don_dich_vu_id' => $this->dataGet['don_dich_vu_id'], 'active' => 1])->one();
        /** @var NhanLich $nhanLich */
        if (is_null($nhanLich)) {
            throw new HttpException(500, "Không xác định dữ liệu");
        }
        return $this->outputSuccess($nhanLich->form_danh_gia);
    }

}
