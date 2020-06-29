<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "long_keywords".
 *
 * @property int $id
 * @property string|null $m_down_name 移动端下拉词
 * @property string|null $m_search_name 移动端其他人还在搜
 * @property string|null $name 关键词名称
 * @property string|null $type 类型
 * @property string|null $m_related_name 相关搜索
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
class LongKeywords extends \yii\db\ActiveRecord
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
            [['m_down_name', 'm_search_name', 'm_related_name', 'name', 'pc_down_name', 'pc_search_name', 'pc_related_name', 'keywords', 'remark', 'url'], 'string', 'max' => 255],
        ];
    }


    const TYPE_M_DOWN = 1;      //移动下拉词
    const TYPE_M_SEARCH = 2;    //移动其他人搜索词
    const TYPE_M_RELATED = 3;   //移动相关搜索
    const TYPE_PC_SEARCH = 4;   //PC其他人搜索词
    const TYPE_PC_RELATED = 5;  //PC相关搜索词

    /**
     * @param string $key
     * @return string|string[]
     * 获取所有的类型
     */
    public function getType($key = 'all')
    {
        $data = [
            self::TYPE_M_DOWN => '移动下拉词',
            self::TYPE_M_SEARCH => '移动其他人搜索词',
            self::TYPE_M_RELATED => '移动相关搜索',
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
            return [-1, '已经重复了'];
        }

        $model = new LongKeywords();
        $model->name = $data['name'];
        $model->type = $data['type'];
        $model->m_down_name = $data['m_down_name'] ?? '';
        $model->m_search_name = $data['m_search_name'] ?? '';
        $model->m_related_name = $data['m_related_name'] ?? '';
        $model->pc_search_name = $data['pc_search_name'] ?? '';
        $model->pc_related_name = $data['pc_related_name'] ?? '';
        $model->keywords = $data['keywords'];
        $model->key_id = $data['key_id'];
        $model->status = $data['status'] ?? 1;
        $model->remark = $data['remark'] ?? '';
        $model->from = $data['from'] ?? '';
        $model->url = $data['url'] ?? '';
        $model->created_at = date('Y-m-d H:i:s');

        if (!$model->save()) {
            return [-1, $model->getErrors()];
        }
    }

    /** 抓取百度的关键词 */
    public static function getKeywords()
    {
        //获取所有的短尾关键词
        $keywords = Keywords::find()->asArray()->limit(1)->all();
        $sameArr = [];
        foreach ($keywords as $keyword) {
            //不可重复获取百度关键词
            if (empty(self::findOne($keyword['id']))) {

                self::getBaiduKey();
            } else {
                $sameArr[] = [];
            }
        }

    }

    /**
     * 获取下拉词 然后根据下拉词获取相关搜索
     */
    public static function getMBaidu($data)
    {
        $url = 'http://m.baidu.com/sugrec?pre=1&p=3&ie=utf-8&json=1&prod=wise&from=wise_web&net=&os=&sp=&callback=jsonp&wd=' . $data['keywords'];

        $url2 = 'http://m.baidu.com/sugrec?pre=1&p=8&ie=utf-8&json=1&prod=wise&from=wise_web&net=&os=&sp=&callback=jsonp&wd=' . $data['keywords'];

        $res = Tools::curlGet($url);
        $res = str_replace('jsonp(', '', $res);
        $res = substr($res, 0, -1);
        $res = json_decode($res, true);
        $mArr = $res;


    }

    /**
     * 获取相关搜素
     */
    public static function getSearchBaidu()
    {
        $url3 = 'http://m.baidu.com/s?word=初中英语';
        $res = Tools::curlGet($url3);

        if (strpos($res, '<a href="https://wappass.baidu.com/static/captcha/tuxing.html') !== false) {
            exit('机器人验证！');
        }

        //清除多余字符串，加快正则匹配速度
        $res = substr($res, 397089);
        $res = substr($res, 0, -205027);


        //大家都在搜关键词 get
//        preg_match('@哪些联想词属于不当内容(.*)? data-action-proxy="true@', $res, $data);
//        $arr = explode('&quot;text&quot;:&quot;', $data[0]);
//        $arrRes = [];
//        $arr = array_slice($arr, 1, 6);
//
//        foreach ($arr as $key => $item) {
//            $arrRes[] = preg_replace('@&quot(.*)?@', '', $item);
//        }
//
//        echo '<pre>';
//        print_r($arrRes);
//        exit;

        //相关搜索词 get
        preg_match('@target="_self" data-visited="off" rl-node="" rl-highlight-color="rgba\(0, 0, 0, .08\)" rl-highlight-radius="5px" class="c-slink c-slink-new-strong c-gap-top-small c-gap-bottom-small c-gap-top-small(.*)?</span><!----><!----></a></div></div><!----></div>@', $res, $data);
        $arr2 = explode('><!----><', $data[1]);

        foreach ($arr2 as $key => $item) {
            if (($key + 1) % 2 == 0) {
                $value = substr($item, strpos($item, ">"));
                $value = str_replace(['</span', '>'], ['', ''], $value);
                $arrRes2[] = $value;
            }
        }


        echo '<pre>';
        print_r($arrRes2);
        exit;


    }

}
