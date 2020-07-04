<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LongKeywords;

/**
 * LongKeywordsSearch represents the model behind the search form of `common\models\LongKeywords`.
 */
class LongKeywordsSearch extends LongKeywords
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'key_id', 'key_search_num',  'status', 'from', 'type'], 'integer'],
            [['m_down_name', 'm_search_name', 'm_related_name', 'name', 'pc_down_name', 'pc_search_name', 'pc_related_name', 'keywords', 'remark', 'url', 'created_at', 'updated_at'], 'safe'],
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
        $query = LongKeywords::find();

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

        if (!empty(\Yii::$app->request->get('key_search_num_min')) && !empty(\Yii::$app->request->get('key_search_num_max'))) {
            $query->andFilterWhere(['between', 'key_search_num', \Yii::$app->request->get('key_search_num_min'),\Yii::$app->request->get('key_search_num_max')]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'key_id' => $this->key_id,
            'key_search_num' => $this->key_search_num,
            'status' => $this->status,
            'type' => $this->type,
            'from' => $this->from,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'm_down_name', $this->m_down_name])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'm_search_name', $this->m_search_name])
            ->andFilterWhere(['like', 'm_related_name', $this->m_related_name])
            ->andFilterWhere(['like', 'pc_down_name', $this->pc_down_name])
            ->andFilterWhere(['like', 'pc_search_name', $this->pc_search_name])
            ->andFilterWhere(['like', 'pc_related_name', $this->pc_related_name])
            ->andFilterWhere(['like', 'keywords', $this->keywords])
            ->andFilterWhere(['like', 'remark', $this->remark])
            ->andFilterWhere(['like', 'url', $this->url]);

        return $dataProvider;
    }
}
