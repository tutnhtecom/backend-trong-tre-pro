<?php 
namespace backend\controllers;
use backend\models\DonDichVu;
use backend\models\LichSuTrangThaiThanhToan;
use backend\onepay\VerifyVpcSecureHash;

use Yii;
use yii\bootstrap\Html;

const MERCHANT_PAYNOW_ID = "DUONGTT";
const MERCHANT_PAYNOW_ACCESS_CODE = "6BEB2546";
const MERCHANT_PAYNOW_HASH_CODE = "6D0870CDE5F24F34F3915FB0045120DB";

class OnepayApiController extends CoreApiController
{
    public function actionIpn()
    {
        $url = "https://webhook.site/6ddd97bd-d51c-4c36-b16b-7755ce68a010";
        
        if (isset($this->dataGet['vpc_Amount'])){
            $url.="?vpc_Amount=".$this->dataGet['vpc_Amount'];
        }
        if (isset($this->dataGet['vpc_Command'])){
            $url.="&vpc_Command=".$this->dataGet['vpc_Command'];
        }
        if (isset($this->dataGet['vpc_MerchTxnRef'])){
            $url.="&vpc_MerchTxnRef=".$this->dataGet['vpc_MerchTxnRef'];
        }
        if (isset($this->dataGet['vpc_Merchant'])){
            $url.="&vpc_Merchant=".$this->dataGet['vpc_Merchant'];
        }
        if (isset($this->dataGet['vpc_Message'])){
            $url.="&vpc_Message=".$this->dataGet['vpc_Message'];
        }
        if (isset($this->dataGet['vpc_OrderInfo'])){
            $url.="&vpc_OrderInfo=".$this->dataGet['vpc_OrderInfo'];
        }
        if (isset($this->dataGet['vpc_TxnResponseCode'])){
            $url.="&vpc_TxnResponseCode=".$this->dataGet['vpc_TxnResponseCode'];
        }
        if (isset($this->dataGet['vpc_Version'])){
            $url.="&vpc_Version=".$this->dataGet['vpc_Version'];
        }
        if (isset($this->dataGet['vpc_SecureHash'])){
            $url.="&vpc_SecureHash=".$this->dataGet['vpc_SecureHash'];
        }
        $merchantId = MERCHANT_PAYNOW_ID;
        $merchantAccessCode = MERCHANT_PAYNOW_ACCESS_CODE;
        $merchantHashCode = MERCHANT_PAYNOW_HASH_CODE;
        $veify = new VerifyVpcSecureHash($merchantId, $merchantAccessCode, $merchantHashCode);
        $check_payment = $veify->onePayVerifySecureHash($url);
       if($check_payment){
        $id = $this->dataGet['vpc_OrderInfo'];
        $donDichVu = DonDichVu::findOne($id);
        $donDichVu->trang_thai_thanh_toan = LichSuTrangThaiThanhToan::DA_THANH_TOAN ;
        if ($donDichVu->save()) {
            return $this->outputSuccess([
                'responsecode'=>1,
                'desc' => 'confirm-success',
                'status' => 'Đã cập nhật đơn hàng là đã thanh toán.'
            ]);
        } else {
            throw new HttpException(500, Html::errorSummary($donDichVu));
        }
       }else{
        return $this->outputSuccess([
            'responsecode' => 1,
            'desc' => 'confirm-success',
            'status' => 'Thanh toán chưa được hệ thống chấp nhận.'
        ]);
       }
    }
}
