<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "images".
 *
 * @property int $id
 * @property int|null $type 类型id 1=文章图片
 * @property int|null $user_id 上传人
 * @property string|null $name 图片名称
 * @property string|null $content 图片内容
 * @property string|null $url 图片地址
 * @property string|null $created_at 创建时间
 * @property string|null $updated_at 修改时间
 */
class Images extends \yii\db\ActiveRecord
{

    const TYPE_ZUOWENWANG = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'images';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'user_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'url'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'user_id' => 'User ID',
            'name' => 'Name',
            'content' => 'Name',
            'url' => 'Url',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * 插入图片库
     */
    public static function createOne($data)
    {
        //判重 不可有所有重复的关键词
        $oldInfo = self::find()->where(['name' => $data['name']])->one();

        if (!empty($oldInfo)) {
            return [-1, $data['name'] . '   已经重复了'];
        }

        $model = new Images();

        foreach ($data as $key => $item) {
            $model->$key = $item;
        }

        $model->created_at = date('Y-m-d H:i:s');
        if (!$model->save(false)) {
            return [-1, $model->getErrors()];
        }
    }


    /**
     * 根据关键词抓取
     * https://pixabay.com/
     * 的图片数据
     */
    public static function catchPixabay($keywords)
    {
        //先进行图片库搜索，有相同的图片就不进行接口查询
        $res1 = Images::find()->where(['like', 'name', ',' . $keywords])->one();
        $res2 = Images::find()->where(['like', 'name', $keywords . ','])->one();

        if ($res1) {
            return [1, $res1->url];
        }

        if ($res2) {
            return [1, $res2->url];
        }

        $keywords = urlencode($keywords);
        $url = 'https://pixabay.com/api/?key=17514488-247283be6b34581cbf430e812&image_type=photo&lang=zh&q=' . $keywords;

        $res = Tools::curlGet($url);

        $resArr = json_decode($res, true);

        if (isset($resArr['hits']) && !empty($resArr['hits'])) {
            $info = $resArr['hits'][0];
            try {
                //标题图片存储七牛云
                list($codeImg, $msgImg) = (new Qiniu())->fetchFile($info['webformatURL'], \Yii::$app->params['QiNiuBucketImg'], Tools::uniqueName('jpg'));
                if ($codeImg < 0) {
                    $error[] = $msgImg;
                }

                if (empty($msgImg)) {
                    return [-1, '没有图片'];
                }

                $data = [
                    'type' => self::TYPE_ZUOWENWANG,
                    'name' => str_replace(' ', '', $info['tags']),
                    'content' => json_encode($resArr),
                    'url' => $msgImg,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                Tools::writeLog($data, 'img.txt');
                list($code, $msg) = self::createOne($data);
                return [1, $msgImg];
            } catch (\Exception $e) {
                //当上传七牛云发生错误的时候则将原数据URL存入
                return [-1, '数据错误'];
            }
        }
        return [-1, '没有数据'];
    }
}
