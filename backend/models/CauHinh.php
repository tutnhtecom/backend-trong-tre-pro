<?php

namespace backend\models;

use Couchbase\HttpException;
use Yii;
use common\models\myActiveRecord;

/**
 * @property integer $id
 * @property string $content
 * @property string $name
 * @property string $ghi_chu
 * @property string $image
 */
class CauHinh extends myActiveRecord
{
    public $tenNganHang = 6;
    public $chuTaiKhoan = 3;
    public $soTaiKhoan = 4;
    public $ghiChuChuyenKhoan = 5;
    public $emailGuiDi = 2;
    public $ckeditor = 1;
    public $sdtQuanLy = 23;

    public static function tableName()
    {
        return '{{trong_tre_cau_hinh}}';
    }

    public function rules()
    {
        return [
            [['ghi_chu', 'name'], 'safe'],
            [['content'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'content' => 'Nội dung',
            'ghi_chu' => 'Ký hiệu',
            'name' => 'Tên',
        ];
    }
    public function  getNoiDung ($id){
        $cauHinh = CauHinh::findOne($id);
        if(is_null($cauHinh)){
            return "";
        }
        return  $cauHinh->ghi_chu;
    }
    public static function getServer(){
        $cauHinh = CauHinh::findOne(16);
        if(is_null($cauHinh)){
            return "";
        }
        return  $cauHinh->ghi_chu;
    }
    public static function getImage ($image){
        return  $image == null ? CauHinh::getServer() . '/upload-file/icon-05.png' : CauHinh::getServer() . '/upload-file/'.$image;
    }
    public static function getLink ($link){
        return  $link == null ? "" : CauHinh::getServer() . '/upload-file/'.$link;
    }
    public function updateContent($content){
        return $this->updateAttributes(['ghi_chu'=>$content]);
    }
    public static function sdtQuanLy(){
        $sdt = CauHinh::findOne(23);
        return is_null($sdt)?"":$sdt->content;
    }
    public static function getContent ($id){
        $cauHinh = CauHinh::findOne($id);
        if(is_null($cauHinh)){
            return 0;
        }
        return  $cauHinh->content;
    }
    public static function spliptText($string){
        $arr  = array_filter(explode('<p>', nl2br($string)));
        $data = [];
        foreach ($arr as $item){
            $data[] = strip_tags(trim($item)) ;
        }
        return $data;
    }

}
