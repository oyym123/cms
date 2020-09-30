<?php

namespace common\models;

use Yii;
use function GuzzleHttp\Psr7\str;

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
class BaiduKeywords extends Base
{
    const CATCH_STATUS_ENABLE = 10; //正常
    const CATCH_STATUS_START = 20;  //可抓取
    const CATCH_STATUS_OVER = 30;  //搜狗抓取挖完毕

    public static function getDomainIds()
    {
        //查询指定20个站 的规则
        $domainIds = [
//            3,
            25,    //arcf.org.cn
            48,    //jlsds.org.cn
            72,    //hbrl22.com
            16,    //0ww9.com
            35,    //hsmengxiao.org.cn
            38,    //ysjj.org.cn
            63,    //xjscpt.org
            60,    //thszxxdyw.org.cn
            30,    //dglanglun.com
            39,    //xljd0571.com
            45,    //whsgtzydzhjjcy.org.cn
            50,    //sclxfl.org.cn
            47,    //hncf.org.cn
            34,    //hebjj.org.cn
            32,    //jsflxh.org.cn
            20,    //qdmjsw.org.cn
            53,    //fs120yy.com
            41,    //cmru.org.cn
            46,    //cxch.org.cn
            76,    //xunke.org.cn
            //-------------------------------




        ];

        return $domainIds;
    }

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

        $model = new BaiduKeywords();

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

    /**
     * 获取百度SDK关键词的数据
     */
    public static function getSdkWords($id = 0)
    {
        set_time_limit(0);
        $andWhere = [];
        if ($id) {
            $andWhere = ['id' => $id];
        }

        //所有的种词
        $error = [];

        $initWords = self::find()->where(['pid' => 0])->andWhere(['>', 'id', '232703'])->all();


        //用种词调用相关词查询接口 最多只能查询到300个
        foreach ($initWords as $key => $initWord) {
            sleep(1);
            //保存种词本身
            $dataSave = [
                'name' => $initWord->keywords,
                'key_id' => $id,
                'type_name' => $initWord->type,
                'keywords' => $initWord->keywords,
            ];

            list($codeLong, $msgLong) = self::setLongKeywords($dataSave, 0);

            if ($codeLong < 0) {
                Tools::writeLog($initWord->keywords . '重复了', 'create_long.txt');
                continue;
            }

            $data = (new BaiDuSdk())->getKeyWords($initWord->keywords);
            Tools::writeLog('获取到   ' . $initWord->keywords . '    所有的拓展词', 'create_long.txt');
            if ($data === false) {
                $error[] = $initWord . '  没有请求请成功！';
                continue;
            }

            foreach ($data as $k => $item) {
                $saveData = [
                    'type' => $initWord->type,
                    'pid' => $initWord->id,
                    'pc_pv' => $item['pcPV'],
                    'show_reasons' => json_encode($item['showReasons'], JSON_UNESCAPED_UNICODE),
                    'm_pv' => $item['mobilePV'],
                    'word_package' => $item['wordPackage'],
                    'all_rec_bid' => $item['recBid'],
                    'businessPoints' => json_encode($item['businessPoints'], JSON_UNESCAPED_UNICODE),
                    'all_pv' => $item['pv'],
                    'keywords' => $item['word'],
                    'from_keywords' => $initWord->keywords,
                    'similar' => $item['similar'],
                    'competition' => $item['competition'],
                    'json_info' => json_encode($item, JSON_UNESCAPED_UNICODE),
                ];

                //保存所有的关键词
                list($code, $msg) = AllBaiduKeywords::createOne($saveData);

                if ($code > 0) {
                    $dataSave = [
                        'name' => $item['word'],
                        'key_id' => $msg->id,
                        'type_name' => $msg->type,
                        'keywords' => $initWord->keywords,
                    ];
                    self::setLongKeywords($dataSave, $k);
                }

                if ($code < 0) {
                    $error[] = $msg;
                }
            }
        }

        echo '<pre>';
        print_r($error);
    }


    /** 设置长尾关键词 */
    public static function setLongKeywords($dataSave, $k)
    {
        list($codeKey, $msgKey) = LongKeywords::createOne($dataSave);
        if ($codeKey > 0) {
            //只抓取前30个至长尾词库
            if ($k < 30) {
//                LongKeywords::bdPushReptile($msgKey);
            }
            return [1, 'success'];
        } else {
            return [-1, $msgKey];
        }
    }

    /** 将数据打乱重组 推入爬虫库 */
    public static function changeSort()
    {
        //查询指定20个站 的规则
        $domainIds = self::getDomainIds();
        //查询出所有的规则分类
        $articleRules = ArticleRules::find()->select('category_id')
            //->where(['in', 'domain_id', $domainIds])
            ->asArray()->all();
        $itemData = [];

        $step = 20;
        $limit = 40;
        for ($i = 0; $i <= $limit; $i++) {
            foreach ($articleRules as $key => $rules) {
                $keywordData = AllBaiduKeywords::find()
                    ->select('id,keywords as name, type as type_name,from_keywords as keywords,pid,m_pv')
                    ->where([
                        'status' => 1,
                        'type_id' => $rules['category_id']
                    ])
                    ->andWhere([
                        'catch_status' => 100
                    ])
                    ->orderBy('id desc')
                    ->offset($i * $step)
                    ->limit($step)
                    ->asArray()
                    ->all();

                foreach ($keywordData as $keywordDatum) {
                    $itemData[] = $keywordDatum;
                }
            }
        }

        return $itemData;


//        echo '<pre>';
//        print_r($itemData);
//        exit;
        return $itemData;

        echo '<pre>';
        print_r($itemData);
        exit;
    }

    /** 推送关键词 */
    public static function pushKeywords()
    {
        set_time_limit(0);
        ignore_user_abort();
        $long = self::changeSort();
        //标记长尾词
        foreach ($long as $l) {
//            sleep(1);
            //匹配下拉词
//            list($code, $msg) = LongKeywords::getBaiduKey(['keywords' => $l['name']], 1);
//
//            if ($code < 0) {
//                echo '<pre>';
//                print_r($msg);

            $l['name'] = trim($l['name']);
            $all = AllBaiduKeywords::findOne($l['id']);
            $all->status = 10;
            $all->save(false);
            LongKeywords::bdPushReptileNew($l, $l['pid']);


//                continue;
//            } else {
//                $arrTitle = [];
//                foreach ($msg as $item) {
//                    if ($item != $l['name'] && (strlen($item) > strlen($l['name']))) {
//                        $arrTitle[] = $item;
//                    }
//                }
//
//                $downKeywords = str_replace(',', '', $arrTitle[0]);
//                echo '下拉词:  ' . $downKeywords . '<br/>';
//                $all = AllBaiduKeywords::findOne($l['id']);
//                $all->status = 10;
//                $all->save(false);
//                LongKeywords::bdPushReptileNew($l, $l['pid'], $downKeywords);
////                    print_r($l);exit;
//            }
        }
//                echo '<pre>';
//                print_r($l['name']);
//                echo ' 1232 ';
//                print_r($arrTitle[0]);
//                exit;

        if (empty($long)) {
            echo '没有符合条件m_pv > 0 && m_pv < 20 的数据 推送';
            exit;
        }
//        }
//        }
    }

    public static function pushPa()
    {
        $long = AllBaiduKeywords::find()
            ->select('id,keywords as name, type as type_name,from_keywords as keywords,m_pv')
            ->where([
                'catch_status' => 100,
                'status' => 1,
            ])
            ->andWhere([
                '>', 'm_pv', 0
            ])
            ->andWhere([
                '<', 'm_pv', 20
            ])
            ->asArray()
            ->all();
//            echo '<pre>';
//            print_r($long);
//            exit;
        //标记长尾词
        foreach ($long as $l) {
            $all = AllBaiduKeywords::findOne($l['id']);
            $all->status = 10;
            $all->save(false);
            LongKeywords::bdPushReptileNew($l, 0);
        }
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
                'type' => $postData['type'],
                'pid' => 0,
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
                'domain_id' => $postData['domain_id'] ?? 0,
                'column_id' => $postData['column_id'] ?? 0,
                'competition' => $info['competition'],
                'json_info' => json_encode($info, JSON_UNESCAPED_UNICODE),
            ];

            //保存所有的关键词
            list($code, $msg) = self::createOne($saveData);

            //只保存主词
            if ($code < 0) {
                $error[] = $msg;
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

    /** 标签 */
    public static function hotKeywords($num = 15)
    {
        $domain = Domain::getDomainInfo();

        $keywords = AllBaiduKeywords::find()
            ->select('id,keywords')
            ->where(['domain_id' => $domain->id])
            ->limit($num)
            ->asArray()
            ->all();

        if ($domain) {
            foreach ($keywords as &$item) {
                $item['url'] = '/' . $domain->start_tags . $item['id'] . $domain->end_tags;
            }
        }
        return $keywords;
    }


    /** 获取域名 */
    public function getDomain()
    {
        return $this->hasOne(Domain::className(), ['id' => 'domain_id']);
    }

    /** 获取类目 */
    public function getColumn()
    {
        return $this->hasOne(DomainColumn::className(), ['id' => 'column_id']);
    }

}
