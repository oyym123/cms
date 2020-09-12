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
class MipFlag extends \yii\db\ActiveRecord
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
    public static function checkIsMip($dbId, $type, $typeId)
    {
        $res = self::find()->where([
            'db_id' => $dbId,
            'type' => $type,
            'type_id' => $typeId,
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

    /** 推送URL */
    public static function pushUrl($domainId = 0)
    {
        if ($domainId) {
            $where = [
                'id' => $domainId
            ];
        }

        $res = Domain::find()->where($where)->all();

        $urls = $errorArr = [];
        $info = [];

        //获取所有的文章进行
        foreach ($res as $re) {
            //判断是否已经提交过了
            $flag = MipFlag::checkIsMip($re->id, MipFlag::TYPE_TAG, $re->tagid);
            if (!empty($flag)) { //表示已经提交过了
                $errorArr[] = $re->tagid;
            } else {
                $info[] = [
                    'type_id' => $re->tagid,
//                    'url' => $domain . '/e/tags/?tagid=' . $re->tagid,
                ];

            }
        }

        if (empty($urls)) {
            Tools::writeLog($re->name . "没有更新的Tag链接可以提交");
            return 1;
        }

        //获取第一条 推送，然后获取到剩余条数，根据剩余条数 再推送
        $urlFirst = [$urls[0]];

//        $resData = $this->push($db->baidu_token, $domain, $urlFirst);


    }
}
