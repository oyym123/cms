<?php

namespace common\models;

use Yii;
use yii\web\Controller;

/**
 * This is the model class for table "long_keywords".
 *
 * @property int $id
 * @property string|null $m_down_name 移动端下拉词
 * @property string|null $m_search_name 移动端其他人还在搜
 * @property string|null $name 关键词名称
 * @property string|null $type 类型
 * @property string|null $m_related_name 相关搜索
 * @property string|null $m_other_name 移动变种下拉词
 * @property string|null $pc_down_name PC端下拉词
 * @property string|null $pc_search_name PC端其他人搜索
 * @property string|null $pc_related_name PC相关词
 * @property string|null $keywords 短尾词
 * @property int|null $key_id 短尾词id
 * @property int|null $key_search_num 短尾词搜索次数
 * @property int|null $status 1=有效 0=无效
 * @property string|null $remark 备注
 * @property int|null $from 1=百度  2=搜狗
 * @property string|null $url 来源地址
 * @property string|null $created_at 创建时间
 * @property string|null $updated_at 修改时间
 */
class LongKeywords extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'long_keywords';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['key_id', 'key_search_num', 'status', 'from', 'type'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['m_down_name', 'm_search_name', 'm_related_name', 'name', 'pc_down_name', 'm_other_name', 'pc_search_name', 'pc_related_name', 'keywords', 'remark', 'url'], 'string', 'max' => 255],
        ];
    }

    const TYPE_M_DOWN = 10;      //移动下拉词
    const TYPE_M_SEARCH = 20;    //移动其他人搜索词
    const TYPE_M_RELATED = 30;   //移动相关搜索
    const TYPE_M_OTHER = 40;     //移动端变种下拉词
    const TYPE_PC_SEARCH = 50;   //PC其他人搜索词
    const TYPE_PC_RELATED = 60;  //PC相关搜索词

    /**
     * @param string $key
     * @return string|string[]
     * 获取所有的类型
     */
    public static function getType($key = 'all')
    {
        $data = [
            self::TYPE_M_DOWN => '移动下拉词',
            self::TYPE_M_SEARCH => '移动其他人搜索词',
            self::TYPE_M_RELATED => '移动相关搜索',
            self::TYPE_M_OTHER => '移动端变种下拉词',
            self::TYPE_PC_SEARCH => 'PC其他人搜索词',
            self::TYPE_PC_RELATED => 'PC相关搜索词',
        ];
        return $key === 'all' ? $data : $data[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '关键词名称',
            'type' => '类型',
            'm_down_name' => '移动端下拉词',
            'm_search_name' => '移动端其他人搜索',
            'm_related_name' => '相关搜索',
            'm_other_name' => '移动端变种下拉词',
            'pc_down_name' => 'PC端下拉词',
            'pc_search_name' => 'PC端其他人搜索',
            'pc_related_name' => 'PC相关词',
            'keywords' => '短尾词',
            'key_id' => '短尾词id',
            'key_search_num' => '短尾词搜索次数',
            'status' => '状态',
            'remark' => '备注',
            'from' => '来源地址',
            'url' => 'Url',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    /** 标签 */
    public static function hotKeywords()
    {
        $keywords = AllBaiduKeywords::find()
            ->select('id,name as keywords')
            ->limit(15)
            ->orderBy('id desc')
            ->asArray()
            ->all();

        $domain = Domain::getDomainInfo();

        if ($domain) {
            foreach ($keywords as &$item) {
                $item['url'] = '/' . $domain->start_tags . $item['id'] . $domain->end_tags;
            }
        }
        return $keywords;
    }

    /**
     * @param $data
     * @return array
     * 创建一个长尾关键词
     */
    public static function createOne($data)
    {
        //判重 不可有所有重复的关键词
        $oldInfo = self::find()->where(['name' => $data['name']])->one();

        if (!empty($oldInfo)) {
            return [-1, $data['name'] . '   已经重复了'];
        }

        $model = new LongKeywords();

        $model->name = $data['name'];
        $model->type = $data['type'];
        $model->type_name = $data['type_name'];
        $model->m_down_name = $data['m_down_name'] ?? '';
        $model->m_search_name = $data['m_search_name'] ?? '';
        $model->m_related_name = $data['m_related_name'] ?? '';
        $model->m_other_name = $data['m_other_name'] ?? '';
        $model->pc_search_name = $data['pc_search_name'] ?? '';
        $model->pc_related_name = $data['pc_related_name'] ?? '';
        $model->key_search_num = $data['key_search_num'] ?? 0;
        $model->keywords = $data['keywords'];
        $model->key_id = $data['key_id'];
        $model->status = $data['status'] ?? 1;
        $model->remark = $data['remark'] ?? '';
        $model->from = $data['from'] ?? '';
        $model->url = $data['url'] ?? '';
        $model->created_at = date('Y-m-d H:i:s');

        if (!$model->save(false)) {
            return [-1, $model->getErrors()];
        } else {
            return [1, $model];
        }
    }

    /** 抓取百度的关键词 */
    public static function getKeywords()
    {
        set_time_limit(0);
        $redis = \Yii::$app->redis;
        $lastKeywords = $redis->get('last_keywords_id');

        if ($lastKeywords) {
            //获取所有的短尾关键词
            $keywords = BaiduKeywords::find()->select('id,keywords,m_pv')
                ->where(['>', 'id', $lastKeywords])
                ->limit(120)
                ->asArray()
                ->all();
        } else {
            //获取所有的短尾关键词
            $keywords = BaiduKeywords::find()->select('id,keywords,m_pv')
                ->where(['>', 'id', 1])
                ->limit(120)
                ->asArray()
                ->all();
        }

        $error = $sameArr = [];

        foreach ($keywords as $keyword) {
            //不可重复获取百度关键词
            $oldInfo = self::find()->select('keywords')->where(['keywords' => $keyword['keywords']])->one();
            if (empty($oldInfo)) {
                list($code, $msg) = self::getBaiduKey($keyword);
                if ($code < 0) {
                    $error[] = $msg;
                }

                sleep(10);
                if ($code == -10) {
                    sleep(20);
                }

                Tools::writeLog($keyword);
            } else {
                Tools::writeLog([$keyword, 'res' => '重复']);
                $sameArr[] = $keyword['keywords'] . '   重复！';
            }
        }

        echo '<pre>';
        print_r($sameArr);
        echo '<hr/>';
        print_r($error);
    }

    public static function curlget($url)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Cookie: BAIDUID=44FF9C9C1A2A9513320EAE14D354AB66:FG=1"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }

    /**
     * 获取下拉词
     */
    public static function getBaiduKey($data, $from = 0)
    {
        //百度下拉框接口提取数据
        $url = 'http://m.baidu.com/sugrec?pre=1&p=3&ie=utf-8&json=1&prod=wise&from=wise_web&net=&os=&sp=&callback=jsonp&wd=' . urlencode($data['keywords']);
        $url2 = 'http://m.baidu.com/sugrec?pre=1&p=20&ie=utf-8&json=1&prod=wise&from=wise_web&net=&os=&sp=&callback=jsonp&wd=' . urlencode($data['keywords']);

        $resDown = Tools::curlNewGet($url);
        $resDown = str_replace('jsonp(', '', $resDown);

        $resDown = substr($resDown, 0, -1);

        $resDown = json_decode($resDown, true)['g'];

        if (!empty($resDown)) {
            $resDown = array_column($resDown, 'q');
        } else {
            return [-1, $data['keywords'] . '    下拉词没有抓取成功'];
        }

        if ($from == 1) {
            return [1, $resDown];
        }

        $resOther = Tools::curlGet($url2);
        $resOther = str_replace('jsonp(', '', $resOther);
        $resOther = substr($resOther, 0, -1);
        $resOther = json_decode($resOther, true)['g'];
        if (!empty($resOther)) {
            $resOther = array_column($resOther, 'q');
        } else {
            $resOther = [];
//            return [-1, '变种下拉词没有抓取成功'];
        }

        //百度内容页提取数据
        $url3 = 'http://m.baidu.com/s?word=' . $data['keywords'];
        $res = Tools::curlGet($url3);

        if (strpos($res, '<a href="https://wappass.baidu.com/static/captcha/tuxing.html') !== false) {
            return [-10, '机器人验证不通过!'];
        }

        //清除多余字符串，加快正则匹配速度
        $res = substr($res, 397089);
        $res = substr($res, 0, -205027);

        //大家都在搜关键词 get
        preg_match('@哪些联想词属于不当内容(.*)? data-action-proxy="true@', $res, $reData);
        $arr = explode('&quot;text&quot;:&quot;', $reData[0]);
        $resSearch = [];
        $arr = array_slice($arr, 1, 6);
        foreach ($arr as $key => $item) {
            $resSearch[] = preg_replace('@&quot(.*)?@', '', $item);
        }

        //相关搜索词 get
        preg_match('@target="_self" data-visited="off" rl-node="" rl-highlight-color="rgba\(0, 0, 0, .08\)" rl-highlight-radius="5px" class="c-slink c-slink-new-strong c-gap-top-small c-gap-bottom-small c-gap-top-small(.*)?</span><!----><!----></a></div></div><!----></div>@', $res, $reData);
        $arr2 = explode('><!----><', $reData[1]);
        $resRelated = [];
        foreach ($arr2 as $key => $item) {
            if (($key + 1) % 2 == 0) {
                $value = substr($item, strpos($item, ">"));
                $value = str_replace(['</span', '>'], ['', ''], $value);
                $resRelated[] = $value;
            }
        }

        $error = [];

        $allKeyWords = [
            10 => $resDown,
            20 => $resSearch,
            30 => $resRelated,
            40 => $resOther,
        ];

        foreach ($allKeyWords as $key => $item) {
            foreach ($item as $k => $value) {
                $dataSave = [
                    'name' => $value,
                    'key_id' => $data['id'],
                    'type' => $key,
                    'keywords' => $data['keywords'],
                ];

                list($code, $msg) = self::createOne($dataSave);

                if ($code < 0) {
                    $error[] = $msg;
                }
            }
        }

        $keywords = self::find()->where(['key_id' => $data['id']])->all();
        $resOther = $resRelated = $resSearch = $resDown = [];
        foreach ($keywords as $keyword) {
            if ($keyword->type == self::TYPE_M_DOWN) {
                $resDown[] = [
                    'id' => $keyword->id,
                    'name' => $keyword->name
                ];
            } elseif ($keyword->type == self::TYPE_M_SEARCH) {
                $resSearch[] = [
                    'id' => $keyword->id,
                    'name' => $keyword->name
                ];
            } elseif ($keyword->type == self::TYPE_M_RELATED) {
                $resRelated[] = [
                    'id' => $keyword->id,
                    'name' => $keyword->name
                ];
            } elseif ($keyword->type == self::TYPE_M_OTHER) {
                $resOther[] = [
                    'id' => $keyword->id,
                    'name' => $keyword->name
                ];
            }
        }

        $mDownStr = json_encode($resDown, JSON_UNESCAPED_UNICODE);

        $mOtherStr = json_encode($resOther, JSON_UNESCAPED_UNICODE);

        $mRelatedStr = json_encode($resRelated, JSON_UNESCAPED_UNICODE);

        $mSearchStr = json_encode($resSearch, JSON_UNESCAPED_UNICODE);

        $dataSave = [
            'm_related_name' => $mRelatedStr,
            'm_search_name' => $mSearchStr,
            'm_down_name' => $mDownStr,
            'm_other_name' => $mOtherStr,
        ];

        self::updateAll($dataSave, ['key_id' => $data['id']]);
        $msg = self::find()->where(['key_id' => $data['id']])->one();

        self::pushReptile($msg);

        echo '<pre>';
        print_r($error);
    }

    /** 将新增的关键词推入到远程爬虫 */
    public static function pushReptile($data = [])
    {
        $url = Tools::reptileUrl() . '/keyword/save-keyword';
        //查询域名 栏目
        $info = BaiduKeywords::find()->select('id,domain_id,column_id')->where(['id' => $data->key_id])->one();
        if ($info) {
            //查询类型
            $column = DomainColumn::findOne($info->column_id);
            if ($column) {
                $dataPush = [
                    'note' => $data->keywords,
                    'fan_key_id' => $info->id,
                    'key_id' => $data->id,
                    'type' => $column->type,
                    'keywords' => $data->name,
                    'm_related_name' => $data->m_related_name,
                    'm_search_name' => $data->m_search_name,
                    'm_down_name' => $data->m_down_name,
                    'm_other_name' => $data->m_other_name,
                    'domain_id' => $info->domain_id,
                    'column_id' => $info->column_id,
                ];

                $res = Tools::curlPost($url, $dataPush);
                if (strpos($res, 'success') === false) {
                    print_r($res);
                    exit;
                    Tools::writeLog($res, 'reptile_keywords_error.log');
                } else {
                    Tools::writeLog([$data->name => '保存成功'], 'reptile_keywords.log');
                }
            }
        }
    }

    /** 将新增的关键词推入到远程爬虫 */
    public static function bdPushReptileNew($data = [], $id, $downKeywords = 0)
    {
        $url = Tools::reptileUrl() . '/keyword/save-keyword';
        //  $url = \Yii::$app->params['online_reptile_url'] . '/keyword/save-keyword';

        $resOther[] = [
            'id' => $data['id'],
            'name' => $data['name']
        ];

        //下拉词
        $resDown[] = [
            'id' => $data['id'],
            'name' => $downKeywords
        ];

        $mOtherStr = json_encode($resOther, JSON_UNESCAPED_UNICODE);

        if ($downKeywords) {
            $resDown = json_encode($resDown, JSON_UNESCAPED_UNICODE);
        } else {
            $resDown = '[]';
        }

        $dataPush = [
            'note' => $data['keywords'],
            'fan_key_id' => $id,
            'key_id' => $data['id'],
            'type' => $data['type_name'],
            'keywords' => $data['name'],
            'm_related_name' => '[]',
            'm_search_name' => '[]',
            'm_down_name' => $resDown,
            'm_other_name' => $mOtherStr,
            'domain_id' => 0,
            'column_id' => 0,
        ];

        $res = Tools::curlPost($url, $dataPush);

        if (strpos($res, 'success') === false) {
            print_r($res);
            exit;
            Tools::writeLog($res, 'reptile_keywords_error.log');
        } else {
            Tools::writeLog([$data['name'] => '保存成功'], 'reptile_keywords.log');
        }
    }

    /** 将新增的关键词推入到远程爬虫 */
    public static function bdPushReptile($data = [], $id)
    {
        $url = Tools::reptileUrl() . '/keyword/save-keyword';
        //  $url = \Yii::$app->params['online_reptile_url'] . '/keyword/save-keyword';

        $resOther[] = [
            'id' => $data->id,
            'name' => $data->name
        ];

        $mOtherStr = json_encode($resOther, JSON_UNESCAPED_UNICODE);

        $dataPush = [
            'note' => $data->keywords,
            'fan_key_id' => $id,
            'key_id' => $data->id,
            'type' => $data->type_name,
            'keywords' => $data->name,
            'm_related_name' => '[]',
            'm_search_name' => '[]',
            'm_down_name' => '[]',
            'm_other_name' => $mOtherStr,
            'domain_id' => 0,
            'column_id' => 0,
        ];

        $res = Tools::curlPost($url, $dataPush);
        if (strpos($res, 'success') === false) {
            print_r($res);
            exit;
            Tools::writeLog($res, 'reptile_keywords_error.log');
        } else {
            Tools::writeLog([$data->name => '保存成功'], 'reptile_keywords.log');
        }
    }

    //获取百度关键词
    public function getBdk()
    {
        return $this->hasOne(AllBaiduKeywords::className(), ['id' => 'key_id']);
    }

    /**
     * 设定规则 定时拉取文章 栏目广度
     */
    public static function setRules($columnId = 0)
    {
//        $columnId = 307;
        set_time_limit(0);

        if ($columnId) {
            $andWhere = ['id' => $columnId];
        } else {
            $andWhere = ['id' => 1];
        }

        if ($columnId == 'all') {
            $andWhere = [];
        }

        //查询指定20个站 的规则
        $domainIds = BaiduKeywords::getDomainIds();

        $url = Tools::reptileUrl() . '/cms/article';

        $_GET['domain'] = 0;

        //查询指定20个站 的规则
        $domainIds = BaiduKeywords::getDomainIds();

        //查询出所有的规则分类
        $articleRules = ArticleRules::find()->select('category_id,domain_id,column_id')->where(['in', 'domain_id', $domainIds])->asArray()->all();

        $itemData = [];

        $step = 20;
        $limit = 90;
        for ($i = 51; $i <= $limit; $i++) {
            foreach ($articleRules as $key => $rules) {
                $column = DomainColumn::find()
                    ->select('id,type,domain_id,zh_name,name')
                    ->where(['id' => $rules['column_id']])->asArray()->one();
                //每个短尾词扩展  6个小指数长尾词 联表查询
                $longKeywords = AllBaiduKeywords::find()->select('id,keywords as name,pid as key_id,type')
                    ->andWhere(['catch_status' => 100])              //表示后台输入的词
                    ->andWhere(['status' => 10])                     //表示已经推送到爬虫库中的数据
                    ->andWhere(['type_id' => $rules['category_id']])
//                    ->andWhere(['>','back_time','2020-08-01 00:00:00'])  有回调参数的关键词
                    //表示没有栏目使用过
                    ->orderBy('id desc')
                    ->offset($i * $step)
                    ->limit($step)
                    ->andWhere(['column_id' => 0])                  //表示没有栏目使用过
                    ->asArray()
                    ->all();

                if (empty($longKeywords)) {
                    echo ' 没有符合条件的词 可以组合文章';
                    continue;
//                      exit('<h1> 没有符合条件的词 可以组合文章 </h1>');
                }

//                echo '<pre>';
//                print_r($column);
//                exit;

                foreach ($longKeywords as $key => $longKeyword) {
                    $longKeyword['type'] = Category::findOne($rules['category_id'])->en_name;
                    //检验是否拉取过数据
                    $oldArticleKey = PushArticle::findx($column['domain_id'])->where(['key_id' => $longKeyword['id']])->one();
                    if (!empty($oldArticleKey)) {
                        Tools::writeLog($column['zh_name'] . ' ---  ' . $longKeyword['name'] . '  长尾词已经拉取过了', 'set_rules.log');
                        continue;
                    }

                    echo $longKeyword['name'] . "<br/>";

                    //根据长尾关键词以及规则 从爬虫库拉取文章数据 保存到相应的文章表中
                    $data = [
                        'key_id' => $longKeyword['id'],
                        'keywords' => $longKeyword['name'],
                        'type' => strtoupper($longKeyword['type']),
                        'one_page_num_min' => 10,
                        'one_page_num_max' => 20,
                        'one_page_word_min' => 20,
                        'one_page_word_max' => 5000,
                        'num' => 4,
                        'trans' => 0
                    ];

//                        echo '<pre>';
//                        print_r($longKeywords);
//                        exit;

                    //发送请求至爬虫库
                    $res = Tools::curlPost($url, $data);
                    if (strpos($res, '还未采集') !== false) {
                        echo $res . '<br/>';
                        continue;
                    }
//                    echo '<pre>';
//                    var_export($res);
//                    exit;
                    $res = json_decode($res, true);

                    $saveData = [];
                    if (isset($res[0]['from_path'])) {
                        foreach ($res as $re) {
                            //存储入库
                            $saveData[] = [
                                'column_id' => $column['id'],
                                'from_path' => $re['from_path'],
                                'domain_id' => $column['domain_id'],
                                'key_id' => $longKeyword['id'],
                                'title_img' => $re['title_img'],
                                'keywords' => $longKeyword['name'],
                                'column_name' => $column['name'],
//                                  'fan_key_id' => $longKeyword['id'],
                                'rules_id' => $rules['id'],
                                'content' => $re['content'],
                                'intro' => $re['intro'],
                                'title' => $re['title'],
                                'user_id' => UserId::getId(),
                                'push_time' => Tools::randomDate('20190601', '20200501'),
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ];
                        }
                    }

//                    echo '<pre>';
//                    var_export($saveData);
//                    exit;

                    if (!empty($saveData)) {
                        //表示没有双词 则匹配
                        if (strpos($saveData[0]['title'], ',') === false) {
                            sleep(1);
                            list($code, $msg) = self::getBaiduKey(['keywords' => $longKeyword['name']], 1);

                            if ($code < 0) {
                                echo '<pre>';
                                print_r($msg);
                            } else {
                                Tools::writeLog('保存' . $longKeyword['name'], 'set_rules.log');

                                $arrTitle = [];
                                foreach ($msg as $item) {
                                    if ($item != $longKeyword['name'] && (strlen($item) > $longKeyword['name'])) {
                                        $arrTitle[] = $item;
                                    }
                                }

                                $saveData[0]['title'] = str_replace(',', '', $arrTitle[0]);
                                $saveData[0]['title'] = $longKeyword['name'] . ',' . $arrTitle[0];

                                //保存下拉词
                                $allKeywords = AllBaiduKeywords::findOne($longKeyword['id']);
                                $allKeywords->m_down_name = str_replace(',', '', $arrTitle[0]);
                                $allKeywords->save(false);

                                PushArticle::setArticle($saveData[0]);
                                Tools::writeLog('保存组合双词' . $saveData[0]['domain_id'] . '  ' . $saveData[0]['title'], 'set_rules.log');
//                                   exit;
                                //推送至远程线上
//                              $res = Tools::curlPost($urlPush, $saveData[0]);
                            }
                        } else {
//                            echo '<pre>';
//                            print_r($saveData);
//                            exit;

                            Tools::writeLog('保存爬取双词' . $saveData[0]['domain_id'], 'set_rules.log');

//                                echo ' < pre>';
//                                print_r($saveData);
//                                exit;

                            //推送至远程线上
//                            $res = Tools::curlPost($urlPush, $saveData[0]);

                            PushArticle::setArticle($saveData[0]);

//                            $bd = AllBaiduKeywords::findOne($longKeyword['id']);
//                            $bd->domain_id = $column['domain_id'];
//                            $bd->column_id = $column['id'];
//                            $bd->save();

//                            PushArticle::batchInsertOnDuplicatex($column['domain_id'], $saveData);
                        }
//                            exit;
//                            sleep(1);
                    } else {
                        Tools::writeLog('保存失败!' . $longKeyword['name'], 'set_rules.log');
                        echo '保存失败!';
                        $doubleK = [];
//                            echo ' < pre>';
//                            var_export($data);
                    }

//                        echo ' < pre>';
//                        print_r($saveData);

//                    }

                }
//                        sleep(5);
            }
        }
        exit;
    }

    public static function rulesTrans($columnId = 0)
    {
        set_time_limit(0);

        if ($columnId) {
            $andWhere = ['id' => $columnId];
        } else {
            $andWhere = ['id' => 1];
        }

        if ($columnId == 'all') {
            $andWhere = [];
        }

        //查询所有栏目
        $domainColumn = DomainColumn::find()->select('id,type,domain_id,zh_name,name')->where([
            'status' => self::STATUS_BASE_NORMAL,
        ])
            ->andWhere($andWhere)
            ->asArray()->all();

        $url = Tools::reptileUrl() . '/cms/article';

        $_GET['domain'] = 0;

        $step = 10;
        for ($i = 1; $i < 10; $i++) {
            foreach ($domainColumn as $column) {
                //查询分类规则
                $rules = ArticleRules::find()->where([
                    'column_id' => $column['id']
                ])->asArray()->one();

                //只拉取有规则的
                if (!empty($rules)) {
                    $longKeywords = AllBaiduKeywords::find()->select('id,keywords as name,pid as key_id,type')
                        ->andWhere(['catch_status' => 100])              //表示后台输入的词
                        ->andWhere(['status' => 10])                     //表示已经推送到爬虫库中的数据
                        ->andWhere(['type_id' => $rules['category_id']]) //表示匹配规则的类型
                        ->andWhere(['column_id' => 0])                   //表示没有栏目使用过
                        ->orderBy('id desc')
                        ->offset($i * $step)
                        ->limit($step)
                        ->asArray()
                        ->all();

                    if (empty($longKeywords)) {
                        echo ' 没有符合条件的词 可以组合文章';
                        continue;
                    }

//                echo '<pre>';
//                print_r($longKeywords);

                    foreach ($longKeywords as $key => $longKeyword) {
                        $longKeyword['type'] = Category::findOne($rules['category_id'])->en_name;
                        //检验是否拉取过数据
                        $oldArticleKey = PushArticle::findx($column['domain_id'])->where(['key_id' => $longKeyword['id']])->one();
                        if (!empty($oldArticleKey)) {
                            Tools::writeLog($column['zh_name'] . ' ---  ' . $longKeyword['name'] . '  长尾词已经拉取过了', 'set_rules.log');
                            continue;
                        }
                        echo ' 关键词： ' . $longKeyword['name'] . '   规则id：' . $rules['id'] . PHP_EOL;

                        //根据长尾关键词以及规则 从爬虫库拉取文章数据 保存到相应的文章表中
                        $data = [
                            'key_id' => $longKeyword['id'],
                            'keywords' => $longKeyword['name'],
                            'type' => strtoupper($longKeyword['type']),
                            'one_page_num_min' => 10,
                            'one_page_num_max' => 20,
                            'one_page_word_min' => 20,
                            'one_page_word_max' => $rules['one_page_word_max'],
                            'num' => 1,
                            'trans' => 1,
                        ];

//                        echo '<pre>';
//                        print_r($longKeywords);
//                        exit;

                        //发送请求至爬虫库
                        $res = Tools::curlPost($url, $data);
                        if (strpos($res, '还未采集') !== false) {
                            echo $res . PHP_EOL;
                            continue;
                        }

                        $res = json_decode($res, true);

                        $contentAll = $content = $saveData = [];

                        if (isset($res[0]['from_path'])) {
                            foreach ($res as $re) {
                                //清理标签  $re['content']
                                $contentArr = $transArr = [];
                                $transStr = '';

                                foreach ($re['json_item'] as $itemTrans) {
                                    $transStr .= $itemTrans['content'] . '(*)';
                                    $contentArr[] = $itemTrans['content'];
                                }

                                //有道翻译
                                $ret = (new YouDaoApi())->startRequest(str_replace('。', '{@}', $transStr));
                                $ret = json_decode($ret, true);

                                //繁体
                                $chinese = new Chinaese();
                                $fanData = $chinese->cns($transStr);

                                $fanRes = array_filter(explode('(*)', $fanData));
                                $enRes = array_filter(explode('(*)', $ret['translation'][0]));

                                $enRes = str_replace(['< / p >', '< p >', '</p b>'], '', $enRes);
                                $strContent = $articleStr = '';

                                $enPart = $fanPart = $contentRes = [];
                                foreach ($re['json_item'] as $key => $trans) {
//                                    $transArr[] = '';
//                                    //进行文章拼接
//                                    $articleStr .=
//                                        '<h2>' . $trans['title'] . '</h2>' .
//                                        $trans['content'] . $enRes[$key] . $fanRes[$key];

                                    $trans['content'] = str_replace(['</p>', '<p>'], '', $trans['content']);
                                    $fanRes[$key] = str_replace(['</p>', '<p>'], '', $fanRes[$key]);
                                    $contentChAll = array_filter(explode('。', $trans['content']));
                                    $contentEnAll = array_filter(explode('{@}', $enRes[$key]));
                                    $contentFanAll = array_filter(explode('。', $fanRes[$key]));

                                    $enPart[] = [$trans['title'] => $contentEnAll];
                                    $fanPart[] = [$trans['title'] => $contentFanAll];
                                    $contentRes[] = [$trans['title'] => $contentChAll];

                                    $strContent .= '<h2>' . $trans['title'] . '</h2>';
                                    foreach ($contentChAll as $kItem => $chItem) {
                                        $strContent .= '<p>' . $chItem . '。 </p>
<p>' . $contentEnAll[$kItem] . '.</p>
<p>' . $contentFanAll[$kItem] . '。</p><br/>';
                                    }
                                }

                                //删除多余字符
                                $strContent = str_replace(['<p>.</p>', '. .</p>'], ['', '.</p>'], $strContent);

                                //英文词分词
                                $enPart = json_encode($enPart, JSON_UNESCAPED_UNICODE);
                                $fanPart = json_encode($fanPart, JSON_UNESCAPED_UNICODE);
                                $contentRes = json_encode($contentRes, JSON_UNESCAPED_UNICODE);

                                //存储入库
                                $saveData[] = [
                                    'column_id' => $column['id'],
                                    'from_path' => $re['from_path'],
                                    'domain_id' => $column['domain_id'],
                                    'key_id' => $longKeyword['id'],
                                    'title_img' => $re['title_img'],
                                    'keywords' => $longKeyword['name'],
                                    'column_name' => $column['name'],
//                                  'fan_key_id' => $longKeyword['id'],
                                    'rules_id' => $rules['id'],
                                    'content' => $strContent,
                                    'intro' => $re['intro'],
                                    'title' => $re['title'],
                                    'en_part_content' => $enPart,
                                    'fan_part_content' => $fanPart,
                                    'all_part_content' => $contentRes,
                                    'user_id' => UserId::getId(),
                                    'push_time' => Tools::randomDate('20190601', '20200501'),
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ];
                            }
                        }

//                        echo '<pre>';
//                        var_export($saveData);
//                        exit;

                        if (!empty($saveData)) {
                            //表示没有双词 则匹配
                            if (strpos($saveData[0]['title'], ',') === false) {
//                                sleep(1);
                                list($code, $msg) = self::getBaiduKey(['keywords' => $longKeyword['name']], 1);
                                if ($code < 0) {
//                                    echo '<pre>';
                                    print_r($msg) . PHP_EOL;
                                } else {
                                    Tools::writeLog('保存' . $longKeyword['name'], 'set_rules.log');

                                    $arrTitle = [];
                                    foreach ($msg as $item) {
                                        if ($item != $longKeyword['name'] && (strlen($item) > $longKeyword['name'])) {
                                            $arrTitle[] = $item;
                                        }
                                    }
                                    $saveData[0]['title'] = str_replace(',', '', $arrTitle[0]);
                                    $saveData[0]['title'] = $longKeyword['name'] . ',' . $arrTitle[0];

                                    //保存下拉词
                                    $allKeywords = AllBaiduKeywords::findOne($longKeyword['id']);
                                    $allKeywords->m_down_name = str_replace(',', '', $arrTitle[0]);
                                    $allKeywords->save(false);
                                    echo PHP_EOL . '成功：' . $longKeyword['id'] . '关键词 ：' . $longKeyword['name'];
                                    PushArticle::setArticle($saveData[0]);

                                    Tools::writeLog('保存组合双词' . $saveData[0]['domain_id'] . '  ' . $saveData[0]['title'], 'set_rules.log');
                                }
                            } else {
                                Tools::writeLog('保存爬取双词' . $saveData[0]['domain_id'], 'set_rules.log');
                                echo PHP_EOL . '成功：' . $longKeyword['id'] . '关键词 ：' . $longKeyword['name'];
                                PushArticle::setArticle($saveData[0]);
                            }
                        } else {
                            Tools::writeLog('保存失败!' . $longKeyword['name'], 'set_rules.log');
                            echo '保存失败!' . $longKeyword['name'];
                        }
                    }
                }
            }
        }
        exit;
    }

    /**
     * 设定规则 定时拉取文章 栏目深度
     */
    public static function setRulesDeep()
    {
        set_time_limit(0);

        //查询所有栏目
        $domainColumn = DomainColumn::find()->select('id,type,domain_id,zh_name,name')->where([
            'status' => self::STATUS_BASE_NORMAL,
        ])->asArray()->all();

        $url = Tools::reptileUrl() . ' /cms/article';


        foreach ($domainColumn as $column) {
            //查询分类规则
            $rules = ArticleRules::find()->where([
                'column_id' => $column['id']
            ])->asArray()->one();

            //只拉取有规则的
            if (!empty($rules)) {
                //根据短尾关键词 获取长尾关键词 小指数词
                $longKeywords = AllBaiduKeywords::find()->select('id,keywords as name,pid')
                    ->where(['>=', 'm_pv', 1])
                    ->andWhere(['<=', 'm_pv', 10])
                    ->andWhere(['type_id' => $rules->category_id])
//                    ->andWhere(['column_id' => null])
                    ->limit(3)
                    ->asArray()
                    ->all();

                foreach ($longKeywords as $key => $longKeyword) {
                    //检验是否拉取过数据
                    $oldArticleKey = PushArticle::findx($column['domain_id'])->where(['key_id' => $longKeyword['id']])->one();

                    if (!empty($oldArticleKey)) {
                        Tools::writeLog($column['zh_name'] . '--- ' . $longKeyword['name'] . '  长尾词已经拉取过了', 'set_rules . log');
                        continue;
                    }

                    echo $longKeyword['name'] . "<br/>";

                    //根据长尾关键词以及规则 从爬虫库拉取文章数据 保存到相应的文章表中
                    $data = [
                        'key_id' => $longKeyword['id'],
                        'keywords' => $longKeyword['name'],
                        'type' => strtoupper($column['type']),
                        'one_page_num_min' => $rules['one_page_num_min'],
                        'one_page_num_max' => $rules['one_page_num_max'],
                        'one_page_word_min' => $rules['one_page_word_min'],
                        'one_page_word_max' => $rules['one_page_word_max'],
                    ];

                    $bd = AllBaiduKeywords::findOne($longKeyword['id']);
                    $bd->domain_id = $column['domain_id'];
                    $bd->column_id = $column['id'];
                    $bd->save();

                    //发送请求至爬虫库
                    $res = Tools::curlPost($url, $data);

                    $res = json_decode($res, true);
                    $saveData = [];
                    if (isset($res[0]['from_path'])) {
                        foreach ($res as $re) {
                            //存储入库
                            $saveData[] = [
                                'column_id' => $column['id'],
                                'from_path' => $re['from_path'],
                                'domain_id' => $column['domain_id'],
                                'key_id' => $longKeyword['id'],
                                'title_img' => $re['title_img'],
                                'keywords' => $longKeyword['name'],
                                'column_name' => $column['name'],
                                'fan_key_id' => $longKeyword['pid'],
                                'rules_id' => $rules['id'],
                                'content' => $re['content'],
                                'intro' => $re['intro'],
                                'title' => $re['title'],
                                'user_id' => rand(1, 3822),
                                'push_time' => Tools::randomDate('20200501', ''),
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ];
                        }
                    }
//
                    if (!empty($saveData)) {
                        Tools::writeLog('保存' . $longKeyword['name'], 'set_rules.log');
                        PushArticle::batchInsertOnDuplicatex($column['domain_id'], $saveData);
//                        sleep(1);
                    } else {
                        Tools::writeLog('保存' . $longKeyword['name'], 'set_rules.log');
//                            echo ' < pre>';
//                            var_export($data);
//                            exit;
                    }
                }
//                        sleep(5);
            }
        }
        exit;
    }
}
