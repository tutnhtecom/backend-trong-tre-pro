<?php

namespace backend\controllers;

use backend\models\DonDichVu;
use backend\models\NhanLich;
use backend\models\VaiTro;
use common\models\myAPI;
use common\models\User;
use Mpdf\Tag\Tr;
use Yii;
use yii\base\Security;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\filters\AccessControl;
use common\models\LoginForm;
use yii\helpers\VarDumper;
use yii\web\HttpException;
use yii\web\Response;

class SiteController extends CoreController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'update-hotline','get-form-giao-vien','save-khao-sat','get-form-phu-huynh','get-khao-sat'],
                        'allow' => true,
//                        'roles' => ['?']
                    ],
                    [
                        'actions' => ['error'],
                        'allow' => true
                    ],
                    [
                        'actions' => ['logout', 'loadform', 'load-form-modal', 'doimatkhau', 'index', 'danh-sach-khach-hang', 'update-san-pham', 'test','get-form-giao-vien','get-form-phu-huynh'],
                        'allow' => true,
//                        'matchCallback' => function($rule, $action){
//                            return Yii::$app->user->identity->username == 'adamin';
//                        }
                        'roles' => ['@']
                    ],
                ],
//                'denyCallback' => function ($rule, $action) {
//                    throw new Exception('You are not allowed to access this page', 404);
//                }
            ],
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'logout' => ['post'],
//                ],
//            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /** index */
    public function actionIndex()
    {
        $this->redirect(Url::toRoute('user/index'));
    }

    /** login */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->renderPartial('login', [
                'model' => $model,
            ]);
        }
    }
    public function actionGetFormGiaoVien()
    {
        return $this->renderPartial('_form');
    }
    public function actionGetFormPhuHuynh()
    {
        return $this->renderPartial('_form_2');
    }
    public function actionSaveKhaoSat()
    {
        $nhanLich = NhanLich::find()->andFilterWhere(['giao_vien_id'=>$_POST['giao_vien_id'],'don_dich_vu_id'=>$_POST['don_dich_vu_id'],'active'=>1])->one();
        /** @var NhanLich $nhanLich */
        if(!is_null($nhanLich)){
            foreach ($_POST as $key => $value) {
                if (empty($value)) {
                    $_POST[$key] = "";
                }
            }
            foreach (range(1,7) as $item){
                if (!isset($_POST['cau_hoi_'.$item])){
                    $_POST['cau_hoi_'.$item] = [];
                }
            }
            if (!isset($_POST['']))
            $nhanLich->form_danh_gia = json_encode($_POST);
            $nhanLich->save();
            return true;
        }
        return false;
    }
   public function actionGetKhaoSat()
   {
       if (!isset($_GET['don_dich_vu_id'])) {
           throw new HttpException(500, "Không xác định dữ liệu");
       }
       $donDichVu = DonDichVu::findOne($_GET['don_dich_vu_id']);
       $nhanLich = NhanLich::find()->andFilterWhere(['giao_vien_id'=>$donDichVu->giao_vien_id,'don_dich_vu_id'=>$_GET['don_dich_vu_id'],'active'=>1])->one();
       /** @var NhanLich $nhanLich */
       if (is_null($nhanLich)) {
           throw new HttpException(500, "Không xác định dữ liệu");
       }
       $data = json_decode($nhanLich->form_danh_gia);
       return $this->renderPartial('_form_2',['data'=>$data]);
   }
    /** logout */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

}
