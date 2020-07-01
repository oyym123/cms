<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "aizhan_rules".
 *
 * @property int $id
 * @property string|null $site_url
 * @property int|null $category_id
 * @property int|null $sort
 * @property int|null $status 10=正常 20=禁用 30=已经爬取完毕
 * @property int|null $domain_id 域名id
 * @property int|null $column_id 栏目id
 * @property int|null $max_search_num 过滤最高指数
 * @property string|null $note 备注
 * @property int|null $user_id 创建人id
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class AizhanRules extends \yii\db\ActiveRecord
{

    const STATUS_ENABLE = 10;
    const STATUS_DISABLE = 20;
    const STATUS_OVER = 30;

    public static function getStatus($key = 'all')
    {
        $data = [
            self::STATUS_ENABLE => '正常',
            self::STATUS_DISABLE => '禁用',
            self::STATUS_OVER => '抓取完毕',
        ];
        return $key === 'all' ? $data : $data[$key];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'aizhan_rules';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'site_url'], 'required'],
            [['category_id', 'sort', 'status', 'domain_id', 'column_id', 'max_search_num', 'user_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['site_url'], 'string', 'max' => 65],
            [['note'], 'string', 'max' => 255],
            [['site_url'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'site_url' => '网址',
            'category_id' => '类型id',
            'sort' => '排序',
            'status' => '状态',
            'domain_id' => '域名id',
            'column_id' => '分类id',
            'max_search_num' => '最高的指数',
            'note' => '备注',
            'user_id' => '用户id',
            'created_at' => '创建数据',
            'updated_at' => '修改时间',
        ];
    }

    /** 获取域名 */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /** 获取域名 */
    public function getDomain()
    {
        return $this->hasOne(Domain::className(), ['id' => 'domain_id']);
    }

    /** 获取域名 */
    public function getColumn()
    {
        return $this->hasOne(DomainColumn::className(), ['id' => 'column_id']);
    }


    /** 抓取数据 */
    public static function catchData()
    {


    }

}
