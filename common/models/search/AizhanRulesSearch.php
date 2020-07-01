<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AizhanRules;

/**
 * AizhanRulesSearch represents the model behind the search form of `common\models\AizhanRules`.
 */
class AizhanRulesSearch extends AizhanRules
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'sort', 'status', 'domain_id', 'column_id', 'max_search_num', 'user_id'], 'integer'],
            [['site_url', 'note', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = AizhanRules::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'category_id' => $this->category_id,
            'sort' => $this->sort,
            'status' => $this->status,
            'domain_id' => $this->domain_id,
            'column_id' => $this->column_id,
            'max_search_num' => $this->max_search_num,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'site_url', $this->site_url])
            ->andFilterWhere(['like', 'note', $this->note]);

        return $dataProvider;
    }
}
