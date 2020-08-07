<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TemplateTpl;

/**
 * TemplateTplSearch represents the model behind the search form of `common\models\TemplateTpl`.
 */
class TemplateTplSearch extends TemplateTpl
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'cate', 't_tags', 't_detail', 't_list', 't_common', 't_home', 't_inside','type', 'status', 'user_id'], 'integer'],
            [['t_customize', 't_ids', 'created_at', 'updated_at','name'], 'safe'],
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
        $query = TemplateTpl::find();

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
            'cate' => $this->cate,
            't_tags' => $this->t_tags,
            't_inside' => $this->t_inside,
            't_detail' => $this->t_detail,
            't_list' => $this->t_list,
            't_common' => $this->t_common,
            't_home' => $this->t_home,
            'type' => $this->type,
            'status' => $this->status,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 't_customize', $this->t_customize])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 't_ids', $this->t_ids]);

        return $dataProvider;
    }
}
