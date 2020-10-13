<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Keywords;
use yii\data\Pagination;

/**
 * KeywordsSearch represents the model behind the search form of `common\models\Keywords`.
 */
class KeywordsSearch extends Keywords
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sort', 'search_num', 'type', 'status', 'rules_id'], 'integer'],
            [['keywords', 'form', 'rank', 'title', 'content', 'note', 'url', 'created_at', 'updated_at'], 'safe'],
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
        $query = Keywords::find()->select('keywords.*,aizhan_rules.column_id');

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 1000],
        ]);

//        $dataProvider->pagination->defaultPageSize =10;

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->innerJoinWith('aizhanRules', 'aizhanRules.id = keywords.rules_id');

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'sort' => $this->sort,
            'search_num' => $this->search_num,
            'type' => $this->type,
            'keywords.status' => $this->status,
            'keywords.note' => $this->note,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'keywords', $this->keywords])
            ->andFilterWhere(['like', 'form', $this->form])
            ->andFilterWhere(['like', 'rank', $this->rank])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'url', $this->url]);

        return $dataProvider;
    }
}
