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
            $model['content']= '看到误导别人的就想说两句，我说了也许没说服力，以下为证，希望可以帮到以后有这样疑问的人，祝你们都能实现自己的梦想：

东南大学二○○九年研究生新生报到须知

一、 报到注册时间：2009年9月1日全天。凡本年度被录取的博士、硕士研究生均于当天凭校园一卡通到指定各校区迎新工作点进行身份确认，详细地点及具体时间，请关注迎新网页的公告。 二、 报到及住宿校区情况： 丁家桥校区：基础医学院、临床医学院、公共卫生学院； 九龙湖校区：机械学院、数学系、物理系、材料科学与工程学院、人文学院、法学院、艺术学院、经济管理学院、外国语学院、化学化工学院、计算机科学与工程学院、情报科学技术研究所。 四牌楼校区：除上述院系以外的其他研究生培养单位。 三、报到时应带以下材料： 1、《新生录取通知书》。 2、复试时未交验过学历或学位证书的应届毕业生： 1)硕士生：必须携带本科毕业证书原件 2)博士生：必须携带硕士学位证书原件 3、本人身份证。 4、近期免冠正面一寸黑白或彩色照片8张。 5、自愿将户口迁入学校的新生应带《户口迁移证》（定向生和委培生的户口不能迁入学校）。要求：新生所持的《户口迁移证》、身份证、《新生录取通知书》上的姓名、身份证号码要完全一致。户口迁移地址填写：东南大学。新生必须自留一份《录取通知书》和《户口迁移证》的复印件备用。 6、党(团)员应带党(团)组织关系转移证明(MBA和原单位定向或委托培养的研究生除外)。转党组织关系,外省新生应由县级以上党委组织部转至江苏省委教育工委组织处；本省新生由县级以上党委组织部转至东南大学党委组织部。团组织关系由基层团委直接转至东南大学团委。 7、随录取通知书发放的有本人校园一卡通、银行卡、代缴学杂费授权书（请事先填写好授权人姓名、身份证号码、银行卡号、联系电话、授权人签名等栏目,报到时交给相关部门）。 四、研究生交费的相关情况： 1、培养费：MBA为2万元/学年；法律专业为1万元/学年；软件专业（学制两年），第一学年2.4万元、第二学年1.6万元，共4万元。 博士生：委培博士生按1.2万元/学年缴纳； 硕士生：委培硕士生按0.8万元/学年缴纳，第一学年享受四等奖助学金的普通硕士生按0.4万元/学年缴纳，第一学年不享受奖助学金的普通硕士生（注：这类考生的录取通知书上只标明录取类别为普通，未标明奖助学金等级）按0.8万元/年缴纳；其他类别研究生（包括博士、硕士）第一学年不需要缴纳培养费。 2、体检费用：每生20元。 3、住宿费：每生每年900元～1200元（视宿舍具体情况而定）。 4、上述三项入学所需费用一律通过本人中国银行借记卡支付，我校不接受现金交费。请新生在报到前15日内将相关费用按足额存入本人银行卡，以保证扣款一次成功。欠费学生将不能注册选课。 5、困难学生可申请贷款。每年最高贷款额度为6000元，但第一学年的费用须在报到注册时交清。江苏、湖北、陕西、重庆、甘肃等省份的研究生可在当地申请生源地助学贷款，其他省份的研究生可来校后申请国家助学贷款，所需材料请关注迎新网页。 五、住宿申请及公寓物品： 1、所有09级研究生新生（除南京市各单位定向或委托培养的研究生），务必于7月20至27日之间在我校迎新网页上确认是否需要住宿，否则视作自动放弃。请广大新生不要提前来校！ 2、学校可提供公寓物品，自愿购买，有需要者须于7月20至27日之间在迎新网站上预约（须整套购买），报到时将会摆放到所分配宿舍内。 六、交通： 1、9月1日7：30起，南京火车站设有研究生新生接待站，可将新生送至各校区。 2、前往丁家桥校区：⑴南京火车站和中央门汽车站，可乘32路公共汽车到青石村下车；⑵从长途汽车东站，可乘45路公共汽车到青石村下车。 3、前往四牌楼校区：⑴从南京火车站西侧乘坐游1路公交车；⑵从南京火车站东侧乘坐44路公交车;⑶从中央门长途车站乘坐游1路公交车；⑷从南京长途东站乘坐2路公交车。以上均在在鸡鸣寺站下车。 4、前往九龙湖校区：⑴从南京火车站乘坐地铁1号线；⑵从中央门长途车站乘坐1路公交车在鼓楼换乘地铁1号线；⑶从南京长途东站乘坐2路公交车在三山街站换乘地铁1号线。乘坐地铁1号线均到安德门站换乘清安线到清水亭东站下车即到九龙湖校区西门。 七、有下列情况之一者将无法办理报到注册手续： 1、各种证件上姓名及出生日期不一致； 2、未按要求携带毕业证书或学位证书； 3、未按要求交纳相关研究生费用。 八、其他注意事项： 1、入住九龙湖校区新生的行李箱长、宽、高尺寸不应大于75×50×25(厘米)，入住其他校区新生的行李箱长、宽、高尺寸不应大于78×52×40(厘米)，以便能放入家具箱柜。蚊帐、过冬衣、被等生活必需品及学习用品自备。 2、托运行李时请用“快件”，并寄“南京火车站”。切勿托寄“慢件”，切勿寄到“南京西站”。行李请勿提前托运，以免遗失或因超期存放被车站扣罚。 3、外地新生，应届毕业生可在原学校按规定领取派遣费，在职人员由原单位发放派遣费，我校不予结算。 4、我校不予办理参加过工作的新生的公积金、失业金、养老金，请放在原单位或人才交流中心代管。 5、请及时登陆迎新网页查看《选课须知》，到校后尽快与导师联系制定培养计划并选课。 6、新生请按时报到，如因特殊原因不能按时报到，应事先向东南大学研究生院研究生管理办公室书面请假（注明学号和姓名，请假不得超过两周）。逾期不报到注册者将取消入学资格。 九、报到期间联系电话： 1、招生咨询：025-83792452； 2、学籍管理、新生请假：025-83795933；传真：025-83613851； 3、住宿咨询：025-83792789； 4、户口咨询：025-83792413；';

            $model['content'] = str_replace(['<br/>'], [''], $model['content']);

            //前面转换
            $upArr = [
                '一，', '二，', '三，', '四，', '五，',
                '一、', '二、', '三、', '四、', '五、',
                '一,', '二,', '三,', '四,', '五,',
                '1、', '2、', '3、', '4、', '5、',
                '1，', '2，', '3，', '4，', '5，',
                '1,', '2,', '3,', '4,', '5,'
            ];


            //后面转换
            $downArr = ['?', '？', '！ ', '？ ', '。 ', '! ',];
            $replaceArrUp = $replaceArrDown = [];

            foreach ($upArr as $item) {
                $model['content'] = str_replace($item,'<br/>' . $item, $model['content']);
            }

            $upArr = ['知乎', '百度知道', '360', '头条'];

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
