<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "domain".
 *
 * @property int $id
 * @property string|null $name 域名名称
 * @property string|null $ip 所在ip
 * @property int|null $status 10=正常  20=禁用
 * @property int|null $user_id 创建人id
 * @property string|null $created_at 创建时间
 * @property string|null $updated_at 修改时间
 */
class Domain extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'domain';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'end_tags', 'start_tags', 'jump_url','zh_name','intro'], 'required'],
            [['status', 'user_id', 'is_jump'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'ip'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '域名地址',
            'ip' => 'Ip',
            'zh_name' => '网站名称',
            'intro' => '描述',
            'end_tags' => '泛目录结尾符',
            'start_tags' => '泛目录开始符',
            'is_jump' => '是否开启流量转化',
            'jump_url' => '流量化跳转时的地址',
            'status' => '状态',
            'user_id' => '创建者',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    //获取键值对
    public static function getDomianName()
    {
        $dbs = self::find()->asArray()->all();
        return ArrayHelper::map($dbs, 'id', 'name');
    }

    public static function getOne($name)
    {
        return self::find()->where(['name' => $name])->one();
    }

    /** 通过顶级域名判断id */
    public static function getDomainInfo()
    {
        $domain = Tools::getDoMain($_SERVER['HTTP_HOST']);
        $domainModel = self::find()->where(['name' => $domain])->one() ?: 0;
        $domainInfo = $domainModel ? $domainModel : 0;
        return $domainInfo;
    }

}
