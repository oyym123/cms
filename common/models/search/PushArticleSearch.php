<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\PushArticle;

/**
 * PushArticleSearch represents the model behind the search form of `common\models\PushArticle`.
 */
class PushArticleSearch extends PushArticle
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'b_id', 'column_id', 'rules_id', 'status'], 'integer'],
            [['column_name', 'domain', 'from_path', 'keywords', 'title_img', 'content', 'intro', 'title', 'push_time', 'created_at', 'updated_at'], 'safe'],
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
        $_GET['domain'] = 0;
        $data = \Yii::$app->request->get('PushArticleSearch');
        if (isset($data['domain_id']) && is_array($data['domain_id'])) {
            $data['domain_id'] = $data['domain_id'][0];
        }

        if (!isset($data['domain_id'])) {
            $data['domain_id'] = 16;
        }

        $query = PushArticle::findx($data['domain_id']);

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
            'b_id' => $this->b_id,
            'column_id' => $this->column_id,
            'rules_id' => $this->rules_id,
            'domain_id' => $this->domain_id,
            'status' => $this->status,
            'push_time' => $this->push_time,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'column_name', $this->column_name])
            ->andFilterWhere(['like', 'domain', $this->domain])
            ->andFilterWhere(['like', 'from_path', $this->from_path])
            ->andFilterWhere(['like', 'keywords', $this->keywords])
            ->andFilterWhere(['like', 'title_img', $this->title_img])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'intro', $this->intro])
            ->andFilterWhere(['like', 'title', $this->title]);
        $query->orderBy('id desc');
        return $dataProvider;
    }
}
