<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "white_article".
 *
 * @property int $id
 * @property string|null $title 标题
 * @property int|null $type 1=文档导入 2=翻译伪原创
 * @property int|null $key_id 关键词id
 * @property string|null $keywords 关键词
 * @property string|null $cut_word 切词
 * @property string|null $image_urls 图片地址 json格式
 * @property string|null $from_path 来路地址
 * @property int|null $word_count 文章字数
 * @property string|null $part_content 文章分段 json格式
 * @property string|null $content 内容
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class WhiteArticle extends \yii\db\ActiveRecord
{
    const TYPE_DOC_TXT = 10;  //txt获取
    const TYPE_DOC_WORD = 20; //word文档获取
    const TYPE_MANUALLY_WRITTEN = 30; //人工编写

    const STATUS_ENABLE = 10;   //有效
    const STATUS_DISABLE = 20;  //无效
    const STATUS_DRAFT = 30;    //草稿

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'white_article';
    }

    /** 获取所有的类型 */
    public static function getType($key = 'all')
    {
        $data = [
            self::TYPE_DOC_TXT => 'txt获取',
            self::TYPE_DOC_WORD => 'word文档获取',
            self::TYPE_MANUALLY_WRITTEN => '手动编写',
        ];
        return $key === 'all' ? $data : $data[$key];
    }

    /** 获取所有的状态 */
    public static function getStatus($key = 'all')
    {
        $data = [
            self::STATUS_ENABLE => '审核有效',
            self::STATUS_DRAFT => '草稿',
            self::STATUS_DISABLE => '无效作废',
        ];
        return $key === 'all' ? $data : $data[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'type', 'key_id', 'word_count'], 'integer'],
            [['image_urls', 'part_content', 'content'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['title', 'keywords', 'cut_word', 'from_path'], 'string', 'max' => 255],
            [['title'], 'unique'],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'type' => '类型',
            'key_id' => '关键词id',
            'keywords' => '关键词',
            'cut_word' => '切词',
            'image_urls' => '图片地址',
            'from_path' => '来源地址',
            'db_id' => '数据库名称',
            'db_class_id' => '栏目',
            'db_name' => '数据库名称',
            'status' => '状态',
            'push_time' => '发布时间',
            'word_count' => '词语统计',
            'part_content' => '内容分块',
            'content' => '内容',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    /** 创建一篇文章 */
    public static function createOne($data)
    {
        //判重 不可重复标题
        $oldInfo = self::find()->where(['title' => $data['title']])->one();

        if (!empty($oldInfo)) {
            return [-1, $data['title'] . '   已经重复了'];
        }

        $model = new WhiteArticle();
        foreach ($data as $key => $item) {
            $model->$key = $item;
        }

        $model->created_at = date('Y-m-d H:i:s');
        if (!$model->save(false)) {
            return [-1, $model->getErrors()];
        }
    }

    /** 获取数据库栏目分类数据 */
    public static function getDbClass()
    {
        $url = 'http://' . '116.193.169.122:89' . '/index.php?r=cms/get-class&db_name=thszxxdyw';
        $res = json_decode(Tools::curlGet($url), true);
        $arr = [];
        foreach ($res as $item) {
            $arr[$item['classid']] = $item['classname'];
        }
        return $arr;
    }

    /** 获取数据库所有的标签 */
    public static function getTags()
    {
        $url = 'http://' . $_SERVER['SERVER_ADDR'] . '/index.php?r=cms/get-class&db_name=jk8818com';
        $res = Tools::curlGet($url);
        $arr = [];
        foreach ($res as $item) {
            $arr[$item['classid']] = $item['classname'];
        }
        return $arr;
    }

}
