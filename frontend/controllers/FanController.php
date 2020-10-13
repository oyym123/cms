<?php

namespace frontend\controllers;

use common\models\AllBaiduKeywords;
use common\models\ArticleRules;
use common\models\BaiduKeywords;
use common\models\BlackArticle;
use common\models\Category;
use common\models\Domain;
use common\models\DomainColumn;
use common\models\Fan;
use common\models\FanUser;
use common\models\LongKeywords;
use common\models\PushArticle;
use common\models\RedisTools;
use common\models\Template;
use common\models\Tools;
use yii\data\Pagination;
use yii\helpers\FileHelper;
use yii\web\Controller;
use Yii;

class FanController extends Controller
{
    /**
     * @OA\Get(
     *   path="/fan/detail",
     *   summary="网页详情 【前端】",
     *   tags={"网页"},
     *   description="展示模板参数 OYYM 2020/7/30 18:29",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="页面id",
     *     @OA\Schema(
     *        type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="返回码",
     *     @OA\JsonContent( type="json", example=
     *     {
     *       "$models['data']['content']": "内容",
     *       "$models['data']['title']": "标题",
     *       "$models['push_time']": "发布时间",
     *       "$models['data']['avatar']": "头像",
     *       "$models['data']['nickname']": "昵称",
     *       "$models['pre']": "上一条 URL",
     *       "$models['next']": "下一条 URL",
     *       "$models['pre_title']": "上一条标题",
     *       "$models['next_title']": "下一条标题",
     *       "$models['user_url']": "跳转到用户URL链接",
     *       "$models['reading']": "阅读量",
     *       "$models['keywords']": "关键词",
     *     }
     *     )
     *   )
     * )
     */
    public function actionDetail()
    {

        $url = Yii::$app->request->url;
        if (preg_match('/\d+/', $url, $arr)) { //获取id
            $model = PushArticle::find()->select('user_id,title_img,keywords,key_id,content,title,intro,push_time')->where(['id' => $arr[0]])->asArray()->one();
            list($layout, $render) = Fan::renderView(Template::TYPE_DETAIL);
            $this->layout = $layout;
            $column = explode('/', $url)[1];

            if ($user = FanUser::findOne($model['user_id'])) {
                $model['nickname'] = $user->username;
                $model['avatar'] = $user->avatar;
            } else {
                $model['nickname'] = '佚名';
                $model['avatar'] = 'http://img.thszxxdyw.org.cn/userImg/b4ae0201906141846584975.png';
            }

            $preTitle = PushArticle::findOne($arr[0] - 1);
            $nextTitle = PushArticle::findOne($arr[0] + 1);

            if ($preTitle) {
                $preTitle = Tools::getKTitle($preTitle->title);
            } else {
                $preTitle = '没有更多内容啦！';
            }

            if ($nextTitle) {
                $nextTitle = Tools::getKTitle($nextTitle->title);
            } else {
                $nextTitle = '没有更多内容啦！';
            }


            $domain = Domain::getDomainInfo();
            $columnInfo = DomainColumn::find()->where(['name' => $column, 'domain_id' => $domain->id])->one();

            $upArr = ['知乎', '百度知道', '360', '头条'];

            $model['content'] = nl2br($model['content']);
            $model['content'] = str_replace($upArr, '', $model['content']);

            $model['content'] = str_replace(['<p>.</p>', '. .</p>', '</p>.</p>', '<h2></h2>'], ['', '.</p>', '', ''], $model['content']);
//
//            $model['content']= str_replace($upArr, $replaceArrUp, $model['content']);
//
//            foreach ($downArr as $item) {
//                $replaceArrDown[] = $item . '<br/>';
//            }
//            $model['content']= str_replace($downArr, $replaceArrDown, $model['content']);

            $model['user_url'] = '/user/index_' . $model['user_id'] . '.html';
            $oldTitle = $model['title'];
            $model['title'] = Tools::getKTitle($model['title']);

            $res = [
                'data' => $model,
                'pre' => '/' . $column . '/' . ($arr[0] - 1) . '.html',
                'next' => '/' . $column . '/' . ($arr[0] + 1) . '.html',
                'pre_title' => $preTitle,
                'next_title' => $nextTitle,
                'keywords' => $model['keywords'],
                'user_url' => '/user/index_' . $model['user_id'] . '.html',
                'keywords_url' => '/' . $domain->start_tags . $model['key_id'] . $domain->end_tags,
                'reading' => substr(time(), 3) + rand(99, 1000),
                'column_info' => [
                    'name' => $columnInfo['zh_name'],
                    'url' => Tools::getLocalUrl(1) . '/' . $column
                ],
            ];

//            echo '<pre>';
//            print_r($res);
//            exit;


            $desc = $model['intro'];

            $view = Yii::$app->view;
            $view->params['detail_tdk'] = [
                'canonical' => 'https://' . $_SERVER['HTTP_HOST'] . $url,
                'title' => $model['title'] . '_' . $domain->zh_name,
                'keywords' => $oldTitle,
                'description' => $desc,
                'og_type' => 'news',
                'og_title' => $model['title'],
                'og_description' => $desc,
                'og_image' => $model['title_img'],
                'og_release_date' => $desc,
            ];

            return $this->render($render, [
                'models' => $res,
            ]);
        }
    }

    /**
     * @OA\Get(
     *     path="/fan/index",
     *     summary="列表页 【前端】 循环参数 $models",
     *     tags={"网页"},
     *     description="展示模板参数 OYYM 2020/7/30 18:35",
     *   @OA\Response(
     *     response=200,
     *     description="返回码",
     *     @OA\JsonContent( type="json", example=
     *     {
     *       "$item['title']": "标题",
     *       "$item['intro']": "简介",
     *       "$item['user_id']": "用户id",
     *       "$item['nickname']": "用户昵称",
     *       "$item['avatar']": "用户头像",
     *       "$item['push_time']": "发布时间",
     *       "$item['is_hot']": "是否热门 0=不热门 1=热门",
     *       "$item['is_top']": "是否置顶 0=不置顶 1=置顶",
     *       "$item['is_recommend']": "是否推荐 0=不推荐 1=推荐",
     *       "$item['keywords_url']": "关键词URL",
     *       "$item['user_url']": "跳转到用户URL链接",
     *       "$item['column_info']['name']": "当前栏目名称",
     *       "$item['column_info']['url']": "当前栏目URL",
     *     }
     *     )
     *   ),
     * )
     */
    public function actionIndex()
    {
        //url转换 分页
        $url = Yii::$app->request->url;
        if (strpos($url, 'index_') && preg_match('/\d+/', $url, $arr)) {
            $_GET['page'] = $arr[0];
        }

        $lastId = PushArticle::find()->select('id')->orderBy('id desc')->one()->id;

        //获取当前栏目
        $columnName = explode('/', $url)[1];
        $domain = Domain::getDomainInfo();

        $column = DomainColumn::find()->where(['name' => $columnName, 'domain_id' => $domain->id])->one();

        list($layout, $render) = Fan::renderView(Template::TYPE_LIST);
        $this->layout = $layout;

        //表示是用户列表
        if (strpos($url, '/user') !== false) {

            preg_match('/\d+/', $url, $userId);
            list($models, $pages) = $this->user($userId, $domain);
            $res = [
                'home_list' => $models,
            ];

            $view = Yii::$app->view;
            $view->params['user_tdk'] = [
                'title' => $models[0]['nickname'] . '_' . $domain->zh_name,
                'keywords' => $models[0]['nickname'] . '_' . $domain->zh_name,
                'intro' => $column->intro . '_' . $domain->zh_name,
                'canonical' => 'https://' . $_SERVER['HTTP_HOST'] . $url,
            ];

            return $this->render($render, [
                'models' => $res,
                'pages' => $pages,
            ]);
        }

        $andWhere = [];

        if ($column->is_change) {
            if ($lastId < 280) {
                $andWhere = [];
            } else {
                $maxRand = rand($lastId - 30, $lastId);
                $minRand = rand($lastId - 280, $lastId - 101);
                $andWhere = ['between', 'id', $minRand, $maxRand];
            }
        }

        $query = PushArticle::find()->select('id,column_id,column_name,user_id,key_id,keywords,title_img,title,intro,push_time')
            ->andWhere($andWhere)
            ->orderBy('Rand()')
            ->andWhere(['column_id' => $column->id])
            ->limit(10);

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->asArray()->all();

        $columnZhName = '';
        if (!empty($models)) {
            $columnObj = DomainColumn::findOne($models[0]['column_id']);
            if (!empty($columnObj)) {
                $columnZhName = $columnObj->zh_name;
                $columnEnName = $columnObj->name;
            }
        }

        foreach ($models as &$item) {
            $item['title'] = Tools::getKTitle($item['title']);
//            $item['push_time'] = date('Y-m-d H:i:s', (time() - 3600));
            $item['url'] = '/' . $columnEnName . '/' . $item['id'] . '.html';
            $item['user_url'] = '/user/index_' . $item['user_id'] . '.html';
            $item['keywords_url'] = '/' . $domain->start_tags . $item['key_id'] . $domain->end_tags;
            if ($user = FanUser::findOne($item['user_id'])) {
                $item['nickname'] = $user->username;
                $item['avatar'] = $user->avatar;
                $item['is_hot'] = 1;
                $item['is_top'] = 1;
                $item['is_recommend'] = 1;
            } else {
                $item['nickname'] = '佚名';
                $item['avatar'] = 'http://img.thszxxdyw.org.cn/userImg/b4ae0201906141846584975.png';

            }
            $item['column_info'] = [
                'name' => $columnZhName,
                'url' => Tools::getLocalUrl(1) . '/' . $columnName
            ];
        }

//        print_r( $this->layout );exit;

        $res = [
            'home_list' => $models,
            'column_info' => [
                'name' => $columnZhName,
                'url' => Tools::getLocalUrl(1) . '/' . $columnName
            ],
        ];


        $view = Yii::$app->view;

        $view->params['list_tdk'] = [
            'title' => $column->title ?: $column->zh_name . '_' . $domain->zh_name,
            'keywords' => $column->keywords ?: $column->zh_name,
            'intro' => $column->intro ?: $column->zh_name,
            'canonical' => 'https://' . $_SERVER['HTTP_HOST'] . '/' . $columnName,
        ];

        return $this->render($render, [
            'models' => $res,
            'pages' => $pages,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/fan/user",
     *     summary="用户页 【前端】 循环参数 $models",
     *     tags={"网页"},
     *     description="展示模板参数 OYYM 2020/7/30 18:35",
     *   @OA\Response(
     *     response=200,
     *     description="返回码",
     *     @OA\JsonContent( type="json", example=
     *     {
     *       "$item['title']": "标题",
     *       "$item['intro']": "简介",
     *       "$item['user_id']": "用户id",
     *       "$item['nickname']": "用户昵称",
     *       "$item['avatar']": "用户头像",
     *       "$item['push_time']": "发布时间",
     *       "$item['is_hot']": "是否热门 0=不热门 1=热门",
     *       "$item['is_top']": "是否置顶 0=不置顶 1=置顶",
     *       "$item['is_recommend']": "是否推荐 0=不推荐 1=推荐",
     *       "$item['keywords']": "关键词",
     *       "$item['keywords_url']": "关键词URL",
     *       "$item['user_url']": "跳转到用户URL链接",
     *       "$item['column_info']['name']": "当前栏目名称",
     *       "$item['column_info']['url']": "当前栏目URL",
     *     }
     *     )
     *   ),
     * )
     */
    public function user($userId, $domain)
    {
        $query = PushArticle::find()->select('id,column_id,column_name,user_id,keywords,key_id,title_img,title,intro,push_time')->where(['user_id' => $userId])->limit(10);
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->asArray()->all();

        $columnZhName = '';
        if (!empty($models)) {
            $columnObj = DomainColumn::findOne($models[0]['column_id']);
            if (!empty($columnObj)) {
                $columnZhName = $columnObj->zh_name;
                $columnEnName = $columnObj->name;
            }
        }

        foreach ($models as &$item) {
            $item['title'] = Tools::getKTitle($item['title']);
            $item['user_url'] = '/user/index_' . $item['user_id'] . '.html';
            $item['url'] = '/' . $columnEnName . '/' . $item['id'] . '.html';
            $item['keywords_url'] = '/' . $domain->start_tags . $item['key_id'] . $domain->end_tags;
            if ($user = FanUser::findOne($item['user_id'])) {
//                $item['push_time'] = Tools::formatTime(time() - 3600);
                $item['push_time'] = Tools::formatTime(strtotime($item['push_time']));
                $item['nickname'] = $user->username;
                $item['avatar'] = $user->avatar;
                $item['is_hot'] = 1;
                $item['is_top'] = 1;
                $item['is_recommend'] = 1;
                $item['column_info'] = [
                    'name' => $columnZhName,
                    'url' => Tools::getLocalUrl(1) . '/' . $columnEnName
                ];
            }
        }


        return [$models, $pages];
    }

    /**
     * @OA\Get(
     *     path="/fan/common",
     *     summary="公共页 【前端】",
     *     tags={"网页"},
     *     description="展示模板参数 OYYM 2020/7/30 18:35",
     *   @OA\Response(
     *     response=200,
     *     description="返回码",
     *     @OA\JsonContent( type="json", example=
     *     {
     *       "Domain::getLinks()":          " 【友情链接】   用于循环  $item['url']         $item['name']",
     *       "BaiduKeywords::hotKeywords()": "【热门标签】   用于循环  $item['url']         $item['keywords']",
     *       "PushArticle::newArticle()": "   【最新文章】   用于循环  $item['url']         $item['title']",
     *       "PushArticle::hotArticle()": "   【热门文章】   用于循环  $item['title_img']   $item['url']    $item['title']   $item['push_time'] $item['nickname']   $item['avatar']  $item['user_url']",
     *       "DomainColumn::getColumn(0, '', 'person')": " 【导航栏】   用于循环  $item['name'] = url   $item['zh_name']",
     *     }
     *     )
     *   ),
     * )
     */
    public function actionCommon()
    {

    }


    /**
     * @OA\Get(
     *     path="/fan/tags-list",
     *     summary="标签页 【前端】 循环参数  $models",
     *     tags={"网页"},
     *     description="展示模板参数 OYYM 2020/7/30 18:35",
     *   @OA\Response(
     *     response=200,
     *     description="返回码",
     *     @OA\JsonContent( type="json", example=
     *     {
     *       "$item['id']": "标签id",
     *       "$item['name']": "标签名称",
     *       "$item['url']": "标签 URL",
     *     }
     *     )
     *   ),
     * )
     */
    public function actionTagsList()
    {
        //url转换 分页
        $url = Yii::$app->request->url;
        if (strpos($url, 'index_') && preg_match('/\d+/', $url, $arr)) {
            $_GET['page'] = $arr[0];
        }

        //获取当前栏目
        $columnName = explode('/', $url)[1];
        $domain = Domain::getDomainInfo();

        $column = DomainColumn::find()
            ->where(['name' => $columnName, 'domain_id' => $domain->id])
            ->one();

        //查询文章规则
        $rules = ArticleRules::find()->select('category_id')->where(['domain_id' => $domain->id])->asArray()->all();
        $typeIds = array_column($rules, 'category_id');
        //该域名下所有设定的类型 没有文章也进行展示
        $query = AllBaiduKeywords::find()
            ->where(['in', 'type_id', $typeIds])
//            ->andWhere(['>', 'm_pv', AllBaiduKeywords::FLAG_M_PV])
            ->select('id,keywords as name')
            ->orderBy('id desc')
            ->limit(10);

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => '120']);

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->asArray()->all();

//        $redis = Yii::$app->redis2;
//
//        $redis->set("test0", '1232');
//        exit;
//
//        if ($redis->hlen("$page" . "0") <= 0) {
//            foreach ($models as $key => $v) {
//                $redis->hmset("$page" . "$key", $v);
//            }
//        } else {
//            foreach ($models as $key => $v) {
//                $b[] = $redis->hmget("$page" . "$key", ["id", "goods_name", "goods_stock", "goods_price", "goods_img", "goods_visit"]);
//            }
//            $models = $b;
//        }
//
//
//        $countQuery = clone $query;
//        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => '120']);
//
//        $models = $query->offset($pages->offset)
//            ->limit($pages->limit)
//            ->asArray()->all();

        $domain = Domain::getDomainInfo();

        if ($domain) {
            foreach ($models as &$item) {
                $item['url'] = '/' . $domain->start_tags . $item['id'] . $domain->end_tags;
            }
        }

        $res = [
            'home_list' => $models,
            'column_info' => [
                'name' => '',
                'url' => ''
            ],
        ];

        list($layout, $render) = Fan::renderView(Template::TYPE_TAGS);
        $this->layout = $layout;

        if (Yii::$app->request->isAjax) {
            exit(json_encode($models));
        } else {
            $view = Yii::$app->view;
            $view->params['tags_list_tdk'] = [
                'title' => $column->title ?: $column->zh_name . '_' . $domain->zh_name,
                'keywords' => $column->keywords ?: $column->zh_name,
                'intro' => $column->intro ?: $column->zh_name,
                'canonical' => 'https://' . $_SERVER['HTTP_HOST'] . '/' . $columnName,
            ];

            return $this->render($render, [
                'column' => DomainColumn::getColumn(),
                'models' => $res,
                'pages' => $pages,
            ]);
        }
    }

    /**
     * @OA\Get(
     *     path="/fan/tags-detail",
     *     summary="泛内页 【前端】",
     *     tags={"网页"},
     *     description="展示模板参数 OYYM 2020/7/30 18:35",
     *   @OA\Response(
     *     response=200,
     *     description="返回码",
     *     @OA\JsonContent( type="json", example=
     *     {
     *       "$models['title']": "标题",
     *       "$models['intro']": "简介",
     *       "$models['user_id']": "用户id",
     *       "$models['user_url']": "跳转到用户URL链接",
     *       "$models['nickname']": "用户昵称",
     *       "$models['avatar']": "用户头像",
     *       "$models['push_time']": "发布时间",
     *     }
     *     )
     *   ),
     * )
     */
    public function actionTagsDetail()
    {
        $url = Yii::$app->request->url;
        if (preg_match('/\d+/', $url, $arr)) { //获取id
            $modelInfo = PushArticle::find()
                ->select('user_id,keywords,id,column_name,title_img,content,title,intro,push_time,column_id')
                ->where(['key_id' => $arr])
//                ->andWhere(['like', 'title_img', 'http'])
                ->asArray()->one();

            $columnZhName = '';
            $model = $modelInfo;
            if (!empty($model)) {
                $columnObj = DomainColumn::findOne($model['column_id']);
                if (!empty($columnObj)) {
                    $columnZhName = $columnObj->zh_name;
                    $columnEnName = $columnObj->name;
                }
            }
            $oldTitle = $model['title'];
            $model['title'] = Tools::getKTitle($model['title']);
            list($layout, $render) = Fan::renderView(Template::TYPE_INSIDE);
            $this->layout = $layout;
            $model['url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $columnEnName . '/' . $model['id'] . '.html';
            $model['user_url'] = '/user/index_' . $model['user_id'] . '.html';

            if ($user = FanUser::findOne($model['user_id'])) {
//                $model['push_time'] = date('Y-m-d H:i:s', (time() - 3600));
                $model['nickname'] = $user->username;
                $model['avatar'] = $user->avatar;
            } else {
                $model['nickname'] = '佚名';
                $model['avatar'] = 'http://img.thszxxdyw.org.cn/userImg/b4ae0201906141846584975.png';
            }
            $res = [
                'data' => $model
            ];

            $domain = Domain::getDomainInfo();
            $view = Yii::$app->view;
            $view->params['tags_tdk'] = [
                'title' => $model['keywords'] . '_' . $domain->zh_name,
                'keywords' => $model['keywords'],
                'intro' => $model['intro'],
                'canonical' => 'https://' . $_SERVER['HTTP_HOST'] . $url,
            ];

            if (empty($modelInfo)) {
                $keywordsInfo = AllBaiduKeywords::findOne($arr);

                $view->params['tags_tdk'] = [
                    'title' => $keywordsInfo->keywords . '_' . $domain->zh_name,
                    'keywords' => $keywordsInfo->keywords,
                    'intro' => $keywordsInfo->keywords,
                    'canonical' => 'https://' . $_SERVER['HTTP_HOST'] . $url,
                ];
                return $this->render($render, ['models' => ['data' => ['title' => $keywordsInfo->keywords]]]);
            }

            return $this->render($render, ['models' => $res]);
        }
    }
}
