<?php
/**
 * Created by PhpStorm.
 * User: hungddvimaru
 * Date: 11/11/16
 * Time: 1:19 AM
 */

namespace common\models;

use backend\models\ChucNang;
use backend\models\PhanQuyen;
use backend\models\QuanLyPhanQuyen;
use backend\models\QuanLyUserVaiTro;
use backend\models\ThongBao;
use backend\models\TokenDevice;
use backend\models\VaiTro;
use backend\models\Vaitrouser;
use kartik\mpdf\Pdf;
use Pusher;
use yii\bootstrap\ActiveForm;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Json;
use yii\helpers\Url;
use Yii;
use yii\bootstrap\Html;
use yii\helpers\VarDumper;
use yii\jui\DatePicker;
use yii\swiftmailer\Mailer;
use yii\web\HttpException;

class myAPI
{
    //

    public static function pusherJS($event,$data){
        $options = array(
            'cluster' => 'ap1',
            'useTLS' => true
        );
        $pusher = new Pusher(
            '8098a7a4cc508ae9b98a',
            '3f332f8a769ad81c533c',
            '1462296',
            $options
        );
       $data['uid'] = Yii::$app->user->id;
        $pusher->trigger('my-channel', $event, $data);
    }
    public static function createEngName($str){
        $str = trim($str);
        $coDau=array("à","á","ạ","ả","ã","â","ầ","ấ","ậ","ẩ","ẫ","ă","ằ","ắ"
        ,"ặ","ẳ","ẵ","è","é","ẹ","ẻ","ẽ","ê","ề","ế","ệ","ể","ễ","ì","í","ị","ỉ","ĩ",
            "ò","ó","ọ","ỏ","õ","ô","ồ","ố","ộ","ổ","ỗ","ơ"
        ,"ờ","ớ","ợ","ở","ỡ",
            "ù","ú","ụ","ủ","ũ","ư","ừ","ứ","ự","ử","ữ",
            "ỳ","ý","ỵ","ỷ","ỹ",
            "đ",
            "À","Á","Ạ","Ả","Ã","Â","Ầ","Ấ","Ậ","Ẩ","Ẫ","Ă"
        ,"Ằ","Ắ","Ặ","Ẳ","Ẵ",
            "È","É","Ẹ","Ẻ","Ẽ","Ê","Ề","Ế","Ệ","Ể","Ễ",
            "Ì","Í","Ị","Ỉ","Ĩ",
            "Ò","Ó","Ọ","Ỏ","Õ","Ô","Ồ","Ố","Ộ","Ổ","Ỗ","Ơ"
        ,"Ờ","Ớ","Ợ","Ở","Ỡ",
            "Ù","Ú","Ụ","Ủ","Ũ","Ư","Ừ","Ứ","Ự","Ử","Ữ",
            "Ỳ","Ý","Ỵ","Ỷ","Ỹ",
            "Đ","ê","ù","à");
        $khongDau=array("a","a","a","a","a","a","a","a","a","a","a"
        ,"a","a","a","a","a","a",
            "e","e","e","e","e","e","e","e","e","e","e",
            "i","i","i","i","i",
            "o","o","o","o","o","o","o","o","o","o","o","o"
        ,"o","o","o","o","o",
            "u","u","u","u","u","u","u","u","u","u","u",
            "y","y","y","y","y",
            "d",
            "A","A","A","A","A","A","A","A","A","A","A","A"
        ,"A","A","A","A","A",
            "E","E","E","E","E","E","E","E","E","E","E",
            "I","I","I","I","I",
            "O","O","O","O","O","O","O","O","O","O","O","O"
        ,"O","O","O","O","O",
            "U","U","U","U","U","U","U","U","U","U","U",
            "Y","Y","Y","Y","Y",
            "D","e","u","a");
        $str = str_replace($coDau,$khongDau,$str);
        $str = trim(preg_replace("/\\s+/", " ", $str));
        $str = preg_replace("/[^a-zA-Z0-9 \-\.]/", "", $str);
        $str = strtoupper($str);
        return $str;
//        return str_replace(" ", '', $str);;
    }

    public static function duyetNhom($object,$parentid = 0,$space = '--', $trees = NULL){
        if(!$trees) $trees = array();
        $nhoms = $object::find()->where(['parent_id' => $parentid])->all();
        /** @var  $nhom  Daily*/
        foreach ($nhoms as $nhom) {
            $trees[] = array('id'=>$nhom->id,'title'=>$space.$nhom->name);
            $trees = myAPI::duyetNhom($object,$nhom->id,"|..".$space,$trees);
        }

        return $trees;
    }

    public static function dsNhom($object){
        $danhmuccons =$object::find()->where('parent_id is null')->all();
        $trees = array();
        /** @var  $danhmuccon Daily */
        foreach ($danhmuccons as $danhmuccon) {
            $trees[] = array('id'=>$danhmuccon->id, 'title'=>$danhmuccon->name);
            $trees = myAPI::duyetNhom($object,$danhmuccon->id,'|--',$trees);
        }
        return $trees;
    }

    public static function dataTree($object,$parentid = NULL,$trees){
        $trees =[];
        $danhmuccons = $object::find()->where(['parent_id'=>$parentid])->all();
        foreach ($danhmuccons as $danhmuccon) {
            $nodes =[];
            $nodes = myAPI::dataTree($object,$danhmuccon->id,$nodes);
            $trees[] = ['id'=>$danhmuccon->id,'title'=>$danhmuccon->name,'nodes'=>$nodes];
        }
        return $trees;
    }

    public static function getNam($namBatDau,$namKetThuc){
        $namBatDau = (int)$namBatDau;
        $namKetThuc = (int)$namKetThuc;
        for($i=$namBatDau;$i <= $namKetThuc;$i++)
        {
            $data[$i] = $i;
        }
        return $data;
    }

    public static function getCapDo($str = 'quan | huyen | phuong | xa | thitran'){
        $data = [
            'quan' => 'Quận',
            'huyen' => 'Huyện',
            'phuong' => 'Phường',
            'xa' => 'Xã',
            'thitran' => 'Thị trấn'
        ];
        return $data[$str];
    }

    public static function getTab($cap = 'quan | huyen | xa | phuong | thitran' ){
        $data = [
            'quan' => 0,
            'huyen' => 0,
            'phuong' => 5,
            'xa' => 5,
            'thitran' => 5
        ];

        $str = '';
        for($i = 0; $i<=$data[$cap]; $i++)
            $str.='&emsp;';

        return $str;
    }

    public static function getMessage($att = "success|danger|warning|info", $content){
        return "<div class='note note-{$att}'>{$content}</div>";
    }

    public static function createMessage($att = 'success | danger | warning | info', $content){
        return [
            'messagePlan' => $content,
            'messageHtml' => self::getMessage($att, $content)
        ];
    }

    /**
     * @param $value
     * @param ActiveRecord $model
     * @param string $attributeTitle
     * @param array $attributeType
     * @return Expression
     */
    public static function getIdOtherModel($value, $model, $attributeTitle = 'name', $attributeType = ['name' => '', 'value' => '']){
        if(trim($value)=="")
            return new Expression('NULL');

        $data = $model->find()->where("code = :name", [':name' => self::createCode(trim($value))])->one();

        if(count($data) == 0){
            $model->{$attributeTitle} = trim($value);
            if($attributeType['name'] != '')
                $model->{$attributeType['name']} = trim($attributeType['value']);

            $model->save();
            return $model->id;
        }
        return $data->id;
    }

    public static function getHeadModal($noidung){
        return '<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
											<h4 class="modal-title">'.$noidung.'</h4>';
    }

    public static function activeDateField($form, $model, $field, $label, $yearRange = '1950:2050'){
        return $form->field($model,$field)->widget(\yii\jui\DatePicker::className(),[
            'language' => 'vi',
            'clientOptions' => [
                'dateFormat' => 'dd/mm/yy',
                'changeMonth' => true,
                'yearRange' => $yearRange,
                'changeYear' => true,
            ],
            'options' => ['class' => 'form-control']
        ])->label($label);
    }

    /**
     * @param $form ActiveForm
     * @param $model ActiveRecord
     * @param $field
     * @param $label
     * @param string $yearRange
     * @param array $options
     * @return mixed
     */
    public static function activeDateField2($form, $model, $field, $label, $yearRange = '2015:2018', $options = ['class' => 'form-control']){
        return $form->field($model,$field)->widget(\yii\jui\DatePicker::className(),[
            'language' => 'vi',
            'dateFormat' => 'dd/MM/yyyy',
            'clientOptions' => [
                'changeMonth' => true,
                'yearRange' => $yearRange,
                'changeYear' => true,
            ],
            'options' => ['class' => 'form-control']
        ])->label($label);
    }

    public static function dateField($name, $value, $class='form-control', $yearRange = '1950:2050'){
        return DatePicker::widget([
            'language' => 'vi',
            'dateFormat' => 'dd/MM/yyyy',
            'name' => $name,
            'value' => $value,
            'clientOptions' => [
                'changeMonth' => true,
                'yearRange' => $yearRange,
                'changeYear' => true,
            ],
            'options' => ['class' => $class]
        ]);
    }
    public static function dateField2($name, $value, $yearRange = '2015:2018',$options= ['class' => 'form-control']){
        return DatePicker::widget([
            'language' => 'vi',
            'dateFormat' => 'dd/MM/yyyy',
            'name' => $name,
            'value' => $value,
            'clientOptions' => [
                'changeMonth' => true,
                'yearRange' => $yearRange,
                'changeYear' => true,
            ],
            'options' => $options
        ]);
    }

    public static function activeDateFieldNoLabel($model, $attribute, $yearRange = '2015: 2025', $options = ['class' => 'form-control','autocomplete'=>'off']){
        return DatePicker::widget([
            'language' => 'vi',
            'model' => $model,
            'dateFormat' => 'dd/MM/yyyy',
            'attribute' => $attribute,
            'clientOptions' => [
                'changeMonth' => true,
                'yearRange' => $yearRange,
                'changeYear' => true,
            ],
            'options' => $options
        ]);
    }

    public static function convertDateSaveIntoDb($date){
        if($date == "")
            return null;

        $splash = '/';
        if(strpos($date, '-') !== false)
            $splash = '-';
        else if(strpos($date, '.') !== false)
            $splash = '.';

        $date = trim($date);
        if($date == "")
            return new Expression('NULL');
        $arr = explode(trim($splash), $date);
        if(count($arr) == 3)
            return implode('-', array_reverse($arr));
        else if(count($arr) == 2)
            return date("Y")."-{$arr[1]}-{$arr[0]}";
        else
            return date("Y")."-".date("m")."-".$arr[0];
    }

    public static function getBtnCloseModal(){
        return Html::button('Đóng lại',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]);
    }

    public static function getBtnFooter( $label, $options = []){
        return Html::button($label, $options);
    }

    public static function getVaitro(){
        return [
            'quantrivien' => '<span class="text-danger"><i class="fa fa-flag"></i> Quản trị viên</span>',
            'quanly' => '<span class="text-warning"><i class="fa fa-flag"></i> Quản lý</span>',
            'nhanvien' => '<span class="text-success"><i class="fa fa-flag"></i> Nhân viên</span>',
        ];
    }

    public static function getAFieldOfAModelFromName($model, $field, $name){
        $code = self::createCode(trim($name));
        $data = $model->find()->where(['code' => $code])->one();
        if(is_null($data))
            return '';
        return $data->{$field};
    }

    public static function getFilterFromTo($searchModel, $fieldFrom, $field_to, $options = ['class' => 'form-control']){
        return Html::activeTextInput($searchModel, $fieldFrom, $options).
            Html::activeTextInput($searchModel, $field_to, $options);
    }

    public static function getBtnSearch(){
        return '<button type="button" class="btn blue btn-search"><i class="fa fa-search"></i> Tìm kiếm</button>';
    }

    public static function getDMY($YMD){
        if($YMD != "")
            return date("d/m/Y", strtotime($YMD));
        return '';
    }
    public static function getGio(){
        $gio=[];
        foreach (range(0,23) as $item)
        {
            $gio[]=$item;
        }
        return $gio;

    }
    public static function getPhut(){
        $phut=[];
        foreach (range(0,59) as $item)
        {
            $phut[]=$item;
        }
        return $phut;

    }
    /**
     * @return string
     */
    public static function getBtnDownload(){
        return Html::button('<i class="fa fa-cloud-download"></i> Tải xuống',['class'=>'btn btn-primary btn-download-ketquatimkiem pull-right']);
    }

    public static function getBtnDeleteAjaxCRUD($text = '', $url, $clsBtn = ''){
        return Html::a('<i class="fa fa-trash"></i> '.$text, $url, ['title' => 'Xóa', 'role' => 'modal-remote', 'data-request-method' => 'post', 'data-toggle' => 'tooltip',
            'data-confirm-title' => 'Thông báo', 'data-confirm-message' => 'Bạn có chắc chắn muốn xóa không ?', 'class' => $clsBtn]);
    }

    public static function getDsThang(){
        $arr = [];
        for ($i= 1; $i<=12; $i++)
            $arr[$i] = "Tháng {$i}";
        return $arr;
    }

    public static function createUpdateBtnInGrid($path, $title = 'Sửa dữ liệu'){
        return Html::a('<i class="fa fa-edit"></i>', $path, ['title' => $title, 'data-pjax' => 0, 'role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-original-tile' => $title]);
    }

    public static function createDeleteBtnInGrid($path, $title = 'Hủy dữ liệu'){
        return Html::a('<i class="fa fa-trash"></i>', $path,['title' => $title, 'data-pjax' => 0, 'role' => 'modal-remote', 'data-request-method' => 'post', 'data-toggle' => 'tooltip', 'data-confirm-title' => 'Thông báo', 'data-confirm-message' => 'Bạn có chắc chắn muốn hủy dữ liệu này?', 'data-original-title' => 'Hủy', 'class' => 'text-danger']);
    }

    /**
     * @param string $model
     * @param int $id
     * {$model}-{$id}
     * @param array $optionsTD
     * @return string
     */
    public static function getBtnDeletInRow($model, $id, $optionsTD = ['class' => 'text-center']){
        return Html::tag('td', Html::a('<i class="fa fa-trash"></i>', '#', ['class' => 'text-danger btn-xoa-dong-tren-bang', 'id' => "{$model}-{$id}"]), $optionsTD);
    }
    public static function getBtnDeletInRowNewRow($options = ['class'=>"text-center"]){
        return Html::tag('td', Html::a('<i class="fa fa-trash"></i>', '#', ['class' => 'text-danger btn-xoa-dong-tren-bang dong-moi-trenbang']), $options);
    }

    /**
     * @param string $id
     * tauthuynoidia-soluongthuyenvien => views/tauthuynoidia/row/_row_soluongthuyenvien
     * @param integer $colspan
     * @param integer $colspan
     * @param integer $colspan
     * @param integer $colspan
     * @return string
     */
    public static function getRowBoSung($id = "{model}-{row_file}", $colspan){
        return Html::tag('tr', Html::tag('td', Html::button('<i class="fa fa-plus"></i> Bổ sung',[
            'class' => 'btn btn-sm btn-primary btn-them-dong-moi',
            'id' => $id
        ]), ['colspan' => $colspan]));
    }

    /**
     * @param $post string
     * @param $model ActiveRecord
     */
    public static function saveAnExistTable($post, $model, $attributes = []){
        if(isset($_POST[$post])){
            foreach ($_POST[$post] as $id => $item) {
                $kqkd = $model->findOne($id);
                $kqkd->attributes = $item;
                foreach ($attributes as $attribute => $value) {
                    $kqkd->{$attribute} = $value;
                }
                if(!$kqkd->save()){
                    var_dump(Html::errorSummary($kqkd));
                    exit;
                }
            }
        }
    }
    /**
     * @param $post array
     * @param $newOBJ string
     * @param $firstField string
     * @param $model ActiveRecord
     * @param $others array
     */
    public static function saveOtherTable($newOBJ, $firstField, $objectName, $others = []){
        $model = new $objectName();
        $arr_fields = $model->attributes;
        if(isset($_POST[$newOBJ][$firstField])){
            foreach ($_POST[$newOBJ][$firstField] as $index => $item) {
                /** @var  $newModel ActiveRecord*/
                $newModel = new $objectName();
                foreach ($arr_fields as $field => $value) {
                    if(isset($_POST[$newOBJ][$field][$index]))
                        $newModel->{$field} = $_POST[$newOBJ][$field][$index];
                }
                foreach ($others as $field => $value) {
                    $newModel->{$field} = $value;
                }
                if(!$newModel->save()) {var_dump(Html::errorSummary($newModel)); exit();};
            }
        }
    }

    /**
     * @param $arrRoles
     * @return bool
     */
    public static function isAccess($arrRoles){
        if(Yii::$app->user->isGuest)
            return false;
        return (new User())->isAccess($arrRoles);
    }

    public static function isHasRole($name){
        $vaitro = VaiTro::findOne(['name' => $name]);
        if(is_null($vaitro))
            return false;
        return !is_null(Vaitrouser::findOne(['vaitro_id' => $vaitro->id, 'user_id' => Yii::$app->user->id]));
    }

    public static function sendMai( $to, $subject, $message, $username, $password, $fromName, $pathFiles = [], $host = 'smtp.gmail.com', $port = '587', $ecryption  = 'tls'){
        $mailer = new Mailer();
        $mailer->setTransport([
//            smtp gmail
            'class' => 'Swift_SmtpTransport',
            'host' => $host,
            'username' => $username,
            'password' => $password,
            'port' => $port,
            'encryption' => $ecryption,
//            'timeout' => 50000000
        ]);

        $mail = $mailer->compose()
            ->setFrom([$username => $fromName])
            ->setTo($to);

        foreach ($pathFiles as $pathFile) {
            $mail->attach($pathFile);
        }

        return $mail
            ->setHtmlBody($message)
            ->setSubject($subject)
            ->setCharset('UTF-8')
            ->send();
    }

    public static function getYMDFromDMY($date, $splash = '-'){
        if($date == '')
            return '';
        $arr = explode($splash, $date);
        return implode('-', array_reverse($arr));
    }

    public static function getCodesMBLDaChon(){
        $arr = [];
        if(\Yii::$app->session->get('ma'))
            $arr = \Yii::$app->session->get('ma');
        if(count($arr) > 0)
            return 'Học viên đã chọn: '.implode(', ',$arr);
        return '';
    }

    /**
     * @param $controller
     * @param $action
     * @return bool
     * QuanLyCongViec;Index
     */
    public static function isAccess2($controller, $action){
        if(Yii::$app->user->isGuest)
            return false;
        else{
            if(Yii::$app->user->identity->getId() == 1)
                return true;
            $action = ucfirst($action);
            $controller_action = "{$controller};{$action}";
            $user_id = Yii::$app->user->id;
            return true;
        }
    }
    ////
    public static function convertDMY2YMD($strDate){
        $arr = explode('/', $strDate);
        return implode('-', array_reverse($arr));
    }
    public static function covertYMD2DMY($strDate){
        if($strDate == '')
            return '';
        return date("d/m/Y", strtotime($strDate));
    }
    public static function covertYMD2TDMY($strDate){
        $arr = explode(' ', $strDate);
        $arrT = $arr[1];
        $arrPD =explode('-', $arr[0]);

        $arrD = implode('-', array_reverse($arrPD));
        $time =$arrD.' '.$arrT;
        return $time;
    }

    public static function covertTDMY2YMD($strDate){

        if (strpos(':',$strDate)>0){
            $arr = explode(' ', $strDate);
            $arrT = $arr[0];
            $arrPD =explode('-', $arr[1]);
            $arrD = implode('-', array_reverse($arrPD));
            $time =$arrD.' '.$arrT;
            return $time;
        }else
            $arr = explode('-', $strDate);
        $time = implode('-', array_reverse($arr));
        return $time;

    }

    public static function get_extension($imagetype)
    {
        if(empty($imagetype)) return false;
        switch($imagetype)
        {
            case 'image/bmp': return '.bmp';
            case 'image/cis-cod': return '.cod';
            case 'image/gif': return '.gif';
            case 'image/ief': return '.ief';
            case 'image/jpeg': return '.jpg';
            case 'image/pipeg': return '.jfif';
            case 'image/tiff': return '.tif';
            case 'image/x-cmu-raster': return '.ras';
            case 'image/x-cmx': return '.cmx';
            case 'image/x-icon': return '.ico';
            case 'image/x-portable-anymap': return '.pnm';
            case 'image/x-portable-bitmap': return '.pbm';
            case 'image/x-portable-graymap': return '.pgm';
            case 'image/x-portable-pixmap': return '.ppm';
            case 'image/x-rgb': return '.rgb';
            case 'image/x-xbitmap': return '.xbm';
            case 'image/x-xpixmap': return '.xpm';
            case 'image/x-xwindowdump': return '.xwd';
            case 'image/png': return '.png';
            case 'image/x-jps': return '.jps';
            case 'image/x-freehand': return '.fh';
            default: return false;
        }
    }
    public static function sendMailLogistics($content, $from, $to, $subject){

        $userSMTP = 'info@xnkbluesky.com';

        $pasSMTP = '#Dvhq3108$';//CauHinh::findOne(['ghi_chu' => 'pass_smtp'])->content;



        $transport = (new \Swift_SmtpTransport('mail.xnkbluesky.com', 465))

            ->setEncryption('SSL')

            ->setUsername($userSMTP)

            ->setPassword($pasSMTP)

        ;

        $mailer = new \Swift_Mailer($transport);

        $message = (new \Swift_Message('test'))

            ->setContentType( 'text/html')

            ->setSubject($subject)->setFrom($from)

            ->setTo($to)

            ->setBody($content)

        ;

        $result = $mailer->send($message);

        return $result;

    }
    public static function sendThongBaoUsers($arr_nguoi_nhan,$noi_dung,$title){

        $tokenArr = [];
        foreach ($arr_nguoi_nhan  as $index=>$item_uid){
            $thongBaoArr = TokenDevice::findAll(['active'=>1,'user_id'=>$item_uid]);
            foreach ($thongBaoArr as $item) {
                if(!in_array($item->token,$tokenArr))
                {
                    $tokenArr[] = $item->token;
                }
            }
        }

        if(count($tokenArr)>0)
        {
            self::ThongBao(implode('{{}}', array_filter($tokenArr)), $title, $noi_dung);
        }
        $save_thong_bao = new ThongBao();
        $save_thong_bao->user_id = Yii::$app->user->id;
        $save_thong_bao->title = $title;
        $save_thong_bao->noi_dung =$noi_dung;
        $save_thong_bao->type = ThongBao::App_CTV;
        if(!$save_thong_bao->save()){
            throw  new  HttpException(500, \yii\helpers\Html::errorSummary($save_thong_bao));
        }
    }
    public static function sendThongBaoUser($nguoi_nhan_id,$noi_dung,$title){
        $thongBaoArr = TokenDevice::findAll(['active'=>1,'user_id'=>$nguoi_nhan_id]);
        $tokenArr = [];
        foreach ($thongBaoArr as $item) {
            $tokenArr[] = $item->token;
        }
        if(count($tokenArr)>0)
        {
            self::ThongBao(implode('{{}}', array_filter($tokenArr)), $title, $noi_dung);
        }
        $save_thong_bao = new ThongBao();
        $save_thong_bao->user_id = Yii::$app->user->id;
        $save_thong_bao->title = $title;
        $save_thong_bao->noi_dung =$noi_dung;
        $save_thong_bao->nguoi_nhan_id = $nguoi_nhan_id;
        $save_thong_bao->type = ThongBao::App_CTV;
        if(!$save_thong_bao->save()){
            throw  new  HttpException(500, \yii\helpers\Html::errorSummary($save_thong_bao));

        }
    }
    public static function ThongBao($tokenDevice,$title,$body){

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://notification.andin.io/index.php?r=api',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'tokenSecret' => md5(md5('ANDINJSC').date("YmdHi")),
                'tokenDevice' => $tokenDevice,
                'title' => $title,
                'body' => $body
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
    }
    // dt2 - dt1
    public static function tinhSoNgay($dt1, $dt2){
        $t1 = strtotime($dt1);
        $t2 = strtotime($dt2);

        $dtd = new \stdClass();
        $dtd->interval = $t2 - $t1;
        $dtd->total_sec = abs($t2-$t1);
        $dtd->total_min = floor($dtd->total_sec/60);
        $dtd->total_hour = floor($dtd->total_min/60);
        $dtd->total_day = floor($dtd->total_hour/24);

        $dtd->day = $dtd->total_day;
        $dtd->hour = $dtd->total_hour -($dtd->total_day*24);
        $dtd->min = $dtd->total_min -($dtd->total_hour*60);
        $dtd->sec = $dtd->total_sec -($dtd->total_min*60);
        return $dtd->total_day;
    }

    public static function getQuy($thang){
        $arr_quy = [
            1 => 'Quý I',
            2 => 'Quý I',
            3 => 'Quý I',
            4 => 'Quý II',
            5 => 'Quý II',
            6 => 'Quý II',
            7 => 'Quý III',
            8 => 'Quý III',
            9 => 'Quý III',
            10 => 'Quý IV',
            11 => 'Quý IV',
            12 => 'Quý IV',
        ];
        return $arr_quy[$thang];
    }

    public static function sendMailGun($content, $from, $to, $subject){
        $userSMTP = 'info@xnkbluesky.com';

        $pasSMTP = '#Dvhq3108$';//CauHinh::findOne(['ghi_chu' => 'pass_smtp'])->content;



        $transport = (new \Swift_SmtpTransport('mail.xnkbluesky.com', 465))

            ->setEncryption('SSL')

            ->setUsername($userSMTP)

            ->setPassword($pasSMTP)

        ;

        $mailer = new \Swift_Mailer($transport);

        $message = (new \Swift_Message('test'))

            ->setContentType( 'text/html')

            ->setSubject($subject)->setFrom($from)

            ->setTo($to)

            ->setBody($content)

        ;

        $result = $mailer->send($message);

        return $result;
    }

    public static function sendMailAmzon($content, $form, $to, $subject){
//        \Yii::$app->mail->getSES()->enableVerifyHost(true);
//        \Yii::$app->mail->getSES()->enableVerifyPeer(true);
        \Yii::$app->mail->compose('contact/html', ['contactForm' => $content])
            ->setFrom([$form => 'QUAN LY CONG VIEC'])
//            ->setFrom($form)
            ->setTo($to)
            ->setSubject($subject)
            ->send();
    }

    public static function pushNotification($content, $ids){
        \Yii::$app->webpusher->userPush($content, $ids);
    }
    /**
     * @param $name
     * Quý I, Quý II, Quý III, Quý IV
     * @param $nam
     * Năm
     * @return mixed
     *
     */

    public static function getRangeMonthsByQuy($name = "quy-i", $nam){
        $quy = [
            'quy-i' => [
                'from' => mktime(0, 0, 0, 1, 1, $nam),
                'to' => mktime(0, 0, 0, 3, 31, $nam)
            ],
            'quy-ii' => [
                'from' => mktime(0, 0, 0, 4, 1, $nam),
                'to' => mktime(0, 0, 0, 6, 30, $nam)
            ],
            'quy-iii' => [
                'from' => mktime(0, 0, 0, 7, 1, $nam),
                'to' => mktime(0, 0, 0, 9, 30, $nam)
            ],
            'quy-iv' => [
                'from' => mktime(0, 0, 0, 10, 1, $nam),
                'to' => mktime(0, 0, 0, 12, 31, $nam)
            ],
        ];
        return $quy[$name];
    }

    /**
     * @param $content string
     * @param $file_name string
     * @param $title string
     * @param $subject string
     * @param $header string
     * @param $footer string
     * @param $margin array
     * $margin[0 => top, 1 => left, 2 => bottom, 3 => right]
     * @param $temPath string
     * @param $urlTaiFile string
     * 'files_excel/'.$file_name
     */
    public static function exportPDF($content, $file_name, $title, $subject, $header, $footer, $margin, $temPath, $urlTaiFile){
        $pdf = new \kartik\mpdf\Pdf([
            'mode' => \kartik\mpdf\Pdf::MODE_UTF8, // leaner size using standard fonts
            'content' => $content,
            'filename' => $file_name,
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '.kv-heading-1{font-size:18px}
                        body{font-family: "Times"; font-size: 8pt} table td,table th{padding: 5px}',
            'options' => [
                'title' => $title,
                'subject' => $subject
            ],
            'methods' => [
                'SetHeader' => [$header],
                'SetFooter' => [$footer],
            ],
            'destination' => Pdf::DEST_FILE,
            'marginLeft' => $margin[1],
            'marginRight' => $margin[3],
            'marginTop' => $margin[0],
            'marginBottom' => $margin[2],
            'tempPath' => $temPath
        ]);

        $pdf->render();
        echo Json::encode([
            'title' => 'Tải file kết quả',
            'content' => \yii\helpers\Html::a('<i class="fa fa-cloud-download"></i> Nhấn vào đây để tải file về!', $urlTaiFile, ['class' => 'text-primary', 'target' => '_blank'])
        ]);
    }

    public static function roundUpToAny($n,$x) {
        return (round($n)%$x === 0) ? round($n) : round(($n+$x/2)/$x)*$x;
    }
    public static function weekOfMonth($date) {
       return 1 + date("W", strtotime(date('Y-m-t',strtotime($date)))) - date("W", strtotime(date('Y-m-1',strtotime($date))));

    }
    public static function validateDate($date){
        $day = (int)explode('/',$date)[0];
        $month=(int)explode('/',$date)[1];
        $year=(int)explode('/',$date)[2];
        return checkdate( $month,  $day , $year);
    }
}
