<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\BaiduKeywords;

/**
 * BaiduKeywordsSearch represents the model behind the search form of `common\models\BaiduKeywords`.
 */
class BaiduKeywordsSearch extends BaiduKeywords
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'pc_show_rate', 'pc_rank', 'competition', 'match_type', 'pc_click', 'pc_pv', 'pc_show', 'pc_ctr', 'all_show_rate', 'all_rank', 'all_cpc', 'all_click', 'all_pv', 'all_show', 'all_ctr', 'm_show_rate', 'm_rank', 'm_click', 'm_pv', 'status'], 'integer'],
            [['keywords', 'from_keywords', 'pc_cpc', 'charge', 'all_charge', 'm_charge', 'm_cpc', 'm_show', 'm_ctr', 'show_reasons', 'businessPoints', 'word_package', 'json_info', 'similar', 'created_at', 'updated_at'], 'safe'],
            [['bid', 'all_rec_bid', 'm_rec_bid'], 'number'],
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
        $query = BaiduKeywords::find();

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

        if (!empty(\Yii::$app->request->get('m_pv_min')) && !empty(\Yii::$app->request->get('m_pv_max'))) {
            $query->andFilterWhere(['between', 'm_pv', \Yii::$app->request->get('m_pv_min'), \Yii::$app->request->get('m_pv_max')]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'pc_show_rate' => $this->pc_show_rate,
            'pc_rank' => $this->pc_rank,
            'competition' => $this->competition,
            'match_type' => $this->match_type,
            'bid' => $this->bid,
            'pc_click' => $this->pc_click,
            'pc_pv' => $this->pc_pv,
            'pc_show' => $this->pc_show,
            'pc_ctr' => $this->pc_ctr,
            'all_show_rate' => $this->all_show_rate,
            'all_rank' => $this->all_rank,
            'all_cpc' => $this->all_cpc,
            'all_rec_bid' => $this->all_rec_bid,
            'all_click' => $this->all_click,
            'all_pv' => $this->all_pv,
            'all_show' => $this->all_show,
            'all_ctr' => $this->all_ctr,
            'm_show_rate' => $this->m_show_rate,
            'm_rank' => $this->m_rank,
            'm_rec_bid' => $this->m_rec_bid,
            'm_click' => $this->m_click,
            'm_pv' => $this->m_pv,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'keywords', $this->keywords])
            ->andFilterWhere(['like', 'from_keywords', $this->from_keywords])
            ->andFilterWhere(['like', 'pc_cpc', $this->pc_cpc])
            ->andFilterWhere(['like', 'charge', $this->charge])
            ->andFilterWhere(['like', 'all_charge', $this->all_charge])
            ->andFilterWhere(['like', 'm_charge', $this->m_charge])
            ->andFilterWhere(['like', 'm_cpc', $this->m_cpc])
            ->andFilterWhere(['like', 'm_show', $this->m_show])
            ->andFilterWhere(['like', 'm_ctr', $this->m_ctr])
            ->andFilterWhere(['like', 'show_reasons', $this->show_reasons])
            ->andFilterWhere(['like', 'businessPoints', $this->businessPoints])
            ->andFilterWhere(['like', 'word_package', $this->word_package])
            ->andFilterWhere(['like', 'json_info', $this->json_info])
            ->andFilterWhere(['like', 'similar', $this->similar]);

        return $dataProvider;
    }
}
