<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "mip_flag".
 *
 * @property int $id
 * @property int|null $db_id 数据库id
 * @property string|null $db_name 数据库名称
 * @property int|null $type 1=文章 2=tag
 * @property int|null $type_id 类型id
 * @property int|null $status 0=禁用 1=正常
 * @property string|null $url
 * @property int|null $remain
 * @property string|null $created_at
 * @property string|null $updated_at 创建时间
 */
class MipFlag extends Base
{
    const TYPE_ARTICLE = 1; //文章类型
    const TYPE_TAG = 2;     //标签类型

    const TYPE_ARTICLE_FAST = 3; //文章快速
    const TYPE_TAG_FAST = 4;     //标签快速

    /** 获取所有的类型 */
    public static function getType($key = 'all')
    {
        $data = [
            self::TYPE_ARTICLE => '文章提交',
            self::TYPE_TAG => '标签提交',
            self::TYPE_ARTICLE_FAST => '快速文章提交',
            self::TYPE_TAG_FAST => '快速标签提交',
        ];
        return $key === 'all' ? $data : $data[$key];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mip_flag';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['db_id', 'type', 'type_id', 'status', 'remain'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['db_name', 'url'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'db_id' => '数据库id',
            'db_name' => '数据库名称',
            'type' => '类型',
            'type_id' => '类型id',
            'url' => '链接',
            'remain' => '剩余条数',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    //查询是否已经推送过了
    public static function checkIsMip($url)
    {
        $res = self::find()->where([
            'url' => $url,
        ])->one();
        return $res;
    }

    /** 插入一条记录 */
    public static function createOne($data)
    {
        $model = new MipFlag();
        $model->db_id = $data['db_id'];
        $model->db_name = $data['db_name'];
        $model->type = $data['type'];
        $model->type_id = $data['type_id'];
        $model->remain = $data['remain'] ?? 0;
        $model->url = $data['url'] ?? '';
        $model->created_at = date('Y-m-d H:i:s');
        if (!$model->save()) {
            return $model->getErrors();
        }
    }

    public static function getAllUrl($name)
    {
        $limit = 3000; //取网站地图后最新的url
        $filePathM = __DIR__ . '/../../frontend/views/site/' . $name . '/home/static/m_site.txt';
        $filePathPC = __DIR__ . '/../../frontend/views/site/' . $name . '/home/static/site.txt';
        if (!file_exists($filePathM) || !file_exists($filePathPC)) {
            Tools::curlGet($name . '/m_site.txt');
            sleep(10);
            Tools::curlGet($name . '/site.txt');
            sleep(10);
        }
        $resM = array_slice(array_filter(explode(PHP_EOL, file_get_contents($filePathM))), 0, $limit);
        $resPc = array_slice(array_filter(explode(PHP_EOL, file_get_contents($filePathPC))), 0, $limit);
        return [$resM, $resPc];
    }

    /** 推送URL */
    public static function pushUrl($domainId = 0, $test = 0, $type = 1)
    {
        //推送
        if ($domainId) {
            $where = [
                'id' => $domainId
            ];
        }

        $domainIds = BaiduKeywords::getDomainIds();
        $errorArr = [];
        $domains = Domain::find()->where(['in', 'id', $domainIds])->all();
        foreach ($domains as $domain) {
            list($resM, $resPc) = self::getAllUrl($domain->name);
            $res = $type == 1 ? $resPc : $resM;

            //获取所有的文章进行
            foreach ($res as $re) {
                //判断是否已经提交过了
                $flag = MipFlag::checkIsMip($re);
                if (!empty($flag)) {          //表示已经提交过了
                    $errorArr[] = $re;
                } else {
                    $info[] = $re;
                }
            }
//
//            echo '<pre>';
//            print_r($info);
//            exit;
            if ($test == 1 && $type == 1) {
                self::dd($info);
            } elseif ($test == 1 && $type == 2) {
                self::dd($info);
            }
            self::pushData($domain, $info, $type);
        }
        echo '<pre>';
        print_r($errorArr);
        echo $domain->name . '  推送完成' . PHP_EOL;
//        exit;
    }

    public static function pushData($domain, $info, $type)
    {
        $flag = $type == 1 ? 'pc' : 'm';

        //推送
        $resData = self::push($domain->baidu_token, $domain->name, [$info[0]], $flag);
        echo '<pre>';
        $resDa = json_decode($resData, true);
//        print_r($resDa);
        $jsonres = json_decode($resData);
        Tools::writeLog($jsonres);

        if (!isset($resDa['success'])) {
            Tools::writeLog(['res' => $domain->name . "百度站长Tag推送失败:", 'data' => $jsonres]);
            return 1;
        } else {
            Tools::writeLog($domain->name . "百度站长Tag成功推送" . $jsonres->success . "条，今日还可推送:" . $jsonres->remain . "条");
            //更新插入 标记已经推送过了
            $saveData = [
                'db_id' => $domain->id,
                'db_name' => $domain->name,
                'type' => MipFlag::TYPE_TAG,
                'type_id' => 0,
                'url' => $info[0],
                'remain' => $jsonres->remain,
            ];
            MipFlag::createOne($saveData);
            $remain = $jsonres->remain;
        }

        if ($remain == 0) {
            Tools::writeLog($domain->name . "Tag推送次数用完");
            return 1;
        } else {
            $remain = 200;
            $urls = array_slice($info, 1, $remain);
            $info = $urls;
        }

        //按照剩余次数进行推送
        $resData = self::push($domain->baidu_token, $domain->name, $info, $flag);
        $resDaSecond = json_decode($resData, true);
        $jsonres = json_decode($resData);

        Tools::writeLog($jsonres);
        if (!isset($resDaSecond['success'])) {
            Tools::writeLog(['res' => $domain->name . "百度站长Tag推送失败:", 'data' => $jsonres]);
            return 1;
        } else {
            Tools::writeLog($domain->name . "百度站长Tag成功推送" . $jsonres->success . "条，今日还可推送:" . $jsonres->remain . "条");
            foreach ($urls as $key => $url) {
                if ($key > 0) {
                    //更新插入 标记已经推送过了
                    $saveData = [
                        'db_id' => $domain->id,
                        'db_name' => $domain->name,
                        'type' => MipFlag::TYPE_TAG,
                        'type_id' => 0,
                        'url' => $url,
                    ];
                    MipFlag::createOne($saveData);
                    $remain = $jsonres->remain;
                }
            }
        }
    }

    //mip推送
    public static function push($token, $domain, $urls, $type = 'm')
    {
        if ($type == 'm') {
            $api = CmsAction::BAIDU_URL . '?site=m.' . $domain . '&token=' . $token;
        } elseif ($type == 'pc') {
            $api = CmsAction::BAIDU_URL . '?site=www.' . $domain . '&token=' . $token;
        }

        $userAgent = self::randUserAgent();

        $ipUrl = 'https://api.xiaoxiangdaili.com/ip/get?appKey=623460644494397440&appSecret=1zblJiRJ&cnt=1&wt=json%20';

//        $ipUrl = 'https://api.xiaoxiangdaili.com/ip/get?appKey=623452295174443008&appSecret=8evtFPzt&cnt=1&wt=json%20';

        $ipInfo = json_decode(Tools::curlGet($ipUrl), true);

        if ($ipInfo['code'] == 200) {
            $ip = $ipInfo['data']['ip'];
            $port = $ipInfo['data']['port'];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC); //代理认证模式
            curl_setopt($ch, CURLOPT_PROXY, $ip); //代理服务器地址
            curl_setopt($ch, CURLOPT_PROXYPORT, $port); //代理服务器端口

            $options = array(
                CURLOPT_URL => $api,
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => implode("\n", $urls),
                curl_setopt($ch, CURLOPT_USERAGENT, $userAgent),
                CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
            );
            curl_setopt_array($ch, $options);
            $result = curl_exec($ch);

//            self::dd($result);

            return $result;
        }
    }

    //mip推送
    public static function pushFast($token, $domain, $urls)
    {
        $api = CmsAction::BAIDU_URL . '?site=' . $domain . '&token=' . $token . '&type=daily';
        $ch = curl_init();
        $options = array(
            CURLOPT_URL => $api,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => implode("\n", $urls),
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        return $result;
    }


    /** 随机user-agent */
    public static function randUserAgent()
    {
        $useragent = [
            "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/22.0.1207.1 Safari/537.1",
            "Mozilla/5.0 (X11; CrOS i686 2268.111.0) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.57 Safari/536.11",
            "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/536.6 (KHTML, like Gecko) Chrome/20.0.1092.0 Safari/536.6",
            "Mozilla/5.0 (Windows NT 6.2) AppleWebKit/536.6 (KHTML, like Gecko) Chrome/20.0.1090.0 Safari/536.6",
            "Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/19.77.34.5 Safari/537.1",
            "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/536.5 (KHTML, like Gecko) Chrome/19.0.1084.9 Safari/536.5",
            "Mozilla/5.0 (Windows NT 6.0) AppleWebKit/536.5 (KHTML, like Gecko) Chrome/19.0.1084.36 Safari/536.5",
            "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/536.3 (KHTML, like Gecko) Chrome/19.0.1063.0 Safari/536.3",
            "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/536.3 (KHTML, like Gecko) Chrome/19.0.1063.0 Safari/536.3",
            "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_0) AppleWebKit/536.3 (KHTML, like Gecko) Chrome/19.0.1063.0 Safari/536.3",
            "Mozilla/5.0 (Windows NT 6.2) AppleWebKit/536.3 (KHTML, like Gecko) Chrome/19.0.1062.0 Safari/536.3",
            "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/536.3 (KHTML, like Gecko) Chrome/19.0.1062.0 Safari/536.3",
            "Mozilla/5.0 (Windows NT 6.2) AppleWebKit/536.3 (KHTML, like Gecko) Chrome/19.0.1061.1 Safari/536.3",
            "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/536.3 (KHTML, like Gecko) Chrome/19.0.1061.1 Safari/536.3",
            "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/536.3 (KHTML, like Gecko) Chrome/19.0.1061.1 Safari/536.3",
            "Mozilla/5.0 (Windows NT 6.2) AppleWebKit/536.3 (KHTML, like Gecko) Chrome/19.0.1061.0 Safari/536.3",
            "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/535.24 (KHTML, like Gecko) Chrome/19.0.1055.1 Safari/535.24",
            "Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/535.24 (KHTML, like Gecko) Chrome/19.0.1055.1 Safari/535.24",
        ];
        return $useragent[rand(0, count($useragent) - 1)];
    }

    /** 获取新的url */
    public static function getCrontabData($filePath, $da, $flag, $num = 50000)
    {
        $_GET['domain'] = 0;
        $domain = $da->name;
        $urls = [];
        //当文件不存在时，全部搜索
        if (file_exists($filePath)) {
            $articles = PushArticle::findx($da->id)->select('id,column_name,key_id')->limit($num)->asArray()->orderBy('id desc')->all();
        } else {   //否则取文件的第一行数据 获得其尾号id 然后将两个数组合并
            $urls = explode(PHP_EOL, file_get_contents($filePath));
            $arr = explode('/', $urls[0]);
            $resUrl = $arr[count($arr) - 1];
            if (preg_match('/\d+/', $resUrl, $lastArr)) {
                $lastId = $lastArr[0];
            }

            $articles = PushArticle::findx($da->id)
                ->select('id,column_name,key_id')
                ->where(['>', 'id', $lastId])
                ->limit($num)
                ->orderBy('id desc')
                ->asArray()
                ->all();
        }

        $data = [];
        foreach ($articles as $article) {
            $data[] = 'https://' . $flag . $domain . '/' . $article['column_name'] . '/' . $article['id'] . '.html';
            $data[] = 'https://' . $flag . $domain . '/' . $da->start_tags . $article['key_id'] . $da->end_tags;
        }

        $data = array_unique(array_merge($data, $urls));
        $str = '';

        foreach ($data as $datum) {
            $str .= $datum . PHP_EOL;
        }

        //存入缓存文件
        file_put_contents($filePath, $str);
    }

    /** 定时生成网站地图 */
    public static function crontabSet()
    {
        set_time_limit(0);
        //获取所有的域名    生成网站地图
        $doamins = Domain::find()->all();
        $_GET['domain'] = 0;
        foreach ($doamins as $da) {
            if (!empty($da->baidu_token)) {
                $domain = $da->name;
                $num = 50000;
                //生成PC端
                $filePath = __DIR__ . '/../../frontend/views/site/' . $domain . '/home/static/site.txt';
                self::getCrontabData($filePath, $da, 'www.', $num);
                //生成移动端

                $filePath = __DIR__ . '/../../frontend/views/site/' . $domain . '/home/static/m_site.txt';
                self::getCrontabData($filePath, $da, 'm.', $num);
            }
        }
        exit;
    }

    public static function pushMip()
    {
        set_time_limit(0);
        //获取所有的域名
        $doamins = Domain::find()->all();
        $_GET['domain'] = 0;
        foreach ($doamins as $da) {
            if (!empty($da->baidu_token) && $da->name != 'demo.com') {
                MipFlag::pushUrl($da->id, 0, 1); //PC推送
            }
        }
    }

    public static function pushMipM()
    {
        set_time_limit(0);
        //获取所有的域名
        $doamins = Domain::find()->all();
        $_GET['domain'] = 0;
        foreach ($doamins as $da) {
            if (!empty($da->baidu_token) && $da->name != 'demo.com') {
                MipFlag::pushUrl($da->id, 0, 2); //移动推送
            }
        }
    }



}
