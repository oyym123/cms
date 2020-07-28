<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "domain_column".
 *
 * @property int $id
 * @property string|null $name 名称
 * @property string|null $tags 标签名称 逗号隔开
 * @property int|null $domain_id 域名id
 * @property string|null $domain_name 域名名称
 * @property int|null $user_id 用户id
 * @property int|null $status 10=正常 20=禁用
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class DomainColumn extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'domain_column';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['domain_id', 'user_id', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'tags', 'domain_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'tags' => 'Tags',
            'domain_id' => '域名id',
            'domain_name' => '域名',
            'user_id' => '创建者',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    //根据当前域名获取 分类
    public static function getColumn()
    {
        $url = $_SERVER['HTTP_HOST'];

        if (strpos($url, 'm.') !== false || strpos($url, 'www.') !== false) {
            $res = explode('.', $url);
            $url = $res[1] . '.' . $res[2];
        }

        //查询这个域名下的所有类目
        $domain = Domain::find()->where(['name' => $url])->one();
        $column = DomainColumn::find()->select('zh_name,name')->where([
            'domain_id' => $domain->id,
            'status' => self::STATUS_BASE_NORMAL
        ])->asArray()->all();
        return $column;
    }
}
