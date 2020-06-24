<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "db_name".
 *
 * @property int $id
 * @property string|null $baidu_token 百度推送token
 * @property string|null $baidu_password 百度密码
 * @property string|null $baidu_account 百度账号
 * @property string|null $domain 域名
 * @property string|null $name 数据库名称
 * @property int|null $status 0=禁用 1=正常
 * @property string|null $updated_at
 * @property string|null $created_at
 */
class DbName extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'db_name';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['baidu_token','mip_time', 'baidu_password', 'baidu_account', 'domain', 'name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'baidu_token' => '百度 Token',
            'mip_time' => 'mip推送时间',
            'baidu_password' => '百度密码',
            'baidu_account' => '百度账号',
            'domain' => '域名',
            'name' => '数据库名称',
            'status' => '状态',
            'updated_at' => '修改时间',
            'created_at' => '创建时间',
        ];
    }

    //获取所有的数据库名称
    public static function getAll()
    {
        $dbs = self::find()->select('name')->where(['status' => 1])->asArray()->all();
        return array_column($dbs, 'name');
    }

    //获取键值对
    public static function getDbName()
    {
        $dbs = self::find()->where(['status' => 1])->asArray()->all();
        return ArrayHelper::map($dbs, 'id', 'name');
    }

}
