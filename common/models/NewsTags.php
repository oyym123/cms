<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "migration".
 *
 * @property string $version
 * @property int|null $apply_time
 */
class NewsTags extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'phome_enewstags';
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

    /** 创建一个tags标签 */
    public static function createOne($data)
    {
        if (empty($data['tagname'])) {
            return [-1, '没有标签名称'];
        }

        $tags = self::find()->where(['tagname' => $data['tagname']])->one();

        if (!empty($tags)) {
            return [-1, '关键词重复'];
        }

        $model = new NewsTags();
        $model->tagname = $data['tagname'];
        $model->tagtitle = $data['tagname'];
        $model->tagkey = $data['tagname'];
        $model->tagdes = $data['tagname'];
        $model->fclast = time();

        if (!$model->save()) {
            return [-1, $model->getErrors()];
        }

        return [1, $model];
    }

    /**
     * 获取不同数据库的数据
     *
     */
    public function result()
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

        $res = NewsTags::find()->all();
        $urls = $errorArr = [];
        $info = [];

        //获取所有的文章进行
        foreach ($res as $re) {
            //判断是否已经提交过了
            $flag = MipFlag::checkIsMip($db->id, MipFlag::TYPE_TAG, $re->tagid);
            if (!empty($flag)) { //表示已经提交过了
                $errorArr[] = $re->tagid;
            } else {
                //拼接URL
                if (strpos($db->domain, 'http') === false) {
                    $domain = $db->detail_domain;
                } else {
                    $domain = $db->domain;
                }

                $urls[] = $domain . '/e/tags/?tagid=' . $re->tagid;

                $info[] = [
                    'type_id' => $re->tagid,
                    'url' => $domain . '/e/tags/?tagid=' . $re->tagid,
                ];
            }
        }

        if (empty($urls)) {
            Tools::writeLog($dbName . "没有更新的Tag链接可以提交");
            return 1;
        }

        //获取第一条 推送，然后获取到生剩余条数，根据剩余条数 再推送
        $urlFirst = [$urls[0]];

        $resData = $this->push($db->baidu_token, $domain, $urlFirst);

        $jsonres = json_decode($resData);

        if ($jsonres->success >= 400) {
            Tools::writeLog($dbName . "百度站长Tag推送失败:" . $jsonres);
            return 1;
        } else {
            Tools::writeLog($dbName . "百度站长Tag成功推送第一条" . $jsonres->success . "，今日还可推送:" . $jsonres->remain . "条");
            foreach ($info as $key => $re) {
                if ($key == 0) {
                    //更新插入 标记已经推送过了
                    $saveData = [
                        'db_id' => $db->id,
                        'db_name' => $db->name,
                        'type' => MipFlag::TYPE_TAG,
                        'type_id' => $re['type_id'],
                        'url' => $re['url'],
                        'remain' => $jsonres->remain,
                    ];
                    MipFlag::createOne($saveData);
                }
            }
            $remain = $jsonres->remain;
        }

        if ($remain == 0) {
            Tools::writeLog($dbName . "Tag推送次数用完");
            return 1;
        } else {
            $urls = array_slice($urls, 1, $remain);
        }

        if (empty($urls)) {
            Tools::writeLog($dbName . "百度站长Tag成功推送1条");
            return 1;
        }

        $resData = $this->push($db->baidu_token, $domain, $urls);
        $jsonres = json_decode($resData);

        if ($jsonres->error >= 400) {
            Tools::writeLog($dbName . "百度站长Tag推送失败:" . $res);
        } else {
            Tools::writeLog($dbName . "百度站长Tag成功推送" . $jsonres->success . "条，今日还可推送:" . $jsonres->remain . "条");
            foreach ($info as $key => $re) {
                if ($key == 0) {
                    continue;
                }
                //更新插入 标记已经推送过了
                $saveData = [
                    'db_id' => $db->id,
                    'db_name' => $db->name,
                    'type' => MipFlag::TYPE_TAG,
                    'type_id' => $re['type_id'],
                    'url' => $re['url'],
                    'remain' => $jsonres->remain,
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

        $res = NewsTags::find()->orderBy('tagid desc')->all();
        $urls = $errorArr = [];
        $info = [];

        //获取所有的文章进行
        foreach ($res as $re) {
            //判断是否已经提交过了
            $flag = MipFlag::checkIsMip($db->id, MipFlag::TYPE_TAG_FAST, $re->tagid);
            if (!empty($flag)) { //表示已经提交过了
                $errorArr[] = $re->tagid;
            } else {
                //拼接URL
                if (strpos($db->domain, 'http') === false) {
                    $domain = $db->detail_domain;
                } else {
                    $domain = $db->domain;
                }

                $urls[] = $domain . '/e/tags/?tagid=' . $re->tagid;

                $info[] = [
                    'type_id' => $re->tagid,
                    'url' => $domain . '/e/tags/?tagid=' . $re->tagid,
                ];
            }
        }

        if (empty($urls)) {
            Tools::writeLog($dbName . "没有快速更新的Tag链接可以提交");
            return 1;
        }

        //获取第一条 推送，然后获取到剩余条数，根据剩余条数 再推送
        $urlFirst = [$urls[0]];

        $resData = $this->pushFast($db->baidu_token, $domain, $urlFirst);

        $jsonres = json_decode($resData);

        if ($jsonres->success >= 400) {
            Tools::writeLog($dbName . "百度快速Tag推送失败:" . $jsonres);
            return 1;
        } else {
            Tools::writeLog($dbName . "百度快速Tag成功推送第一条" . $jsonres->success . "，今日还可推送:" . $jsonres->remain . "条");
            foreach ($info as $key => $re) {
                if ($key == 0) {
                    //更新插入 标记已经推送过了
                    $saveData = [
                        'db_id' => $db->id,
                        'db_name' => $db->name,
                        'type' => MipFlag::TYPE_TAG_FAST,
                        'type_id' => $re['type_id'],
                        'url' => $re['url'],
                        'remain' => $jsonres->remain,
                    ];
                    MipFlag::createOne($saveData);
                }
            }
            $remain = $jsonres->remain;
        }

        if ($remain == 0) {
            Tools::writeLog($dbName . "快速Tag推送次数用完");
            return 1;
        } else {
            $urls = array_slice($urls, 1, $remain);
        }

        if (empty($urls)) {
            Tools::writeLog($dbName . "百度快速Tag成功推送1条");
            return 1;
        }

        $resData = $this->pushFast($db->baidu_token, $domain, $urls);
        $jsonres = json_decode($resData);

        if ($jsonres->error >= 400) {
            Tools::writeLog($dbName . "百度快速Tag推送失败:" . $res);
        } else {
            Tools::writeLog("百度快速Tag成功推送" . $jsonres->success . "条，今日还可推送:" . $jsonres->remain . "条");
            $count = count($info);
            foreach ($info as $key => $re) {
                if ($key == 0) {
                    continue;
                }

                if ($key == ($count - 1)) { //记录最后一条记录
                    $remain = $jsonres->remain;
                } else {
                    $remain = 0;
                }

                //更新插入 标记已经推送过了
                $saveData = [
                    'db_id' => $db->id,
                    'db_name' => $db->name,
                    'type' => MipFlag::TYPE_TAG_FAST,
                    'type_id' => $re['type_id'],
                    'url' => $re['url'],
                    'remain' => $remain,
                ];
                MipFlag::createOne($saveData);
            }
        }

        echo '<pre>';
        print_r($urls);
        echo '<hr/>';
        print_r($errorArr);
    }

    //mip推送
    public function push($token, $domain, $urls)
    {
        $api = CmsAction::BAIDU_URL . '?site=' . $domain . '&token=' . $token;
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
        $api = CmsAction::BAIDU_URL . '?site=' . $domain . '&token=' . $token . '&type=daily';
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
