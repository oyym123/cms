<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "article_rules".
 *
 * @property int $id
 * @property string|null $name 规则名称
 * @property int|null $category_id 分类id
 * @property int|null $domain_id 域名id
 * @property int|null $column_id 目录id
 * @property string|null $method_ids 方法id集合
 * @property int|null $one_page_num_min 一篇文章最低拼接数量
 * @property int|null $one_page_num_max 一篇文章最高拼接数量
 * @property int|null $one_page_word_min 一篇文章最少字数
 * @property int|null $one_page_word_max 一篇文章最高字数
 * @property int|null $one_day_push_num 一天推送的文章数量
 * @property string|null $push_time_sm 推送给神马的时间
 * @property string|null $push_time_bd 推送给百度的时间
 * @property int|null $user_id 用户id
 * @property int|null $status 10=正常 20=禁用
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class ArticleRules extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article_rules';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['method_ids', 'domain_id', 'column_id', 'category_id'], 'required'],
            [['one_page_num_min', 'one_page_num_max', 'one_page_word_min', 'one_page_word_max', 'one_day_push_num', 'user_id', 'status', 'category_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'push_time_sm', 'push_time_bd'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '规则名称',
            'category_id' => '分类id',
            'domain_id' => '域名id',
            'column_id' => '目录id',
            'method_ids' => '方法id集合',
            'one_page_num_min' => '一篇文章最低拼接数量',
            'one_page_num_max' => '一篇文章最高拼接数量',
            'one_page_word_min' => '一篇文章最少字数',
            'one_page_word_max' => '一篇文章最高字数',
            'one_day_push_num' => '一天推送的文章数量',
            'push_time_sm' => '推送给神马的时间',
            'push_time_bd' => '推送给百度的时间',
            'user_id' => '用户id',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }


    /** 处理文章 */
    public static function dealData()
    {
        DomainTpl::setTmp();
        exit;
        //查询规则
        $rules = self::find()->where([
            'status' => self::STATUS_BASE_NORMAL,
        ])->all();
        $error = [];


        //循环处理规则
        foreach ($rules as $rule) {
            //类型筛选
            list($code, $msg) = Category::cateArticle($rule->category_id);

            if ($code < 0) {
                $error[] = $msg;
            } else {
                $data = $msg;

                //TODO::手法处理


                //TODO::拼接检验

                foreach ($data as $item) {
                    //保存数据
                    $saveData = [
                        'b_id' => $item['id'],
                        'column_id' => $rule->column_id,
                        'column_name' => ($x = $rule->column) ? $x->name : '',
                        'rules_id' => $rule->id,
                        'domain_id' => $rule->domain_id,
                        'domain' => ($x = $rule->domain) ? $x->name : '',
                        'from_path' => $item['from_path'],
                        'keywords' => $item['keywords'],
                        'title_img' => $item['title_img'],
                        'status' => self::STATUS_BASE_NORMAL,
                        'content' => $item['content'],
                        'intro' => $item['intro'],
                        'title' => $item['title'],
                        'push_time' => Tools::randomDate('20200501', ''),
                        'created_at' => date('Y-m-d H:i:s'),
                    ];

                    list($saveCode, $saveMsg) = PushArticle::createOne($saveData);
                    if ($saveData < 0) {
                        $error[] = $saveMsg;
                    }
                }
            }
        }
    }

    /** 获取域名 */
    public function getDomain()
    {
        return $this->hasOne(Domain::className(), ['id' => 'domain_id']);
    }

    /** 获取类型 */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }


    /** 获取类目 */
    public function getColumn()
    {
        return $this->hasOne(DomainColumn::className(), ['id' => 'column_id']);
    }
}
