<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\DbName;

/**
 * DbNameSearch represents the model behind the search form of `common\models\DbName`.
 */
class DbNameSearch extends DbName
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['baidu_token', 'baidu_password', 'baidu_account', 'domain', 'name', 'updated_at', 'created_at'], 'safe'],
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
        $query = DbName::find();

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
            'status' => $this->status,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'baidu_token', $this->baidu_token])
            ->andFilterWhere(['like', 'baidu_password', $this->baidu_password])
            ->andFilterWhere(['like', 'baidu_account', $this->baidu_account])
            ->andFilterWhere(['like', 'domain', $this->domain])
            ->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
