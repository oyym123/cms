<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "baidu_keywords".
 *
 * @property int $id
 * @property string|null $keywords 百度关键词
 * @property string|null $from_keywords 来源词
 * @property int|null $pc_show_rate PC端展示率
 * @property int|null $pc_rank PC端排名
 * @property string|null $pc_cpc
 * @property string|null $charge
 * @property int|null $competition 竞争激烈程度
 * @property int|null $match_type 匹配模式
 * @property float|null $bid 出价
 * @property int|null $pc_click PC点击量
 * @property int|null $pc_pv PC_pv
 * @property int|null $pc_show 展示
 * @property int|null $pc_ctr
 * @property int|null $all_show_rate
 * @property int|null $all_rank
 * @property string|null $all_charge
 * @property int|null $all_cpc
 * @property float|null $all_rec_bid
 * @property int|null $all_click
 * @property int|null $all_pv
 * @property int|null $all_show
 * @property int|null $all_ctr
 * @property int|null $m_show_rate
 * @property int|null $m_rank
 * @property string|null $m_charge
 * @property string|null $m_cpc
 * @property float|null $m_rec_bid
 * @property int|null $m_click
 * @property int|null $m_pv
 * @property string|null $m_show
 * @property string|null $m_ctr
 * @property string|null $show_reasons json格式 展示理由
 * @property string|null $businessPoints json格式 热门指数
 * @property string|null $word_package 词集
 * @property string|null $json_info 所有信息
 * @property string|null $similar 相似度
 * @property int|null $status 1=有效 0=无效
 * @property string|null $created_at 创建时间
 * @property string|null $updated_at 修改时间
 */
class AllBaiduKeywords extends \yii\db\ActiveRecord
{
    const CATCH_STATUS_ENABLE = 10; //正常
    const CATCH_STATUS_START = 20;  //可抓取
    const CATCH_STATUS_OVER = 30;  //搜狗抓取挖完毕

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'all_baidu_keywords';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['domain_id', 'column_id', 'keywords'], 'required'],
            [['pc_show_rate', 'pc_rank', 'competition', 'column_id', 'domain_id', 'match_type', 'pc_click', 'pc_pv', 'pc_show', 'pc_ctr', 'all_show_rate', 'all_rank', 'all_cpc', 'all_click', 'all_pv', 'all_show', 'all_ctr', 'm_show_rate', 'm_rank', 'm_click', 'm_pv', 'status'], 'integer'],
            [['bid', 'all_rec_bid', 'm_rec_bid'], 'number'],
            [['word_package', 'json_info', 'type'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['keywords', 'from_keywords', 'pc_cpc', 'charge', 'all_charge', 'm_charge', 'm_show', 'm_ctr', 'show_reasons', 'businessPoints', 'similar'], 'string', 'max' => 255],
            [['m_cpc'], 'string', 'max' => 11],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'keywords' => '关键词',
            'from_keywords' => '来源词',
            'pc_show_rate' => 'Pc Show Rate',
            'pc_rank' => 'Pc Rank',
            'type' => '类型',
            'column_id' => '栏目',
            'domain_id' => '域名',
            'pc_cpc' => 'Pc Cpc',
            'charge' => 'Charge',
            'competition' => '竞争度 （百分比）',
            'match_type' => 'Match Type',
            'bid' => 'Bid',
            'pc_click' => 'Pc Click',
            'pc_pv' => 'PC端PV',
            'pc_show' => 'Pc Show',
            'pc_ctr' => 'Pc Ctr',
            'all_show_rate' => 'All Show Rate',
            'all_rank' => 'All Rank',
            'all_charge' => 'All Charge',
            'all_cpc' => 'All Cpc',
            'all_rec_bid' => '推荐出价',
            'all_click' => 'All Click',
            'all_pv' => '总PV',
            'all_show' => 'All Show',
            'all_ctr' => 'All Ctr',
            'm_show_rate' => 'M Show Rate',
            'm_rank' => 'M Rank',
            'm_charge' => 'M Charge',
            'm_cpc' => 'M Cpc',
            'm_rec_bid' => 'M Rec Bid',
            'm_click' => 'M Click',
            'm_pv' => '移动端 PV',
            'm_show' => 'M Show',
            'm_ctr' => 'M Ctr',
            'show_reasons' => 'Show Reasons',
            'businessPoints' => 'Business Points',
            'word_package' => 'Word Package',
            'json_info' => 'Json Info',
            'similar' => 'Similar',
            'status' => 'Status',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    /**
     * 创建关键词
     */
    public static function createOne($data)
    {
        //判重 不可有所有重复的关键词
        $oldInfo = self::find()->where(['keywords' => $data['keywords']])->one();

        if (!empty($oldInfo)) {
            return [-1, $data['keywords'] . '   已经重复了'];
        }

        $model = new AllBaiduKeywords();

        foreach ($data as $key => $item) {
            $model->$key = $item;
        }

        $model->created_at = date('Y-m-d H:i:s');
        if (!$model->save(false)) {
            return [-1, $model->getErrors()];
        } else {
            return [1, $model];
        }
    }


}
