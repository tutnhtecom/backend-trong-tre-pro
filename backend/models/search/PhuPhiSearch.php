<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\PhuPhi;

/**
 * PhuPhiSearch represents the model behind the search form about `backend\models\PhuPhi`.
 */
class PhuPhiSearch extends PhuPhi
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'type_id'], 'integer'],
            [['tong_tien'], 'number'],
            [['ghi_chu', 'active', 'created', 'updated', 'tieu_de'], 'safe'],
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
    public function search($params)
    {
        $query = PhuPhi::find();

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
            'tong_tien' => $this->tong_tien,
            'created' => $this->created,
            'updated' => $this->updated,
            'user_id' => $this->user_id,
            'type_id' => $this->type_id,
        ]);

        $query->andFilterWhere(['like', 'ghi_chu', $this->ghi_chu])
            ->andFilterWhere(['like', 'active', $this->active])
            ->andFilterWhere(['like', 'tieu_de', $this->tieu_de]);

        return $dataProvider;
    }
}
