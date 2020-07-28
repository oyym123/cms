<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ArticleRules;

/**
 * ArticleRulesSearch represents the model behind the search form of `common\models\ArticleRules`.
 */
class ArticleRulesSearch extends ArticleRules
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'domain_id', 'column_id', 'one_page_num_min', 'one_page_num_max', 'one_page_word_min', 'one_page_word_max', 'one_day_push_num', 'user_id', 'status'], 'integer'],
            [['name', 'method_ids', 'push_time_sm', 'push_time_bd', 'created_at', 'updated_at'], 'safe'],
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
        $query = ArticleRules::find();

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
            'domain_id' => $this->domain_id,
            'column_id' => $this->column_id,
            'one_page_num_min' => $this->one_page_num_min,
            'one_page_num_max' => $this->one_page_num_max,
            'one_page_word_min' => $this->one_page_word_min,
            'one_page_word_max' => $this->one_page_word_max,
            'one_day_push_num' => $this->one_day_push_num,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'method_ids', $this->method_ids])
            ->andFilterWhere(['like', 'push_time_sm', $this->push_time_sm])
            ->andFilterWhere(['like', 'push_time_bd', $this->push_time_bd]);

        return $dataProvider;
    }
}
