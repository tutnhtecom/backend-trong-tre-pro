<?php

namespace backend\models\search;

use backend\models\ChucNang;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ChucNangSearch represents the model behind the search form about `backend\models\ChucNang`.
 */
class ChucNangSearch extends ChucNang
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name', 'nhom', 'controller_action'], 'safe'],
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
        $query = ChucNang::find();

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

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'nhom', $this->nhom])
            ->andFilterWhere(['like', 'controller_action', $this->controller_action]);

        return $dataProvider;
    }
}
