<?php

namespace backend\controllers;

use backend\models\VaiTro;
use common\models\myAPI;
use common\models\User;
use Yii;
use backend\models\DanhMuc;
use backend\models\search\DanhMucSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use \yii\web\Response;
use yii\helpers\Html;

class DanhMucController extends Controller {

    //load-child
    public function actionLoadChild(){
        if(!empty($_POST['parent'])){
            $data = DanhMuc::find()->select(['id', 'name'])
                ->andFilterWhere(['in','parent_id', $_POST['parent']])
                ->andFilterWhere(['active' => 1])
                ->andFilterWhere(['type'=>$_POST['type']])
                ->all();
        }else{
            $data = '';
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $data;
    }

//    OLD
    /** index */
    public function actionIndex()
    {
        $searchModel = new DanhMucSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /** view */
    public function actionView($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);

        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title'=> "Danh mục ".$model->name,
                'content'=>$this->renderAjax('view', [
                    'model' => $model,
                ]),
                'footer'=> Html::button('<i class="fa fa-close"></i> Đóng lại',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                    Html::a('<i class="fa fa-save"></i> Cập nhật',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
            ];
        }else{
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }

    /** create */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new DanhMuc();

        $quan_huyen = [];

        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Tạo mới danh mục",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                        'quan_huyen' => $quan_huyen
                    ]),
                    'footer'=> Html::button('<i class="fa fa-close"></i> Đóng lại',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::button('<i class="fa fa-save"></i> Lưu lại',['class'=>'btn btn-primary','type'=>"submit"])

                ];
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Tạo mới danh mục",
                    'danhmuc' => $model,
                    'content'=>'<span class="text-success">Thêm danh mục thành công!</span>',
                    'footer'=> Html::button('<i class="fa fa-close"></i> Đóng lại',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::a('<i class="glyphicon glyphicon-plus"></i> Tạo thêm',['create'],['class'=>'btn btn-primary','role'=>'modal-remote'])

                ];
            }else{
                return [
                    'title'=> "Tạo mới danh mục",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                        'quan_huyen' => $quan_huyen
                    ]),
                    'footer'=> Html::button('<i class="fa fa-close"></i> Đóng lại',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::button('<i class="fa fa-save"></i> Lưu lại',['class'=>'btn btn-primary','type'=>"submit"])

                ];
            }
        }else{
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['index']);
            } else {
                return $this->render('create', [
                    'model' => $model,
                    'quan_huyen' => $quan_huyen
                ]);
            }
        }
    }

    /** update */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $quan_huyen = [];

        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Cập nhật danh mục ".$model->name,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                        'quan_huyen' => $quan_huyen
                    ]),
                    'footer'=> Html::button('<i class="fa fa-close"></i> Đóng lại',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::button('<i class="fa fa-save"></i> Lưu lại',['class'=>'btn btn-primary','type'=>"submit"])
                ];
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Thông tin {$model->name}",
                    'content'=>$this->renderAjax('view', [
                        'model' => $model,
                        'quan_huyen' => $quan_huyen
                    ]),
                    'footer'=> Html::button('<i class="fa fa-close"></i> Đóng lại',['class'=>'btn btn-default pull-left btn-edit-after-save','data-dismiss'=>"modal"]).
                        Html::a('<i class="fa fa-edit"></i> Chỉnh sửa',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];
            }else{
                return [
                    'title'=> "Cập nhật danh mục ".$model->name,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                        'quan_huyen' => $quan_huyen
                    ]),
                    'footer'=> Html::button('<i class="fa fa-close"></i> Đóng lại',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::button('<i class="fa fa-save"></i> Lưu lại',['class'=>'btn btn-primary','type'=>"submit"])
                ];
            }
        }else{
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                    'quan_huyen' => $quan_huyen
                ]);
            }
        }
    }

    /** delete */
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $model->active = 0;
        $model->save();

        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            return $this->redirect(['index']);
        }


    }

    /** bulk-deleye */
    public function actionBulkDelete()
    {
        $request = Yii::$app->request;
        $pks = explode(',', $request->post( 'pks' ));
        foreach ( $pks as $pk ) {
            $model = $this->findModel($pk);
            $model->delete();
        }

        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            return $this->redirect(['index']);
        }

    }

    protected function findModel($id)
    {
        if (($model = DanhMuc::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Danh mục không tồn tại');
        }
    }

    //get-khu-vuc
    public function actionGetKhuVuc(){
        if(!empty($_POST['value'])){
            $model =  DanhMuc::findOne($_POST['value']);
            $data = DanhMuc::find()->select(['id', 'name'])
                ->andFilterWhere(['parent_id' => $model->id])
                ->andFilterWhere(['active' => 1])
                ->andFilterWhere(['type'=>$_POST['type']])
                ->all();
        }else{
            $data = '';
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $data;
    }
    public function actionGetKhuVucChonNhieu(){
        if(!empty($_POST['value'])){
            $model =  ArrayHelper::map(DanhMuc::find()->andFilterWhere(['in','name',$_POST['value']])->all(),'id','id');
            $data = DanhMuc::find()->select(['id', 'name'])
                ->andFilterWhere(['in','parent_id', $model])
                ->andFilterWhere(['active' => 1])
                ->andFilterWhere(['type'=>$_POST['type']])
                ->all();
        }else{
            $data = '';
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $data;
    }
    public function actionGetNhom(){
        if(!empty($_POST['value'])){
            $model = DanhMuc::findOne($_POST['value']);
            $data = DanhMuc::find()->select(['id','code', 'name'])
                ->andFilterWhere(['parent_id' => $model->id])
                ->andFilterWhere(['active' => 1])
                ->andFilterWhere(['type'=>$_POST['type']])
                ->all();
        }else{
            $data = '';
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $data;
    }
}
