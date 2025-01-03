<?php namespace backend\controllers;

use backend\controllers\CoreApiController;
use backend\models\CauHinh;
use backend\models\DonDichVu;
use backend\models\KhieuNai;
use backend\models\QuanLyUserVaiTro;
use common\models\User;
use yii\bootstrap\Html;
use yii\web\HttpException;

class KhieuNaiController extends CoreApiController
{
    public function actionDanhSachKhieuNai(){
        $this->checkGetInput(['tuKhoa','thang']);
        $khieuNai = KhieuNai::find()
            ->andFilterWhere(['active'=>1])
        ;
        if ($this->dataGet['tuKhoa']!=""){
            $khieuNai->andFilterWhere(['like','hoten',$this->dataGet['tuKhoa']]);
        }
        if ($this->dataGet['thang']!=""){
            $thang =explode('/',$this->dataGet['thang']);
            $tuNgay = date($thang[1]."-".$thang[0]."-1");
            $denNgay = date($thang[1]."-".$thang[0]."-t");
            $khieuNai->andFilterWhere(['>=','date(created)',$tuNgay]);
            $khieuNai->andFilterWhere(['<=','date(created)',$denNgay]);
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
    public function actionChiTietPhanHoi(){
        $this->checkGetInput(['phan_hoi_id']);
        if ($this->dataGet['phan_hoi_id']==""){
            throw new HttpException(500,"Vui lòng truyền phan_hoi_id");
        }
        $khieuNai = KhieuNai::findOne($this->dataGet['phan_hoi_id']);
        if (is_null($khieuNai)){
            throw new HttpException(403,"Không xác định dữ liệu");
        }
        $phuHuynh = $khieuNai->user;
        return $this->outputSuccess([
            'id'=>$khieuNai->id,
            'phuHuynh'=>is_null($phuHuynh)?null:[
                'id'=>$phuHuynh->id,
                'hoten'=>$phuHuynh->hoten,
                'anh_nguoi_dung'=>$phuHuynh->getImage(),
                'dia_chi'=>$phuHuynh->dia_chi,
                'vai_tro'=>$phuHuynh->getVaiTro(),
                'dien_thoai'=>$phuHuynh->dien_thoai,
            ],
            'created'=>date('d/m/Y',strtotime($khieuNai->created)),
            'noi_dung'=>$khieuNai->noi_dung,
            'phan_hoi'=>$khieuNai->phan_hoi,
            'tinh_trang'=>$khieuNai->tinh_trang,
            'xu_ly_phan_hoi' => $khieuNai->tinh_trang == KhieuNai::DA_XU_LY ?? 0
        ]);
    }
    public function actionCapNhatXuLyPhanHoi(){
        $this->checkField(['phan_hoi_id','phan_hoi','xac_nhan']);
        if ($this->dataPost['phan_hoi_id']==""){
            throw new HttpException(500,"Vui lòng truyền phan_hoi_id");
        }
        $khieuNai = KhieuNai::findOne($this->dataPost['phan_hoi_id']);
        if (is_null($khieuNai)){
            throw new HttpException(403,"Không xác định dữ liệu");
        }
        if ($this->dataPost['phan_hoi']==""){
            throw new HttpException(500,"Vui lòng nhập kết quả phản hồi");
        }
        $khieuNai->phan_hoi =$this->dataPost['phan_hoi'];
        if ($this->dataPost['xac_nhan']==1){
            $khieuNai->tinh_trang = KhieuNai::DA_XU_LY;
        }else{
            $khieuNai->tinh_trang = KhieuNai::CHUA_XU_LY;
        }
        if (!$khieuNai->save()){
            throw new HttpException(500,Html::errorSummary($khieuNai));
        }
        return $this->outputSuccess("","Phản hồi thành công");
    }

}
