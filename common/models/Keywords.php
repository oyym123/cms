<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "keywords".
 *
 * @property int $id
 * @property string|null $keywords 关键词
 * @property int|null $type 类型
 * @property int|null $sort 排序
 * @property string|null $form 来自地方 人工 = user 其他= other
 * @property string|null $rank 排名 来自爱站网
 * @property int|null $search_num 搜索量
 * @property string|null $title 网页标题
 * @property string|null $content 具体内容
 * @property string|null $note 备注
 * @property string|null $url 链接
 * @property string|null $created_at 创建时间
 * @property string|null $updated_at 修改时间
 */
class Keywords extends Base
{

    const FROM_USER = 'user';   //来自人工
    const FROM_ROBOT = 'robot'; //来自机器人

    const TYPE_SHORT_MOBILE = 1;       //移动端短尾词
    const TYPE_LONG_MOBILE = 2;        //移动端长尾词
    const TYPE_SHORT_PC = 3;           //PC端短尾词
    const TYPE_LONG_PC = 4;            //PC端长尾词

    public static function getNoAllow()
    {
        return [
            '.',
            '图片',
            '照片',
            '视频',
            'txt',
            'pdf',
            '!',
            ' ',
            '#',
            'ppt',
            'doc',
            'mp4',
            'mp3',
            '_',
            ':',
            '下载',
            '！',
            '!',
            '&',
            '百度云',
            '微信',
            '小说',
            '微盘'
        ];
    }

    /** 获取所有来源 */
    public static function getFrom($key = 'all')
    {
        $data = [
            self::FROM_USER => '人工',
            self::FROM_ROBOT => '机器人',
        ];
        return $key === 'all' ? $data : $data[$key];
    }

    /** 获取所有的类型 */
    public static function getType($key = 'all')
    {
        $data = [
            self::TYPE_SHORT_MOBILE => '移动端短尾词',
            self::TYPE_LONG_MOBILE => '移动端长尾词',
            self::TYPE_SHORT_PC => 'PC端短尾词',
            self::TYPE_LONG_PC => 'PC端长尾词',
        ];
        return $key === 'all' ? $data : $data[$key];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'keywords';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sort', 'search_num', 'type', 'status', 'rules_id'], 'integer'],
            [['content'], 'string'],
            [['created_at', 'updated_at', 'check_time'], 'safe'],
            [['keywords', 'form', 'rank', 'title', 'note', 'url'], 'string', 'max' => 255],
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
            'sort' => '排序',
            'type' => '类型',
            'form' => '来自',
            'rank' => '爱站排名',
            'search_num' => '搜索数量',
            'title' => '网页标题',
            'content' => '内容页数据',
            'check_time' => '检测时间',
            'note' => '备注',
            'status' => '状态',
            'url' => '链接',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    /** 获取规则 */
    public function getAizhanRules()
    {
        return $this->hasOne(AizhanRules::className(), ['id' => 'rules_id']);
    }

    /** 创建一个关键词*/
    public static function createOne($data)
    {
        if (empty($data['keywords'])) {
            return [-1, '没有关键词'];
        }

        $keywords = Keywords::find()->where(['keywords' => $data['keywords']])->one();

        if (!empty($keywords)) {
            return [-1, '关键词重复'];
        }

        $model = new Keywords();
        $model->keywords = $data['keywords'];
        $model->sort = $data['sort'] ?? 0;
        $model->type = $data['type'];
        $model->form = $data['form'] ?? 'user';
        $model->rank = $data['rank'];
        $model->search_num = $data['search_num'];
        $model->title = $data['title'];
        $model->content = $data['content'];
        $model->note = $data['note'] ?? '';
        $model->url = $data['url'] ?? '';
        $model->created_at = $data['created_at'] ?? date('Y-m-d H:i:s');

        if (!$model->save(false)) {
            return [-1, $model->getErrors()];
        } else {
            return [1, $model];
        }
    }

    /** 爬取爱站网的竞品关键词数据 */
    public static function catchKeyWords()
    {
        set_time_limit(0);
        //爱站网址 mobile =手机
        $aizhanUrl = 'https://baidurank.aizhan.com/mobile/';
        $rulesAll = AizhanRules::find()->where(['status' => AizhanRules::STATUS_ENABLE])->all();
        //循环要爬取的网址
        foreach ($rulesAll as $rules) {
            //通过第一页 获取到总共有多少页
            $targetUrl = $rules->site_url;
            $url = $aizhanUrl . $targetUrl . '/-1/0/1/position/1/';
            $res = Tools::curlGet($url);
            preg_match('@baidurank-pager(.*)?baidurank-content box@s', $res, $result);
            $result = array_filter(explode('rel="nofollow"', $result[0]));
            $pageNum = count($result) - 1;

            //获取后面的页数
            for ($i = $pageNum; $i > 0; $i--) {
                $url = $aizhanUrl . $targetUrl . '/-1/0/' . $i . '/position/1/';
                $res = Tools::curlGet($url);
                preg_match('@<td class="title">
							<a class="gray"(.*)?</td>
					</tr>@s', $res, $result);
                $result = array_filter(explode('<td class="title">', $result[0]));

                if (empty($result)) {
                    exit('抓取完成，没有数据了！');
                }

                $error = [];
//            $result = array_splice($result,0,1);
                header("content-Type: text/html; charset=Utf-8");
                foreach ($result as $key => $re) {
                    preg_match('@第(.*)?位@', $re, $rank1);
                    preg_match('@第(.*)?页@', $re, $rank2);
                    preg_match('@/">(.*)?</td>
						<td class="owner">@s', $re, $searchNum);
                    preg_match('@<a name="baiduLink" rel="nofollow" target="_blank" href="(.*)?" class="gray" title="(.*)?">@', $re, $info);
                    preg_match('@<a class="gray" rel="nofollow" target="_blank" href="(.*)?" title="(.*)?">@', $re, $keywords);
                    preg_match('/\d+/', $searchNum[1], $arrNum);

//                $content = Tools::curlGet($info[1]);
//                $content = mb_convert_encoding($content, 'UTF-8');

                    $data = [
                        'rank' => $rank1[1] . ',' . $rank2[1],
                        'search_num' => $arrNum[0],
                        'type' => self::TYPE_SHORT_MOBILE,
                        'keywords' => $keywords[2],
                        'url' => $info[1],
                        'title' => $info[2],
                        'form' => self::FROM_ROBOT,
                        'content' => $url,
                        'note' => $url,
                    ];

                    $flag = 0;
                    foreach (self::getNoAllow() as $no) {
                        if (strpos($no, $keywords[2]) !== false) {
                            $flag = 1;
                        }
                    }

                    //当词语中包含一些不应该存在的词 则不保存
                    if ($flag == 1) {
                        continue;
                    }

                    list($code, $msg) = self::createOne($data);
                    if ($code < 0) {
                        $error[] = $keywords[2] . $msg;
                    }
                }
                self::dd($error);
            }

            $rules->status = AizhanRules::STATUS_OVER;
            $rules->updated_at = date('Y-m-d H:i:s');
            $rules->save(false);
        }
    }

    /** 筛选后的词入库 */
    public static function setKeywords($data)
    {
        $rulesAll = AizhanRules::find()->where(['status' => AizhanRules::STATUS_ENABLE])->all();

        //循环规则
        foreach ($rulesAll as $rules) {
            $keywords = Keywords::find()->where([
                'rules_id' => $rules->id,
                'note' => 100,
                'status' => Base::S_ON
            ])->andWhere(['<=', 'search_num', 10])->all();

            foreach ($keywords as $keyword) {
                sleep(1);
                //相关词 导入到长尾词库
                list($code, $longKeywords) = LongKeywords::getBaiduKey([
                    'keywords' => $data['keywords'],
                    'id' => $keyword->id
                ], 0);

                if ($code < 0) {
                    $error[] = $longKeywords;
                } else {
                    $longKeywords = array_merge([$keyword->keywords], $longKeywords);
                    //导入到总词库
                    list($code1, $msg1) = AllBaiduKeywords::setKeywordsAiZhan([
                        'type_id' => $rules->category_id,
                        'keywords' => implode(PHP_EOL, $longKeywords)
                    ]);
                    if ($code1 < 0) {
                        $error[] = $msg1;
                    }
                }
            }
        }

        print_r($error);
        exit;
    }


    /** 获取下拉词相关词 */
    public function getKeywords($keywords)
    {
        //循环所有的tags
        $keywords = BaiduKeywords::find()->where(['status' => 1])->all();

        foreach ($keywords as $keyword) {
            //获取下拉词 并且保存到长尾词库
            list($code, $longKeywords) = LongKeywords::getBaiduKey([
                'keywords' => $keyword->keywords,
                'id' => $keyword->id
            ], 2);

            if ($code < 0) {
                continue;
            }

            $remainKeywords = $mDownTags = [];

            //取三个下拉词 存到tags
            foreach ($longKeywords as $key => $longKeyword) {
                if ($key <= 2) {
                    $mDownTags[] = $longKeyword;
                } else {
                    $remainKeywords[] = $longKeyword;
                }
            }


            print_r($longKeywords);
            exit;

            //剩下的下拉词 拿去保存


            //导入到总词库
            list($code1, $msg1) = AllBaiduKeywords::setKeywordsTags([
                'type_id' => $keyword->type_id,
                'keywords' => implode(PHP_EOL, $keywords)
            ]);
            if ($code1 < 0) {
                $error[] = $msg1;
            }
        }

    }
}
