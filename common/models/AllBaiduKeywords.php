<?php

namespace common\models;

use Yii;
use yii\data\Pagination;

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
class AllBaiduKeywords extends Base
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
            [['pc_show_rate', 'pc_rank', 'competition', 'column_id', 'domain_id', 'match_type', 'pc_click', 'pc_pv', 'pc_show', 'pc_ctr', 'all_show_rate', 'all_rank', 'all_cpc', 'all_click', 'all_pv', 'all_show', 'all_ctr', 'm_show_rate', 'm_rank', 'm_click', 'm_pv', 'status', 'catch_status'], 'integer'],
            [['bid', 'all_rec_bid', 'm_rec_bid'], 'number'],
            [['word_package', 'json_info', 'type'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['from_keywords', 'pc_cpc', 'charge', 'all_charge', 'm_charge', 'm_show', 'm_ctr', 'show_reasons', 'businessPoints', 'similar'], 'string', 'max' => 255],
            [['keywords', 'json_info', 'type','m_down_name'], 'string'],
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


    public static function getKeywordsUrl($flag, $domain = 0)
    {
        if ($domain == 0) {
            $domain = Domain::getDomainInfo();
        }

        $models = AllBaiduKeywords::find()
            ->where(['domain_id' => $domain->id])
            ->select('id')
            ->asArray()
            ->all();

        foreach ($models as &$item) {
            $item['url'] = 'http://' . $flag . $domain->name . '/' . $domain->start_tags . $item['id'] . $domain->end_tags;
        }
        return $models;
    }

    /**
     * 新增关键词 并且调用百度营销词接口获取相应的参数
     */
    public static function setKeywords($postData)
    {
        set_time_limit(0);
        $keywords = Tools::cleanKeywords($postData['keywords']);

        if (count($keywords) > 10000) {
            return [-1, '最多一次只能导入10000个词'];
        }

        $error = [];

        foreach ($keywords as $item) {
            //判重 不可有所有重复的关键词 减少接口请求次数
            $oldInfo = self::find()->where([
                'keywords' => $item,
//                'column_id' => $postData['column_id']
            ])->one();

            if (!empty($oldInfo)) {
                $oldInfo->catch_status = 100;
                $oldInfo->save(false);
                $error[] = $item . '  已经重复了！';
                continue;
            }

            $data = [[]];
//            $data = (new BaiDuSdk())->getRank($item);
            if ($data === false) {
//                $error[] = $item . '  没有请求请成功！';
//                continue;
            }

            $info = $data[0];
            $tname = '';

            //获取词的顶级分类名称
            $topName = Category::findOne($postData['type_id']);
            if ($topName) {
                $tname = $topName->en_name;
            }

            $saveData = [
                'show_reasons' => '后台添加',
                'm_pv' => $info['mobile']['pv'] ?? 0,
                'm_show' => $info['mobile']['show'] ?? 0,
                'type' => $tname,
                'type_id' => $postData['type_id'],
                'pid' => 0,
                'm_ctr' => $info['mobile']['ctr'] ?? 0,
                'm_click' => $info['mobile']['click'] ?? 0,
                'm_rec_bid' => $info['mobile']['recBid'] ?? 0,
                'm_charge' => $info['mobile']['charge'] ?? 0,
                'm_rank' => $info['mobile']['rank'] ?? 0,
                'm_show_rate' => $info['mobile']['showRate'] ?? 0,
                'all_cpc' => $info['all']['cpc'] ?? 0,
                'all_ctr' => $info['all']['ctr'] ?? 0,
                'all_click' => $info['all']['cpc'] ?? 0,
                'all_pv' => $info['all']['pv'] ?? 0,
                'all_charge' => $info['all']['charge'] ?? 0,
                'all_show' => $info['all']['show'] ?? 0,
                'all_rank' => $info['all']['rank'] ?? 0,
                'all_show_rate' => $info['all']['showRate'] ?? 0,
                'all_rec_bid' => $info['all']['recBid'] ?? 0,
                'pc_ctr' => $info['pc']['ctr'] ?? 0,
                'pc_show' => $info['pc']['show'] ?? 0,
                'pc_pv' => $info['pc']['pv'] ?? 0,
                'pc_rank' => $info['pc']['rank'] ?? 0,
                'pc_show_rate' => $info['pc']['showRate'] ?? 0,
                'pc_click' => $info['pc']['click'] ?? 0,
                'bid' => $info['bid'] ?? 0,
                'catch_status' => 100,  //表示人工
                'word_package' => '',
                'businessPoints' => json_encode([], JSON_UNESCAPED_UNICODE),
                'keywords' => $item,
                'from_keywords' => '',
                'similar' => '',
                'domain_id' => $postData['domain_id'] ?? 0,
                'column_id' => $postData['column_id'] ?? 0,
                'competition' => $info['competition'] ?? 0,
                'json_info' => json_encode($info, JSON_UNESCAPED_UNICODE),
            ];

            //保存所有的关键词
            list($code, $msg) = self::createOne($saveData);

            //只保存主词
            if ($code < 0) {
                $error[] = $msg;
                print_r($msg);
                exit;
            } else {

                //保存主词
//                $dataSave = [
//                    'name' => $info['word'],
//                    'key_id' => $msg->id,
//                    'type_name' => $msg->type,
//                    'keywords' => $info['word'],
//                ];
//
//                list($codeKey, $msgKey) = LongKeywords::createOne($dataSave);
//
//                if ($codeKey > 0) {
//                    LongKeywords::bdPushReptile($msgKey);
//                }


                //保存扩展词
//                self::getSdkWords($msg->id);
            }
        }

        if (!empty($error)) {
            return [-1, $error];
        } else {
            return [1, 'success'];
        }
    }

    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'type_id']);
    }

    public static function cate()
    {
        $res = self::find()->select('type_id')->distinct('type_id')->all();
        $arr = [];
        foreach ($res as $re) {
            $arr[$re->type_id] = $re->category->name;
        }
        return $arr;
    }
}
