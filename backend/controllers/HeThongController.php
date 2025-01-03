<?php namespace backend\controllers;

use backend\models\Banner;
use backend\models\CauHinh;
use backend\models\DanhMuc;
use backend\models\PhuPhi;
use backend\models\TinTuc;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\web\HttpException;
use yii\web\UploadedFile;

class HeThongController extends CoreApiController
{
    public function actionCapNhatBanner()
    {

    }

    public function actionCapNhatCauHinh()
    {
        $this->checkField(['id', 'content']);
        $id = $this->dataPost['id'];//id config
        if ($id == '') {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $config = CauHinh::findOne($id);
        if (is_null($config)) {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $config->content = $this->dataPost['content'];
        if (!$config->save()) {
            throw new HttpException(500, Html::errorSummary($config));
        };
        return $this->outputSuccess('', 'Cập nhật cấu hình thành công');
    }

    public function actionGetCauHinh()
    {
        $this->checkGetInput(['id']);
        $id = $this->dataGet['id'];//id config
        if ($id == '') {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $config = CauHinh::findOne($id);
        if (is_null($config)) {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        return $this->outputSuccess([
            'id' => $config->id,
            'content' => $config->content,
            'name' => $config->name,
        ]);
    }

    public function actionDanhSachCauHinh()
    {
        return CauHinh::find()->select(['id','name','content','ghi_chu'])->all();
    }

    public function actionHeSinhThai()
    {
        $cauHinh = CauHinh::findOne(14);
        return $this->outputSuccess([
            'image' => CauHinh::getImage($cauHinh->image),
            'content' => $cauHinh->ghi_chu
        ]);
    }

    public function actionCapNhatHeSinhThai()
    {
        $this->checkField(['content']);
        $cauHinh = CauHinh::findOne(14);
        $cauHinh->ghi_chu = $this->dataPost['content'];
        $files = UploadedFile::getInstanceByName('image');
        if (!empty($files)) {
            $path = (dirname(dirname(__DIR__))) . '/upload-file';
            $link = date('Y/m/d') . '/' . '_' . \Yii::$app->security->generateRandomString() . $this->get_extension_image($files->type);
            $cauHinh->image = $link;
            if (FileHelper::createDirectory($path . '/' . date('Y/m/d') . '/', $mode = 0775, $recursive = true)) {
                $files->saveAs($path . '/' . $link);
            }
        }
        if (!$cauHinh->save()) {
            throw new HttpException(500, Html::errorSummary($cauHinh));
        }
        return $this->outputSuccess('', "Cập nhât hệ sinh thái thành công");
    }

    public function actionGioiThieuApp()
    {
        $cauHinh = CauHinh::findOne(13);
        return $this->outputSuccess([
            'image' => CauHinh::getImage($cauHinh->image),
            'content' => $cauHinh->ghi_chu
        ]);
    }

    public function actionCapNhatGioiThieuApp()
    {
        $this->checkField(['content']);
        $cauHinh = CauHinh::findOne(13);
        $cauHinh->ghi_chu = $this->dataPost['content'];
        $files = UploadedFile::getInstanceByName('image');
        if (!empty($files)) {
            $path = (dirname(dirname(__DIR__))) . '/upload-file';
            $link = date('Y/m/d') . '/' . '_' . \Yii::$app->security->generateRandomString() . $this->get_extension_image($files->type);
            $cauHinh->image = $link;
            if (FileHelper::createDirectory($path . '/' . date('Y/m/d') . '/', $mode = 0775, $recursive = true)) {
                $files->saveAs($path . '/' . $link);
            }
        }
        if (!$cauHinh->save()) {
            throw new HttpException(500, Html::errorSummary($cauHinh));
        }
        return $this->outputSuccess('', "Cập nhât giới thiệu app thành công");
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
            'nhanLich' => $cauHinh->getNoiDung(17),
            'rutTien' => $cauHinh->getNoiDung(18),
        ]);
    }

    public function actionCapNhatLienHeVaTroGiup()
    {
        $this->checkField(['donViChuQuan', 'truSo', 'website', 'hotline', 'email', 'nhanLich', 'rutTien']);
        $cauHinh = CauHinh::findOne(19);
        $files = UploadedFile::getInstanceByName('image');
        if (!empty($files)) {
            $path = (dirname(dirname(__DIR__))) . '/upload-file';
            $link = date('Y/m/d') . '/' . '_' . \Yii::$app->security->generateRandomString() . $this->get_extension_image($files->type);
            $cauHinh->image = $link;
            if (FileHelper::createDirectory($path . '/' . date('Y/m/d') . '/', $mode = 0775, $recursive = true)) {
                $files->saveAs($path . '/' . $link);
            }
        }
        if (!$cauHinh->save()) {
            throw new HttpException(500, Html::errorSummary($cauHinh));
        }
        $cauHinhs = ['donViChuQuan' => 7, 'truSo' => 8, 'website' => 9, 'hotline' => 20, 'email' => 2, 'nhanLich' => 17, 'rutTien' => 18];
        foreach ($cauHinhs as $field => $item) {
            $cauHinh = CauHinh::findOne($item);
            $cauHinh->updateContent($this->dataPost[$field]);
        }
        return $this->outputSuccess('', "Cập nhât liên hệ / hỗ trợ thành công");
    }

    public function actionThemMoiBanner()
    {
        $this->checkField([
            'link',
        ]);
        $banner = new Banner();
        $image = $this->saveImage();
        if ($image == "") {
            throw new HttpException(500, "Vui lòng chọn ảnh");
        }
        $banner->image = $image;
        $banner->link = $this->dataPost['link'];
        $banner->user_id = $this->uid;
        if (!$banner->save()) {
            throw new HttpException(500, Html::errorSummary($banner));
        }
        return $this->outputSuccess("", 'Lưu banner thành công');
    }

    public function actionSuaBanner()
    {
        $this->checkField([
            'link', 'banner_id'
        ]);
        if ($this->dataPost['banner_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền banner_id");
        }
        $banner = Banner::findOne($this->dataPost['banner_id']);
        if (is_null($banner)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $image = $this->saveImage();
        if ($image !== "") {
            $banner->image = $image;
        }
        $banner->link = $this->dataPost['link'];
        $banner->user_id = $this->uid;
        if (!$banner->save()) {
            throw new HttpException(500, Html::errorSummary($banner));
        }
        return $this->outputSuccess("", 'Lưu banner thành công');
    }

    public function actionXoaBanner()
    {
        $this->checkField(['banner_id']);
        if ($this->dataPost['banner_id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $tinTuc = Banner::findOne($this->dataPost['banner_id']);
        if (is_null($tinTuc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $tinTuc->delete();
        return $this->outputSuccess("", 'Xóa banner thành công');
    }

    public function actionDanhSach()
    {
        $banner = Banner::find()
            ->select(['link', 'image', 'id'])
            ->andFilterWhere(['active' => 1, 'status' => 1]);
        $count = $banner->count();
        $banner = $banner->limit($this->limit)->offset(($this->page - 1) * $this->limit)->orderBy(['created' => $this->sort == 1 ? SORT_DESC : SORT_ASC])->all();
        if (count($banner) > 0) {
            foreach ($banner as $item) {
                /** @var Banner $item */
                $item->image = CauHinh::getImage($item->image);
            }
        }
        return $this->outputListSuccess2($banner, $count);
    }

    public function actionDanhSachHuongDanApp()
    {
        $tinTuc = TinTuc::find()
            ->select(['tieu_de', 'id', 'link'])
            ->andFilterWhere(['active' => 1, 'type' => 7]);
        if ($this->tuKhoa != "") {
            $tinTuc->andFilterWhere(['like', 'tieu_de', $this->dataGet['tuKhoa']]);
        }
        return $this->outputSuccess($tinTuc->all());
    }

    public function actionChiTietHuongDanApp()
    {
        $this->checkGetInput(['id']);
        if ($this->dataGet['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $tinTuc = TinTuc::findOne(['id' => $this->dataGet['id'], 'active' => 1, 'type' => 7]);
        if (is_null($tinTuc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        return $this->outputSuccess([
            'id' => $tinTuc->id,
            'tieu_de' => $tinTuc->tieu_de,
            'link' => $tinTuc->link,
        ]);
    }

    public function actionTaoMoiHuongDanApp()
    {
        $this->checkField([
            'tieu_de',
            'link',
        ]);
        if ($this->dataPost['tieu_de'] == "") {
            throw new HttpException(500, 'Vui lòng nhập tiêu đề');
        }
        if ($this->dataPost['link'] == "") {
            throw new HttpException(500, 'Vui lòng gán link bài viết');
        }
        $tinTuc = new TinTuc();
        $tinTuc->status = 1;
        $tinTuc->tieu_de = $this->dataPost['tieu_de'];
        $tinTuc->type = 7;
        $tinTuc->link = $this->dataPost['link'];
        $tinTuc->user_id = $this->uid;
        if (!$tinTuc->save()) {
            throw new HttpException(500, \yii\bootstrap\Html::errorSummary($tinTuc));
        }
        return $this->outputSuccess("", 'Lưu hướng dẫn app thành công');
    }

    public function actionXoaHuongDan()
    {
        $this->checkField(['id']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $tinTuc = TinTuc::findOne($this->dataPost['id']);
        if (is_null($tinTuc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        $tinTuc->updateAttributes(['active' => 0, 'updated' => date('Y-m-d H:i:s')]);
        return $this->outputSuccess("", 'Xóa hướng dẫn thành công');
    }

    public function actionSuaHuongDanApp()
    {
        $this->checkField([
            'id',
            'tieu_de',
            'link',
        ]);

        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $tinTuc = TinTuc::findOne($this->dataPost['id']);
        if (is_null($tinTuc)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if ($this->dataPost['tieu_de'] == "") {
            throw new HttpException(500, 'Vui lòng nhập tiêu đề');
        }
        if ($this->dataPost['link'] == "") {
            throw new HttpException(500, 'Vui lòng gán link bài viết');
        }
        $tinTuc->tieu_de = $this->dataPost['tieu_de'];
        $tinTuc->link = $this->dataPost['link'];
        $tinTuc->user_id = $this->uid;
        if (!$tinTuc->save()) {
            throw new HttpException(500, \yii\bootstrap\Html::errorSummary($tinTuc));
        }
        return $this->outputSuccess("", 'Lưu hướng dẫn app thành công');
    }

    public function actionGetChietKhau()
    {
        return $this->outputSuccess([
            'chiet_khau_don' => CauHinh::find()->andFilterWhere(['id' => 24])->select(['id', 'name', 'content'])->one(),
            'phu_thu_an_trua' => CauHinh::find()->andFilterWhere(['id' => 25])->select(['id', 'name', 'content'])->one(),
            'phu_thu_them_tre' => CauHinh::find()->andFilterWhere(['id' => 26])->select(['id', 'name', 'content'])->one(),
            'phu_phi_them_gio' => CauHinh::find()->andFilterWhere(['id' => 27])->select(['id', 'name', 'content'])->one(),
            'phu_cap_them' => CauHinh::find()->andFilterWhere(['id' => 28])->select(['id', 'name', 'content'])->one()
        ]);
    }

    public function actionSaveChietKhau()
    {
        $this->checkField(['id', 'content']);
        if ($this->dataPost['id'] == "") {
            throw new HttpException(500, "Vui lòng truyền id");
        }
        $cauHinh = CauHinh::findOne($this->dataPost['id']);
        if (is_null($cauHinh)) {
            throw new HttpException(403, "Không xác định dữ liệu");
        }
        if (intval($this->dataPost['content']) == 0) {
            throw new HttpException(500, "Chiết khấu phải lớn hơn 0");
        }
        $cauHinh->content = $this->dataPost['content'];
        if (!$cauHinh->save()) {
            throw new HttpException(500, \yii\bootstrap\Html::errorSummary($cauHinh));
        }
        return $this->outputSuccess('', 'Cập nhật chiết khấu thành công');
    }
}
