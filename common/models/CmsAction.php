<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "migration".
 *
 * @property string $version
 * @property int|null $apply_time
 */
class CmsAction extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'phome_ecms_news_data_1';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [

        ];
    }

    //获取不同数据库的数据
    public function result()
    {
        $res = CmsAction::find()->where(['dokey' => 1])->one();

        //获取所有的文章进行
        echo '<pre>';
        print_r($res);
        exit;
    }


    public function push($api, $urls) {
        $urls = array(
            'http://www.example.com/1.html',
            'http://www.example.com/2.html',
        );
        $api = 'http://data.zz.baidu.com/urls?site=https://m.xjscpt.org&token=X0XBWzsIU1iIVYXI';
        $ch = curl_init();
        $options =  array(
            CURLOPT_URL => $api,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => implode("\n", $urls),
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        echo $result;
    }
}
