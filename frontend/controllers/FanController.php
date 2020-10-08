<?php

namespace frontend\controllers;

use common\models\AllBaiduKeywords;
use common\models\BaiduKeywords;
use common\models\BlackArticle;
use common\models\Domain;
use common\models\DomainColumn;
use common\models\Fan;
use common\models\FanUser;
use common\models\LongKeywords;
use common\models\PushArticle;
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
//            $model['content'] = "2. 请翻开书到12页。
//15 Please take out your notebooks/exercise books.请拿出笔记本/练习本。
//16 No more talking, please. 请安静。
//17 Attention, please. 请注意。
//18 Let’s have a dictation. 让我们来听写。
//19 We’re going to have a new lesson today.今天我们要上新课。
//20 First let’s have a revision. 首先我们复习一下。
//21 Who can answer this question? 谁能回答这个问题？
//22 Do you have any questions? 你们有问题吗？
//24 Let me see. 让我看看/想想。
//25 Put up your hands if you have any questions. 如果有问题请举手。
//26 Raise your hands, please. 请举手。
//27 Hands down. 把手放下。
//28 Repeat after me/Follow me. 跟我读。
//29 Listen to me, please. 请听我说。
//30 Look at the blackboard/screen, please. 请看黑板/屏幕。
//31 All eyes on me, please. 请都看着我。
//32 Can you solve this problem? 能做出这道题吗？
//33 Let’s read it together. Ready, go!大家齐声朗读，预备，起。
//34 Read slowly and clearly. 读慢一点，清楚一点。
//35 Who wants to try? 谁想试一试？
//36 Who wants to do it on the blackboard? 谁愿意到黑板上来做？
//37 Are you through? 做完了吗？
//38 Have you finished? 做完了吗？
//39 You did a very good job. 做得不错。
//40 Very good./Good try./ Well done! 完成得不错。
//41 Terrific!/ Wonderful! / Excellent! 很棒！
//42 Please give him (her) a big hand. 请给他/她一些掌声。
//43 Can you follow me? 能跟上吗？
//44 Do you understand? 你听懂了吗？
//45 Don’t be nervous. 不要紧张。
//46 Any one can help him/ her? 谁来帮他/她一下？
//47 Any volunteers? 谁自愿回答？
//48 I beg your pardon? 对不起，能再说一遍吗？
//49 Take it easy.请放心/别紧张。
//50 Be brave / active, please. 请勇敢/主动些。
//51 Who wants to try? 谁来试试？
//52 Come up to the front, please. 请到前面来。
//53 Go back to your seat, please. 请回座位。
//54 Come on. You can do it. 来吧！你能做到的。
//55 Come on, you’re almost there.来吧！你快（做/答）对了。
//56 I’ll give you a clue (hint). 我给你一些提示。
//57 You can do it this way. 你可以这样来做。
//58 Let’s play a game. 让我们玩个游戏。
//59 Are you tired? Let’s take a break.累了吗？休息一下。
//60 Look up the word in the dictionary. 在字典里查这个词。
//61 Take notes, please.请作笔记。
//62 Are you clear ? 明白了吗？
//63 Is that right /correct? 那个正确吗？
//64 Can you find the mistakes? 你能找出错误吗？
//65 Do you know how to correct the mistakes? 你知道怎么改错吗？
//66 Are you ready? 准备好了吗？
//67 Can you guess it? 能猜猜吗？
//68 Yes. You’re right.对，你对了。
//69 I’m sorry. Can you say that again? 对不起，能再说一遍吗?
//70 Take your time. 慢慢来。
//71 Use your head. 动动脑筋。
//72 Good idea! That makes sense. 好主意。有道理。
//73 Whose turn is it? 轮到谁了？
//74 Now you’re going to read one by one. 现在你们依次朗读。
//75 Who’s next? 接下来是谁？
//76 You’re next.接下来是你。
//77 It’s your turn.轮到你了。
//78 Just hands. No voices. 不要说，请举手。
//79 Do it on your own.自己做。
//80 From the very beginning. 从头开始。
//81 Please read it to the end. 请读到结尾。
//82 Stop here, please. 请停下来。
//83 Hands up before you answer. 回答问题前，请举手。
//84 Here’s your homework for today. 这是今天的家庭作业。
//85 Hand in your homework tomorrow. 家庭作业明天交。
//86 Please pass the exercise books to the front.请将练习本递到前面来。
//87 Who wants to come to the front? 谁愿意到前面来？
//88 Come to my office after class. 下课后到办公室找我。
//89 Come and see me after class. 课后找我。
//90 Watch me and I'll show you.看着我，我来演示。
//91 I want all of you to answer this question. 我请大家一齐来回答这个问题。";
//            $model['content'] = str_replace(['<br/>','\r\n'], ['','<p></p>'], $model['content']);
//
//            $numberLower = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10'];
//            $numberUp = ['一', '二', '三', '四', '五', '六', '七', '八', '九', '十'];
//
//            $symbols = [
//                '、' => '<p></p>',
//                ':' => '<p></p>',
//                '：' => '<p></p>',
//                '.' => '<p></p>',
//            ];
//
//            $resReplace = ['⑴', '⑵', '⑶', '⑷'];
//
//            $upArrOne = [];
//
//            foreach ($numberLower as $itemLower) {
//                foreach ($symbols as $key => $symbol) {
//                    $upArrOne[$itemLower . $key] = $symbol;
//                }
//            }
//
//            $upArrTwo = [];
//            foreach ($numberUp as $itemUp) {
//                foreach ($symbols as $key => $symbol) {
//                    $upArrTwo[$itemUp . $key] = $symbol . '　　<p></p>';
//                }
//            }
//
//
//            //后面转换
//            $downArr = ['?', '？', '！ ', '？ ', '。 ', '! ',];
//            $replaceArrUp = $replaceArrDown = [];
//
//            foreach ($upArrOne as $key => $item) {
//                $model['content'] = str_replace($key, $item . $key, $model['content']);
//            }
//
//            foreach ($upArrTwo as $key => $item) {
//                $model['content'] = str_replace($key, $item . $key, $model['content']);
//            }
//
//            foreach ($resReplace as $item) {
//                $model['content'] = str_replace($item, '<p></p>' . $item, $model['content']);
//            }

            $upArr = ['知乎', '百度知道', '360', '头条'];

            $model['content'] = nl2br( $model['content']);
            $model['content'] = str_replace($upArr, '', $model['content']);

            $model['content'] = str_replace(['<p>.</p>', '. .</p>', '</p>.</p>'], ['', '.</p>', ''], $model['content']);
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
                'title' => $model['title'],
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

        $query = AllBaiduKeywords::find()
            ->where(['domain_id' => $domain->id])
            ->select('id,keywords as name')
            ->limit(10);

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => '120']);

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->asArray()->all();

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
                $model['nickname'] = $user->username;
                $model['avatar'] = $user->avatar;
            } else {
                $model['nickname'] = '佚名';
                $model['avatar'] = 'http://img.thszxxdyw.org.cn/userImg/b4ae0201906141846584975.png';
            }
            $res = [
                'data' => $model
            ];

            $view = Yii::$app->view;
            $view->params['tags_tdk'] = [
                'title' => $model['keywords'],
                'keywords' => $model['keywords'],
                'intro' => $model['intro'],
                'canonical' => 'https://' . $_SERVER['HTTP_HOST'] . $url,
            ];

            if (empty($modelInfo)) {
                return $this->render($render, ['models' => ['data' => ['title' => '没有内容了!']]]);
            }

            return $this->render($render, ['models' => $res]);
        }
    }
}
