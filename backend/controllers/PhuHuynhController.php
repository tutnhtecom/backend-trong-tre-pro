<?php namespace backend\controllers;

use backend\models\CauHinh;
use backend\models\DonDichVu;
use backend\models\KhieuNai;
use backend\models\QuanLyUserVaiTro;
use common\models\User;
use Yii;
use yii\bootstrap\Html;
use yii\web\HttpException;

class PhuHuynhController extends CoreApiController
{
    public function actionDanhSach(){
        $this->checkGetInput(['tuKhoa']);
        $users = QuanLyUserVaiTro::find()
            ->select(['hoten','dien_thoai','id','anh_nguoi_dung','email'])
            ->andFilterWhere(['active'=>1,'is_admin'=>0,'status'=>10,'vai_tro'=>User::PHU_HUYNH])
        ;
        if ($this->dataGet['tuKhoa']!=""){
            $users->andFilterWhere(['or',
                ['like','hoten',$this->dataGet['tuKhoa']],
                ['like','dien_thoai',$this->dataGet['tuKhoa']],
            ]);
        }
        $count = count($users->all());
        $users = $users->limit($this->limit)->offset(($this->page-1)*$this->limit)->orderBy(['created_at'=>$this->sort==1?SORT_DESC:SORT_ASC])->all();
        $data = [];
        if (count($users)>0){
            /** @var QuanLyUserVaiTro $item */
            foreach ($users as $item){
                $item->anh_nguoi_dung = CauHinh::getServer().'/upload-file/'.($item->anh_nguoi_dung==null?"user-nomal.jpg":$item->anh_nguoi_dung);
                $data[]= $item;
            }
        }
        return $this->outputListSuccess2($data, $count);
    }
    public function actionChiTiet(){
        $this->checkGetInput(['id']);
        if ($this->dataGet['id']==""){
            throw new HttpException(500,"Vui lòng truyền id");
        }
        $user = QuanLyUserVaiTro::find()->select([
            'anh_nguoi_dung','id','danh_gia','hoten','vai_tro_name','dien_thoai','email','dia_chi',
            'ho_ten_con','ngay_sinh_cua_con','cmnd_cccd','ghi_chu'
        ])->andFilterWhere(['id'=>$this->dataGet['id'],'active'=>1,'status'=>10,'vai_tro'=>User::PHU_HUYNH])->one();
        if (is_null($user)){
            throw new HttpException(403,"Không xác định dữ liệu");
        }
        $user->anh_nguoi_dung = CauHinh::getServer().'/upload-file/'.($user->anh_nguoi_dung==null?"user-nomal.jpg":$user->anh_nguoi_dung);
        $user->ngay_sinh_cua_con =$user->ngay_sinh_cua_con==null?"":date('d/m/Y',strtotime($user->ngay_sinh_cua_con));
        return $this->outputSuccess($user);
    }
    public function actionDanhSachDon()
    {
        $this->checkGetInput(['phu_huynh_id']);
        $donDichVu = DonDichVu::find()
            ->andFilterWhere(['phu_huynh_id' => $this->dataGet['phu_huynh_id'],'active'=>1]);
        if ($this->dataGet['tuKhoa'] != "") {
            $donDichVu->andFilterWhere(['like', 'ma_don_hang', $this->dataGet['tuKhoa']]);
        }
        $count = $donDichVu->count();
        $donDichVu = $donDichVu->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy(['created' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        $data = [];
        if (count($donDichVu) > 0) {
            foreach ($donDichVu as $item) {
                /** @var $item DonDichVu */
                $giaoVien = $item->giaoVien;
                $phuHuynh = $item->phuHuynh;
                $data[] = [
                    'id' => $item->id,
                    'ma_don_hang' => $item->ma_don_hang,
                    'gio_bat_dau' => $item->gio_bat_dau,
                    'created' => date("d/m/Y • H:i", strtotime($item->created)),
                    'trang_thai' => $item->trang_thai,
                    'phuHuynh'=>is_null($phuHuynh)?null:[
                        'id'=>$phuHuynh->id,
                        'hoten'=>$phuHuynh->hoten,
                        'dien_thoai'=>$phuHuynh->dien_thoai,
                        'anh_nguoi_dung'=>$phuHuynh->getImage(),
                        'vai_tro'=>'Phụ huynh'
                    ],
                    'dichVu' => $item->dichVu->ten_dich_vu,
                    'chonCa' => $item->getCaDayName(),
                    'dia_chi' => $item->dia_chi,
                    'giaoVien'=>is_null($giaoVien)?null:[
                        'id'=>$giaoVien->id,
                        'hoten'=>$giaoVien->hoten,
                        'dien_thoai'=>$giaoVien->dien_thoai,
                        'anh_nguoi_dung'=>$giaoVien->getImage(),
                        'trinh_do'=>$giaoVien->getTrinhDo()
                    ],

                ];
            }
        }
        return $this->outputListSuccess2($data, $count);
    }

    public function actionDanhSachKhieuNai(){
        $this->checkGetInput(['phu_huynh_id','tuKhoa']);
        if ($this->dataGet['phu_huynh_id']==""){
            throw new HttpException(500,"Vui lòng truyền tham số phu_huynh_id");
        }
        $khieuNai = KhieuNai::find()->andFilterWhere(['user_id'=>$this->dataGet['phu_huynh_id']])
            ->andFilterWhere(['active'=>1])
        ;
        if ($this->dataGet['tuKhoa']!=""){
            $khieuNai->andFilterWhere(['like','hoten',$this->dataGet['tuKhoa']]);
        }
        $count = count($khieuNai->all());
        $khieuNai = $khieuNai->limit($this->limit)->offset(($this->page-1)*$this->limit)->orderBy(['created'=>$this->sort==1?SORT_DESC:SORT_ASC])->all();
        $data = [];
        if (count($khieuNai)>0){
            foreach ($khieuNai as $item){
                /* @var $item KhieuNai*/
                $phuHuynh = $item->user;
                $data[]= [
                    'id'=>$item->id,
                    'phuHuynh'=>[
                        'id'=>$phuHuynh->id,
                        'hoten'=>$phuHuynh->hoten,
                        'anh_nguoi_dung'=>$phuHuynh->getImage(),
                    ],
                    'created'=>date('d/m/Y',strtotime($item->created)),
                    'noi_dung'=>$item->noi_dung,
                    'tinh_trang'=>$item->tinh_trang
                ];
            }
        }
        return $this->outputListSuccess2($data, $count);
    }
    public function actionResetPassword()
    {
      $this->checkField(['phu_huynh_id','password', 'password_comfirm']);
      if ($this->dataPost['phu_huynh_id']==""){
        throw new HttpException(500,"Vui lòng truyền phu_huynh_id");
      }
      $user = User::findOne($this->dataPost['phu_huynh_id']);
      if (is_null($user)){
        throw new HttpException(500,"Không xác đinh dữ liệu");
      }
      if (strlen($this->dataPost['password']) < 6) {
        throw new HttpException(500, 'Mật khẩu tối thiểu 6 kí tự');
      }
      if ($this->dataPost['password'] !== $this->dataPost['password_comfirm']) {
        throw new HttpException(500, 'Nhập lại mật khẩu không chính xác');
      }
      $user->updateAttributes(['auth_key' => null, 'password_hash' => Yii::$app->security->generatePasswordHash($this->dataPost['password'])]);
      return $this->outputSuccess("", "Đổi mật khẩu thành công");
    }

}
