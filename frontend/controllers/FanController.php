<?php

namespace frontend\controllers;

use common\models\BlackArticle;
use common\models\DomainColumn;
use common\models\Tools;
use yii\data\Pagination;
use yii\helpers\FileHelper;
use yii\web\Controller;
use Yii;

class FanController extends Controller
{
    public function actionDetail()
    {
        $this->layout = "fan1/home";
        $url = Yii::$app->request->url;
        if (preg_match('/\d+/', $url, $arr)) {
            $model = BlackArticle::find()->where(['id' => $arr[0]])->asArray()->one();
            $render = Tools::jumpDomain('fan1/m_static/detail.html', 'fan1/static/detail.html', $_SERVER['HTTP_HOST']);
            return $this->renderPartial($render, ['model' => $model]);
        }
    }

    public function actionIndex()
    {
        //url转换 分页
        $url = Yii::$app->request->url;
        if (strpos($url, 'index_') && preg_match('/\d+/', $url, $arr)) {
            $_GET['page'] = $arr[0];
        }

        $this->layout = "fan1/home";
        $query = BlackArticle::find()->limit(10);
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        $render = Tools::jumpDomain('fan1/m_static/index', 'fan1/static/index', $_SERVER['HTTP_HOST']);
        return $this->render($render, [
            'column' => DomainColumn::getColumn(),
            'models' => $models,
            'pages' => $pages,
        ]);
    }

    /** 生成缓存的数据 */
    public function actionCacheData()
    {
        $lists = BlackArticle::find()->where()->limit(10)->all();
        foreach ($lists as $list) {
            $path = Url::to(['acticle/view', 'id' => $list]);  // '/acticle/view/104.html'
            if (substr($path, 0, 1) === '/') {
                $path = substr($path, 1);  // 'article/view/104.html'
            }
            FileHelper::createDirectory(dirname($path));             // 创建目录
            $result = self::runAction('view', ['id' => $list]);   // 获取执行结果
            file_put_contents($path, $result);                       // 写入文件
        }
    }
}