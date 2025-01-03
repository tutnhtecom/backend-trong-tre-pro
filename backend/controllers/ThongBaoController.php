<?php namespace backend\controllers;

use backend\models\CauHinh;
use backend\models\DanhMuc;
use backend\models\QuanLyUserVaiTro;
use backend\models\ThongBao;
use backend\models\ThongBaoUser;
use common\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\HttpException;

class ThongBaoController extends CoreApiController
{
    public function actionDanhSach(){
        $this->checkGetInput(['tuKhoa']);
        $thongBao = ThongBao::find()
            ->select(['tieu_de', 'noi_dung', 'image', 'created', 'id', 'user_id'])
            ->andFilterWhere(['active'=>1])
        ;
        if ($this->dataGet['tuKhoa']!=""){
            $thongBao->andFilterWhere(['like','tieu_de',$this->dataGet['tuKhoa']]);
        }
        if (isset($this->dataGet['type'])){
            if ($this->dataGet['type']!=""){
                $thongBao->andFilterWhere(['type_id'=>$this->dataGet['type']]);
            }
        }
        $count = count($thongBao->all());
        $thongBao = $thongBao->limit($this->limit)->offset(($this->page-1)*$this->limit)->orderBy(['created'=>$this->sort==1?SORT_DESC:SORT_ASC])->all();
        $data = [];
        if (count($thongBao)>0){
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
                        'id'=>$user->id,
                        'hoten'=>$user->hoten,
                        'anh_nguoi_dung'=>$user->getImage(),
                    ],

                ];
            }
        }
        $data2 = [];
        foreach ($data as $item) {
            $data2[] = $item;
        }
        return $this->outputListSuccess2($data2, $count);
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
    public function actionTaoMoi()
    {
        $this->checkField(['type_id', 'noi_dung', 'to_id', 'tieu_de']);
        if ($this->dataPost['type_id'] == "") {
            throw new HttpException(500, "Vui lòng chọn chủ đề");
        }
        if ($this->dataPost['noi_dung'] == "") {
            throw new HttpException(500, "Vui lòng nhập nội dung");
        }
        if ($this->dataPost['to_id'] == "") {
            throw new HttpException(500, "Vui lòng chọn gửi tới");
        }
        $thongBao = new ThongBao();
        $thongBao->type_id =$this->dataPost['type_id'];
        $thongBao->to_id =$this->dataPost['to_id'];
        $thongBao->noi_dung = $this->dataPost['noi_dung'];
        $thongBao->tieu_de = $this->dataPost['tieu_de'];
        $thongBao->user_id = $this->uid;

        switch (intval($this->dataPost['to_id'])){
            case 60: {
                $this->checkField(['giao_vien_id']);
                $thongBao->giao_vien_id =$this->dataPost['giao_vien_id'];
                break;
            }
            case 61:{
                $this->checkField(['phu_huynh_id']);
                $thongBao->phu_huynh_id =$this->dataPost['phu_huynh_id'];
                break;
            }
            case 62:{
                $this->checkField(['dich_vu_id']);
                $thongBao->dich_vu_id =$this->dataPost['dich_vu_id'];
                break;
            }
            case 63:{
                $this->checkField(['lao_dong_id']);
                $thongBao->lao_dong_id =$this->dataPost['lao_dong_id'];
                break;
            }
            default:
                throw new HttpException(500, "Không tìm thấy loại gửi tới");
        }
        $image = $this->saveImage();
        if ($image != "") {
            $thongBao->image = $image;
        }
        $this->saveThongBao($thongBao);
        return $this->outputSuccess("", "Thêm thông báo thành công");
    }
    public function actionXoa()
    {
        $this->checkField(['id']);
        if ($this->dataPost['id']==""){
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $thongBao = ThongBao::findOne(['id'=>$this->dataPost['id'],'active'=>1]);
        if (is_null($thongBao)){
            throw new HttpException(500, "Không xác định dữ liệu");
        }
        $thongBao->active = 0;
        if (!$thongBao->save()){
            throw new HttpException(500,Html::errorSummary($thongBao));
        }
        return $this->outputSuccess('','Xóa thông báo thành công');
    }

    public function actionGetType()
    {
        return $this->outputSuccess($this->getDanhMuc(DanhMuc::THONG_BAO));
    }

    public function actionGetTo()
    {
        return $this->outputSuccess($this->getDanhMuc(DanhMuc::TO_THONG_BAO));
    }

    public function actionDanhSachGiaoVien()
    {
        $users = QuanLyUserVaiTro::find()
            ->select(['hoten', 'id', 'anh_nguoi_dung', 'danh_gia', 'trinh_do_name'])
            ->andFilterWhere(['active' => 1, 'is_admin' => 0, 'status' => 10, 'vai_tro' => User::GIAO_VIEN]);
        if ($this->tuKhoa != "") {
            $users = $users->andFilterWhere(['like', 'hoten', $this->tuKhoa]);
        }
        $users = $users->all();
        /** @var QuanLyUserVaiTro $user */
        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'hoten' => $user->hoten,
                'id' => $user->id,
                'anh_nguoi_dung'=>$user->getImage(),
                'danh_gia'=>$user->danh_gia,
                'trinh_do'=>$user->trinh_do_name
            ];
        }
        return $this->outputSuccess($data);
    }
    public function actionDanhSachPhuHuynh()
    {
        $users = QuanLyUserVaiTro::find()
            ->select(['hoten', 'id', 'anh_nguoi_dung', 'danh_gia', 'trinh_do_name'])
            ->andFilterWhere(['active' => 1, 'is_admin' => 0, 'status' => 10, 'vai_tro' => User::PHU_HUYNH]);
        if ($this->tuKhoa != "") {
            $users = $users->andFilterWhere(['like', 'hoten', $this->tuKhoa]);
        }
        $users = $users->all();
        /** @var QuanLyUserVaiTro $user */
        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'hoten' => $user->hoten,
                'id' => $user->id,
                'anh_nguoi_dung'=>$user->getImage(),
                'vai_tro'=>(new User())->getVaiTro()
            ];
        }
        return $this->outputSuccess($data);
    }
    public function actionThongBaoUser()
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
}
