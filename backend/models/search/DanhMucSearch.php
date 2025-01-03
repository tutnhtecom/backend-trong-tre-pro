<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\DanhMuc;

/**
 * DanhMucSearch represents the model behind the search form about `backend\models\DanhMuc`.
 */
class DanhMucSearch extends DanhMuc
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'safe'],
            [['type'], 'safe'],
            [['hide', 'parent_id'], 'safe'],
            [['name', 'code'], 'safe'],
            [['viet_tat'], 'safe'],
            [['parent_id'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $nhom = '')
    {
        $query = DanhMuc::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);
        if($nhom != '')
            $query->andFilterWhere(['type' => $nhom]);

        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'type', $this->type]);

        return $dataProvider;
    }

    public function searchLoaiHoiVien($params, $nhom = '')
    {
        $query = DanhMuc::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'type' => DanhMuc::PHAN_LOAI_HOI_VIEN
        ]);

        $query->andFilterWhere([
            'id' => $this->id,
        ]);
        if($nhom != '')
            $query->andFilterWhere(['type' => $nhom]);

        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'type', $this->type]);
        $query->andFilterWhere(['like', 'viet_tat', $this->viet_tat]);

        return $dataProvider;
    }

    public function searchNganHang($params, $nhom = '')
    {
        $query = DanhMuc::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'type' => DanhMuc::NGAN_HANG
        ]);

        $query->andFilterWhere([
            'id' => $this->id,
        ]);
        if($nhom != '')
            $query->andFilterWhere(['type' => $nhom]);

        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'type', $this->type]);

        return $dataProvider;
    }

    public function searchXepLoaiHoiVien($params, $nhom = '')
    {
        $query = DanhMuc::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'type' => DanhMuc::XEP_LOAI_HOI_VIEN
        ]);

        $query->andFilterWhere([
            'id' => $this->id,
        ]);
        if($nhom != '')
            $query->andFilterWhere(['type' => $nhom]);

        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'type', $this->type]);

        return $dataProvider;
    }
}
