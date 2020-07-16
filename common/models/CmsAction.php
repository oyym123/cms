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

    const BAIDU_URL = 'http://data.zz.baidu.com/urls';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'phome_ecms_news';
    }

    public static function getDb()
    {
        return Yii::$app->get('db2');
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
        $request = Yii::$app->request;
        $dbName = $request->get('db_name');
        
        if (empty($dbName)) {
            $dbName = $request->post('db_name');
        }

        $db = DbName::find()->where([
            'name' => $dbName,
            'status' => 1
        ])->one();

        if (empty($db)) {
            return [-1, '不存在该数据库！'];
        }

        $res = CmsAction::find()->all();
        $urls = $errorArr = [];
        $info = [];

        //获取所有的文章进行
        foreach ($res as $re) {
            //判断是否已经提交过了
            $flag = MipFlag::checkIsMip($db->id, MipFlag::TYPE_ARTICLE, $re->id);
            if (!empty($flag)) { //表示已经提交过了
                $errorArr[] = $re->id;
            } else {
                //拼接URL
                if (strpos($db->domain, 'http') === false) {
                    $domain = $db->detail_domain;
                } else {
                    $domain = $db->domain;
                }

                $urls[] = $domain . $re->titleurl;

                $info[] = [
                    'type_id' => $re->id,
                    'url' => $domain . $re->titleurl
                ];
            }
        }

        if (empty($urls)) {
            Tools::writeLog($domain . "没有更新的链接可以提交");
            return 1;
        }

        //获取第一条 推送，然后获取到生剩余条数，根据剩余条数 再推送
        $urlFirst = [$urls[0]];

        $resData = $this->push($db->baidu_token, $domain, $urlFirst);

        $jsonres = json_decode($resData);

        if ($jsonres->success >= 400) {
            Tools::writeLog($domain . "百度站长推送失败:" . $jsonres);
            return 1;
        } else {
            Tools::writeLog($domain . "百度站长成功推送第一条" . $jsonres->success . "，今日还可推送:" . $jsonres->remain . "条");
            foreach ($info as $key => $re) {
                if ($key == 0) {
                    //更新插入 标记已经推送过了
                    $saveData = [
                        'db_id' => $db->id,
                        'db_name' => $db->name,
                        'type' => MipFlag::TYPE_ARTICLE,
                        'type_id' => $re['type_id'],
                        'remain' => $jsonres->remain,
                        'url' => $re['url'],
                    ];
                    MipFlag::createOne($saveData);
                }
            }
            $remain = $jsonres->remain;
        }

        if ($remain == 0) {
            Tools::writeLog($domain . "推送次数用完");
            return 1;
        } else {
            $urls = array_slice($urls, 1, $remain);
        }

        if (empty($urls)) {
            Tools::writeLog($domain . "百度站长成功推送1条");
            return 1;
        }

        $resData = $this->push($db->baidu_token, $domain, $urls);
        $jsonres = json_decode($resData);

        if ($jsonres->error >= 400) {
            Tools::writeLog($domain . "百度站长推送失败:" . $res);
        } else {
            Tools::writeLog($domain . "百度站长成功推送" . $jsonres->success . "条，今日还可推送:" . $jsonres->remain . "条");
            foreach ($info as $key => $re) {
                if ($key == 0) {
                    continue;
                }
                //更新插入 标记已经推送过了
                $saveData = [
                    'db_id' => $db->id,
                    'db_name' => $db->name,
                    'type' => MipFlag::TYPE_ARTICLE,
                    'type_id' => $re['type_id'],
                    'remain' => $jsonres->remain,
                    'url' => $re['url'],
                ];
                MipFlag::createOne($saveData);
            }
        }

        echo '<pre>';
        print_r($urls);
        echo '<hr/>';
        print_r($errorArr);
        return 1;
    }

    //获取不同数据库的数据
    public function result2()
    {
        $request = Yii::$app->request;
        $dbName = $request->get('db_name');
        $db = DbName::find()->where([
            'name' => $dbName,
            'status' => 1
        ])->one();

        if (empty($db)) {
            return [-1, '不存在该数据库！'];
        }

        $res = CmsAction::find()->orderBy('id desc')->all();
        $urls = $errorArr = [];
        $info = [];

        //获取所有的文章进行
        foreach ($res as $re) {
            //判断是否已经提交过了
            $flag = MipFlag::checkIsMip($db->id, MipFlag::TYPE_ARTICLE_FAST, $re->id);
            if (!empty($flag)) { //表示已经提交过了
                $errorArr[] = $re->id;
            } else {
                //拼接URL
                if (strpos($db->domain, 'http') === false) {
                    $domain = $db->detail_domain;
                } else {
                    $domain = $db->domain;
                }

                $urls[] = $domain . $re->titleurl;

                $info[] = [
                    'type_id' => $re->id,
                    'url' => $domain . $re->titleurl
                ];
            }
        }

        if (empty($urls)) {
            Tools::writeLog($dbName . "没有快速更新的链接可以提交");
            return 1;
        }

        //获取第一条 推送，然后获取到生剩余条数，根据剩余条数 再推送
        $urlFirst = [$urls[0]];

        $resData = $this->pushFast($db->baidu_token, $domain, $urlFirst);

        $jsonres = json_decode($resData);

        if ($jsonres->success >= 400) {
            Tools::writeLog($dbName . "百度快速推送失败:" . $jsonres);
            return 1;
        } else {
            Tools::writeLog($dbName . "百度快速成功推送第一条" . $jsonres->success . "，今日还可推送:" . $jsonres->remain . "条");
            foreach ($info as $key => $re) {
                if ($key == 0) {
                    //更新插入 标记已经推送过了
                    $saveData = [
                        'db_id' => $db->id,
                        'db_name' => $db->name,
                        'type' => MipFlag::TYPE_ARTICLE_FAST,
                        'type_id' => $re['type_id'],
                        'remain' => $jsonres->remain,
                        'url' => $re['url'],
                    ];
                    MipFlag::createOne($saveData);
                }
            }
            $remain = $jsonres->remain;
        }

        if ($remain == 0) {
            Tools::writeLog($dbName . "快速推送次数用完");
            return 1;
        } else {
            $urls = array_slice($urls, 1, $remain);
        }

        if (empty($urls)) {
            Tools::writeLog($dbName . "百度快速成功推送1条");
            return 1;
        }

        $resData = $this->pushFast($db->baidu_token, $domain, $urls);
        $jsonres = json_decode($resData);

        if ($jsonres->error >= 400) {
            Tools::writeLog($dbName . "百度快速推送失败:" . $res);
        } else {
            Tools::writeLog($dbName . "百度快速成功推送" . $jsonres->success . "条，今日还可推送:" . $jsonres->remain . "条");
            foreach ($info as $key => $re) {
                if ($key == 0) {
                    continue;
                }
                //更新插入 标记已经推送过了
                $saveData = [
                    'db_id' => $db->id,
                    'db_name' => $db->name,
                    'type' => MipFlag::TYPE_ARTICLE_FAST,
                    'type_id' => $re['type_id'],
                    'remain' => $jsonres->remain,
                    'url' => $re['url'],
                ];
                MipFlag::createOne($saveData);
            }
        }

        echo '<pre>';
        print_r($urls);
        echo '<hr/>';
        print_r($errorArr);
        return 1;
    }


    //mip推送
    public function push($token, $domain, $urls)
    {
        $api = self::BAIDU_URL . '?site=' . $domain . '&token=' . $token;
        $ch = curl_init();
        $options = array(
            CURLOPT_URL => $api,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => implode("\n", $urls),
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        return $result;
    }

    //mip推送
    public function pushFast($token, $domain, $urls)
    {
        $api = self::BAIDU_URL . '?site=' . $domain . '&token=' . $token . '&type=daily';
        $ch = curl_init();
        $options = array(
            CURLOPT_URL => $api,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => implode("\n", $urls),
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        return $result;
    }
}
