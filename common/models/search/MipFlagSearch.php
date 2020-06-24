<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\MipFlag;

/**
 * MipFlagSearch represents the model behind the search form of `common\models\MipFlag`.
 */
class MipFlagSearch extends MipFlag
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'db_id', 'type', 'type_id', 'status','remain'], 'integer'],
            [['db_name', 'created_at', 'updated_at','url'], 'safe'],
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
        $query = MipFlag::find();

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
            'db_id' => $this->db_id,
            'type' => $this->type,
            'type_id' => $this->type_id,
            'remain' => $this->remain,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'db_name', $this->db_name]);
        $query->andFilterWhere(['like', 'url', $this->url]);

        return $dataProvider;
    }
}
