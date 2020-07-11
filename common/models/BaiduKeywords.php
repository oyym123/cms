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
class BaiduKeywords extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'baidu_keywords';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pc_show_rate', 'pc_rank', 'competition', 'match_type', 'pc_click', 'pc_pv', 'pc_show', 'pc_ctr', 'all_show_rate', 'all_rank', 'all_cpc', 'all_click', 'all_pv', 'all_show', 'all_ctr', 'm_show_rate', 'm_rank', 'm_click', 'm_pv', 'status'], 'integer'],
            [['bid', 'all_rec_bid', 'm_rec_bid'], 'number'],
            [['word_package', 'json_info'], 'string'],
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

        $model = new BaiduKeywords();

        foreach ($data as $key => $item) {
            $model->$key = $item;
        }

        $model->created_at = date('Y-m-d H:i:s');
        if (!$model->save(false)) {
            return [-1, $model->getErrors()];
        }
    }

    /**
     * 获取百度SDK关键词的数据
     */
    public function getSdkWords()
    {
        set_time_limit(0);
        //所有的种词
        $initWords1 = ['外教一对一', '英语一对一', '英语培训', '少儿英语', '小学英语', '初中英语', '高中英语', '大学英语', '成人英语', '英语听力', '英语语法', '英语作文', '英语单词', '雅思英语', '托福英语'];
        $error = [];

        //获取所有来源词
        $oneInfo = self::find()->select('from_keywords')->asArray()->distinct('from_keywords')->all();
        $fromKeyArr = array_column($oneInfo, 'from_keywords');

        $initWords = self::find()->select('keywords')->where(['not in', 'keywords', $fromKeyArr])->asArray()->all();
        $initWords = array_column($initWords, 'keywords');

        //用种词调用相关词查询接口 最多只能查询到300个
        foreach ($initWords as $key => $initWord) {
            if (in_array($initWord, $fromKeyArr)) {
                $error[] = $initWord . '   来源词重复了！';
                continue;
            }

            sleep(1);
            $data = (new BaiDuSdk())->getKeyWords($initWord);
            if ($data === false) {
                $error[] = $initWord . '  没有请求请成功！';
                continue;
            }

            foreach ($data as $item) {
                $saveData = [
                    'pc_pv' => $item['pcPV'],
                    'show_reasons' => json_encode($item['showReasons'], JSON_UNESCAPED_UNICODE),
                    'm_pv' => $item['mobilePV'],
                    'word_package' => $item['wordPackage'],
                    'all_rec_bid' => $item['recBid'],
                    'businessPoints' => json_encode($item['businessPoints'], JSON_UNESCAPED_UNICODE),
                    'all_pv' => $item['pv'],
                    'keywords' => $item['word'],
                    'from_keywords' => $initWord,
                    'similar' => $item['similar'],
                    'competition' => $item['competition'],
                    'json_info' => json_encode($item, JSON_UNESCAPED_UNICODE),
                ];

                //保存所有的关键词
                list($code, $msg) = self::createOne($saveData);
                if ($code < 0) {
//                    $error[] = $msg;
                }
            }
        }

        echo '<pre>';
        print_r($error);
    }

    /**
     * 新增关键词 并且调用百度营销词接口获取相应的参数
     */
    public static function setKeywords($keywords)
    {
        $keywords = Tools::cleanKeywords($keywords);
        $error = [];
        foreach ($keywords as $item) {
            //判重 不可有所有重复的关键词 减少接口请求次数
            $oldInfo = self::find()->where(['keywords' => $item])->one();
            if (!empty($oldInfo)) {
                $error[] = $item . '  已经重复了！';
                continue;
            }

            $data = (new BaiDuSdk())->getRank($item);
            if ($data === false) {
                $error[] = $item . '  没有请求请成功！';
                continue;
            }

            $info = $data[0];

            $saveData = [
                'show_reasons' => '后台添加',
                'm_pv' => $info['mobile']['pv'],
                'm_show' => $info['mobile']['show'],
                'm_ctr' => $info['mobile']['ctr'],
                'm_click' => $info['mobile']['click'],
                'm_rec_bid' => $info['mobile']['recBid'],
                'm_charge' => $info['mobile']['charge'],
                'm_rank' => $info['mobile']['rank'],
                'm_show_rate' => $info['mobile']['showRate'],
                'all_cpc' => $info['all']['cpc'],
                'all_ctr' => $info['all']['ctr'],
                'all_click' => $info['all']['cpc'],
                'all_pv' => $info['all']['pv'],
                'all_charge' => $info['all']['charge'],
                'all_show' => $info['all']['show'],
                'all_rank' => $info['all']['rank'],
                'all_show_rate' => $info['all']['showRate'],
                'all_rec_bid' => $info['all']['recBid'],
                'pc_ctr' => $info['pc']['ctr'],
                'pc_show' => $info['pc']['show'],
                'pc_pv' => $info['pc']['pv'],
                'pc_rank' => $info['pc']['rank'],
                'pc_show_rate' => $info['pc']['showRate'],
                'pc_click' => $info['pc']['click'],
                'bid' => $info['bid'],
                'word_package' => '',
                'businessPoints' => json_encode([], JSON_UNESCAPED_UNICODE),
                'keywords' => $info['word'],
                'from_keywords' => '',
                'similar' => '',
                'competition' => $info['competition'],
                'json_info' => json_encode($info, JSON_UNESCAPED_UNICODE),
            ];

            //保存所有的关键词
            list($code, $msg) = self::createOne($saveData);
            if ($code < 0) {
                $error[] = $msg;
            }
        }

        if (!empty($error)) {
            return [-1, $error];
        } else {
            return [1, 'success'];
        }
    }
}
