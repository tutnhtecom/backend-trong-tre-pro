<?php namespace backend\controllers;

use backend\models\BanGiao;
use backend\models\CauHinh;
use backend\models\ChiLuong;
use backend\models\DanhMuc;
use backend\models\DichVu;
use backend\models\DonDichVu;
use backend\models\GiaDichVu;
use backend\models\GiaHanDon;
use backend\models\GiaoDich;
use backend\models\GiaoVienDanhGiaBuoiHoc;
use backend\models\KhieuNai;
use backend\models\LichSuTrangThaiDon;
use backend\models\LichSuTrangThaiThanhToan;
use backend\models\PhuPhi;
use backend\models\QuanLyUserVaiTro;
use backend\models\TienDoKhoaHoc;
use common\models\myAPI;
use common\models\User;
use Exception;
use yii\bootstrap\Html;
use yii\helpers\VarDumper;
use yii\web\HttpException;

class ServicesController extends CoreApiController
{
    public $vnp_TmnCode = 'TTRETEST';
    public $vnp_HashSecret = 'K07UU0ZZYP543OCZ7XDH7QTNM23TBAKI';
    public $vnp_Url = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';
    public $vnp_Returnurl = '/services/vnpay-return';
    public $vnp_apiUrl = 'http://sandbox.vnpayment.vn/merchant_webapi/merchant.html';
    public $apiUrl = 'https://sandbox.vnpayment.vn/merchant_webapi/api/transaction';

    //Connet QRPAY
    public function actionCreatePayment()
    {
        //Lấy id đơn dịch vụ
        $this->checkField(['don_dich_vu_id']);
        if ($this->dataPost['don_dich_vu_id'] == "") {
            throw new HttpException(400, "Vui lòng truyền don_dich_vu_id");
        }
        $donDichVu = DonDichVu::findOne(['id' => $this->dataPost['don_dich_vu_id'], 'phu_huynh_id' => $this->uid]);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $startTime = date("YmdHis");
        $expire = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));
        $vnp_TxnRef = $donDichVu->id; //Mã giao dịch thanh toán tham chiếu của merchant
        $vnp_Amount = $donDichVu->tong_tien; // Số tiền thanh toán
        $vnp_Locale = 'vn'; //Ngôn ngữ chuyển hướng thanh toán
        $vnp_BankCode = ""; //Mã phương thức thanh toán
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR']; //IP Khách hàng thanh toán

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $this->vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount * 100,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => "Thanh toan GD:" . $vnp_TxnRef,
            "vnp_OrderType" => "other",
            "vnp_ReturnUrl" => CauHinh::getServer() . $this->vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_ExpireDate" => $expire
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $this->vnp_Url . "?" . $query;
        if (isset($this->vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $this->vnp_HashSecret);//
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        return $this->outputSuccess($vnp_Url);
    }

    public function actionVnpayIpn()
    {
        $this->checkGetInput(['vnp_SecureHash', 'vnp_TransactionNo', 'vnp_BankCode', 'vnp_Amount']);
        $inputData = array();
        $returnData = array();
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $this->vnp_HashSecret);
        $vnpTranId = $inputData['vnp_TransactionNo']; //Mã giao dịch tại VNPAY
        $vnp_BankCode = $inputData['vnp_BankCode']; //Ngân hàng thanh toán
        $vnp_Amount = $inputData['vnp_Amount'] / 100; // Số tiền thanh toán VNPAY phản hồi

        $Status = 0; // Là trạng thái thanh toán của giao dịch chưa có IPN lưu tại hệ thống của merchant chiều khởi tạo URL thanh toán.
        $orderId = $inputData['vnp_TxnRef'];

        try {
            //Check Orderid
            //Kiểm tra checksum của dữ liệu
            if ($secureHash == $vnp_SecureHash) {
                //Lấy thông tin đơn hàng lưu trong Database và kiểm tra trạng thái của đơn hàng, mã đơn hàng là: $orderId
                //Việc kiểm tra trạng thái của đơn hàng giúp hệ thống không xử lý trùng lặp, xử lý nhiều lần một giao dịch
                //Giả sử: $order = mysqli_fetch_assoc($result);
                $order = DonDichVu::findOne($orderId);
                if ($order != NULL) {
                    if ($order->tong_tien == $vnp_Amount) //Kiểm tra số tiền thanh toán của giao dịch: giả sử số tiền kiểm tra là đúng. //$order["Amount"] == $vnp_Amount
                    {
                        if (!is_null($order->status) && $order->status == 0) {
                            if ($inputData['vnp_ResponseCode'] == '00' && $inputData['vnp_TransactionStatus'] == '00') {
                                $Status = 1; // Trạng thái thanh toán thành công
                                $order->status = 1;
                                $order->ngay_thanh_toan = date('Y-m-d');
                                $order->trang_thai_thanh_toan = LichSuTrangThaiThanhToan::DA_THANH_TOAN;
                            } else {
                                $Status = 2; // Trạng thái thanh toán thất bại / lỗi
                            }
                            $order->save();
                            //Cài đặt Code cập nhật kết quả thanh toán, tình trạng đơn hàng vào DB
                            //
                            //
                            //
                            //Trả kết quả về cho VNPAY: Website/APP TMĐT ghi nhận yêu cầu thành công
                            $returnData['RspCode'] = '00';
                            $returnData['Message'] = 'Confirm Success';
                        } else {
                            $returnData['RspCode'] = '02';
                            $returnData['Message'] = 'Order already confirmed';
                        }
                    } else {
                        $returnData['RspCode'] = '04';
                        $returnData['Message'] = 'invalid amount';
                    }
                } else {
                    $returnData['RspCode'] = '01';
                    $returnData['Message'] = 'Order not found';
                }
            } else {
                $returnData['RspCode'] = '97';
                $returnData['Message'] = 'Invalid signature';
            }
        } catch (Exception $e) {
            $returnData['RspCode'] = '99';
            $returnData['Message'] = 'Unknow error';
        }
//Trả lại VNPAY theo định dạng JSON
        return $returnData;
    }

    public function actionVnpayReturn()
    {
        if ($this->dataGet['vnp_ResponseCode'] == '00' && $this->dataGet['vnp_TransactionStatus'] == '00') {
            $this->redirect('/services/thanh-cong', 302);
        } else {
            $this->redirect('/services/that-bai', 302);
        }
    }

    public function actionThanhCong()
    {
        return '';
    }

    public function actionThatBai()
    {
        return '';
    }

    public function actionGoogle()
    {
        $site_subdomain = 'app-1114241-1';
        $site_public_key = '7c3a873b-0482-486d-b308-67d2b294ca8a';
        $site_private_key = '3683232d-78fc-4fe1-bc97-134c4c59bc14';

        //API Access Domain
        $site_domain = $site_subdomain . '.api.oneall.com';

        //Connection Resource
        $resource_uri = 'https://' . $site_domain . '/connections.json';

        //Setup connection
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $resource_uri);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_USERPWD, $site_public_key . ":" . $site_private_key);
        curl_setopt($curl, CURLOPT_TIMEOUT, 15);
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($curl, CURLOPT_FAILONERROR, 0);

        //Send request
        $result_json = curl_exec($curl);
        curl_close($curl);

        //Done
        return json_decode($result_json);
    }
//    public function actionChiLuong(){
//        $tienDo = TienDoKhoaHoc::find()->andFilterWhere(['trang_thai'=>TienDoKhoaHoc::DA_HOAN_THANH])->all();
//        /** @var TienDoKhoaHoc $item */
//        foreach ($tienDo as $item){
//            $anTrua = PhuPhi::findOne(['don_dich_vu_id'=> $item->don_dich_vu_id,'type_id'=>DanhMuc::AN_TRUA,'active'=>1]);
//            $themGio = PhuPhi::findOne(['don_dich_vu_id'=> $item->don_dich_vu_id,'type_id'=>DanhMuc::THEM_GIO,'active'=>1]);
//            $heSoLuong = CauHinh::getContent(24);
//            $themTre = CauHinh::getContent(26);
//            $ppThemGio = CauHinh::getContent(27);
//            $ppAnTrua = CauHinh::getContent(25);
//            $chiLuong = new ChiLuong();
//            $chiLuong->don_dich_vu_id = $item->don_dich_vu_id;
//            $chiLuong->don_gia =is_null($item->donDichVu)?0: $item->donDichVu->getDonGia();
//            $chiLuong->tong_tien = $chiLuong->don_gia*$heSoLuong/100 + $chiLuong->don_gia*($item->donDichVu->so_luong_be-1)*$themTre/100;
//            $chiLuong->an_trua =is_null($anTrua)?0: $anTrua->tong_tien*$ppAnTrua/100;
//            $chiLuong->them_gio = is_null($themGio)?0:$themGio->tong_tien*$ppThemGio/100;
//            $chiLuong->thanh_tien = $chiLuong->tong_tien+$chiLuong->an_trua+$chiLuong->them_gio;
//            $chiLuong->giao_vien_id = $item->giao_vien_id;
//            $chiLuong->buoi_hoc_id = $item->id;
//            $chiLuong->user_id = $item->user_id;
//            $chiLuong->created = is_null($item->ket_ca)?$item->created:$item->ket_ca;
//            if (!$chiLuong->save()) {
//                throw new HttpException(500, Html::errorSummary($chiLuong));
//            }
//        }
//        return "okee";
//    }
//    public function actionUpdatePhuPhi(){
//        $phuPhi = PhuPhi::find()->andFilterWhere(['not in','type_id',[43,44]])->all();
//        /** @var PhuPhi $item */
//        foreach ($phuPhi as $item){
//            $ppKhac = CauHinh::getContent(28);
//            $giaoDich = new GiaoDich();
//            $giaoDich->so_tien = $item->tong_tien*$ppKhac/100;
//            $giaoDich->ghi_chu = $item->ghi_chu;
//            $giaoDich->type = GiaoDich::NAP_TIEN;
//            $giaoDich->user_id = $item->donDichVu->giao_vien_id;
//            $giaoDich->type_id = $item->type_id;
//            $giaoDich->tieu_de = "Cộng tiền cho đơn số";
//            $giaoDich->don_dich_vu_id = $item->don_dich_vu_id;
//            $giaoDich->created = $item->created;
//            if (!$giaoDich->save()){
//                throw new HttpException(500,Html::errorSummary($giaoDich));
//            }
//        }
//    }
//    public function actionUpdate()
//    {
//        $data = [];
//        $donDichVus = DonDichVu::find()->andFilterWhere(['<=', 'date(created)', date('Y-m-d')])->andFilterWhere(['active' => 1])->orderBy(['created' => SORT_DESC])->andFilterWhere(['<>', 'trang_thai', LichSuTrangThaiDon::DA_HUY])->limit(100)->offset(600)->all();
//        foreach ($donDichVus as $donDichVu) {
//           $dels =  TienDoKhoaHoc::find()->andFilterWhere(['don_dich_vu_id' => $donDichVu->id])->andWhere('vao_ca is null')->andFilterWhere(['<>','trang_thai',TienDoKhoaHoc::DA_HUY])->all();
//            foreach ($dels as $del){
//                $del->delete();
//            }
//            $tienDos = TienDoKhoaHoc::find()->andFilterWhere(['don_dich_vu_id' => $donDichVu->id])->all();
//            foreach ($tienDos as $index=> $tienDo) {
//                $tienDo->updateAttributes(['buoi'=>$index+1,'ngay_day'=>$donDichVu->getDateByBuoi($index+1)]);
//                $tienDo->updateSortBuoiHocTheoDate();
//                $giaoVienDanhGia = GiaoVienDanhGiaBuoiHoc::findOne(['buoi_hoc_id'=>$tienDo->id]);
//                if (!is_null($giaoVienDanhGia)) {
//                    $giaoVienDanhGia->updateAttributes(['created'=>$tienDo->ngay_day]);
//                }
//            }
//            foreach (range(1,$donDichVu->so_buoi) as $buoi){
//                $donDichVu->tienDoKhoaHoc($buoi,true);
//            }
//        }
//        return $this->outputSuccess($data);
//    }
    public function actionUpdate()
    {
        $this->checkGetInput(['don_dich_vu_id', 'ngay_day', 'buoi']);
        if ($this->dataGet['ngay_day'] == "") {
            throw new HttpException(500, "Vui lòng chọn ngày");
        }
        $donDichVu = DonDichVu::findOne($this->dataGet['don_dich_vu_id']);
        if (is_null($donDichVu)) {
            throw new HttpException(403, "Không tìm thấy đơn");
        }
        if (intval($this->dataGet['buoi']) == 0 || intval($this->dataGet['buoi']) > $donDichVu->so_buoi) {
            throw new HttpException(500, "Thông tin buổi học không hợp lệ");
        }
        $id = $donDichVu->tienDoKhoaHoc($this->dataGet['buoi'])['id'] ?? null;
        $caDay = TienDoKhoaHoc::findOne($id);
        if (is_null($caDay)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        /** @var TienDoKhoaHoc $checkDate */
        $checkDate = TienDoKhoaHoc::find()->andFilterWhere(['don_dich_vu_id'=>$caDay->don_dich_vu_id, 'active' => 1])
            ->andFilterWhere(['date(ngay_day)'=>myAPI::convertDMY2YMD($this->dataGet['ngay_day'])])
            ->andFilterWhere(['<>','trang_thai',TienDoKhoaHoc::DA_HUY])->one();
        if (!is_null($checkDate)){
            throw new HttpException(500, "Ngày dạy đã trùng với buổi số {$checkDate->buoi}");
        }
        $caDay->ngay_day = $this->dataGet['ngay_day'];
        if (!$caDay->save()) {
            throw new HttpException(500, \yii\bootstrap\Html::errorSummary($caDay));
        } else {
            $caDay->updateSortBuoiHocTheoDate();
        }
        return $this->outputSuccess("", "Đổi ngày dạy thành công");
    }
}
