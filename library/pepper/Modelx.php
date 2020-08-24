<?php

namespace library\pepper;

use Yii;
use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

/**
 * 分表基础类
 * Class Modelx
 * @package library
 */

class Modelx extends \yii\db\ActiveRecord
{

    //原始表名，表结构母版
    protected static $originalName = '';
    //动态表名
    protected static $tableName = '';
    //分表关键字
    protected static $targetKey = '';
    //redis表名set key
    protected static $tableSetKey = '';

    /**
     * @param null $targetKey
     * @param array $config
     * @throws Exception
     */
    public function __construct($targetKey = null, $config = []){
        parent::__construct($config);
        static::$tableName = static::renderTable($targetKey);
    }

    public static function tableName(){
        return static::$tableName;
    }

    /**
     * 根据关键值获得表名
     * @throws Exception
     * @return string
     */
    public static function getTableName(){
        return static::$originalName . '_'. self::$targetKey;
//        throw new Exception(get_called_class() . "::" . __FUNCTION__ . ' must be override');
////        return static::$originalName . '_'. (static::$targetKey % 10);
    }

    /**
     * 根据vip_card探测表名
     * @param null $targetKey
     * @return string
     * @throws Exception
     */
    public static function renderTable($targetKey = null){
        if(!$targetKey) //表示没后缀 则展示原表
            return static::$originalName;

        static::$targetKey = $targetKey;
        $tableName = static::getTableName();
        /*暂时关闭自动创建分表
        //if hit cache
        $redis = Yii::$app->redis;
        if($redis->sismember(static::$tableSetKey, $tableName))
            return $tableName;

        //if hit db
        $db = static::getDb();
        if($db->createCommand("SHOW TABLES LIKE '{$tableName}'")->queryAll()){
            $redis->sadd(static::$tableSetKey, $tableName);
            return $tableName;
        }else{ //maybe
            $redis->srem(static::$tableSetKey, $tableName);
        }

        //just do it
        $originalTable = static::$originalName;
        $createTableRet = $db->createCommand("SHOW CREATE TABLE `{$originalTable}`")->queryOne();
        $createTable = str_replace("`{$originalTable}`","`{$tableName}`",$createTableRet['Create Table']);
        $createTable = preg_replace('/\sAUTO_INCREMENT=\d+/','',$createTable);
        try{
            $db->createCommand($createTable)->execute();
            $redis->sadd(static::$tableSetKey, $tableName);
        }catch (Exception $ex){
            throw new Exception("Error execute sql: {$createTable}");
        }*/

        return $tableName;

    }

    /**
     * 扩展的find
     * @param $targetKey
     * @return \yii\db\ActiveQuery
     */
    public static function findx($targetKey = null){
        static::$tableName = static::renderTable($targetKey);
        return Yii::createObject(ActiveQuery::className(), [get_called_class(), ['from' => [static::tableName()]]]);
    }

    /**
     * 扩展的findAll
     * @param null $targetKey
     * @param array $params
     * @return \yii\db\ActiveQuery[]
     */
    public static function findAllx($targetKey = null,$params = []){
        return static::findByConditionx($targetKey, $params)->all();
    }

    /**
     * @Override
     * @param array $row
     * @return static
     */
    public static function instantiate($row){
        return new static(static::$targetKey);
    }

    /**
     * 禁止使用findBySql
     * @param string $sql
     */
    public static function findBySqlx($sql){
        throw new InvalidCallException("not allowed. {$sql}");
    }

    /**
     * 扩展的findOne
     * @param null $targetKey
     * @param array $condition
     * @return null|static|ActiveQuery
     */
    public static function findOnex($targetKey = null, $condition = []){
        return static::findByConditionx($targetKey, $condition)->one();
    }

    /**
     * 内部实现
     * @param null $targetKey
     * @param $condition
     * @return ActiveQuery[]|ActiveQuery
     * @throws InvalidConfigException
     */
    protected static function findByConditionx($targetKey = null, $condition){

        $query = static::findx($targetKey);

        if (!ArrayHelper::isAssociative($condition)) {
            // query by primary key
            $primaryKey = static::primaryKey();
            if (isset($primaryKey[0])) {
                $condition = [$primaryKey[0] => $condition];
            } else {
                throw new InvalidConfigException('"' . get_called_class() . '" must have a primary key.');
            }
        }

        return $query->andWhere($condition);
    }

    /**
     * 插入数据
     * @param $models
     * @param array $data
     * @return bool|mixed //自增id | false
     */
    public static function insertGetId($models, array $data)
    {
        foreach ($data as $k => $v) {
            $models->$k = $v;
        }

        if (!($models->save())) {
            Yii::info(['InsertGetId.error' => $models->getErrors()], 'common');
            return [false,implode('  ',array_unique(array_column($models->getErrors(),'0')))];
        }

        return [$models->attributes['id'],''];
    }


    public static function ignoreInsertx($targetKey = null,array $result)
    {
        $columns = array_keys($result[0]);
        $rows    = array_values($result);
        static::$tableName = static::renderTable($targetKey);
        $sql     = static::getDb()->getQueryBuilder()->batchInsert(static::tableName(), $columns, $rows);
        $sql     = str_replace("INSERT INTO", "INSERT IGNORE INTO", $sql);
        return static::getDb()->createCommand($sql)->execute();
    }


    public static function batchInsertOnDuplicatex($targetKey = null,array $result, array $update = array())
    {
        $columns = array_keys($result[0]);
        $rows    = array_values($result);
        if (empty($update)) {
            $update = $columns;
        }
        static::$tableName = static::renderTable($targetKey);
        $sql = static::getDb()->getQueryBuilder()->batchInsert(static::tableName(), $columns, $rows);

        $updates = [];
        foreach ($update as $item) {
            $updates[] = "{$item}=VALUES($item)";
        }
        $sql = "{$sql} ON DUPLICATE KEY UPDATE " . implode(", ", $updates);

        return static::getDb()->createCommand($sql)->execute();
    }


    public static function updateAllx($targetKey = null,$attributes, $condition = '', $params = [])
    {
        static::$tableName = static::renderTable($targetKey);
        $command = static::getDb()->createCommand();
        $command->update(static::tableName(), $attributes, $condition, $params);

        return $command->execute();
    }
}